let loginWindowId = null;
let loginTabId = null;
let openTabId = null;
let accountList = [];   //所有账号列表
let currentAccountIndex = 0;  //当前选的账号index
let closedPlugins = []; //关闭的所有扩展
let pluginId = chrome.runtime.id; //当前扩展id
let pageStatus = 'login'; // login  account  logout
let lastPluginId = null;
let username = null;
let password = null;


function setUser(user, pass) {
    username = user;
    password = pass;
}

function setPageStatus(status) {
    pageStatus = status;
}

function setLastPluginId(id) {
    lastPluginId = id;
}

function setLoginWindowId(val) {
    loginWindowId = val;
}

function setLoginTabId(val) {
    loginTabId = val;
}

function setOpenTabId(val) {
    openTabId = val;
}

function setAccountList(val) {
    accountList = val;
}

function setCurrentAccountIndex(val) {
    currentAccountIndex = val;
}

function getCurrentAccount() {
    let item = accountList[currentAccountIndex];
    console.log(item)
    return ({
        encodeToken: str_decrypt(item.encodeToken),
        accountList: accountList
    });
}


/**
 * 解密函数
 * @param str 待解密字符串
 * @returns {string}
 */
function str_decrypt(str) {
    // str = base64decode(str);
    str = window.atob(str);
    var c = String.fromCharCode(str.charCodeAt(0) - str.length);

    for (var i = 1; i < str.length; i++) {
        c += String.fromCharCode(str.charCodeAt(i) - c.charCodeAt(i - 1));
    }
    return c;
}

chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {

    if (request.type === 'getCurrentAccount') {
        let item = accountList[currentAccountIndex];
        sendResponse({
            encodeToken: str_decrypt(item.encodeToken),
            accountList: accountList
        });
    } else if (request.type === 'getTabId') {
        sendResponse({currentTabId: sender.tab.id, openTabId: openTabId});
    } else if (request.type === 'getPageStatus') {
        sendResponse(pageStatus);
    } else if (request.type === 'getAccountList') {
        sendResponse(accountList);
    } else if (request.type === 'checkLogin') {
        //检查是否已经登陆了插件，没有则clear
        if (pageStatus !== 'logout') {
            this.loginInfoClear();
        }
        sendResponse({});
    } else {
        console.log(request, sender)
    }
});


function sendMessageToContentScript(message, callback) {
    chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
        chrome.tabs.sendMessage(tabs[0].id, message, function (response) {
            if (callback) callback(response);
        });
    });
}

function clearCookie(domain) {
    chrome.cookies.getAll({domain: domain}, function (cookies) {
        $.each(cookies, function (index, val) {
            chrome.cookies.remove({url: "https://" + val.domain, name: val.name})
        })
    })
}

function clearStorage() {
    chrome.tabs.query({'currentWindow': true}, function (tabArray) {

        for (var i = 0; i < tabArray.length; i++) {
            let tabInfo = tabArray[i];
            if (tabInfo.url.includes('app.kwfinder.com')) {
                chrome.tabs.executeScript(
                    tabArray[i].id,
                    {code: 'localStorage.clear();'}
                );
            }
        }
    })
}

/**
 * 清除cookie 和相关登录信息
 */
function loginInfoClear() {
    clearCookie('mangools.com');
    clearCookie('kwfinder.com');
    clearCookie('app.kwfinder.com');
    clearStorage()
}

setInterval(function () {
    //每分钟检测一次是否是最新设备在线
    if (lastPluginId && pageStatus !== 'login' && username) {

        $.post('https://vipfor.me/api/check/', {
            'username': username,
            'password': password,
            'last_plugin_id': lastPluginId
        }, function (response) {
            let jsonObj = JSON.parse(response);
            console.log(jsonObj);

            if (jsonObj.code !== 200) {
                loginInfoClear();
                pageStatus = 'login';
            }
        });
    }

}, 60000);

chrome.tabs.onUpdated.addListener(function (tabId, changeInfo, tab) {

    //当account状态时清除storage
    if (tabId === openTabId && pageStatus === 'account' && tab.status === 'complete') {

        chrome.tabs.executeScript(tab.id, {code: 'localStorage.clear();'}, function (result) {
            console.log('clear storage result');
            console.log(result)
        });
        loginInfoClear()
    }
})