@extends ('backend.layouts.app')

@section ('title', 'Bulk Edit Tracks')

@section('page-header')
    <h1>
        {{ trans('labels.backend.music.tracks.management') }}
        <small>{{ trans('labels.backend.music.tracks.edit') }}</small>
    </h1>
@endsection

@section('content')
<div id="app">
    {{ Form::open(['route' => 'admin.music.tracks.bulk-update', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}


	<div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.music.tracks.edit') }}</h3>

            <div class="box-tools pull-right">
                @include('backend.music.partials.track-header-buttons')
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
	    @foreach ($tracks as $track)

            <div class="box-body">
                <div class="form-group">
                    <label for="full_title" class="col-lg-2 control-label">
                        Full Title
                    </label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="tracks[{!! $track->id !!}][full_title]" 
                            value='{!! "{$track->artists_title_comma} - {$track->title} {$track->features_title_comma}"  !!}'>
                    </div><!--col-lg-10-->
                </div><!--form control-->

			</div>
			@if (!$loop->last)
				<hr>
			@endif
        @endforeach
    </div>

    <div class="box box-info">
        <div class="box-body">
            <div class="pull-left">
                <a href="{{ route('admin.music.tracks.index') }}" class="btn btn-danger btn-md">
                    <i class="fa fa-close" data-toggle="tooltip" data-placement="top" title="{{ trans('buttons.general.cancel') }}"></i> {{ trans('buttons.general.cancel') }}
                </a>
            </div><!--pull-left-->

            <div class="pull-right">
                <button type="submit" class="btn btn-success btn-md">
                    <i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="{{ trans('buttons.general.crud.edit') }}"></i> {{ trans('buttons.general.crud.edit') }}
                </button>
            </div><!--pull-right-->

            <div class="clearfix"></div>
        </div><!-- /.box-body -->
    </div><!--box-->

    {{ Form::close() }}
</div>
@endsection