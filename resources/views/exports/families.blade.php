<table>
    <thead>
    <tr>
        <th>#</th>
        <th><b>name</b></th>
        <th><b>phone</b></th>
        <th><b>email</b></th>
    </tr>
    </thead>
    <tbody>

        <tr>
            <td>{{ 0 }}</td>
            <td>{{(string)$staff->name}}</td>
            <td>{{ $staff->phone }}</td>
            <td>{{ $staff->email }}</td>
        </tr>

    @foreach($users as $index => $user)
        <tr>
            <td>{{ $index+1 }}</td>
            <td>{{(string)$user->name}}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->email }}</td>
        </tr>
    @endforeach
    </tbody>
</table>