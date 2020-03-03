$(document).ready(function () {
  var bg = chrome.extension.getBackgroundPage();

  function showLogin() {
    $('#mDiv').show();
    $('#mDivb').hide();
    $("#mDivc").hide();
    bg.setPageStatus("login");
  }

  function showAccountList(accountList) {
    //修改字段
    $('#accountList').html('');
    accountList.forEach(function (item, index) {
      $('#accountList').append('<div id="bDiv2" class="divaa loginNum" data-index="' + index + '"><span>登录账号' + (index + 1) + '</span></div>')
    });
    //账号成功则显示 mDivb
    $('#mDiv').hide();
    $('#mDivb').show();
    $("#mDivc").hide();
    bg.setPageStatus("account");
  }

  function showLogout() {
    $('#mDiv').hide();
    $('#mDivb').hide();
    $("#mDivc").show();
    bg.setPageStatus("logout");
  }

  chrome.runtime.sendMessage({type: 'getPageStatus'}, function (pageStatus) {
    console.log(pageStatus);
    if (pageStatus === 'login') {
      $('#mDiv').show();
    } else if (pageStatus === 'logout') {
      $("#mDivc").show();
    } else if (pageStatus === 'account') {
      chrome.runtime.sendMessage({type: 'getAccountList'}, function (accountList) {
        showAccountList(accountList)
      })

    }
  });

  chrome.storage.local.get({username: null, password: null}, function (items) {
    console.log(items.username, items.password);

    $("#i1").val(items.username);
    $("#i2").val(items.password);
  });

  //登录按钮
  $("#login").on("click", function () {

    let timestamps = Math.round(new Date() / 1000) + 86400 * 30;

    //set-cookie: PHPSESSID=msdmw49w8f5f4r2ro4tvxhaedam5n40t; Path=/; Domain=semrush.com; Expires=Mon, 01 Mar 2021 14:14:54 GMT; HttpOnly; Secure
    // set-cookie: SSO-JWT=eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJtc2RtdzQ5dzhmNWY0cjJybzR0dnhoYWVkYW01bjQwdCIsImlhdCI6MTU4MzA3MjA5NCwiaXNzIjoic3NvIiwidWlkIjo1MzQ0MDkyfQ.fY1SY-OTghaiX4t4bskKvNnxVHBUO6schNLpSc41AAQJZQeACtKEoEMzT-j4vfqcHNCcQYgJ6oWOUpef5EmAOQ; Path=/; Domain=semrush.com; Expires=Mon, 01 Mar 2021 14:14:54 GMT; HttpOnly; Secure
    // set-cookie: sso_token=0e6d872f30b184a38643f2ecee995da410390c35a5659535fb4dd7fa8680e385; Path=/; Domain=semrush.com; Expires=Mon, 01 Mar 2021 14:14:54 GMT; HttpOnly; Secure
    chrome.cookies.set(
        {
          'url': 'https://www.semrush.com',
          "name": "PHPSESSID",
          'value': "msdmw49w8f5f4r2ro4tvxhaedam5n40t",
          'domain': 'semrush.com',
          'httpOnly': true,
          'secure': true,
          'expirationDate': timestamps
        }, function (cookie) {
          console.log(cookie);
        }
    );
    chrome.cookies.set(
        {
          'url': 'https://www.semrush.com',
          "name": "SSO-JWT",
          'value': "eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJtc2RtdzQ5dzhmNWY0cjJybzR0dnhoYWVkYW01bjQwdCIsImlhdCI6MTU4MzA3MjA5NCwiaXNzIjoic3NvIiwidWlkIjo1MzQ0MDkyfQ.fY1SY-OTghaiX4t4bskKvNnxVHBUO6schNLpSc41AAQJZQeACtKEoEMzT-j4vfqcHNCcQYgJ6oWOUpef5EmAOQ",
          'domain': 'semrush.com',
          'httpOnly': true,
          'secure': true,
          'expirationDate': timestamps
        }, function (cookie) {
          console.log(cookie);
        }
    );
    chrome.cookies.set(
        {
          'url': 'https://www.semrush.com',
          "name": "sso_token",
          'value': "0e6d872f30b184a38643f2ecee995da410390c35a5659535fb4dd7fa8680e385",
          'domain': 'semrush.com',
          'httpOnly': true,
          'secure': true,
          'expirationDate': timestamps
        }, function (cookie) {
          console.log(cookie);
        }
    );
    alert('finish');


    return;
    bg.closePlugins();

    //账号登陆
    let username = $("#i1").val();
    let password = $("#i2").val();

    let url = "https://vipfor.me/api/login/";
    let data = {username: username, password: password, site_id: 2};
    let index = layer.load(1, {
      shade: [0.1, '#fff'] //0.1透明度的白色背景
    });
    $.post(url, data, function (response) {
      let jsonObj = JSON.parse(response);
      console.log(jsonObj);

      layer.close(index);
      if (jsonObj.code === 200 && jsonObj.data.is_active === true) {
        chrome.storage.local.set({username: username, password: password}, function () {
          console.log('本地账号保存成功！');
          bg.setUser(username, password);
        });
        bg.setAccountList(jsonObj.data.account_list);
        bg.setLastPluginId(jsonObj.data.last_plugin_id);
        showAccountList(jsonObj.data.account_list);
      } else {
        alert("登陆失败或账号过期");
      }
    });
  });

  //选账号登录按钮
  $(document).on('click', '.loginNum', function () {
    bg.setCurrentAccountIndex($(this).attr('data-index'));

    chrome.windows.create({
      url: "https://mangools.com/users/sign_in",
      width: 420,
      height: 800,
      type: "popup"
    }, function (window) {

      bg.setLoginWindowId(window.id);
      bg.setLoginTabId(window.tabs[0].id);

      showLogout();
    });
  });

  //登出
  $("#logout").on("click", function () {
    bg.revertPlugins();
    bg.loginInfoClear();
    bg.closeOpenWindow();

    showLogin();
  });

});