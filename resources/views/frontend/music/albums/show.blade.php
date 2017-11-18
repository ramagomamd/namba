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
                    <a href="{{ route('frontend.music.categories.show', $album->category) }}">
                        <strong>{!! $album->category->name !!}</strong>
                    </a>
                </li>
                <li>
                    <a href="{{ route('frontend.music.categories.genres', 
                            [$album->category, $album->genre]) }}">
                        <strong>{!! $album->genre->name !!}</strong>
                    </a>
                </li>
                <li>
                    <a href="{{ route('frontend.music.categories.genres.albums', 
                        [$album->category, $album->genre]) }}">
                        <strong>Albums</strong>
                    </a>
                </li>
                <li class="active">{!! $album->full_title !!}</li>
            </ol>
            <div class="panel-heading">
                <h1 class="h3 text-center"><strong>{!! $title !!}</strong></h1>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6"  align="center">
                        @if (!is_null($album->cover))
                            <div style="margin-bottom: 1em">
                                <img src="{!! $album->cover->getUrl() !!}" alt="{!! $album->full_title !!}"
                                class="img-thumbnail">
                            </div>
                        @endif
                    </div> 
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <td><em>Title:</em></td>
                                <td>
                                    <strong>{!! $album->title !!}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td><em>Artist:</em></td>
                                <td>
                                    <strong>{!! $album->getArtistsLink('frontend') !!}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td><em>Category:</em></td>
                                <td>
                                    <a href="{{ route('frontend.music.categories.show', 
                                        $album->category) }}">
                                        <strong>{{ $album->category->name }}</strong>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><em>Genre:</em></td>
                                <td>
                                    <a href="{{ route('frontend.music.genres.show', 
                                        $album->genre) }}">
                                        <strong>{{ $album->genre->name }}</strong>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><em>Total Tracks:</em></td>
                                <td>
                                    <strong>{{ $album->tracks->count() }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td><em>Total PlayTime:</em></td>
                                <td>
                                    <strong>{{ $album->play_time }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td><em>Time Added:</em></td>
                                <td>
                                    <strong>{{ $album->created_at->diffForHumans() }}</strong>
                                </td>
                            </tr>
                        </table>
                        @if (!is_null($album->zip))
                            <p>
                                <a href="{{ $album->zip->getUrl() }}" class="btn btn-success btn-block">
                                    <span class="fa fa-download"> Full Zip Download</span>
                                </a>
                            </p>
                        @endif
                        @if ($album->links->isNotEmpty())
                            @foreach($album->links as $link)
                                <a href="{!! $link->url !!}" class="btn btn-primary btn-block" target="_blank">
                                    <i class="fa fa-link"></i> Download Zip From {!! title_case($link->site_name) !!}
                                </a>
                                @if (!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        @endif
                        <br>
                    </div> 
                    <hr>
                     <div class="col-md-12" align="center">
                        <p class="lead bg-info">
                            <strong>
                                {!! title_case("Sharing is caring... <br>
                                     Share this album with your social media friends, they'll love it!") 
                                !!}
                            </strong>
                        </p>
                         <div align="center" style="margin: 1em">
                            <!-- Go to www.addthis.com/dashboard to customize your tools --> 
                            <div class="addthis_inline_share_toolbox"></div>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading with-border">
                <h4 class="box-title"><strong>Album Tracklist</strong></h4>
            </div>

            <div class="panel-body">
                @if ($album->tracks->isNotEmpty())
                    @include('frontend.music.tracks.list', ['tracks' => $album->tracks])
                @else
                    <p class="text-center lead">
                        {!! title_case("No tracks uploaded yet for  <strong>{$album->title}</strong>") !!}
                    </p>
                @endif
            </div>
        </div>
        <div class="well well-lg" align="center">
            <p class="lead">
                <strong>Share {!! $album->full_title !!}</strong>
            </p>
            <div align="center" style="margin: 1em">
                <!-- Go to www.addthis.com/dashboard to customize your tools --> 
                <div class="addthis_inline_share_toolbox"></div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading with-border">
                <h4 class="box-title"><strong>Related Albums</strong></h4>
            </div>

            <div class="panel-body">
                @forelse ($related as $r)
                <div class="col-sm-12 col-md-6">
                    <div class="media" style="margin: 1em">
                        @if ($r->cover)
                        <img src="{!! $r->cover->getFullUrl('thumb') !!}" alt="{!! $r->full_title !!}" 
                            class="pull-left img-thumbnail" style="margin-right: 1em">
                        @else
                            <span class="thumbnail">No Cover</span>
                        @endif
                        <div class="media-body">
                            <h4 class="media-heading">
                                <a href="{!! route('frontend.music.albums.show', 
                                        [$r->category, $r->genre, $r, $r->slug]) !!}">{{ $r->full_title }}
                                </a>
                            </h4>
                            <a href="{!! route('frontend.music.categories.show', $r->category) !!}">
                                <em>{!! $r->category->name !!}</em>
                            </a> <i class="fa fa-exchange"></i>
                            <a href="{!! route('frontend.music.genres.show', $r->genre) !!}">
                                <em>{!! $r->genre->name !!}</em>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                    <p>No Related Albums Yet</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('before-scripts')
    <script>
        var playlist = {!! $album->tracks !!}
    </script>
@endsection