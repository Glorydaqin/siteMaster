//const TabGroup = require('electron-tabs') normally but for main :
const TabGroup = require("../index");
const dragula = require("dragula");
const {ipcRenderer} = require('electron')
const Store = require('electron-store');
const store = new Store();

const apiHost = 'http://sitemaster.com';
const siteMap = {1: 'ahrefs', 2: 'mangools'}
let lastPluginId = false;
let siteId = 1;
let innerAccountList = [];
let innerAccountId = 1; // 选中的服务账号id
let isLogin = false; // 是否登陆中状态
let username = '';
let password = '';

//剩余量状态相关参数     //alias 别名 urlContain 链接包含 maxHit 最大可以使用次数  leftHit 剩余使用次数
let limitMap = [{
    'alias': '搜索量',
    'urlContain': 'js/layer-v3.1.1/theme/default/loading-2.gif',
    'maxHit': 10,
    'leftHit': 5
}];
let filterExtension = ['.js', '.css', '.png', '.jpg', '.jpeg', '.bmp',
    '.ico', '.svg', '.woff2']

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

// 恢复保存的账号
function rewriteUserInfo() {
    console.log(store.get('vipLoginUserInfo'));
    var res = store.get('vipLoginUserInfo');
    if (res) {
        let username = res.split(',')[0];
        let password = res.split(',')[1];
        $("#username").val(username);
        $("#password").val(password);
    }
}

rewriteUserInfo();

let btnFanhui = document.getElementById('btn-fanhui');
let btnShuaxin = document.getElementById('btn-shuaxin');
let btnHide = document.getElementById("btn-hide");
btnFanhui.onclick = function () {
    if (tabGroup.getActiveTab().webview.canGoBack()) {
        tabGroup.getActiveTab().webview.goBack();
    }
}
btnShuaxin.onclick = function () {
    tabGroup.getActiveTab().setIcon('js/layer-v3.1.1/theme/default/loading-2.gif')
    tabGroup.getActiveTab().webview.reload();
}

btnHide.onclick = function () {
    let uc = $("#user-center");
    uc.toggle();
    // btnHide.innerText = uc.css("display") === 'none' ? '显示' : '隐藏';
    if (uc.css("display") === 'none') {
        btnHide.innerText = '展开面板';
        btnHide.classList.add("light");
    } else {
        btnHide.innerText = '隐藏面板';
        btnHide.classList.remove("light");
    }
}

function getPostCheck() {
    $.post(apiHost + '/api/check_v2/', {
        'last_plugin_id': lastPluginId,
        'site_id': siteId
    }, function (response) {
        console.log(response)
        let jsonObj = JSON.parse(response);
        limitMap = jsonObj.data;
        let result = ipcRenderer.sendSync('saveLimitMap', limitMap);
        console.log(result)

        if (jsonObj.code !== 200) {
            logout();
            alert(jsonObj.message ? jsonObj.message : '账号在其他设备登陆')
        }
    });
}

