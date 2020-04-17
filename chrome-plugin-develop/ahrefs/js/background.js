let loginWindowId = null;
let loginTabId = null;
let accountList = [];   //所有账号列表
let currentAccountIndex = 0;  //当前选的账号index
let closedPlugins = []; //关闭的所有扩展
let pluginId = chrome.runtime.id; //当前扩展id
let todayFirst = true;
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

function setAccountList(val) {
  accountList = val;
}

function setCurrentAccountIndex(val) {
  currentAccountIndex = val;
}

function getCurrentAccount() {
  let item = accountList[currentAccountIndex];
  return ({
    username: item.username,
    password: str_decrypt(item.password),
    type: item.type,
    accountList: accountList
  });
}

/**
 * 解密函数
 * @param str 待解密字符串
 * @returns {string}
 */
function str_decrypt(str) {
  // str = decodeURIComponent(str);
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
      username: item.username,
      password: str_decrypt(item.password),
      type: item.type,
      accountList: accountList
    });
  } else if (request.type === 'getTabId') {
    sendResponse({currentTabId: sender.tab.id, loginTabId: loginTabId});
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

function closePlugins() {
  chrome.management.getAll(function (allPlugins) {
    $.each(allPlugins, function (index, val) {
      if (val.enabled === true && val.id !== pluginId) {
        chrome.management.setEnabled(val.id, false);
        savePluginInfo(val.id)
      }
    });
  });
}

function revertPlugins() {
  $.each(closedPlugins, function (index, val) {
    chrome.management.setEnabled(val, true, function () {
      closedPlugins.splice(index, 1)
    });
  });
}

function savePluginInfo(pluginId) {
  closedPlugins.push(pluginId);
}

function closeOpenWindow() {
  if (loginWindowId) {
    chrome.windows.remove(loginWindowId)
  }
}

function clearCookie(domain) {
  chrome.cookies.getAll({domain: domain}, function (cookies) {
    $.each(cookies, function (index, val) {
      chrome.cookies.remove({url: "https://" + val.domain, name: val.name})
    })
  })
}

/**
 * 清除cookie 和相关登录信息
 */
function loginInfoClear() {
  clearCookie('ahrefs.com');
}

setInterval(function () {

  //定时检查扩展,避免禁用的被用户主动启动
  chrome.management.getAll(function (allPlugins) {
    $.each(allPlugins, function (index, val) {
      if (val.enabled === true && val.id !== pluginId && val.id in closedPlugins) {
        chrome.management.setEnabled(val.id, false);
      }
    });
  });

  //定时检查mangools相关页面是否存在
  //pageStatus !== login 并且页面3分钟内存在mangool页面 给后台传递在线信息 ，后台返回上一次登录设备，确保单一设备登录

}, 3000);

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

}, 5000);

//安装时触发
chrome.runtime.onInstalled.addListener({
  //清除 cookie storage
});

//卸载时触发
chrome.runtime.onSuspend.addListener(function () {
  this.loginInfoClear();
});

chrome.windows.onRemoved.addListener(function (windowId) {
  if (windowId === loginWindowId) {
    loginWindowId = null;
  }
});
