<table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Email</th>
        <th scope="col">Tag line</th>
        <th scope="col">Phone Number</th>
        <th scope="col">Verify</th>
        <th scope="col">Country</th>
        <th scope="col">Timezone</th>
        <th scope="col">Bio</th>
        <th scope="col">RPA Software</th>
        <th scope="col">RPA Experience</th>
        <th scope="col">Hidden Status</th>
    </tr>
    </thead>

    <tbody>
    @foreach($freelancers as $freelancer)
        <tr>
            <td>{{ $freelancer->id ?? '' }}</td>
            <td>{{ $freelancer->firstname ?? '' }}</td>
            <td>{{ $freelancer->lastname ?? '' }}</td>
            <td>{{ $freelancer->email ?? '' }}</td>
            <td>{{ $freelancer->tag_line ?? '' }}</td>
            <td>{{ (!is_null($freelancer->callingCode) && $freelancer->phone_number ? '+' . $freelancer->callingCode->calling_code . ' ' : '') . $freelancer->phone_number }}</td>
            <td>
                @switch($freelancer->active)
                    @case(\App\Domains\Auth\Models\User::IS_ACTIVE)
                        Active
                        @break
                    @case(\App\Domains\Auth\Models\User::IS_DECLINED)
                        Declined
                        @break
                    @default
                        Not verified
                        @break
                @endswitch
            </td>
            <td>{{ optional($freelancer->country)->name ?? '' }}</td>
            <td>{{ optional($freelancer->utc)->name ?? '' }}</td>
            <td>{!! nl2br(e($freelancer->bio ?? '')) !!}</td>
            <td>
                @foreach($freelancer->categories as $category)
                    @if ($category->name)
                        {{ $category->name}}
                        <br>
                    @endif
                @endforeach
            </td>
            <td>{{ optional($freelancer->experience)->name ?? '' }}</td>
            <td>{{ $freelancer->is_hidden ? 'Yes' : 'No' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
