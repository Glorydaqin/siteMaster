<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - Supremseo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="1ZsXhWUzGcV7KHqo8x7KK3F2W1zRLjdIsrUbSqCv">
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/style.css?1">
</head>
<body>
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<div class="auth-wrapper">
    <div class="auth-content text-center">
        <a href="/"><img src="/assets/images/logo.png" class="img-fluid mb-4"></a>
        <div class="card">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="card-body">
                        <form role="form" method="POST" action="https://supremseo.com/register">
                            <input type="hidden" name="_token" value="1ZsXhWUzGcV7KHqo8x7KK3F2W1zRLjdIsrUbSqCv">                            <h3 class="mb-3">Register</h3>
                            <div class="input-group mb-3">
                                <input type="text" placeholder="Your Name" name="name" class="form-control " value="" required autocomplete="name">
                            </div>
                            <div class="input-group mb-3">
                                <input type="email" placeholder="Email address" name="email" class="form-control " value="" required autocomplete="email">
                            </div>
                            <div class="input-group mb-3">
                                <input name="password" class="form-control " placeholder="Password" type="password" required autocomplete="current-password">
                            </div>
                            <div class="input-group mb-4">
                                <input name="password_confirmation" class="form-control " placeholder="Confirm password" type="password" required autocomplete="current-password">
                            </div>
                            <button class="btn btn-block btn-primary mb-4 rounded">Register</button>
                            <p class="mb-0 text-muted">Already have an account? <a href="https://supremseo.com/login" class="f-w-400">Login</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/vendor-all.min.js"></script>
<script src="/assets/js/plugins/bootstrap.min.js"></script>
<script src="/assets/js/pcoded.min.js?1"></script>
</body>
</html>
