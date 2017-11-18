
@if ($trendingTracks->isNotEmpty())
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="level">
                    <h3 class="flex">Trending This Week</h3>
                </div>
            </div><!--panel-heading-->

            <div class="list-group">
                @foreach ($trendingTracks as $track)
                    <li class="list-group-item">
                        <div class="level">
                            <a class="flex" href="{!! $track->frontend_show_route !!}">
                                <strong>
                                    <i class="fa fa-file-sound-o"></i> {!! $track->full_title !!}
                                </strong>
                            </a>
                            <code>
                                <strong>{!! $track->duration !!}</strong>
                            </code>
                        </div>
                    </li>
                @endforeach
            </div><!--panel-body-->
        </div><!--panel-->
    @endif