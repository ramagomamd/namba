<table class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>Select</th>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Category <span class="fa fa-arrows"></span> Genre</th>
            <th>Site</th>
            <th>Link</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($albums as $album)
        <tr>
            <td><input type="checkbox" name="edit[]"></td>
            <td>{{ $album->id }}</td>
            <td>{!! str_limit($album->title, 40) !!}</td> 
            <td>
                @if ($album->status == 'crawled')
                    <span class='label label-success'>Crawled</span>
                @elseif ($album->status == 'uncrawled')
                    <span class='label label-warning'>Not Crawled</span>
                @elseif ($album->status == 'error')
                    <span class='label label-danger'>Error</span>
                @endif
            </td>
            <td>{{ $album->category }} <span class="fa fa-arrows-alt"></span> {{ $album->genre }}</td>
            <td>{{ title_case($album->site_name) }}</td>
            <td>{{ str_limit($album->link, 35) }}</td>
            <td>
                <span class="fa fa-trash"></span>
                <span class="fa fa-edit"></span>
                <span class="fa fa-eye"></span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>