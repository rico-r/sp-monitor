@extends("layouts.master")
@section("main-content")
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4>Reset Password</h4>
                </div>
                <div class="card-body">

                    @if (session()->has("error"))
                        <div class="alert alert-danger">
                            {{ session()->get("error") }}
                        </div>
                    @endif
                    <form action="{{ route('reset.password.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group mb-4">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" name="email" autofocus value="{{ old('email') }}"
                                placeholder="Enter your Email">
                            <div class="error">
                                @error("email")
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" name="new_password"
                                placeholder="Enter New Password">
                            <div class="error">
                                @error("new_password")
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password-confirm">Confirm
                                Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="Re enter Password">
                            <div class="error">
                                @error("confirm_password")
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="submit-btn">
                                Reset Password
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
