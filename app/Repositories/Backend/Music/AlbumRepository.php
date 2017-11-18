<?php 

namespace App\Repositories\Backend\Music;

use App\Repositories\BaseRepository;
use App\Models\Music\Album\Album;
use App\Events\Backend\Music\Album\AlbumCreated;
use App\Events\Backend\Music\Album\AlbumUpdated;
use App\Events\Backend\Music\Album\AlbumDeleted;
use App\Exceptions\GeneralException;
use Symfony\Component\HttpFoundation\File\File;
use App\Helpers\Validators\Mimes;
use SplFileInfo;
use Download;
use Spatie\Image\Image;
use App\Models\Music\Artist\Artist;
use Illuminate\Http\UploadedFile;
use App\Repositories\Backend\Music\CacheRepository;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
use Exception;


class AlbumRepository extends BaseRepository
{
	const MODEL = Album::class;

	protected $tracks;
	protected $artists;
	protected $genres;
	protected $validate;
	protected $categories;
	protected $cache;

	public function __construct(CacheRepository $cache, TrackRepository $tracks, ArtistRepository $artists, 
								GenreRepository $genres, Mimes $validate, CategoryRepository $categories)
	{
		$this->tracks = $tracks;
		$this->artists = $artists;
		$this->genres = $genres;
		$this->validate = $validate;
		$this->categories = $categories;
		$this->cache = $cache;
	}

	public function create(array $data)
	{
		$fulltitle = str_ireplace('–', '-', $data['full_title']);
		$fulltitle = splitTitle($fulltitle);
		$data['title'] = $fulltitle['title'];
		$data['artists'] = $fulltitle['artists'];
		$album = $this->createAlbumStub($data);
		$this->attachCategoryAndGenre($album, $data)->save();
		$this->attachArtists($album, $data['artists']);
		// Set Album Slug 

		$slug = str_slug("{$album->fresh()->full_title}");
		if ($this->query()->similarSlug($slug)->exists()) {
            $album->forceDelete();
            return false;
        }
        $album->update(['slug' => $slug]);
		event(new AlbumCreated($album));

		return $album;
	}

	public function update(Album $album, array $input)
	{
		// Create Artist Entity or Fetch One
		$album->artists()->detach();
		$this->attachArtists($album, $input['artists']);

		$album->title = $input['title'];
		$album->description = $input['description'];
		$album->type = $input['type'];

		// Detach Previous Category
		$album->category()->dissociate();
		// Detach Previous Genres
		$album->genre()->dissociate();
		// Create and Attach Genres & Category
		$this->attachCategoryAndGenre($album, $input)->save();
		// Set Album Slug 
		$this->setSlug($album);

		event(new AlbumUpdated($album));

		if ($album->tracks->count() > 1) {
			$tracks = $album->tracks;
			$tracks->each(function($track) {
				// TODO: Queue this  part...
				$this->tracks->setFileTags($track);
				$this->cache->clear('tracks', $track->id);
			});
		}

		return $album;
	}

	public function updateBulk(array $datas)
	{
		foreach($datas as $id => $data) {
			$album =  Album::findOrFail($id);
			$fulltitle = splitTitle($data['full_title']);
			$data['title'] = $fulltitle['title'];
			$data['artists'] = $fulltitle['artists'];

			$album->title = $data['title'];
			$album->description = $data['description'];
			$album->type = $data['type'];

			// Detach Previous Category
			$album->category()->dissociate();

			// Detach Previous Genres
			$album->genre()->dissociate();
			$this->attachCategoryAndGenre($album, $data)->save();

			// Create Artist Entity or Fetch One
			$album->artists()->detach();
			$this->attachArtists($album, $data['artists']);

			// Set Album Slug 
			$this->setSlug($album);
			event(new AlbumUpdated($album));
		}

		return true;
	}

	public function deleteBulk(array $datas)
	{
		foreach($datas as $id => $data) {
			if ($data['confirm'] == 'yes') {
				$album =  Album::findOrFail($id);
				$this->delete($album);
			}
		}
		return true;
	}

