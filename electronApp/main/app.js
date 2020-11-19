const {BrowserWindow, app, ipcMain, session} = require('electron');

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

  // Query all cookies.
  session.defaultSession.cookies.get({})
      .then((cookies) => {
        console.log(cookies)
      }).catch((error) => {
    console.log(error)
  })



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

});
