const {BrowserWindow, app, ipcMain, shell, session, Menu} = require('electron');

app.on('ready', function () {
    // let isLogin = false;
    Menu.setApplicationMenu(Menu.buildFromTemplate([]))

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


    // // Modify the user agent for all requests to the following urls.
    const filter = {
        urls: ['*://app.kwfinder.com/*',
            '*://app.serpchecker.com/*',
            '*://*.ahrefs.com/*']
    }
    // session.defaultSession.webRequest.onBeforeRequest(filter, (details, callback) => {
    //     // 请求前拦截
    //
    //     console.log(details)
    //
    //     if (details.resourceType == 'image') {
    //         callback({cancel: true})
    //     } else {
    //         callback({cancel: false})
    //     }
    //
    //
    // })
    session.defaultSession.webRequest.onResponseStarted(filter, (details) => {
        // 接收响应时记录
        console.log(details)
        mainWindow.webContents.send('recordVisit', details)
    })

});
