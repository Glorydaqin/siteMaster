const {BrowserWindow, app, ipcMain, shell, session, Menu} = require('electron');
const os = require("os");
const isWin7 = os.release().startsWith('6.1')
// win7 下关闭硬件加速 || 解决win7白屏问题 || 待验证
if (isWin7) {
    app.disableHardwareAcceleration()
}

app.on('ready', function () {

    const mainWindow = new BrowserWindow({
        width: 1200,
        height: 800,
        show: false,
        webPreferences: {
            nodeIntegration: true,
            webviewTag: true,
            webSecurity: false
        }
    });

    if (process.platform === 'darwin') {
        const template = [
            {
                label: "Application",
                submenu: [
                    {
                        label: "Quit", accelerator: "Command+Q", click: function () {
                            app.quit();
                        }
                    }
                ]
            },
            {
                label: "Edit",
                submenu: [
                    {label: "Copy", accelerator: "CmdOrCtrl+C", selector: "copy:"},
                    {label: "Paste", accelerator: "CmdOrCtrl+V", selector: "paste:"},
                ]
            }
        ];
        Menu.setApplicationMenu(Menu.buildFromTemplate(template))
    } else {
        Menu.setApplicationMenu(Menu.buildFromTemplate([]))
    }


    let limitMap = [];

    // 打开开发工具
    mainWindow.openDevTools();

    mainWindow.loadURL('file://' + __dirname + '/main.html');
    mainWindow.on('ready-to-show', function () {
        mainWindow.show();
        mainWindow.focus();
    });

    ipcMain.on('insertCookie', function (event, cookie) {
        console.log(cookie);  // prints "ping"

        // 修改cookie 。 存在的会覆盖
        // let cookie = { url: 'http://www.github.com', name: 'dummy_name', value: 'dummy' }
        session.defaultSession.cookies.set(cookie)
            .then(() => {
                // success
                event.returnValue = {code: 0, message: 'success'}; // 同步回复
            }, (error) => {
                console.error(error)

                event.returnValue = {code: 1, message: error}; // 同步回复
            })
    });

    ipcMain.on('saveLimitMap', function (event, data) {
        console.log(data);  // prints "ping"

        limitMap = data;
        event.returnValue = {code: 0, message: 'save limit success'}; // 同步回复
    });

    ipcMain.on('clearCookie', function (event) {
        session.defaultSession.cookies.get({})
            .then((cookies) => {
                cookies.forEach(cookie => {
                    let url = '';
                    // get prefix, like https://www.
                    url += cookie.secure ? 'https://' : 'http://';
                    url += cookie.domain.charAt(0) === '.' ? 'www' : '';
                    // append domain and path
                    url += cookie.domain;
                    url += cookie.path;
                    session.defaultSession.cookies.remove(url, cookie.name, (error) => {
                        if (error) {
                            console.log(`error removing cookie ${cookie.name}`, error);
                        }
                    });
                })
            }).catch((error) => {
            console.log(error)
            event.returnValue = {code: 1, message: error}; // 同步回复
        })
        event.returnValue = {code: 0, message: 'success'}; // 同步回复
    });

    ipcMain.on('clearSession', function (event) {
        // console.log(sessionOption);  // prints "ping"

        session.clearStorageData()
            .then(() => {
                // success
                event.returnValue = {code: 0, message: 'success'}; // 同步回复
            }, (error) => {
                console.error(error)
                event.returnValue = {code: 1, message: error}; // 同步回复
            })
    });

    ipcMain.on('openUrlWithBrowser', (event, url) => {
        shell.openExternal(url);
    });

    ipcMain.on('appAction', function (event, type) {
        if (type === 'relaunch') {
            app.relaunch();
        }
        if (type === 'quit') {
            app.quit();
        }
    });

    ipcMain.on('openUrlWithBrowser', (event, url) => {
        shell.openExternal(url);
    });

    // // Modify the user agent for all requests to the following urls.
    const filter = {
        urls: [
            '*://*.mangools.com/*',
            '*://*.kwfinder.com/*',
            '*://*.serpchecker.com/*',
            '*://*.serpwatcher.com/*',
            '*://*.linkminer.com/*',
            '*://*.siteprofiler.com/*',
            '*://*.ahrefs.com/*'
        ]
    }

    session.defaultSession.webRequest.onBeforeRequest(filter, (details, callback) => {
        // 请求前拦截
        let isLeft = true;
        for (var i = 0; i < limitMap.length; i++) {
            if (details.url.lastIndexOf(limitMap[i].urlContain) > 0 && limitMap[i].leftHit <= 0) {
                isLeft = false;
            }
        }
        if (isLeft) {
            console.log('access true');
            callback({cancel: false})
        } else {
            console.log('access fail');
            callback({cancel: true})
        }
    })
    session.defaultSession.webRequest.onResponseStarted(filter, (details) => {
        // 接收响应时记录
        // console.log(details.url)
        mainWindow.webContents.send('recordVisit', details)
    })

});
