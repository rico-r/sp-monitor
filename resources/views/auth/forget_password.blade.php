@extends("layouts.master")
@section("main-content")
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h4>Forgot Password?</h4></div>
                <div class="card-body">
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
                   
                    <form id ="thisForm" action="{{ route('forget.password.post') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label>
                                Enter your email below to recieve a password Reset Link in your email</label>
                            <input type="text" name="email" placeholder="Enter your registered email"
                                class="form-control" />
                        </div>

                        <div class="form-group d-flex justify-content-end mt-5">
                            <button type="submit" class="submit-btn">Submit</button>
                        </div>
                        <div id ="loader"></div>

                        <div class="">
                            <p>Don't have an account yet? <a href="{{ route('register') }}" class="create_now">Create
                                    Now</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("js")
<script>
    $("#thisForm").submit(function() {
        $(".submit-btn").attr("disabled", true);
        $("#loader").show();
    });
</script>
@endpush