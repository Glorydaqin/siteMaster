let username = null;
let password = null;

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

document.addEventListener('DOMContentLoaded', function () {

  var style = "<style>" +
      "a[href='/user/logout']{display:none !important}" +
      "a[href='/account/my-account']{display:none !important;}" +
      "[class$='subscriptionMessage']{display:none}" +
      "</style>";
  $(style).insertAfter('head');

  let url = window.location.href;
});
