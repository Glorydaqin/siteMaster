
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Supremseo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="0Bwkq7s7hDBjLJ4byR6EC2TW80lo3IbWtKpSJ7HB">
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
        <div class="alert alert-info mb-4" role="alert">
            Please <a href="/password/reset" class="f-w-400">Reset Password</a> if you face any issue.
        </div>
        <div class="card">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="card-body">
                        <h3 class="mb-4">Sign in</h3>
                        <form role="form" method="POST" action="/login/">
                            <div class="input-group mb-3">
                                <input type="email" placeholder="Email address" name="email" class="form-control " value="" required autocomplete="email">
                            </div>
                            <div class="input-group mb-4">
                                <input name="password" class="form-control " placeholder="Password" type="password" required autocomplete="current-password">
                            </div>
                            <div class="form-group text-left mt-2">
                                <div class="checkbox checkbox-primary d-inline">
                                    <input id="remember" name="remember" type="checkbox" >
                                    <label for="remember" class="cr"> Remember me</label>
                                </div>
                            </div>
                            <button class="btn btn-block btn-primary mb-4 rounded">Login</button>
                            <p class="mb-2 text-muted">Forgot password? <a href="/password/reset" class="f-w-400">Reset</a></p>
                            <p class="mb-0 text-muted">Don't have an account? <a href="/register/" class="f-w-400">Register</a></p>
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
