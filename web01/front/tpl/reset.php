
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password - Supremseo</title>
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
        <div class="card">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="card-body">
                        <h3 class="mb-4">Recover account</h3>
                        <form role="form" method="POST" action="/password/email">
                            <input type="hidden" name="_token" value="0Bwkq7s7hDBjLJ4byR6EC2TW80lo3IbWtKpSJ7HB">                            <div class="input-group mb-3">
                                <input type="email" placeholder="Email address" name="email" class="form-control " value="" required autocomplete="email">
                            </div>
                            <button class="btn btn-block btn-primary mb-4 rounded">Reset password</button>
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
