@extends('frontend.layouts.app')

@section('meta')
    {!! SEOMeta::generate(true) !!}
    {!! OpenGraph::generate(true) !!}
    {!! Twitter::generate(true) !!}
@endsection

@section('content')
    <div class="col-md-12">
        <div class="panel panel-default">
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('frontend.index') }}">
                        <i class="fa fa-home"></i> <strong>Home</strong>
                    </a>
                </li>
                <li>
                    <a href="{{ route('frontend.music.categories.show', 
                                $track->trackable->category) }}">
                        <strong>{{ $track->trackable->category->name }}</strong>
                    </a>
                </li>
                <li>
                    <a href="{{ route('frontend.music.categories.genres', 
                            [$track->trackable->category, $track->trackable->genre]) }}">
                        <strong>{{ $track->trackable->genre->name }}</strong>
                    </a>
                </li>
                @if ($track->trackable_type == 'albums')
                    <li>
                        <a href="{{ route('frontend.music.categories.genres.albums', 
                            [$track->trackable->category, $track->trackable->genre]) }}">
                            <strong>Albums</strong>
                        </a>
                    </li>
                    <li>
                        <a href="$track->trackable->frontend_show_route">
                            <strong>{!! $track->trackable->full_title !!}</strong>
                        </a>
                    </li>
                    <li class="active">{!! $track->full_title !!}</li>
                @elseif ($track->trackable_type == 'singles')
                    <li>
                        <a href="{{ route('frontend.music.categories.genres.singles', 
                            [$track->trackable->category, $track->trackable->genre]) }}">
                            <strong>Singles</strong>
                        </a>
                    </li>
                    <li class="active">{!! $track->full_title !!}</li>
                @endif
            </ol>
            <div class="panel-heading">
                <h1 class="h3 text-center"><strong>{!! $title !!}</strong></h1>
            </div>
            <div class="panel-body">
                <div class="col-md-12" align="center">
                    @if ($track->cover)
                        <img src="{{ $track->cover->getUrl() }}" alt="{{ $track->title }}"
                            class="img-thumbnail">
                    @endif
                    <hr>
                    <div align="center" style="margin: 1em">
                        <!-- Go to www.addthis.com/dashboard to customize your tools --> 
                        <div class="addthis_inline_share_toolbox"></div>
                    </div>
                </div>
                <div class="col-md-12" style="margin: 1em 0 1em 0">
                    <player inline-template v-cloak>
                        <div class="player" style="margin: 2em">
                          <div v-if="sound.state() === 'loaded'" class="track-info">
                            <span class="track-title">
                              @{{ title }}
                            </span>
                          </div>
                          <div v-if="sound.state() !== 'unloaded' && sound.state() !== 'loading'" class="time-controls">
                            <span class="current-duration">
                              <i class="fa fa-headphones"></i>
                            </span>
                            <span class="player-controls">
                              <a @click.prevent="repeat = !repeat" class="fa fa-retweet" :class="{active: !repeat}"></a>
                            </span>
                            <span class="total-duration">
                              <i class="fa fa-headphones"></i>
                            </span>
                          </div>
                          <div class="player-footer">
                            <div class="player-bar">
                              <div class="progress"  ref="progress"></div>
                            </div>
                            <div class="song-controls">
                              <a class="next-track" style="cursor: default;">
                                @{{ timer }}
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
                              <a class="next-track" style="cursor: default;">
                                @{{ duration }}
                              </a>
                            </div>
                          </div>
                        </div>
                    </player>
                </div>
                <div class="col-md-12">
                    <table class="table">
                        @isset($track->artists)
                        <tr>
                            <td><em>Artists:</em></td>
                            <td>
                                <strong>{!! $track->getArtistsLink('frontend') !!}</strong>
                            </td>
                        </tr>
                        @endisset
                        @isset($track->title)
                        <tr>
                            <td><em>Title:</em></td>
                            <td>
                                <strong>{{ $track->title }}</strong>
                            </td>
                        </tr>
                        @endisset
                        @isset($track->year)
                        <tr>
                            <td><em>Year:</em></td>
                            <td><strong>{{ $track->year }}</strong></td>
                        </tr>
                        @endisset
                        @isset($track->number)
                        <tr>
                            <td><em>Track #:</em></td>
                            <td><strong>{{ $track->number }}</strong></td>
                        </tr>
                        @endisset
                        @if($track->belongs_to)
                            {!! $track->belongs_to !!}
                        @endif
                        <tr>
                            <td>
                                <em>
                                    Genre:
                                </em>
                            </td>
                            <td>
                                <a href="{{ route('frontend.music.genres.show', 
                                    $track->trackable->genre) }}">
                                    <strong>{{ $track->trackable->genre->name }}</strong>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><p>
                                <em>
                                    Category:
                                </em>
                                </p></td>
                            <td>
                                <a href="{{ route('frontend.music.categories.show', 
                                    $track->trackable->category) }}">
                                    <strong>{{ $track->trackable->category->name }}</strong>
                                </a>
                            </td>
                        </tr>
                        @isset($track->producer)
                        <tr>
                            <td><em>Producer: </em></td>
                            <td><strong>{!! $track->getProducerLink('frontend') !!}</strong></td>
                        </tr>
                        @endisset
                        @isset($track->bitrate)
                        <tr>
                            <td><em>Bitrate: </em></td>
                            <td><strong>{{ $track->bitrate }}</strong></td>
                        </tr>
                        @endisset
                        @isset($track->size)
                        <tr>
                            <td><em>Size: </em></td>
                            <td><strong>{{ $track->size }}</strong></td>
                        </tr>
                        @endisset
                        @isset($track->duration)
                        <tr>
                            <td><em>Duration: </em></td>
                            <td><strong>{{ $track->duration }}</strong></td>
                        </tr>
                        @endisset
                    </table>
                    <div align="center" style="margin-bottom: 15px">
                        {{ Form::open(['route' => ['frontend.music.tracks.download', $track], 
                                        'method' => 'post']) }}
                            <button class="btn btn-success" type="submit">
                                <i class="fa fa-download"></i> Direct Download
                            </button>
                        {{ Form::close() }}
                        <br>
                        @if ($track->links->isNotEmpty())
                            @foreach($track->links as $link)
                                <a href="{!! $link->url !!}" class="btn btn-primary" target="_blank">
                                    <i class="fa fa-link"></i> Download From {!! title_case($link->site_name) !!}
                                </a>
                                @if (!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        @endif
                        <hr>
                        <p class="lead bg-info">
                            <strong>Did You Download? Please support us by sharing this hit!</strong>
                        </p>
                        <div align="center" style="margin: 1em">
                            <!-- Go to www.addthis.com/dashboard to customize your tools --> 
                            <div class="addthis_inline_share_toolbox"></div>
                        </div>
                    </div>
            
                    <div class="panel-footer">
                        <p>Uploaded: <em>{{ $track->created_at->diffForHumans() }}</em></p>
                    </div>
                </div> 
            </div><!-- /.panel-body -->
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading with-border">
                <h4 class="box-title"><strong>Related Tracks</strong></h4>
            </div>
    
            @foreach ($tracks->chunk(4) as $chunk)
                <div class="panel-body">
                    @foreach ($chunk as $t)
                    <div class="col-sm-6 col-md-3">
                        <div class="thumbnail">
                            @if ($t->cover)
                            <div class="thumbnail">
                                <span class="fa fa-file-sound-o" style="font-size: 5em"></span>
                            </div>
                            @else
                                <span class="thumbnail">No Cover</span>
                            @endif
                        </div>
                        <div class="caption">
                            <h4>
                                <a href="{!! $t->frontend_show_route !!}">{{ $t->full_title }}</a>
                            </h4>
                            <p>{!! $t->duration !!} <i class="fa fa-exchange"></i> {!! $t->size !!}</p>
                            <a href="{!! route('frontend.music.categories.show', $t->trackable->category) !!}">
                                <em>{!! $t->trackable->category->name !!}</em>
                            </a> <i class="fa fa-exchange"></i>
                            <a href="{!! route('frontend.music.genres.show', $t->trackable->genre) !!}">
                                <em>{!! $t->trackable->genre->name !!}</em>
                            </a>
                            <p>
                                <a href="{!! $t->frontend_show_route !!}" 
                                        class="btn btn-primary btn-block" role="button">
                                    View
                                </a>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div> 
                @if (!$loop->last)
                    <hr>
                @endif
            @endforeach
        </div>
    </div>
@endsection

@section('before-scripts')
    <script>
        var playlist = [{!! json_encode($track->toArray()) !!}]
    </script>
@endsection