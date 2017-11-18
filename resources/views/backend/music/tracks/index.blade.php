@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.music.tracks.management'))

@section('after-styles')
    {{ Html::style("css/datatables.min.css") }}
@endsection

@section('page-header')
    <h1>
        {{ trans('labels.backend.music.tracks.management') }}
    </h1>
@endsection

@section('content')

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $title }}</h3>

        <div class="box-tools pull-right">
            @include('backend.music.partials.track-header-buttons')
        </div><!--box-tools pull-right-->
    </div><!-- /.box-header -->

    <div class="box-body">
        <div class="table-responsive">
            {!! $tracks->appends(\Request::except('page'))->render() !!}
            @include('backend.music.tracks.list')
            {!! $tracks->appends(\Request::except('page'))->render() !!}
        </div>
    </div><!-- /.box-body -->
</div>

@endsection