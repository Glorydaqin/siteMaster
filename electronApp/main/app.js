const {BrowserWindow, app, ipcMain} = require('electron');

app.on('ready', function () {
    // let isLogin = false;

    const mainWindow = new BrowserWindow({
        width: 800,
        height: 600,
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

    // ipcMain.on('message', function (event, arg) {
    //     console.log(event);
    //     console.log(arg);
    //
    //     ipcMain.reply('reply', 'afsdfa');
    // });
});
