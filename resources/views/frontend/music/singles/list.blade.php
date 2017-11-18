@if ($singles->isNotEmpty())
	@foreach ($singles as $single)
		<div class="media">
			<a class="pull-left img-thumbnail" 
				href="{!! route('frontend.music.categories.show', $single->track->trackable->category) !!}" 
				style="margin-right: 1em">
				<span class="fa fa-music" style="font-size: 3em"></span>
			</a>
			<div class="media-body">
				<div class="level">
					<h4 class="flex h5 media-heading">
						<strong>
							<a href="{!! $single->track->frontend_show_route !!}">
								{!! $single->track->full_title !!}
							</a>
						</strong>
					</h4>
					<code><strong>{!! $single->track->duration !!}</strong></code>
				</div>
				<a href="{!! route('frontend.music.categories.show', $single->track->trackable->category) !!}">
					<em>{!! $single->track->trackable->category->name !!}</em>
				</a> <i class="fa fa-exchange"></i>
				<a href="{!! route('frontend.music.genres.show', $single->track->trackable->genre) !!}">
					<em>{!! $single->track->trackable->genre->name !!}</em>
				</a>
			</div> 
		</div> 
		@if (!$loop->last)
			<hr>
		@endif
	@endforeach
@else
	<div class="well">
		<p class="lead text-center">No Singles Yet</p>
	</div>
@endif