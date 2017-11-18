{{ Form::open(['route' => 'admin.music.tracks.bulk-actions', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}
<table id="tracks-table" class="table table-striped table-condensed table-hover">
<thead>
    <tr>
        <th>X</th>
        <th>{{ trans('labels.backend.music.tracks.table.id') }}</th>
        <th>{{ trans('labels.backend.music.tracks.table.title') }}</th>
        <th>{{ trans('labels.backend.music.tracks.table.slug') }}</th>
        <th>{{ trans('labels.backend.music.tracks.table.duration') }}</th>
        <th>{{ trans('labels.general.actions') }}</th>
    </tr>
</thead>

<tbody>
    @foreach ($tracks as $track)
    <tr>
        <td><input type="checkbox" name="tracks[{!! $track->id !!}]" value="{!! $track->id !!}"></td>
        <td>{{ $track->id }}</td>
        <td>{{ str_limit($track->full_title, 35) }}</td>
        <td>{{ str_limit($track->slug, 35) }}</td>
        <td>{{ $track->duration }}</td>
        <td>
            {!! $track->show_button !!}
            {!! $track->delete_button !!}  
        </td>
    </tr>
    @endforeach
</tbody></table>

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