	public function attachArtists(Album $album, $artists)
	{
		$artists = str_ireplace([' & ', ' and ', ' vs '], ',', $artists);
		// Create Artist Entity or Fetch One
		if (isset($artists) && !is_null($artists)) {
			$artists = explode(',', $artists);
			foreach ($artists as $artist) {
				$artist = $this->artists->createArtistStub($artist);
				$album->artists()->attach($artist);
			}
		} else {
			$album->artists()->attach(Artist::UNKNOWN_ID);
		}
	}

	public function attachCategoryAndGenre(Album $album, array $data)
	{
		// Attach Category
		if (!empty($data['category'])) {
			// Sync Main Category
			$category = $this->categories->createCategoryStub($data['category']);
			$album->category()->associate($category);
		} else {
			$album->delete();
			throw new GeneralException('Failed to create category for the album');
		}

		// Create and Attach Genres
		if (isset($data['genre']) && !is_null($data['genre'])) {
			$genre = $this->genres->createGenreStub($data['genre']);
			$album->genre()->associate($genre);
		} else {
			$album->delete();
			throw new GeneralException('Failed to create genre for the album');
		}

		return $album;
	}

	public function setSlug(Album $album)
	{
		$slug = str_slug("{$album->fresh()->full_title}");
		if ($slug != $album->slug && $this->query()->similarSlug($slug)->exists()) {
            $count = 1;
            while ($this->query()->similarSlug(
            		$slug = str_slug("{$album->full_title}-{$count}"))->exists()) {
            	$count++;
            }
        }
        $album->update(['slug' => $slug]);
	}

	public function uploadTrack(array $data)
	{
		$file = $data['file'];
		$album = $this->query()->findOrFail($data['album_id']);

		if ($file->isValid() && $album) {
			// TODO: Queue this  part...
			$track = $this->tracks->create($file, $album);

			// $track = $this->tracks->create($tags, $file_path, $album);
			if (!$track) {
				return [
					'message' => 'Failed to save track to database',
					'code' => 508
				];
			} else {
				return [
					'message' => 'Successfully Uploaded File to server',
					'code' => 201
				];
			}		
		} else {
			return [
				'message' => 'Failed to upload track, either track is invalid or corrupt',
				'code' => 508
			];
		}
	}

	public function uploadTracksViaUrl(array $data)
	{
		$album = $this->query()->findOrFail($data['album_id']);
		$links = collect(explode(',', $data['remote-links']));

		$links->each(function($link) use ($album) {

			try {
				$file = $album->addMediaFromUrl($link)
					->toMediaLibrary('file');
				// dd($file);
				$trackFile = (new UploadedFile($file->getPath(), true));
				// TODO: Queue this  part...
				$track = $this->tracks->create($trackFile, $album);

				if (isset($file) && !empty($file)) {
					$file->delete();
				}
				return true;

			} catch (Exception $e) {
				if (isset($file) && !empty($file)) {
					$file->delete();
				}
				return false;
			}
		});
	}

	public function uploadZip(array $data)
	{
		$file = $data['file'];
		if ($file->isValid()) {
			$filename = $file->getClientOriginalName();
			$filename = str_ireplace('–', '-', $filename);
			if (str_contains($filename, '-')) {
				$data['full_title'] = $filename;
				if (!$album = $this->create($data)) return false;

				$files = (new ExtractArchiveRepository($file->getRealPath()))->extract();

				$files = collect($files)->map(function ($file) {
					return (new UploadedFile($file->getRealPath(), true));
				});

				$covers = $files->filter(function ($file) {
					return $this->validate->image($file);
				});

				if ($covers->isNotEmpty()) {
					$bigCover = $covers->max(function ($cover) {
						return $cover->getSize();
					});
					$cover = $covers->filter(function ($cover) use ($bigCover) {
						return $cover->getSize() === $bigCover;
					})->shift();
					if (isset($cover) && !empty($cover) && !is_null($cover)) {
						$this->uploadCover(['cover' => $cover, 'albumId' => $album->id]);
					} 
				} 
				if ($files->isNotEmpty()) {
					// dd($files);
					$files->each(function ($file) use ($album) {
						// dd($this->validate->audio($file));
						if ($this->validate->audio($file)) {
							// dd($file);
							// TODO: Queue this  part...
							$track = $this->tracks->create($file, $album);
						}
					});
				}
			} else {
				return false;
			}
			return $album;
		}	
	}

