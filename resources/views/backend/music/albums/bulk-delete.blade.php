@extends ('backend.layouts.app')

@section ('title', 'Bulk Delete Albums')

@section('page-header')
    <h1>
        {{ trans('labels.backend.music.albums.management') }}
        <small>Delete</small>
    </h1>
@endsection

@section('content')
<div id="app">
    {{ Form::open(['route' => 'admin.music.albums.bulk-delete', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}


	<div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Delete</h3>

            <div class="box-tools pull-right">
                @include('backend.music.partials.album-header-buttons')
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
	    
		<div class="box-body">
			@foreach ($albums as $album)
			<div class="list-group">
				<input type="hidden" name="albums[{!! $album->id !!}]">
				<a href="#" class="list-group-item active">
					<h4 class="list-group-item-heading">
						{!! $album->full_title !!}
					</h4>
				</a>
				<a href="#" class="list-group-item">
					<h4 class="list-group-item-heading">
						{!!  title_case('are you sure you want to delete this album?') !!}
						<div class="checkbox" style="margin-bottom: 1em">
						    <label class="btn btn-success btn-sm" style="padding: 1em; margin-right: 1em">
						        <input type="radio" name="albums[{!! $album->id !!}][confirm]" value="yes" checked> Yes
						    </label>
						    <label class="btn btn-danger btn-sm" style="padding: 1em; margin-right: 1em">
						        <input type="radio" name="albums[{!! $album->id !!}][confirm]" value="no"> No
						    </label>
						</div><!--col-lg-10-->
					</h4>
					<p class="list-group-item-text"> 
						This Album Has <code>{!! $album->tracks_count !!}</code> Tracks And They Will Be Deleted Altogether
					</p>
				</a>
			</div>
			@endforeach
        </div>
    </div>

    <div class="box box-info">
        <div class="box-body">
            <div class="pull-left">
                <a href="{{ route('admin.music.albums.index') }}" class="btn btn-info btn-md">
                    <i class="fa fa-close" data-toggle="tooltip" data-placement="top" title="{{ trans('buttons.general.cancel') }}"></i> {{ trans('buttons.general.cancel') }}
                </a>
            </div><!--pull-left-->

            <div class="pull-right">
                <button type="submit" class="btn btn-warning btn-md">
                    <i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="{{ trans('buttons.general.crud.delete') }}"></i> {{ trans('buttons.general.crud.delete') }}
                </button>
            </div><!--pull-right-->

            <div class="clearfix"></div>
        </div><!-- /.box-body -->
    </div><!--box-->

    {{ Form::close() }}
</div>
@endsection