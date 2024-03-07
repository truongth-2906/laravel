<table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Email</th>
        <th scope="col">Phone Number</th>
        <th scope="col">Verify</th>
        <th scope="col">Business name</th>
        <th scope="col">Business sector</th>
        <th scope="col">Country</th>
        <th scope="col">Timezone</th>
        <th scope="col">Bio</th>
    </tr>
    </thead>

    <tbody>
    @foreach($employers as $employer)
        <tr>
            <td>{{ $employer->id ?? '' }}</td>
            <td>{{ $employer->firstname ?? '' }}</td>
            <td>{{ $employer->lastname ?? '' }}</td>
            <td>{{ $employer->email ?? '' }}</td>
            <td>{{ (!is_null($employer->callingCode) && $employer->phone_number ? '+' . $employer->callingCode->calling_code . ' ' : '') . $employer->phone_number }}</td>
            <td>
                @switch($employer->active)
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
            <td>{{ optional($employer->company)->name ?? '' }}</td>
            <td>{{ optional($employer->sector)->name ?? '' }}</td>
            <td>{{ optional($employer->country)->name ?? '' }}</td>
            <td>{{ optional($employer->utc)->name ?? '' }}</td>
            <td>{!! nl2br(e($employer->bio) ?? '') !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>
