{{ Form::open(['route' => 'admin.music.tracks.bulk-actions', 'class' => 'form-horizontal', 'role' => 'form', 
    'method' => 'post']) }}
<table id="tracks-table" class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>X</th>
            <th>@sortablelink('id', 'ID')</th>
            <th>@sortablelink('title')</th>
            <th>{{ trans('labels.backend.music.artists.owner') }}</th>
            <th>@sortablelink('slug')</th>
            <th>@sortablelink('duration')</th>
            <th>{{ trans('labels.general.actions') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($tracks as $track)
        <tr>
            <td><input type="checkbox" name="tracks[{!! $track->id !!}]" value="{!! $track->id !!}"></td>
            <td>{!! $track->id !!}</td>
            <td>{!! str_limit($track->title . $track->features_title_comma, 45) !!}</td>
            <td>{!! str_limit($track->artists_title_comma, 55) !!}</td>
            <td>{!! str_limit($track->slug, 35) !!}</td>
            <td>{!! $track->duration !!}</td>
            <td>{!! $track->show_button !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<input type="hidden" name="action" value="Edit">
<div class="btn-group">
    <button class="btn btn-primary" type="submit">Edit</button>
</div>
{{ Form::close() }}