	public function uploadViaUrl(array $data)
	{
		// dd($data);
		$full_title = str_ireplace('–', '-', $data['full_title']);
		if (str_contains($full_title, '-')) {
			if (!$album = $this->create($data)) return false;

			// dd($album);
			if (isset($data['cover']) && !empty($data['cover']) && !is_null($data['cover'])) {
				$this->uploadCover(['cover' => $data['cover'], 'albumId' => $album->id]);
			}

			if (!is_null($data['remote_link']) && !empty($data['remote_link']))  {
				try {
					$zip = $album->addMediaFromUrl($data['remote_link'])
									->toMediaLibrary('zip');
					$files = (new ExtractArchiveRepository($zip->getPath()))->extract();

					$files = collect($files)->map(function ($file) {
						return (new UploadedFile($file->getRealPath(), true));
					});

					$zip->delete();

					if (isset($files) && $files->isNotEmpty()) {
						// dd($files);
						$files->each(function ($file) use ($album) {
							// dd($this->validate->audio($file));
							if ($this->validate->audio($file)) {
								// dd($file);
								// TODO: Queue this  part...
								$track = $this->tracks->create($file, $album);
							}
						});
						return $album;
					} else {
						$album->delete();
						return false;
					}
				} catch (Exception $e) {
					$album->delete();
					return false;
				}
			}
			return false;
		} 
		return false;
	}

	public function crawl(array $data)
	{
		DB::table('albums_crawler')->where('id', $data['id'])
									->update(['status' => 'crawled']);
		$data['title'] = str_replace('–', '-', $data['title']);
		if (str_contains($data['title'], '-')) {
			$fulltitle = splitTitle($data['title']);
			$data['title'] = $fulltitle['title'];
			$data['artists'] = $fulltitle['artists'];
			$data['description'] = null;
			$album = $this->createAlbumStub($data);
			$this->attachCategoryAndGenre($album, $data)->save();
			$this->attachArtists($album, $data['artists']);
			// Set Album Slug 
			$this->setSlug($album);
			event(new AlbumCreated($album));
// 			dd($album);
			
			try {
						$cover = $data['cover'];
					if (isset($cover) && !empty($cover) && !is_null($cover)) {
						$this->uploadCover(['cover' => $cover, 'albumId' => $album->id]);
					}	
			} catch (Exception $e) {
				$cover = null;
			}
			

			if (!is_null($data['zip']) && !empty($data['zip']))  {
				try {
					// TODO: Queue uploding of zip file
					$zip = $album->addMediaFromUrl($data['zip'])
									->toMediaLibrary('zip');
				} catch (Exception $e) {
					$zip = null;
				}
			} else {
				$zip = null;
			}

			if (isset($zip) && !is_null($zip)) {
				try {
					$files = (new ExtractArchiveRepository($zip->getPath()))->extract();

					$files = collect($files)->map(function ($file) {
						return (new UploadedFile($file->getRealPath(), true));
					});

					$zip->delete();
				} catch (Exception $e) {
					$zip = null;
				}

				if (isset($files) && $files->isNotEmpty()) {
					// dd($files);
					$files->each(function ($file) use ($album) {
						// dd($this->validate->audio($file));
						if ($this->validate->audio($file)) {
							// dd($file);
							// TODO: Queue this  part...
							$track = $this->tracks->create($file, $album);
						}
					});
					DB::table('albums_crawler')->where('id', $data['id'])
									->update(['status' => 'crawled']);
					return true;
				} else {
					$zip = null;
				}
			} 

			if (is_null($zip)) {
				$tracks = DB::table('tracks_crawler')
							->where('crawlable_id', $data['id'])
							->where('crawlable_type', 'albums')
							->get();
// 				dd($tracks);

				if ($tracks->isNotEmpty()) {
					$tracks->each(function($crawler) use ($album, $data) {
// 						dd($crawler);
						try {
							$file = $album->addMediaFromUrl($crawler->link)->toMediaLibrary('file');
							// dd($file);
							$file = (new UploadedFile($file->getPath(), true));
							// TODO: Queue this  part...
							$track = $this->tracks->create($file, $album);
// 							$file->delete();
							DB::table('albums_crawler')->where('id', $data['id'])
									->update(['status' => 'crawled']);
							return true;
						} catch (Exception $e) {
							$album->delete();
							DB::table('albums_crawler')->where('id', $data['id'])
									->update(['status' => 'error']);
							return false;
						}
					});
					return true;

				} else {
					$album->delete();
					DB::table('albums_crawler')->where('id', $data['id'])
									->update(['status' => 'error']);
					return false;
				}
			}
		} else {
			DB::table('albums_crawler')->where('id', $data['id'])
									->update(['status' => 'error']);
			return false;
		}
	}

