<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Users List</h1>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Password</th>
    </tr>
    </thead>
    <tbody>
    {{$users}}
{{--{{$title}}--}}
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
{{--            <td>{{$users->createdt}}</td>--}}
        </tr>
    @endforeach
    </tbody>

</table>
</body>
</html>
