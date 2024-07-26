@extends("layouts.master")
@section("main-content")
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h4>Login</h4></div>
                <class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has("message"))
                        <div class="alert alert-success">
                            {{ session()->get("message") }}
                        </div>
                    @endif
                    <form class="formLogin" action="{{ route('login.post') }}" method="post">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" placeholder="Enter your Email"
                                value="{{ old('email') }}" />
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter Password" />
                        </div>
                        <div class="form-group mt-5">
                            <button type="submit" class="submit-btn">Log in</button>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('forget.password') }}">Forgot password?</a>
                        </div>
                        <div class="mt">
                        <p>Tidak Punya Akun? <a href="{{ route('register') }}" class="create_now">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
