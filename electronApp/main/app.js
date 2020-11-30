const {BrowserWindow, app, ipcMain,shell, session,Menu} = require('electron');

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
  // mainWindow.openDevTools();

  mainWindow.loadURL('file://' + __dirname + '/main.html');
  mainWindow.on('ready-to-show', function () {
    mainWindow.show();
    mainWindow.focus();
  });


  ipcMain.on('insertCookie', function(event, cookie) {
    console.log(cookie);  // prints "ping"

    // 修改cookie 。 存在的会覆盖
    // let cookie = { url: 'http://www.github.com', name: 'dummy_name', value: 'dummy' }
    session.defaultSession.cookies.set(cookie)
        .then(() => {
          // success
          event.returnValue = {code: 0, message:'success'}; // 同步回复
        }, (error) => {
          console.error(error)

          event.returnValue = {code: 1, message:error}; // 同步回复
        })
  });

  ipcMain.on('clearSession', function(event) {
    // console.log(sessionOption);  // prints "ping"

    session.clearStorageData()
        .then(() => {
          // success
          event.returnValue = {code: 0, message:'success'}; // 同步回复
        }, (error) => {
          console.error(error)
          event.returnValue = {code: 1, message:error}; // 同步回复
        })
  });


  ipcMain.on('openUrlWithBrowser', (event, url) => {
    shell.openExternal(url);
  });

});
