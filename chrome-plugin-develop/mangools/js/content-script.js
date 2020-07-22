let username = null;
let password = null;
let tabId = null;
let openTabId = null;

// let loginButtonInsert = '<p style="margin-top: 10px;display: block;width: 320px;margin: 0 auto;background: #e4a5a5;">点击Sign in手动登陆</p>'

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
    } else if (request.type === 'addCookie') {
        let new_cookie = {
            'url': 'https://mangools.com/',
            "name": "_mangotools_com_session",
            'value': request.cookie,
            // 'domain': 'mangools.com',
            'httpOnly': true,
            'secure': true,
            // 'expirationDate': timestamps
        };
        chrome.cookies.set(
            new_cookie, function (cookie) {
                console.log('set cookie')
                console.log(cookie);

                sendResponse({result: true, msg: cookie});
            }
        );
    }
});

chrome.runtime.sendMessage({type: 'getTabId'}, function (response) {
    console.log(response);
    tabId = response.currentTabId;
    openTabId = response.openTabId;

    if (openTabId === tabId && response.pageStatus === 'account') {
        //account 页面清理storage
        chrome.tabs.executeScript(null, {code: 'localStorage.clear();'}, function (result) {
            console.log('clear storage result');
            console.log(result)
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {

    //禁用右键（防止右键查看源代码）
    window.oncontextmenu = function () {
        return false;
    };

    var timer = setInterval(function () {
        // if ($(".mg-header>nav>div").css('display') == 'none') {
        //   clearInterval(timer);
        //   return;
        // }
        // $(".mg-header>nav>div").css("cssText", 'display:none !important');

    }, 1000);

});
