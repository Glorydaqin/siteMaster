//const TabGroup = require('electron-tabs') normally but for main :
const TabGroup = require("../index");
const dragula = require("dragula");
const {ipcRenderer} = require('electron')

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

tabGroup.addTab({
    title: 'baidu',
    src: 'http://baidu.com',
});

tabGroup.addTab({
    title: "Electron",
    src: "https://ie.icoa.cn/",
    visible: true,
    active: true
});

let btnFanhui = document.getElementById('btn-fanhui');
let btnShuaxin = document.getElementById('btn-shuaxin');
let btnHide = document.getElementById("btn-hide");
let userCenter = document.getElementById('user-center');
btnFanhui.onclick = function () {
    // if(tabGroup.getActiveTab().webview.canGoBack()){
    //   tabGroup.getActiveTab().webview.goBack();
    // }


    tabGroup.getActiveTab().webview.openDevTools();
    let cookie = 'TDZWdVpvU0Z0VE5kSURQQ3FDYjNlU3JTVUFWYWk0a212WFVWNCtBQ1Z3M0pXRlB4R1UzVDVCeVhncjh3VmFYQW9yMzZybUphTEM4WjJvSFUrQkVlZHVyMTRuTjdLUWd6STlpZlloeTA2dUFoY1RmNnVEMkMra2xpUHhCMzZzc2x3R3l2dWttUmtWeG5Cc3Z6WHhqR2tudjM4UGRnTXJzTVkzVVpNN1QyT3Q4cFhPSFdJUmhnQnRxWmNvVUFoZGNabmpKbVFwYUEwWGw3SkFJOXRKR1Fpa2dBN2J4b1JzZzg1Y2FiQStBaExwNnVtMEFueEljTDRveElkQnhmajZ0bFFQaUQ5NURublBNQm5LNEZNbVBxNVhNc1E2NGNLZ1p5ejlVbjU0WG1hZ0xLcUJsdWVRdFNjRy9VM1ZZbEZ0a2otLU1MWDAySXJMcUFXcVdWcldsT0REaUE9PQ%3D%3D--5942359398dde41fea56a33d7c709725485cb802';


    // tabGroup.getActiveTab().webview.executeJavaScript(`
    // document.cookie = "_mangotools_com_session='` + cookie + `';httpOnly=true;secure=true;url='https://mangools.com/'";
    // `, false, function (result) {
    //   console.log(result)
    // })
    tabGroup.getActiveTab().webview.executeJavaScript(`
    document.cookie = "_mangotools_com_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
    document.cookie = "_mangotools_com_session=` + cookie + `; expires=Fri, 31 Dec 9999 23:59:59 GMT; domain=mangools.com; path=/";
    `, false, function (result) {
        console.log(result)
    })
    //   // 先用来写cookie
    //   let new_cookie = {
    //     'url': 'https://mangools.com/',
    //     "name": "_mangotools_com_session",
    //     'value': request.cookie,
    //     // 'domain': 'mangools.com',
    //     'httpOnly': true,
    //     'secure': true,
    //     // 'expirationDate': timestamps
    //   };
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
 * 登录
 */
function login() {

    let username = $("#username").val();
    let password = $("#password").val();
    let data = {username: username, password: password, site_id: 2, v: 3.3};

    $.post('https://vipfor.me/api/login_v2/', data, function (response) {
        let jsonObj = JSON.parse(response);
        console.log(jsonObj)
        if (jsonObj.code === 200 && jsonObj.data.is_active === true) {

            // layer.msg("账号剩余:" + jsonObj.data.left_day + '天');

            initInnerAccount('mangools')
        } else {
            alert(jsonObj.message);
        }
    })

}

function logout() {

}


function initInnerAccount(type = 'mangools') {
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

    }
}

/**
 * 切换植入账号
 * @param index
 */
function changeInnerAccount(index = 0) {

}