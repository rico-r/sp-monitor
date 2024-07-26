@extends('layouts.master')
@section('main-content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4>Register</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form id = "thisForm" class="formRegister" action="{{ route('register.post') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" placeholder="Enter Name" name="name" class="form-control"
                                value="{{ old('name') }}" />
                            <div class="error">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="text">NIP</label>
                            <input type="text" name="nip" placeholder="nip" class="form-control"
                                value="{{ old('nip') }}" />
                            <div class="error">
                                @error('nip')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="text" name="email" placeholder="Email Address" class="form-control"
                                value="{{ old('email') }}" />
                            <div class="error">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" />
                            <div class="error">
                                @error('password')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="Confirm Password" />
                            <div class="error">
                                @error('confirm_password')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label>Jabatan:</label>
                            <select name="jabatan" required>
                                <option value="">Select Jabatan</option>
                                @foreach($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id_jabatan }}" {{ old('jabatan_id') == $jabatan->id_jabatan ? 'selected' : '' }}>{{ $jabatan->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="submit-btn">Create My Account</button>
                        </div>
                        <div id ="loader"></div>
                        <div class="mt-5">
                            <p>Already have an account? <a href="{{ route('login') }}" class="create_now">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    $("#thisForm").submit(function() {
        $(".submit-btn").attr("disabled", true);
        $("#loader").show();
    });
</script>
@endpush