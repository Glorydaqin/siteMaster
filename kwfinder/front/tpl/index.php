<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body{
            background: #00438b;
            color: white;
        }
        .form-item{
            margin-bottom: 24px;
            vertical-align: top;
        }
        .form-item label{
            width: 60px;
            vertical-align: middle;
            font-size: 14px;
            /* color: #515a6e; */
            line-height: 1;
            padding: 10px 12px 10px 0;
            box-sizing: border-box;
        }
        .form-item input{
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
            transition: border .2s ease-in-out,background .2s ease-in-out,box-shadow .2s ease-in-out;
        }
        input:hover,input:focus{
            border-color: #57a3f3;
        }
        .form-item .button{
            color: #fff;
            background-color: #f80;
            border-color: #f80;
            transition: all .2s ease-in-out;
        }
        .button:hover{
            cursor: pointer;
            background-color: #57a3f3;
            border-color: #57a3f3;
        }
        body{
            position: relative;
        }
        .login-form{
            width: 300px;
            margin: 200px auto 0;
            transform: translateY(-50%);
            /* border: 1px solid #dcdee2; */
            padding: 30px;
        }
    </style>
</head>


<body>

<form action="/" method="post" class="login-form">
<!--    用户名: <input type="text" name="username">-->
<!--    密码: <input type="text" name="password">-->

    <div class="form-item">
        <label for="name">用户名</label>
        <input type="text" id="name" name="username">
    </div>
    <div class="form-item">
        <label for="name">密码</label>
        <input type="text" id="name" name="password">
    </div>
    <div class="form-item">
        <label for=""></label>
        <input class="button" type="submit" value="Sign In">
    </div>
</form>

</body>


</html>