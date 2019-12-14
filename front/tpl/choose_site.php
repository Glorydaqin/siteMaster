<html>
<head>
    <title>选择平台</title>
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

        .box-side {
            width: 600px;
            margin: 0 auto;
        }

        .box {
            width: 200px;
            border: 1px solid deepskyblue;
            float: left;
            height: 200px;
            margin: 10px 25px;
            line-height: 180px;
            font-size: 20px;
            text-align: center;
            color: dimgray;
            background-color: beige;
        }
    </style>
</head>


<body>
<p>
    <?= $welcome ?>
    <a href="<?= PROTOCOL . DOMAIN ?>/">退出</a>
</p>

<p>
<div class="box-side">
    <? foreach ($site_list as $site) { ?>
        <? if ($site['is_available']) { ?>
            <a href="/choose_site/?site_id=<?= $site['id'] ?>">
                <div class="box">
                    <?= strtoupper($site['name']) ?>
                </div>
            </a>
        <? } else { ?>
            <div class="box">
                <?= strtoupper($site['name']) ?>(待开通)
            </div>
        <? } ?>
    <? } ?>
</div>

</p>

<br>

</body>


</html>