@extends('master')

@section('content')
    <h1>Hello World</h1>
    @can('isSuperadmin')
        Saya Super Admin
    @elsecan('isMasterManager')
        Saya Master Manager
    @else
        Saya Tidak Punya Role
    @endcan
@endsection
