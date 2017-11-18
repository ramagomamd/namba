<?php 

namespace App\Repositories\Backend\Music;

use App\Repositories\BaseRepository;
use App\Models\Music\Single\Single;
use App\Events\Backend\Music\Single\SingleCreated;
use App\Events\Backend\Music\Single\SingleUpdated;
use App\Events\Backend\Music\Single\SingleDeleted;
use App\Exceptions\GeneralException;
use App\Services\Music\Tags;
use Spatie\Image\Image;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\Music\CacheRepository;

class SingleRepository extends BaseRepository
{
	const MODEL = Single::class;

	protected $categories;
	protected $genres;
	protected $tracks;
	protected $cache;

	public function __construct(GenreRepository $genres, TrackRepository $tracks, CacheRepository $cache,
								CategoryRepository $categories)
	{
		$this->categories = $categories;
		$this->genres = $genres;
		$this->tracks = $tracks;
		$this->cache = $cache;
	}

	public function create(array $input)
	{	
		$file = $input['file'];
		// Fetch files ID3 Tags
		if (!$tags = (new Tags($file))->getInfo()) {
			return [
				'message' => 'Failed to read file ID3 Tags',
				'code' => 508
			];
		}

		$single = self::MODEL;
		$single = new $single;
		
		// Attach Category
		$this->attachCategoryAndGenre($single, $input);

		$single->save();

		if ($single && $file->isValid()) {
			// TODO: Queue this  part...
			$track = $this->tracks->create($file, $single);

			if (!$track) {
				$single->delete();
				return [
					'message' => 'Failed to save track to database',
					'code' => 508
				];
			}
		}
		
		return [
			'message' => 'Successfully Uploaded File to server',
			'code' => 201
		];
	}

	public function attachCategoryAndGenre(Single $single, array $data)
	{
		// dd($data);
		// Attach Category
		if (!empty($data['category'])) {
			// Sync Main Category
			$category = $this->categories->createCategoryStub($data['category']);
			$single->category()->associate($category);
		} else {
			$single->delete();
			throw new GeneralException('Failed to create category for the single');
		}

		// Create and Attach Genres
		if (isset($data['genre']) && !is_null($data['genre'])) {
			$genre = $this->genres->createGenreStub($data['genre']);
			$single->genre()->associate($genre);
		} else {
			$single->delete();
			throw new GeneralException('Failed to create genre for the single');
		}

		return $single;
	}

	public function crawl(array $data)
	{
		// DB::table('singles_crawler')->where('id', $data['crawlable_id'])
		// 				->update(['status' => 'crawled']);
		// dd($data);
		// dd($data->cover);
		$single = self::MODEL;
		$single = new $single;
		
		/*$input['category'] = $data->category;
		$input['genre'] = $data->genre;*/
		// Attach Category
		$this->attachCategoryAndGenre($single, $data);

		if ($single->save()) {
			try {
				$this->uploadCover($single, $data['cover']);
			} catch (\Exception $e) {

			}

			$url = $data['link'];
			// $name = substr($url, strrpos($url, '/') + 1);
			try {
				$file = $single->addMediaFromUrl($url)
						->toMediaLibrary('file');
				// dd($file);
				$file = (new UploadedFile($file->getPath(), true));
				$data['title'] = str_replace('–', '-', $data['title']);
				$data['title'] = str_ireplace('new song', '', $data['title']);
				if (str_contains($data['title'], '-')) {
					// dd($data['title']);
					$fulltitle = splitTitle($data['title']);
					// dd($fulltitle);
					$results = sanitizeTitle($fulltitle['title']);
					// dd($results);
					$artists = splitArtists(sanitizeArtists($fulltitle['artists']));
					// dd($artists);
					if (!is_null($results)) {
			        	$title = $results['title'];
			        	// dd($results['features']);
			        	if (is_null($artists['features']) && !is_null($results['features'])) {
			        		$artists['features'] .= $results['features'];
			        	} elseif (!is_null($results['features'])) {
			        		$artists['features'] .= ", {$results['features']}";
			        	}
			        } 
					$single->artists = $artists;
					$single->title =  $title;
				} else {
					$single->delete();
					DB::table('singles_crawler')->where('id', $data['crawlable_id'])
						->update(['status' => 'error']);
					return false;
				}

				// TODO: Queue this  part...
				$track = $this->tracks->create($file, $single);

				if ($track) {
					DB::table('singles_crawler')->where('id', $data['crawlable_id'])
						->update(['status' => 'crawled']);
					return true;
				} else {
					$single->delete();
					DB::table('singles_crawler')->where('id', $data['crawlable_id'])
						->update(['status' => 'error']);
					return false;
				}
			} catch (\Exception $e) {
				$single->delete();
				DB::table('singles_crawler')->where('id', $data['crawlable_id'])
						->update(['status' => 'error']);
				return false;
			}
		}
	}

	public function uploadViaUrl(array $data)
	{
		$links = collect(explode(',', $data['remote-links']));

		$links->each(function($link) use ($data) {
			$single = self::MODEL;
			$single = new $single;
			$this->attachCategoryAndGenre($single, $data)->save();

			try {
				$file = $single->addMediaFromUrl($link)
					->toMediaLibrary('file');
				// dd($file);
				$trackFile = (new UploadedFile($file->getPath(), true));
				// TODO: Queue this  part...
				$track = $this->tracks->create($trackFile, $single);

				$file->delete();
				return true;

			} catch (\Exception $e) {
				$single->delete();
				return false;
			}
		});
	}

	public function uploadCover(Single $single, $url)
	{
		if (isset($url)) {
			$cover = $single->addMediaFromUrl($url)
					->toMediaLibrary('cover');

			if ($cover) {
				$watermark_image = $this->cache->findOrMake('settings', 'watermark_logo')
												->getFirstMedia('image')
												->getPath();
												
				Image::load($cover->getPath())
					->watermark($watermark_image)
					->watermarkOpacity(50)
					->watermarkPosition(Manipulations::POSITION_TOP)      // Watermark at the top
					->watermarkHeight(50, Manipulations::UNIT_PERCENT)    // 50 percent height
					->watermarkWidth(100, Manipulations::UNIT_PERCENT)
					->width(310)
					->height(330)
					->optimize()
					->save();
			}

			return $cover ? true : false;
		}
		return false;
	}

	public function update(Single $single, array $input)
	{
		$single->description = $input['description'];

		$single->category()->dissociate();
		
		// Detach Genre
		$single->genre()->dissociate();
		$this->attachCategoryAndGenre($single, $input);
		$single->save();

		if (isset($input['file']) && $input['file']->isValid()) {
			// TODO: Queue this  part...
			$track = $this->tracks->create($input['file'], $single);

			if ($track) {
				$this->cache->clear('tracks', $track->id);
			}
		}
		return $single;
	}

	public function updateBulk(array $datas)
	{
		foreach($datas as $id => $data) {
			$single =  Single::findOrFail($id);

			$this->update($single, $data);
		}

		return true;
	}

	public function deleteBulk(array $datas)
	{
		foreach($datas as $id => $data) {
			if ($data['confirm'] == 'yes') {
				$single =  Single::findOrFail($id);
				$this->delete($single);
			}
		}
		return true;
	}

	public function delete(Single $single)
	{
		if ($single->track()->delete() && $single->delete()) {
			event(new SingleDeleted($single));

			$data = [
				'flash_success' => trans('alerts.backend.music.singles.deleted')
			];
		} else {
			$data = [
				'flash_success' => trans('exceptions.backend.music.singles.delete_error')
			];
		}
		return $data;
		
	}
}