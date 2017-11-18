<form method="POST" action="{{ route('frontend.music.singles.store') }}" class="form-horizontal" 
     enctype="multipart/form-data">
     {!! csrf_field() !!}
         <input type="hidden" name="category" value="south african">
         <input type="hidden" name="genre" value="hip hop">
         <input type="file" name="file"> <br>
         <button type="submit" class="btn btn-default btn-md">Upload</button>
 </form> 