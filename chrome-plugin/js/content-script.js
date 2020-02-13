let username = null;
let password = null;

chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
  if (request.type === 'data'){
    username = request.data.username;
    password = request.data.password;
    sendResponse('收到account了！');
  }
});

document.addEventListener('DOMContentLoaded', function () {
  console.log('DOMContentLoaded 我被执行了！');

  let url = window.location.href;
  console.log(url);
  // content-script 没得这个权限
  // chrome.runtime.getBackgroundPage(function (backgroundPage) {
  //   console.log('back')
  //   console.log(backgroundPage)
  // })

  if (url.includes("https://mangools.com/users/sign_in") && username && password) {
    //给当前页面写入账号密码

    alert("准备使用account:" + username + "." + password);
  }
});

$(function () {

  // console.log(chrome.runtime.getBackgroundPage)
  // console.log('logwindd:' + logWindowId)
  // console.log("logtabid:" + loginTabId)
  //
  // console.log('in jq 我被执行了！');
});
