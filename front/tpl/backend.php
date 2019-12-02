<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>


<body>
用户名:<input type="text" name="txtName" id="txtUserName" />
密码:<input type="password" name="txtPWD"  id="txtUserPwd"/><br />
<input type="button" value="登录" id="btnLogin" />
<span id="msg" style="font-size:14px;color:red"></span><br />
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript">
    $(function () {
        $("#msg").css("display","none");
        $("#btnLogin").click(function () {
            userLogin();
        });
    });
    function userLogin() {
        var userName = $("#txtUserName").val();
        var userPwd = $("#txtUserPwd").val();
        if (userName != "" && userPwd != "") {
            $.post("/", { "userName": userName, "userPwd": userPwd }, function (data) {
                var serverData = data.split(':');
                if (serverData[0] == "ok") {
                    window.location.href = "/";
                } else {
                    $("#msg").css("display", "block");
                    $("#msg").text(serverData[1]);
                    $("#txtUserName").val("");
                }

            });

        } else {
            $("#msg").css("display", "block");
            $("#msg").text("用户名密码不能为空!!");
        }
    }
</script>
</body>


</html>