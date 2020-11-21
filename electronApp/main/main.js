//const TabGroup = require('electron-tabs') normally but for main :
const TabGroup = require("../index");
const dragula = require("dragula");
const {ipcRenderer} = require('electron')

const siteMap = {1: 'ahrefs', 2: 'mangools'}
let lastPluginId = false;
let siteId = 1;
let innerAccountList = [];
let isLogin = false; // 是否登陆中状态
let username = '';
let password = '';

let tabGroup = new TabGroup({
  // 显示新加标签
  // newTab: {
  //   title: 'New Tab'
  // },
  ready: function (tabGroup) {
    dragula([tabGroup.tabContainer], {
      direction: "horizontal"
    });
  }
});
//
// tabGroup.addTab({
//   title: "Electron",
//   src: "https://ie.icoa.cn/",
//   visible: true,
//   active: true
// });
tabGroup.addTab({
  title: "ahrefs",
  src: "https://ahrefs.com/user/login/",
  visible: true,
  active: true
});

let btnFanhui = document.getElementById('btn-fanhui');
let btnShuaxin = document.getElementById('btn-shuaxin');
let btnHide = document.getElementById("btn-hide");
btnFanhui.onclick = function () {
  if (tabGroup.getActiveTab().webview.canGoBack()) {
    tabGroup.getActiveTab().webview.goBack();
  }
}
btnShuaxin.onclick = function () {
  tabGroup.getActiveTab().webview.reload();
}

btnHide.onclick = function () {
  let uc = $("#user-center");
  uc.toggle();
  btnHide.innerText = uc.css("display") === 'none' ? '显示' : '隐藏';
}

setInterval(function () {
  //每5分钟检测一次是否是最新设备在线
  if (lastPluginId && isLogin) {

    $.post('https://vipfor.me/api/check/', {
      'username': username,
      'password': password,
      'last_plugin_id': lastPluginId
    }, function (response) {
      let jsonObj = JSON.parse(response);
      console.log(jsonObj);

      if (jsonObj.code !== 200) {
        logout();
        alert('账号在其他设备登陆')
      }
    });
  }

}, 1000 * 60 * 3);


/**
 * 更新账号服务器列表
 */
function initInnerAccountList() {
  $(".account-list").empty();
  let html = "<ul>";
  innerAccountList.forEach(function (item, key) {
    html += "<li class=\"list-group-item\">" +
        "<span class=\"badge badge-primary cursor_pointer\" onclick='changeInnerAccount(" + key + ")'>切换</span>服务账号" + (key + 1) + "&nbsp;&nbsp;</li>";
  })
  html += "</ul>";
  $(".account-list").append(html);
}

/**
 * 切换植入账号
 * @param index
 */
function changeInnerAccount(index = 0) {
  let currentAccount = innerAccountList[index];
  let type = siteMap[siteId];

  //去除蒙版
  $('.browser .cover').hide();

  if (type === 'mangools') {
    //清除浏览器标签
    tabGroup.getTabs().forEach((tab) => {
      tab.close(true)
    })

    var url = 'https://app.kwfinder.com/?login_token=9CRYzQY7wytkTZPwSsFF&sso_ticket=420629489d99646c3c7332a1f08844b1930bf308e6b38f045765713b84aa469e';
    //打开新的标签页
    tabGroup.addTab({
      title: "开启中,请稍候..",
      src: url,
      visible: true,
      active: true
    });
  }
  if (type === 'ahrefs') {
    //清除浏览器标签
    tabGroup.getTabs().forEach((tab) => {
      tab.close(true)
    })

    // 注入cookie
    let cookie = str_decrypt(currentAccount.encodeToken);

    let result = ipcRenderer.sendSync('insertCookie',
        {name: 'BSSESSID', value: cookie, url: 'https://ahrefs.com'}
    );

    if (result.code === 0) {
      let url = 'https://ahrefs.com/dashboard';
      tabGroup.addTab({
        title: "开启中,请稍候..",
        src: url,
        visible: true,
        active: true
      });
    } else {
      console.log(result);
      alert(result.message);
    }
  }
}

/**
 * 登录
 */
function login() {

  username = $("#username").val();
  password = $("#password").val();
  siteId = parseInt($("#chooseSite").val());

  if (siteId === 0) {
    alert("请选择平台");
    return;
  }
  let data = {username: username, password: password, site_id: siteId, v: 3.3};

  $.post('https://vipfor.me/api/login_v2/', data, function (response) {
    let jsonObj = JSON.parse(response);
    console.log(jsonObj)
    if (jsonObj.code === 200 && jsonObj.data.is_active === true) {

      // layer.msg("账号剩余:" + jsonObj.data.left_day + '天');
      $(".login").hide();
      $(".logout .username").text(username);
      $(".logout .left_day").text(jsonObj.data.left_day);
      $(".logout").show();
      $(".main").show();

      //[ {encodeToken: "nebBkq61l5euxqS9r6Lc262Es6aLio7L1dKmobicoKKcoomjuqyWpA=="} ]
      innerAccountList = jsonObj.data.account_list;
      initInnerAccountList()
    } else {
      alert(jsonObj.message);
    }
  })

}

function logout() {
  $(".login").show();
  $('.browser .cover').show(); //萌版显示
  $(".logout").hide();
  $(".main").hide();
  tabGroup.getTabs().forEach((tab) => {
    tab.close(true)
  })
  if (siteId === 2) {
    //mangools退出链接
    //https://app.kwfinder.com/?login_token&sso_ticket
    tabGroup.addTab({
      title: "退出中..",
      src: "https://app.kwfinder.com/?login_token&sso_ticket",
      visible: true,
      active: true
    });
  }
}
