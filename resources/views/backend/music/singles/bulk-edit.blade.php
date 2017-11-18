@extends ('backend.layouts.app')

@section ('title', 'Bulk Edit Singles')

@section('page-header')
    <h1>
        {{ trans('labels.backend.music.singles.management') }}
        <small>{{ trans('labels.backend.music.singles.edit') }}</small>
    </h1>
@endsection

@section('content')
<div id="app">
    {{ Form::open(['route' => 'admin.music.singles.bulk-update', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}


	<div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.music.singles.edit') }}</h3>

            <div class="box-tools pull-right">
                @include('backend.music.partials.single-header-buttons')
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
	    @foreach ($singles as $single)

            <div class="box-body">
                <div class="form-group">
                    <label for="full_title" class="col-lg-2 control-label">
                        Full Title
                    </label>

                    <div class="col-lg-10">
                        {!! $single->track->full_title  !!}
                    </div><!--col-lg-10-->
                </div><!--form control-->

                <div class="form-group">
                    <label for="categories" class="col-lg-2 control-label">
                        {{ str_singular(trans('validation.attributes.backend.music.categories.owner')) }}:
                    </label>
                    <div class="col-lg-10" style="display: inline-block;">
                        <select name="singles[{!! $single->id !!}][category]" class="form-control">
                            @foreach ($categories as $category)
                                <option value="{!! $category->name !!}"
                                {{ ($category->id == $single->category->id) ? "selected": "" }}>
                                {!! $category->name !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="genres" class="col-lg-2 control-label">
                        {{ str_singular(trans('validation.attributes.backend.music.genres.owner')) }}:
                    </label>
                    <div class="col-lg-10" style="display: inline-block;">
                        <select name="singles[{!! $single->id !!}][genre]" class="form-control">
                            @foreach ($genres as $genre)
                                <option value="{!! $genre->name !!}"
                                {{ ($genre->id == $single->genre->id) ? "selected": "" }}>
                                {!! $genre->name !!}</option>
                            @endforeach
                        </select> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="col-lg-2 control-label">
                        Description:
                    </label>

                    <div class="col-lg-10">
                        <textarea rows="4" cols="50" name="singles[{!! $single->id !!}][description]" maxlength="500"
                            class="form-control" data-vv-as="Single Description" v-validate="'min:2|max:190'"
                            placeholder="Single Description">{!! $single->description !!}
                        </textarea>
                        <span class="text-danger" v-if="errors.has('description')" 
                            v-text="errors.first('description')">
                        </span>
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
                <a href="{{ route('admin.music.singles.index') }}" class="btn btn-danger btn-md">
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