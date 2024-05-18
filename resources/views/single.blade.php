<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Base Laravel</title>

    @include('styles')

    @yield('css')

</head>

<body class="hold-transition login-page">

    @yield('content')

    @include('scripts')

    @yield('js')

</body>

</html>
