<table class="table table-striped table-responsive table-condensed table-hover">
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
        @foreach ($singles as $single)
        <tr>
            <td><input type="checkbox" name="edit[]"></td>
            <td>{{ $single->id }}</td>
            <td>{!! str_limit($single->title, 40) !!}</td> 
            <td>
                @if ($single->status == 'crawled')
                    <span class='label label-success'>Crawled</span>
                @elseif ($single->status == 'uncrawled')
                    <span class='label label-warning'>Not Crawled</span>
                @elseif ($single->status == 'error')
                    <span class='label label-danger'>Error</span>
                @endif
            </td>
            <td>{{ $single->category }} <span class="fa fa-arrows-alt"></span> {{ $single->genre }}</td>
            <td>{{ title_case($single->site_name) }}</td>
            <td>{{ str_limit($single->link, 35) }}</td>
            <td>
                <span class="fa fa-trash"></span>
                <span class="fa fa-edit"></span>
                <span class="fa fa-eye"></span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>