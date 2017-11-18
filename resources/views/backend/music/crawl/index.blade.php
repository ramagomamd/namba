@extends ('backend.layouts.app')

@section ('title', 'Manage Crawled Music')

@section('page-header')
    <h1>
        Manage Crawled Music
        <small>{{ $title }}</small>
    </h1>
@endsection

@section('content')
<div id="app">
    <div class="box box-success">
        <div class="box-header with-border">
            <ul class="nav nav-pills">
                <li :class="{active: albums.main}" @click.prevent="toggleMain('albums')">
                    <a href="#"><strong>Albums</strong></a>
                </li>
                <li :class="{active: singles.main}" @click.prevent="toggleMain('singles')">
                        <a href="#"><strong>Singles</strong></a>
                </li>
            </ul>
        </div>

        <div class="caption" align="center">
            <div class="caption">
                <h3>{{ $title }} @{{ albums? 'Albums' : 'Singles' }}</h3>
                <div v-if="albums.main">
                    <button class="btn btn-success btn-md"> Album Crawls
                    </button>
                </div>
                <div v-if="singles.main">
                    <button class="btn btn-success btn-md"> Single Crawls
                    </button>
                </div>
            </div>
            <hr>
        </div>
    </div>
        
    <div v-if="albums.main">

        <div v-show="albums.list">
            @if ($albums->isNotEmpty())
            <div class="box box-info">
                <div class="box-body">
                    <div class="col-xs-12">
                        <div class="box-header with-border">
                            <h4><strong>
                                @if ($albums->count() == 1)
                                    {{ str_singular(trans('labels.backend.music.albums.owner')) }}
                                @else
                                    {{ trans('labels.backend.music.albums.owner') }}
                                @endif
                            </strong></h4>
                        </div>
                            
                        <div class="list-group" 
                            style="margin-left: 10px; margin-right: 10px; margin-top: 10px">
                            @include('backend.music.crawl.albums-list')
                            <div class="clearfix"></div>
                        </div>
                        @if ($albums_count > 5)
                        <div align="center"> 
                            <a href="{!! route('admin.music.crawl.albums') !!}" class="btn btn-info btn-lg" 
                                style="margin-top: 20px; margin-bottom: 20px">
                                {{ 'All ' . $title . ' Albums' }}
                            </a>
                        </div>
                        @endif
                    </div><!-- col-xs-12 -->
                </div>
            </div>
            @else
                <p class="lead">No Albums To Crawl Yet</p>
            @endif
        </div>

    </div>

    <div v-if="singles.main">
        <div v-show="singles.list">
            @if ($singles->isNotEmpty())
            <div class="box box-info">
                <div class="box-body">
                    <div class="col-xs-12">
                        <div class="box-header with-border">
                            <h4><strong>
                                @if ($singles->count() == 1)
                                    {{ str_singular(trans('labels.backend.music.singles.owner')) }}
                                @else
                                    {{ trans('labels.backend.music.singles.owner') }}
                                @endif
                            </strong></h4>
                        </div>
                            
                        <div class="list-group" 
                            style="margin-left: 10px; margin-right: 10px; margin-top: 10px">
                            @include('backend.music.crawl.singles-list')
                            <div class="clearfix"></div>
                        </div>
                        @if ($singles_count > 5)
                        <div align="center"> 
                            <a href="{!! route('admin.music.crawl.singles') !!}" class="btn btn-info btn-lg" 
                                style="margin-top: 20px; margin-bottom: 20px">
                                {{ 'All ' . $title . ' Singles' }}
                            </a>
                        </div>
                        @endif
                    </div><!-- col-xs-12 -->
                </div>
            </div>
            @else
                <p class="lead">No Singles To Crawl Yet</p>
            @endif
        </div>
    </div>
</div>

@endsection

@section('after-scripts')
    <script src="{{ asset('js/backend/music/crawl.js') }}"></script>
@endsection