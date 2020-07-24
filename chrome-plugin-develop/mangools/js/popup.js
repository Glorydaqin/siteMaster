let openTabId = null;

function sendMessageToContentScript(message, callback) {
    chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
        chrome.tabs.sendMessage(tabs[0].id, message, function (response) {
            console.log("send message to tabId:" + tabs[0].id);
            if (callback) callback(response);
        });
    });
}

$(document).ready(function () {
    var bg = chrome.extension.getBackgroundPage();

    function showLogin() {
        $('#mDiv').show();
        $('#mDivb').hide();
        $("#mDivc").hide();
        bg.setPageStatus("login");
    }

    function showAccountList(accountList) {
        //修改字段
        $('#accountList').html('');
        accountList.forEach(function (item, index) {
            $('#accountList').append('<div id="bDiv2" class="divaa loginNum" data-index="' + index + '"><span>登录账号' + (index + 1) + '</span></div>')
        });
        //账号成功则显示 mDivb
        $('#mDiv').hide();
        $('#mDivb').show();
        $("#mDivc").hide();
        bg.setPageStatus("account");
    }

    function showLogout() {
        $('#mDiv').hide();
        $('#mDivb').hide();
        $("#mDivc").show();
        bg.setPageStatus("logout");
    }

    chrome.runtime.sendMessage({type: 'getPageStatus'}, function (pageStatus) {
        console.log(pageStatus);
        if (pageStatus === 'login') {
            $('#mDiv').show();
        } else if (pageStatus === 'logout') {
            $("#mDivc").show();
        } else if (pageStatus === 'account') {
            chrome.runtime.sendMessage({type: 'getAccountList'}, function (accountList) {
                showAccountList(accountList)
            })
        }
    });

    chrome.storage.local.get({username: null, password: null}, function (items) {
        $("#i1").val(items.username);
        $("#i2").val(items.password);
    });

    //登录按钮
    $("#login").on("click", function () {
        // bg.closePlugins();

        let mainfest = chrome.runtime.getManifest();

        //账号登陆
        let username = $("#i1").val();
        let password = $("#i2").val();

        let url = "https://vipfor.me/api/login_v2/";
        let data = {username: username, password: password, site_id: 2, v: mainfest.version};
        let index = layer.load(1, {
            shade: [0.1, '#fff'] //0.1透明度的白色背景
        });
        $.post(url, data, function (response) {
            let jsonObj = JSON.parse(response);
            console.log(jsonObj);

            layer.close(index);
            if (jsonObj.code === 200 && jsonObj.data.is_active === true) {
                chrome.storage.local.set({username: username, password: password}, function () {
                    console.log('本地账号保存成功！');
                    bg.setUser(username, password);
                });
                bg.setAccountList(jsonObj.data.account_list);
                bg.setLastPluginId(jsonObj.data.last_plugin_id);


                chrome.tabs.create({url: 'https://app.kwfinder.com', active: false}, function (tab) {
                    console.log(tab)
                    openTabId = tab.id
                    bg.setOpenTabId(tab.id);
                    showAccountList(jsonObj.data.account_list);
                })

            } else {
                alert(jsonObj.message);
            }
        });
    });

    //选账号登录按钮
    $(document).on('click', '.loginNum', function () {
        bg.setCurrentAccountIndex($(this).attr('data-index'));
        let accountInfo = bg.getCurrentAccount();

        let new_cookie = {
            'url': 'https://mangools.com/',
            "name": "_mangotools_com_session",
            'value': accountInfo.username,
            // 'domain': 'mangools.com',
            'httpOnly': true,
            'secure': true,
            // 'expirationDate': timestamps
        };
        chrome.cookies.set(
            new_cookie, function (cookie) {

                chrome.tabs.query({'currentWindow': true}, function (tabArray) {

                    let allTabIds = []
                    for (var i = 0; i < tabArray.length; i++) {
                        allTabIds.push(tabArray[i].id)
                    }

                    if (allTabIds && allTabIds.indexOf(openTabId) > -1) {
                        chrome.tabs.reload(openTabId, function () {
                            chrome.tabs.update(openTabId, {active: true})
                        });
                    } else {
                        chrome.tabs.create({url: 'https://app.kwfinder.com'})
                    }

                })

                showLogout();
            }
        );
    });

    //登出
    $("#logout").on("click", function () {
        bg.loginInfoClear();

        showLogin();
    });

});
