@if ($albums->isNotEmpty())
	@foreach ($albums as $album)
			<div class="media">
				<a class="pull-left img-thumbnail" href="{!! $album->frontend_show_route !!}" style="margin-right: 1em">
					<span class="fa fa-folder-open" style="font-size: 3em"></span>
				</a>
				<div class="media-body">
					<div class="level">
						<h4 class="flex h5 media-heading">
							<strong>
								<a href="{!! $album->frontend_show_route !!}">
									{!! $album->full_title !!}
								</a>
							</strong>
						</h4>
						<code><strong>{!! $album->tracks_count ?  $album->tracks_count . '  Tracks' : 'No Track' !!}</strong></code>
					</div>
					<!-- <em>{!! $album->getArtistsLink('frontend') !!}</em> -->
					<a href="{!! route('frontend.music.categories.show', $album->category) !!}">
						<em>{!! $album->category->name !!}</em>
					</a> <i class="fa fa-exchange"></i>
					<a href="{!! route('frontend.music.genres.show', $album->genre) !!}">
						<em>{!! $album->genre->name !!}</em>
					</a>
				</div>
			</div> 
		@if (!$loop->last)
			<hr>
		@endif
	@endforeach

@else
	<div class="well">
		<p class="lead text-center">No Albums Yet</p>
	</div>
@endif