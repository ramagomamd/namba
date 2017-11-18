<table id="users-table" class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>@sortablelink('id', 'ID')</th>
            <th>@sortablelink('name')</th>
            <th>@sortablelink('slug')</th>
            <th>{{ trans('labels.general.actions') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
            <td>{{ $category->slug }}</td>
            <td>{!! $category->action_buttons !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>