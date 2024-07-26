@extends("layouts.master")
@section("main-content")
<div class="container home">
    <h1>Error</h1>
    <h3> @if (session("error"))
        <div class="alert alert-danger">
            {{ session("error") }}
        </div>
      @endif</h3>
    <a href="{{route("home")}}">Go to Home</a>
</div>
@endsection
