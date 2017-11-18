{{ Form::open(['route' => 'admin.music.singles.bulk-actions', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}
<table id="users-table" class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>X</th>
            <th>@sortablelink('id', 'ID')</th>
            <th>{{ trans('labels.backend.music.singles.table.title') }}</th>
            <th>@sortablelink('category.name', 'Category')</th>
            <th>@sortablelink('genre.name', 'Genre')</th>
            <th>{{ trans('labels.general.actions') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($singles as $single)
        <tr>
            <td><input type="checkbox" name="singles[{!! $single->id !!}]" value="{!! $single->id !!}"></td>
            <td>{{ $single->id }}</td>
            <td>{!! $single->track->full_title !!}</td> 
            <td>{{ $single->track->trackable->category->name }}</td>
            <td>{{ $single->track->trackable->genre->name }}</td>
            <td>{!! $single->action_buttons !!}</td>
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