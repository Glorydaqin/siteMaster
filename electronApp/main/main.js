//const TabGroup = require('electron-tabs') normally but for main :
const TabGroup = require("../index");
const dragula = require("dragula");
const {ipcRenderer} = require('electron')

const siteMap = {1: 'ahrefs', 2: 'mangools'}
let lastPluginId = false;
let siteId = 1;
let innerAccountList = []

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
let userCenter = document.getElementById('user-center');
btnFanhui.onclick = function () {
  if(tabGroup.getActiveTab().webview.canGoBack()) {
    tabGroup.getActiveTab().webview.goBack();
  }
}
btnShuaxin.onclick = function () {
  tabGroup.getActiveTab().webview.reload();
}

btnHide.onclick = function () {
  let styleHide = userCenter.getElementsByTagName('div')[0].style.display;
  userCenter.getElementsByClassName('main')[1].style.display = styleHide === 'none' ? 'block' : 'none';
  btnHide.innerText = styleHide === 'none' ? '显示' : '隐藏';
}

/**
 * 更新账号服务器列表
 */
function initInnerAccountList() {
  $(".account-list").empty();
  let html = "<ul>";
  innerAccountList.forEach(function (item, key) {
    html += "<li><a href=\"#\" onclick='changeInnerAccount(" + key + ")'>账号" + (key + 1) + "</a></li>";
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
    // tabGroup.getTabs().forEach((tab) => {
    //   tab.close(true)
    // })

    // 注入cookie
    let result = ipcRenderer.send('insertCookie', 'ping');


    // 注入cookie
    tabGroup.getActiveTab().webview.openDevTools();
    let cookie = str_decrypt(currentAccount.encodeToken);

    tabGroup.getActiveTab().webview.executeJavaScript(`
    document.cookie = "BSSESSID=` + cookie + `; domain=.ahrefs.com; path=/";
    `, false, function (result) {
      console.log(result)

      // 打开一个ahrefs页面
      var url = 'https://ahrefs.com/dashboard';
      //打开新的标签页
      tabGroup.addTab({
        title: "开启中,请稍候..",
        src: url,
        visible: true,
        active: true
      });
    })

    //'url': 'https://ahrefs.com/',
    //       "name": "BSSESSID",
    //       'value': accountInfo.encodeToken,
    //       'domain': '.ahrefs.com',
    //       'httpOnly': true,
    //       'secure': true,

    // 刷新

  }
}

/**
 * 登录
 */
function login() {

  let username = $("#username").val();
  let password = $("#password").val();
  siteId = $("#chooseSite").val();
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

      //[ {encodeToken: "nebBkq61l5euxqS9r6Lc262Es6aLio7L1dKmobicoKKcoomjuqyWpA=="} ]
      innerAccountList = jsonObj.data.account_list;
      initInnerAccountList()
    } else {
      alert(jsonObj.message);
    }
  })

}

function logout() {

}


function initInnerAccount(type = 'mangools') {

}
