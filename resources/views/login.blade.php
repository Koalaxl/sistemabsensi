<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Absensi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Login</h2>
    @if($errors->any())
        <div style="color:red;">{{ $errors->first() }}</div>
    @endif

    <form action="{{ url('/login') }}" method="POST">
        @csrf
        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <label>Login Sebagai:</label>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="guru">Guru</option>
            <option value="guru_piket">Guru Piket</option>
        </select>
        <br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
