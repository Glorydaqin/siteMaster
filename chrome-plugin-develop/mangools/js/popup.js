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
    $("#i1").val(items.username);
    $("#i2").val(items.password);
  });

  //登录按钮
  $("#login").on("click", function () {
    // bg.closePlugins();

    let mainfest = chrome.runtime.getManifest();

    //账号登陆
    let username = $("#i1").val();
    let password = $("#i2").val();

    let url = "https://vipfor.me/api/login/";
    let data = {username: username, password: password, site_id: 2, v: mainfest.version};
    let index = layer.load(1, {
      shade: [0.1, '#fff'] //0.1透明度的白色背景
    });
    $.post(url, data, function (response) {
      let jsonObj = JSON.parse(response);
      console.log(jsonObj);

      layer.close(index);
      if (jsonObj.code === 200 && jsonObj.data.is_active === true) {
        bg.loginInfoClear();
        chrome.storage.local.set({username: username, password: password}, function () {
          console.log('本地账号保存成功！');
          bg.setUser(username, password);
        });
        bg.setAccountList(jsonObj.data.account_list);
        bg.setLastPluginId(jsonObj.data.last_plugin_id);
        showAccountList(jsonObj.data.account_list);
      } else {
        alert(jsonObj.message);
      }
    });
  });

  //选账号登录按钮
  $(document).on('click', '.loginNum', function () {
    bg.setCurrentAccountIndex($(this).attr('data-index'));
    let accountInfo = bg.getCurrentAccount();

    if (accountInfo.type === '2') {
      //cookie 模式 种植cookie
      let new_cookie = {
        'url': 'https://mangools.com/',
        "name": "_mangotools_com_session",
        'value': accountInfo.username,
        // 'domain': 'mangools.com',
        'httpOnly': true,
        'secure': true,
        // 'expirationDate': timestamps
      };
      // chrome.tabs.create({url: 'https://app.kwfinder.com'}, function (tab) {
      //
      //   // 创建页面后 清除cookie 后重新植入cookie 然后刷新页面
      //   bg.loginInfoClear();
      //
      //   chrome.cookies.set(
      //       new_cookie, function (cookie) {
      //         console.log(cookie);
      //         // setTimeout(function () {
      //         //   chrome.tabs.reload(tab.id)
      //         // }, 5000);
      //
      //         showLogout();
      //       }
      //   );
      // });

      chrome.cookies.set(
          new_cookie, function (cookie) {
            chrome.tabs.create({url: 'https://app.kwfinder.com'});
            showLogout();
          }
      );
    } else {
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
    }
  });

  //登出
  $("#logout").on("click", function () {
    // bg.revertPlugins();
    bg.loginInfoClear();
    bg.closeOpenWindow();

    showLogin();
  });

});
