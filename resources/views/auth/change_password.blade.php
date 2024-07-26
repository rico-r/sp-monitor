@extends("layouts.master")
@section("main-content")
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4>Change Password</h4>
                </div>
                <div class="card-body">
                    @if (session("error"))
                        <div class="alert alert-danger">
                            {{ session("error") }}
                        </div>
                    @endif
                    @if (session("success"))
                        <div class="alert alert-success">
                            {{ session("success") }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('password.change.post') }}">
                        {{ csrf_field() }}
                        <div class="form-group mt-3">
                            <label for="new-password">Current Password</label>
                            <input id="current_password" type="password" class="form-control" name="current_password">

                            <div class="error">
                                @error("current_password")
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="new-password">New Password</label>
                            <input id="new_password" type="password" class="form-control" name="new_password">
                            <div class="error">
                                @error("new_password")
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="new-password-confirm">Confirm New
                                Password</label>
                            <input id="new-password-confirm" type="password" class="form-control" name="confirm_password">
                            <div class="error">
                                @error("confirm_password")
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group  mt-5">
                            <button type="submit" class="btn btn-primary">
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