setInterval(function () {
    //每5分钟检测一次是否是最新设备在线
    //也会检查有效期是否到了
    if (lastPluginId && isLogin) {
        getPostCheck();
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
    innerAccountId = currentAccount['id'];

    //去除蒙版
    $('.browser .cover').hide();
    // 隐藏面板
    let uc = $("#user-center");
    uc.toggle();
    btnHide.innerText = '展开面板';
    btnHide.classList.add("light");

    if (type === 'mangools') {
        //清除浏览器标签
        tabGroup.getTabs().forEach((tab) => {
            tab.close(true)
        })

        var urls = ['app.serpchecker.com', 'app.serpwatcher.com', 'app.linkminer.com', 'app.siteprofiler.com', 'app.kwfinder.com'];

        urls.forEach(function (url) {
            //打开新的标签页
            tabGroup.addTab({
                title: "开启中,请稍候..",
                src: "https://" + url + '/?' + str_decrypt(currentAccount.encodeToken),
                iconURL: 'js/layer-v3.1.1/theme/default/loading-2.gif',
                visible: true,
                active: true
            });
        })
        // var url = 'https://app.kwfinder.com/?' + currentAccount.encodeToken;
    }
    if (type === 'ahrefs') {
        //清除浏览器标签
        tabGroup.getTabs().forEach((tab) => {
            tab.close(true)
        })

        // 注入cookie
        let cookie = str_decrypt(currentAccount.encodeToken);

        let clearResult = ipcRenderer.sendSync('clearCookie');

        let result = ipcRenderer.sendSync('insertCookie',
            {name: 'BSSESSID', value: cookie, url: 'https://ahrefs.com'}
        );

        if (clearResult.code === 0 && result.code === 0) {
            let url = 'https://ahrefs.com/dashboard';
            tabGroup.addTab({
                title: "开启中,请稍候..",
                src: url,
                iconURL: 'js/layer-v3.1.1/theme/default/loading-2.gif',
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
    let data = {username: username, password: password, site_id: siteId, v: 3.4};

    layer.load(1, {
        shade: [0.2, '#fff'] //0.1透明度的白色背景
    });

    $.post(apiHost + '/api/login_v3/', data, function (response) {
        let jsonObj = JSON.parse(response);
        console.log(jsonObj)
        layer.closeAll('loading');

        if (jsonObj.code === 200 && jsonObj.data.is_active === true) {
            //记录账号密码
            store.set('vipLoginUserInfo', username + ',' + password)

            isLogin = true;
            // layer.msg("账号剩余:" + jsonObj.data.left_day + '天');
            $(".login").hide();
            $(".logout .username").text(username);
            $(".logout .left_day").text(jsonObj.data.left_day);
            $(".logout").show();
            $(".main").show();

            //[ {encodeToken: "nebBkq61l5euxqS9r6Lc262Es6aLio7L1dKmobicoKKcoomjuqyWpA=="} ]
            innerAccountList = jsonObj.data.account_list;
            lastPluginId = jsonObj.data.last_plugin_id;
            initInnerAccountList()

            getPostCheck();
        } else {
            alert(jsonObj.message);
        }
    })

}

function logout() {
    isLogin = false;
    lastPluginId = false;
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

function openWithBrowser(url) {
    ipcRenderer.send('openUrlWithBrowser', url);
}

//根据url计算剩余量
function doLimit(urls) {
    let isChange = false;
    limitMap.forEach(function (item, key) {
        if (urls.lastIndexOf(item.urlContain) > 0) {
            limitMap[key].leftHit = item.leftHit - 1;
            isChange = true;
        }
    })
    if (isChange) {
        let result = ipcRenderer.sendSync('saveLimitMap', limitMap);
        console.log(result)
    }
}


function recordVisit(url) {
    console.log(url)
    //对于剩余量实时计算
    doLimit(url)

    //上报服务器
    $.post(apiHost + "/api/record/",
        {last_plugin_id: lastPluginId, url: url, account_id: innerAccountId},
        function (response) {
            console.log('post response')
            console.log((response))
        });
}

ipcRenderer.on('recordVisit', (event, data) => {
    console.log(data) // Prints 'whoooooooh!'
    //{
    //   id: 316,
    //   url: 'https://cdn.ahrefs.com/app/tools/js/vendors~admin-support~content-explorer~dashboard~rank-tracker~site-explorer.ec1d3aa6.chunk.js',
    //   method: 'GET',
    //   timestamp: 1608130767846.8882,
    //   resourceType: 'script',
    //   ip: '127.0.0.1',
    //   fromCache: false,
    //   statusLine: 'HTTP/1.1 200',
    //   statusCode: 200,
    //   webContentsId: 3,
    //   referrer: 'https://ahrefs.com/dashboard'
    // }

    var extension = null;
    var url = data.url;
    var search = url.indexOf("?"); //去除?后面的
    if (search > 0) {
        url = url.substr(0, search);
    }
    var extensionPosition = url.lastIndexOf('.');//拿到最后一个'.'
    var filterPosition = url.lastIndexOf('/');//最后一个'/'位置
    if (extensionPosition > 0 && filterPosition < extensionPosition) {
        extension = url.substr(extensionPosition);
    }

    if (filterExtension.includes(extension)) {
        //包含在需要过滤的数组中
        return;
    }

    recordVisit(data.url)
})
