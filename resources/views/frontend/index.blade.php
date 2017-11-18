@extends('frontend.layouts.app')

@section('meta')
    {!! SEOMeta::generate(true) !!}
    {!! OpenGraph::generate(true) !!}
    {!! Twitter::generate(true) !!}
@endsection

@section('before-content')
    <div class="well well-md">
        <h1 class="h4 text-center">{!! $title !!}</h1>
    </div>
@endsection

@section('content')

    @if ($latestAlbums->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">Latest Albums</code></h3>

                       <a href="{!! route('frontend.music.albums.index') !!}" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a> 
                    </div>
                </div><!--panel-heading-->

                <div class="panel-body">
                    @include('frontend.music.albums.list', ['albums' => $latestAlbums])
                </div><!--panel-body-->
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($latestSingles->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">Latest Singles</h3>

                        <div>
                           <a href="{!! route('frontend.music.singles.index') !!}" class="btn btn-success btn-md">
                                View All...
                            </a> 
                        </div>
                    </div>
                </div><!--panel-heading-->

                <div class="panel-body">
                    @include('frontend.music.singles.list', ['singles' => $latestSingles])
                </div><!--panel-body-->
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($mzansiAlbums->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">South African Albums</h3>

                       <a href="/south-african/albums" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a> 
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
                    @foreach ($mzansiAlbums as $album)
                        <li class="list-group-item">
                            <div class="level">
                                <a class="flex" href="{!! $album->frontend_show_route !!}">
                                    <strong>
                                        <i class="fa fa-folder"></i> {!! $album->full_title !!}
                                    </strong>
                                </a>
                                <code>
                                    <strong>{!! $album->tracks_count ?  $album->tracks_count . '  Tracks' : 'No Track' !!}</strong>
                                </code>
                            </div>
                        </li>
                    @endforeach
                </div><!--panel-body-->
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($mzansiSingles->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">South African Singles</h3>

                        <a href="/south-african/singles" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a>
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
                    @foreach ($mzansiSingles as $single)
                        <li class="list-group-item">
                            <div class="level">
                                <a class="flex" href="{!! $single->track->frontend_show_route !!}">
                                    <strong>
                                        <i class="fa fa-file-sound-o"></i> {!! $single->track->full_title !!}
                                    </strong>
                                </a>
                                <code>
                                    <strong>{!! $single->track->duration !!}</strong>
                                </code>
                            </div>
                        </li>
                    @endforeach
                </div><!--panel-body-->
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($internationalAlbums->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">American Albums</h3>

                       <a href="/american/albums" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a> 
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
                    @foreach ($internationalAlbums as $album)
                        <li class="list-group-item">
                            <div class="level">
                                <a class="flex" href="{!! $album->frontend_show_route !!}">
                                    <strong>
                                        <i class="fa fa-folder"></i> {!! $album->full_title !!}
                                    </strong>
                                </a>
                                <code>
                                    <strong>{!! $album->tracks_count ?  $album->tracks_count . '  Tracks' : 'No Track' !!}</strong>
                                </code>
                            </div>
                        </li>
                    @endforeach
                </div><!--panel-body-->
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($internationalSingles->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">American Singles</h3>

                        <a href="/american/singles" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a>
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
                    @foreach ($internationalSingles as $single)
                        <li class="list-group-item">
                            <div class="level">
                                <a class="flex" href="{!! $single->track->frontend_show_route !!}">
                                    <strong>
                                        <i class="fa fa-file-sound-o"></i> {!! $single->track->full_title !!}
                                    </strong>
                                </a>
                                <code>
                                    <strong>{!! $single->track->duration !!}</strong>
                                </code>
                            </div>
                        </li>
                    @endforeach
                </div><!--panel-body-->

            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($nigerianAlbums->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">Nigerian Albums</h3>

                       <a href="/nigerian/albums" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a> 
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
                    @foreach ($nigerianAlbums as $album)
                        <li class="list-group-item">
                            <div class="level">
                                <a class="flex" href="{!! $album->frontend_show_route !!}">
                                    <strong>
                                        <i class="fa fa-folder"></i> {!! $album->full_title !!}
                                    </strong>
                                </a>
                                <code>
                                    <strong>{!! $album->tracks_count ?  $album->tracks_count . '  Tracks' : 'No Track' !!}</strong>
                                </code>
                            </div>
                        </li>
                    @endforeach
                </div><!--panel-body-->
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

    @if ($nigerianSingles->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">Nigerian Singles</h3>

                        <a href="/nigerian/singles" 
                            class="btn btn-success btn-md">
                            <strong>View All...</strong>
                        </a>
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
                    @foreach ($nigerianSingles as $single)
                        <li class="list-group-item">
                            <div class="level">
                                <a class="flex" href="{!! $single->track->frontend_show_route !!}">
                                    <strong>
                                        <i class="fa fa-file-sound-o"></i> {!! $single->track->full_title !!}
                                    </strong>
                                </a>
                                <code>
                                    <strong>{!! $single->track->duration !!}</strong>
                                </code>
                            </div>
                        </li>
                    @endforeach
                </div><!--panel-body-->

            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

@endsection