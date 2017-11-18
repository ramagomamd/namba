@if ($tracks->isNotEmpty())
<player inline-template v-cloak>
	<div>
		<div class="player" style="margin: 2em">
	      <div v-if="sound.state() !== 'unloaded' && sound.state() !== 'loading'" class="track-info">
            <span class="track-title">
              @{{ title }}
            </span>
          </div>
	      <div v-if="playing" class="time-controls">
	        <span class="current-duration">
	          @{{ timer }}
	        </span>
	        <span class="player-controls">
	          <a @click.prevent="repeat = !repeat" class="fa fa-retweet" :class="{active: !repeat}"></a>
	        </span>
	        <span class="total-duration">
	          @{{ duration }}
	        </span>
	      </div>
	      <div class="player-footer">
	        <div class="player-bar">
	          <div class="progress"  ref="progress"></div>
	        </div>
	        <div class="song-controls">
	          <a  v-if="playlist.length > 1" @click.prevent="skip('prev')" class="prev-track">
	            <i class="fa fa-step-backward"></i>
	          </a>
	          <a v-else class="next-track" style="cursor: default;">
	            <i class="fa fa-headphones"></i>
	          </a>
	          <a v-if="!playing && sound.state() !== 'loading'" @click.prevent="play" class="play">
	            <i class="fa fa-play"></i>
	          </a>
	          <a v-else-if="!playing && sound.state() === 'loading'" class="play">
	          	@if (isset($loading))
					<img src="{!! $loading !!}" alt="Loading...">
	          	@else
	                <span class="fa fa-spinner"> Loading...</span>
	            @endif
              </a>
	          <a v-else @click.prevent="pause" class="pause">
	            <i class="fa fa-pause"></i>
	          </a>
	          <a v-if="playlist.length > 1" @click.prevent="skip('next')" class="next-track">
	            <i class="fa fa-step-forward"></i>
	          </a>
	          <a v-else class="next-track" style="cursor: default;">
	            <i class="fa fa-headphones"></i>
	          </a>
	        </div>
	      </div>
	    </div>
		@foreach ($tracks->chunk(2) as $chunk)
			<div class="row">
				@foreach ($chunk as $track)
					<div class="col-md-6">
						<div class="media" style="margin: 1em">
							<div class="pull-left">
								<button v-if="index === {!! $track->index !!} && playing && sound.state() !== 'loading'"
									class="btn btn-warning" @click.prevent="pause">
									<span class="fa fa-pause-circle media-object" style="font-size: 3em"></span>
									<br> <strong>Pause</strong>
								</button>
								<button v-else-if="index === {!! $track->index !!} && sound.state() === 'loading'"
									class="btn btn-default">
									@if (isset($loading))
										<img src="{!! $loading !!}" alt="Loading...">
						          	@else
						                <span class="fa fa-spinner"> Loading...</span>
						            @endif
								</button>
								<button v-else class="btn btn-info" 
									@click.prevent="skipTo({!! $track->index !!})">
									<span class="fa fa-play-circle media-object" style="font-size: 3em"></span>
									<br> <strong>Play</strong>
								</button>
							</div>
							<div class="media-body">
								<h4 class="media-heading">
									<strong>
										<a href="{!! $track->frontend_show_route !!}">
											{!! $track->title !!}
										</a>
									</strong>
								</h4>
								<p>{!! $track->getArtistsLink('frontend') !!}</p>
								<p>{!! $track->duration !!} <i class="fa fa-exchange"></i> {!! $track->size !!}</p>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@endforeach
	</div>
</player>
@else
	<div class="well">
		<p class="lead text-center">No Tracks Yet</p>
	</div>
@endif