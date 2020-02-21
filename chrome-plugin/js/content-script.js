let username = null;
let password = null;
let tabId = null;
let loginTabId = null;
let loginButtonInsert = '<p style="margin-top: 10px;display: block;width: 320px;margin: 0 auto;background: #e4a5a5;">点击Sign in手动登陆</p>'

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

document.addEventListener('DOMContentLoaded', function () {

  //禁用右键（防止右键查看源代码）
  window.oncontextmenu = function () {
    return false;
  };
  //禁止任何键盘敲击事件（防止F12和shift+ctrl+i调起开发者工具）
  window.onkeydown = window.onkeyup = window.onkeypress = function () {
    window.event.returnValue = false;
    return false;
  };
  //如果用户在工具栏调起开发者工具，那么判断浏览器的可视高度和可视宽度是否有改变，如有改变则关闭本页面
  var h = window.innerHeight, w = window.innerWidth;
  window.onresize = function () {
    if (h != window.innerHeight || w != window.innerWidth) {
      window.close();
      window.location = "about:blank";
    }
  };

  console.log('DOMContentLoaded 我被执行了！');

  let url = window.location.href;

  if (url.includes("https://mangools.com/users/sign_in")) {
    if (tabId === loginTabId) {
      //给当前页面写入账号密码
      chrome.runtime.sendMessage({type: 'getCurrentAccount'}, function (response) {
        console.log('拿到账号');
        console.log(response);

        setTimeout(function () {

          $("#user_email").val(response.username).attr("readonly", "readonly");
          $("#user_password").val(response.password).attr("readonly", "readonly");

          //注入点击按钮
          $(loginButtonInsert).insertAfter('.uk-text-center button');
        }, 500);

        setTimeout(function () {
          // simulateClick($(".mg-btn"), true);
        }, 3000)
      });

      //注入让用户主动点击提醒按钮
    } else {
      console.log('用户自主打开登陆页面')
    }

  }
});