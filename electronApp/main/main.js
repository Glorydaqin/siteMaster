//const TabGroup = require('electron-tabs') normally but for main :
const TabGroup = require("../index");
const dragula = require("dragula");
const {ipcRenderer} = require('electron')
const Store = require('electron-store');
const store = new Store();

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
    if(uc.css("display") === 'none'){
        btnHide.innerText = '展开面板';
        btnHide.classList.add("light");
    }else{
        btnHide.innerText = '隐藏面板';
        btnHide.classList.remove("light");
    }
}

setInterval(function () {
    //每5分钟检测一次是否是最新设备在线
    //也会检查有效期是否到了
    if (lastPluginId && isLogin) {

        $.post('https://vtool.club/api/check/', {
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
                src: "https://" + url + '/?' + currentAccount.encodeToken,
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

        let result = ipcRenderer.sendSync('insertCookie',
            {name: 'BSSESSID', value: cookie, url: 'https://ahrefs.com'}
        );

        if (result.code === 0) {
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
    let data = {username: username, password: password, site_id: siteId, v: 3.3};

    layer.load(1, {
        shade: [0.2, '#fff'] //0.1透明度的白色背景
    });

    $.post('https://vtool.club/api/login_v3/', data, function (response) {
        let jsonObj = JSON.parse(response);
        console.log(jsonObj)
        layer.closeAll('loading');

        if (jsonObj.code === 200 && jsonObj.data.is_active === true) {
            //记录账号密码
            store.set('vipLoginUserInfo',username + ',' + password)

            isLogin = true;
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
    isLogin = false;
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
