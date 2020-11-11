const {electron, app, ipcMain} = require('electron');

app.on('ready', function () {
  const mainWindow = new electron.BrowserWindow({
    width: 1600,
    height: 1200,
    webPreferences: {
      nodeIntegration: true,
      webviewTag: true,
      webSecurity: false
    }
  });
  // 打开开发工具
  mainWindow.openDevTools();

  mainWindow.loadURL('file://' + __dirname + '/electron-tabs.html');
  mainWindow.on('ready-to-show', function () {
    mainWindow.show();
    mainWindow.focus();
  });

  ipcMain.on('message', function (event, arg) {
    console.log(event);
    console.log(arg);

    ipcMain.reply('reply', 'afsdfa');
  });
});
