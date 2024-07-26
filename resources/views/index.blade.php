@extends('layouts.master')
@section('main-content')
<div class="container home">
    <h1>Home</h1>

    <p> @if(Session::has('message'))
        <div class="alert alert-success">
            {{ Session::get('message') }}
            @php
                Session::forget('message');
            @endphp
        </div>
        @endif</p>
</div>
@endsection
