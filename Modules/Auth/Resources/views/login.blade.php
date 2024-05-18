@extends('single')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('') }}"><b>Base</b>Laravel</a>
        </div>

        <div class="card" id="container-login">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Login untuk memulai sesi</p>
                <form action="{{ url('auth/login/check') }}" method="post" id="form-login" data-type="login">
                    {{ csrf_field() }}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 offset-8">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </div>
                </form>

                <p class="mb-1">
                    <a href="javascript:;" id="action-forgot">Lupa password</a>
                </p>
            </div>
        </div>

        <div class="card" id="container-forgot" style="display: none">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Lupa Password</p>
                <form action="{{ url('auth/login/forgot') }}" method="post" id="form-forgot">
                    {{ csrf_field() }}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 offset-8">
                            <button type="submit" class="btn btn-primary btn-block">Reset</button>
                        </div>
                    </div>
                </form>

                <p class="mb-1">
                    <a href="javascript:;" id="action-login">Kembali ke login</a>
                </p>
            </div>
        </div>

        <div class="card" id="container-forgot-message" style="display: none">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Tautan untuk pengaturan ulang kata sandi telah dikirimkan.</p>
                <p class="login-box-msg pb-0">Silahkan cek email anda.</p>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        $('document').ready(function() {

            $('#form-login').formHandler({
                rules: {
                    email: VALIDATOR.email,
                    password: VALIDATOR.password
                },
                messages: {
                    email: VALIDATOR_MESSAGES.email,
                    password: VALIDATOR_MESSAGES.password
                },
            }, doLogin);

            $('#form-forgot').formHandler({
                rules: {
                    email: VALIDATOR.email
                },
                messages: {
                    email: VALIDATOR_MESSAGES.email
                },
            }, doForgot);


            $('#action-forgot').on('click', function(e) {
                swap('#container-login', '#container-forgot');
            });

            $('#action-login').on('click', function(e) {
                swap('#container-forgot', '#container-login');
            });

        });

        function doLogin(form) {
            let submitButton = $('#form-login button[type=submit]')
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serializeObject(),
                type: POST,
                dataType: JSON_DATA,
                beforeSend: function() {
                    disable(submitButton)
                    loading(submitButton, true)
                },
                success: function(payload, message, xhr) {
                    if (payload.code != 200) {
                        showMessage(payload.message, 'error')
                    } else {
                        showMessage(payload.message)
                        setTimeout(() => {
                            redirect('dashboard')
                        }, 500);
                    }
                },
                error: function(xhr, message, error) {
                    let payload = xhr.responseJSON
                    showMessage(payload.message, 'error')
                },
                complete: function(data) {
                    loading(submitButton, false)
                    enable(submitButton)
                }
            })
        }

        function doForgot(form) {
            let submitButton = $('#form-forgot button[type=submit]')
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serializeObject(),
                type: POST,
                dataType: JSON_DATA,
                beforeSend: function() {
                    disable(submitButton)
                    loading(submitButton, true)
                },
                success: function(payload, message, xhr) {
                    if (payload.code != 200)
                        showMessage(payload.message, 'error')
                    else
                        swap('#container-forgot', '#container-forgot-message')
                },
                error: function(xhr, message, error) {
                    let payload = xhr.responseJSON
                    showMessage(payload.message, 'error')
                },
                complete: function(data) {
                    loading(submitButton, false)
                    enable(submitButton)
                }
            })

        }
    </script>
@endsection