	public function createZip(Album $album)
	{
		if ($album->tracks->count() > 1) {
				// Create  new Zip File  and  Delete old  one
			$compress = Download::from($album);
			if ($compress) {
				$file = $compress instanceof SplFileInfo ? $compress : new SplFileInfo($compress);
				$file = new File($file->getRealPath());
				$filename = $album->slug . '-full-zipped-album';

				if ($album->hasMedia('zip')) {
					$zipped = $album->getMedia('zip');
				}
				$zip = $album->addMedia($file)
						->usingName("{$album->full_title} Zipped Album")
						->usingFileName(str_slug($album->slug) . "-full-zipped-album.{$file->guessExtension()}")
						->toMediaLibrary('zip');
				
				if ($zip) {
					if (isset($zipped)) $zipped->each->delete();
					return true;
				}
				return false;
			}
		}
		return false;
	}

	public function scanTracksForCover(Album $album)
	{
		if (!$album->hasMedia('cover') && $album->tracks->isNotEmpty()) {
			$track = $album->tracks->first(function($track) {
				return $track->hasMedia('cover');
			}); 
			if ($track) {
				$cover = $track->getFirstMedia('cover');
				$extension = $cover->getExtensionAttribute();
				$album->copyMedia($cover->getPath())
					->usingName($album->full_title)
					->usingFileName(str_slug($album->full_title) . ".{$extension}")
					->toMediaLibrary('cover');
				return true;
			}
			return false;
		}
		return false;
	}

	public function uploadCover($data)
	{
		$cover = $data['cover'];

		$album = $this->query()->findOrFail($data['albumId']);
		if (isset($cover)) {
			if ($album->hasMedia('cover')) {
				$covers = $album->getMedia('cover'); 
			}

			if (!is_string($cover))  {
				if (!$this->validate->image($cover)) return false;
				$cover = $album->copyMedia($cover)
					->usingName($album->full_title)
					->usingFileName(str_slug($album->full_title) . ".{$cover->extension()}")
					->toMediaLibrary('cover');
			} else {
				$cover = $album->addMediaFromUrl($cover)
					->toMediaLibrary('cover');
			}
			

			if ($cover) {
				if (isset($covers)) {
					$covers->each->delete();
				}

				// $watermark_image = Cache::get('watermark_logo')->getFirstMedia('image')->getPath();
				$watermark_image = $this->cache->findOrMake('settings', 'watermark_logo')
												->getFirstMedia('image')
												->getPath();

				Image::load($cover->getPath())
					->watermark($watermark_image)
					->watermarkPosition(Manipulations::POSITION_TOP)      // Watermark at the top
					->watermarkHeight(50, Manipulations::UNIT_PERCENT)    // 50 percent height
					->watermarkWidth(100, Manipulations::UNIT_PERCENT)
					->width(340)
					->height(360)
					->optimize()
					->save();
			}

			return $cover ? true : false;
		}
		return false;
	}

	public function createAlbumStub($input)
	{
		$album = self::MODEL;
		$album = new $album;
		$album->title = $input['title'];
		$album->description = $input['description'];
		$album->type = $input['type'];
		
		return $album;
	}

    public function delete(Album $album)
	{
		if ($album->delete()) {
			event(new AlbumDeleted($album));

			return true;
		}

		throw new GeneralException(trans('exceptions.backend.music.albums.delete_error'));
		
	}
}