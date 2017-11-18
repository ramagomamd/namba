@extends ('backend.layouts.app')

@section ('title', 'Manage Singles Crawls')

@section('page-header')
    <h1>
        Single Crawl Management
    </h1>
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $title }}</h3>

        <div class="box-tools">
            <div class="pull-right mb-10 hidden-sm hidden-xs">
                {{ Form::open(['route' => ['admin.music.crawl.singles'], 
                                'method' => 'post']) }}
                    <input type="submit" name="download" value="Crawl" 
                            class="btn btn-primary btn-sm">
                {{ Form::close() }}
            </div><!--pull right-->
        </div><!--box-tools-->
    </div><!-- /.box-header -->

    <div class="box-body">
    	@if ($singles->isNotEmpty())
            <div class="table-responsive"> 
            	@include('backend.music.crawl.singles-list')
            	{!! $singles->links() !!}           
            </div><!--table-responsive-->
        @else
            <div>
            	<p class="lead">No Singles To Crawl Yet</p>
            </div>
        @endif
    </div><!-- /.box-body -->  
</div>
@endsection