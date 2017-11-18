@extends('frontend.layouts.app')

@section('before-content')
    <div class="well well-md">
        <h1 class="h4 text-center">All Albums</h1>
    </div>
@endsection

@section('content')

	 @if ($albums->isNotEmpty())
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="level">
                        <h3 class="flex">All Albums</code></h3>
                    </div>
                </div><!--panel-heading-->

                <div class="list-group">
	                	@foreach ($albums as $album)
	                		@if (!is_null($album->zip))
	                			<a href="#" class="list-group-item active">
									{!! $album->full_title !!}
								</a>
		                		<div class="list-group-item">
		                			<div class="input-group" style="margin-bottom: 1em">
		                				<span class="input-group-addon">Title</span>
										<input type="text" value="{!! $album->full_title !!}" class="form-control">
									</div>
									<div class="input-group">
		                				<span class="input-group-addon">zip</span>
										<input type="text" value="{!! $album->zip->getFullUrl() !!}" class="form-control">
									</div>
		                		</div>
	                		@endif
	                	@endforeach
	                	{!! $albums->links() !!}
                </div>
            </div><!--panel-->
        </div><!--col-md-6-->
    @endif

@endsection