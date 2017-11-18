{{ Form::open(['route' => 'admin.music.albums.bulk-actions', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}
<table id="users-table" class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>X</th>
            <th>@sortablelink('id', 'ID')</th>
            <th>@sortablelink('title')</th>
            <th>Artists</th>
            <th>@sortablelink('slug')</th>
            <th>@sortablelink('category.name', 'Category')</th>
            <th>@sortablelink('genre.name', 'Genre')</th>
            <th>{{ trans('labels.backend.music.albums.table.tracks_number') }}</th>
            <th>{{ trans('labels.general.actions') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($albums as $album)
        <tr>
            <td><input type="checkbox" name="albums[{!! $album->id !!}]" value="{!! $album->id !!}"></td>
            <td>{{ $album->id }}</td>
            <td>{{ $album->title }}</td>
            <td>{{ $album->artists_title_comma }}</td>
            <td>{{ $album->slug }}</td>
            <td>{{ $album->category->name }}</td>
            <td>{{ $album->genre->name }}</td>
             <td>{{ $album->tracks_count }}</td>
            <td>{!! $album->action_buttons !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="checkbox" style="margin-bottom: 1em">
    <label class="btn btn-warning btn-sm" style="padding: 1em; margin-right: 1em">
        <input type="radio" name="action" value="Edit" checked> Edit
    </label>
    <label class="btn btn-danger btn-sm" style="padding: 1em; margin-right: 1em">
        <input type="radio" name="action" value="Delete"> Delete
    </label>
</div><!--col-lg-10-->
<div class="btn-group">
    <button class="btn btn-primary" type="submit">Submit</button>
</div>
{{ Form::close() }}