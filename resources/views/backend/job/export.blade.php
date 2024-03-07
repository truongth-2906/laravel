<table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Business name</th>
        <th scope="col">Company representative full name</th>
        <th scope="col">Job title</th>
        <th scope="col">Job status</th>
        <th scope="col">Country</th>
        <th scope="col">Timezone</th>
        <th scope="col">Job Description</th>
        <th scope="col">RPA Software</th>
        <th scope="col">RPA Experience</th>
    </tr>
    </thead>

    <tbody>
    @foreach($jobs as $job)
        <tr>
            <td>{{ $job->id }}</td>
            <td>{{ optional($job->company)->name }}</td>
            <td>{{ optional($job->user)->name }}</td>
            <td>{{ $job->name }}</td>
            <td>{{ $job->status ? 'Open' : 'Close' }}</td>
            <td>{{ optional($job->country)->name }}</td>
            <td>{{ optional($job->timezone)->name }}</td>
            <td>{!! nl2br($job->description) !!}</td>
            <td>
                @foreach($job->categories as $category)
                    {{ $category->name }}
                    <br>
                @endforeach
            </td>
            <td>{{ optional($job->experience)->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
