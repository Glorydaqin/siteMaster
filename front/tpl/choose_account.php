<html>
<head>
    <title>选择账号</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .form-item {
            margin-bottom: 24px;
            vertical-align: top;
        }

        .login-form {
            width: 300px;
            margin: 200px auto 0;
            transform: translateY(-50%);
            border: 1px solid #dcdee2;
            padding: 30px;
        }

        .form-item label {
            width: 60px;
            vertical-align: middle;
            font-size: 14px;
            color: #515a6e;
            line-height: 1;
            padding: 10px 12px 10px 0;
            box-sizing: border-box;
        }

        .form-item .input {
            display: block;
            width: 100%;
            height: 32px;
            line-height: 1.5;
            padding: 4px 7px;
            font-size: 14px;
            border: 1px solid #dcdee2;
            border-radius: 4px;
            color: #515a6e;
            background-color: #fff;
            background-image: none;
            position: relative;
            cursor: text;
            transition: border .2s ease-in-out, background .2s ease-in-out, box-shadow .2s ease-in-out;
        }

        input:hover, input:focus {
            border-color: #57a3f3;
        }

        .form-item .button {
            color: #fff;
            background-color: #2d8cf0;
            border-color: #2d8cf0;
            transition: all .2s ease-in-out;
        }

        .button:hover {
            cursor: pointer;
            background-color: #57a3f3;
            border-color: #57a3f3;
        }
    </style>
</head>


<body>
<p>
    <?=$welcome?>
    <a href="<?=PROTOCOL.DOMAIN?>/">退出</a>
</p>

<form action="/choose_account/" method="post" class="login-form">

    <div class="form-item">
        <label>选择账号</label>
        <select class="input" name="account_id" id="">
            <? foreach ($account_list as $item) { ?>
                <option value="<?= $item['id'] ?>">账号 <?= $item['id'] ?></option>
            <? } ?>
        </select>
    </div>

    <br>
    <div class="form-item">
        <input class="button input" type="submit" value="进入">
    </div>

</form>

</body>


</html>