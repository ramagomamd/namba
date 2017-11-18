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
                    <a href="{!! route('frontend.index') !!}">
                        <i class="fa fa-home"></i> <strong>Home</strong>
                    </a>
                </li>
                <li>
                    <a href="{{ route('frontend.music.categories.show', $category) }}">
                        <strong>{!! $category->name !!}</strong>
                    </a>
                </li>
                <li>
                    {!! $genre->name !!}
                </li>
            </ol> 
            <div class="panel-heading">
                <h1 class="h3 text-center"><strong>{!! $title !!}</strong></h1>
            </div>
        </div><!--panel-->
    </div><!--col-xs-12-->

    @if ($albums->isEmpty() || $singles->isEmpty())
        <div class="col-md-12">
            @if ($albums->isEmpty())
                <div class="well">
                    <p class="lead text-center">No Albums Yet For 
                        <strong>{!! $category->name !!} {!! $genre->name !!}</strong>
                    </p>
                </div>
            @elseif ($singles->isEmpty())
                <div class="well">
                    <p class="lead text-center">No Singles Yet For 
                        <strong>{!! $category->name !!} {!! $genre->name !!}</strong>
                    </p>
                </div>
            @endif
        </div>
    @endif

    @if ($albums->isNotEmpty())
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="level">
                    <h3 class="flex">{!! $category->name !!} {!! $genre->name !!} Albums</code></h3>

                   <a href="{{ route('frontend.music.categories.genres.albums', 
                            [$category, $genre]) }}" 
                        class="btn btn-success btn-md">
                        <strong>View All...</strong>
                    </a> 
                </div>
            </div><!--panel-heading-->

            <div class="panel-body">
                @include('frontend.music.albums.list')
            </div><!--panel-body-->
        </div><!--panel-->
    </div><!--col-md-6-->
    @endif

    @if ($singles->isNotEmpty())
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="level">
                    <h3 class="flex">{!! $category->name !!} {!! $genre->name !!} Singles</h3>

                    <div>
                       <a href="{{ route('frontend.music.categories.genres.singles', 
                                [$category, $genre]) }}" class="btn btn-success btn-md">
                            View All...
                        </a> 
                    </div>
                </div>
            </div><!--panel-heading-->

            <div class="panel-body">
                @include('frontend.music.singles.list')
            </div><!--panel-body-->
        </div><!--panel-->
    </div><!--col-md-6-->
    @endif

@endsection