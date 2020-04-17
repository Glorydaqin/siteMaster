let username = null;
let password = null;
let tabId = null;
let loginTabId = null;

function mockClick(element) {
  let dispatchMouseEvent = function (target, var_args) {
    console.log('action:' + var_args);
    let e = document.createEvent("MouseEvents");
    e.initEvent.apply(e, Array.prototype.slice.call(arguments, 1));
    target.dispatchEvent(e);
  };
  if (element) {
    dispatchMouseEvent(element, 'mouseover', true, true);
    dispatchMouseEvent(element, 'mousedown', true, true);
    dispatchMouseEvent(element, 'click', true, true);
    dispatchMouseEvent(element, 'mouseup', true, true);
  }
}

// 模拟点击
function simulateClick(dom, mouseEvent) {
  let domNode = dom.get(0)
  console.log('simulateClick', dom, mouseEvent)
  if (mouseEvent && domNode) {
    return mockClick(domNode)
  }
  try {
    domNode.trigger("tap")
    domNode.trigger("click")
  } catch (error) {
    try {
      mockClick(domNode)
    } catch (err) {
      console.log('fullback to mockClick', err)
    }
  }
}


chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
  if (request.type === 'data') {
    username = request.data.username;
    password = request.data.password;
    sendResponse('收到account了！');
  }
});
chrome.runtime.sendMessage({type: 'getTabId'}, function (response) {
  console.log(response);
  tabId = response.currentTabId;
  loginTabId = response.loginTabId;
});
// //登陆状态重置代码
// chrome.runtime.sendMessage({type: 'checkLogin'}, function (response) {
// });

document.addEventListener('DOMContentLoaded', function () {

  // //禁用右键（防止右键查看源代码）
  // window.oncontextmenu = function () {
  //   return false;
  // };
  // //禁止任何键盘敲击事件（防止F12和shift+ctrl+i调起开发者工具）
  // window.onkeydown = window.onkeyup = window.onkeypress = function () {
  //   window.event.returnValue = false;
  //   return false;
  // };

  // var timer = setInterval(function () {
  //   if ($("a[href='/user/logout']").css('display') == 'none') {
  //     clearInterval(timer);
  //     return;
  //   }
  //   $("#userMenuDropdown").css("cssText", 'display:none !important');
  // }, 1000);

  var style = "<style>" +
      "a[href='/user/logout']{display:none !important}" +
      "a[href='/account/my-account']{display:none !important;}" +
      "[class$='subscriptionMessage']{display:none}" +
      "</style>";
  $(style).insertAfter('head');

  let url = window.location.href;
});
