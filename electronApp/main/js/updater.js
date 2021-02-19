const os = require("os");
const cp = window.cp;
var https = require('https');
var fs = require('fs');
console.log(packageInfo)
const {shell} = require('electron');

// 检查更新
var isDownload = false;
var latestVersion = [];
var downloadInfo = {
    percent: 0,
    downloadBytes: 0,
    totalBytes: 1,
};
var percent = 0;

$.ajax({
    type: "GET",//使用get请求请求正确
    url: apiHost + "/app/version.json",
    dataType: "json",
    cache: false,
    //data:
    success: function (data) {
        console.log("data: " + JSON.stringify(data))

        if (appVersion !== data.version) {
            // 版号不一致. 弹出更新弹框
            $(".version").text('v' + data.version)
            latestVersion = data['version-list'][0];
            // upgradeInfo = data['version-list'][0].info;

            $(".upgrade-box .loading").hide();
            $(".upgrade-box .upgrade").show();
        } else {
            $(".upgrade-box .loading").text('已是最新版本');
            setTimeout(function () {
                $(".upgrade-box").hide();
            }, 1500)
        }
    },
    error: function () {
        $(".upgrade-box .loading").text('检查更新失败');
        setTimeout(function () {
            $(".upgrade-box").hide();
        }, 1500)
    }
})

function openUpgradeInfo() {
    parent.layer.alert(latestVersion.info.join("</br>"), {
        skin: 'layui-layer-molv', //样式类名
        // anim: 5,
        // closeBtn:1,
        // title:'信息',
    });
}

// function downloadFile(file_url, targetPath) {
//     // Save variable to know progress
//     var received_bytes = 0;
//     var total_bytes = 0;
//
//     var req = request({
//         method: 'GET', uri: file_url
//     });
//
//     var out = fs.createWriteStream(targetPath);
//     req.pipe(out);
//
//     req.on('response', function (data) {
//         // Change the total bytes value to get progress later.
//         total_bytes = parseInt(data.headers['content-length']);
//     });
//
//     req.on('data', function (chunk) {
//         // Update the received bytes
//         received_bytes += chunk.length;
//
//         showProgress(received_bytes, total_bytes);
//     });
//
//     req.on('end', function () {
//         alert("File succesfully downloaded");
//     });
// }
//
// function showProgress(received, total) {
//     var percentage = (received * 100) / total;
//     console.log(percentage + "% | " + received + " bytes out of " + total + " bytes.");
// }

var download = function (url, dest, cb) {
    var file = fs.createWriteStream(dest);
    var request = https.get(url, function (response) {
        downloadInfo.totalBytes = response.headers['content-length'];

        response.pipe(file);
        file.on('finish', function () {
            file.close(cb);  // close() is async, call cb after close completes.
        });

        let rawData = '';
        response.on('data', (chunk) => {
            rawData += chunk;

            downloadInfo.downloadBytes = rawData.length;
            console.log('total percent:' + downloadInfo.downloadBytes / downloadInfo.totalBytes)
            $(".upgrade-box .process").width(Math.floor(downloadInfo.downloadBytes / downloadInfo.totalBytes * 100).toFixed(2) + '%')
        });
        response.on('end', () => {
            percent = 1;
            downloadInfo.downloadBytes = downloadInfo.totalBytes;
            console.log('total percent:1', dest)
            $(".upgrade-box .process").width('100%')
        });

    }).on('error', function (err) { // Handle errors
        fs.unlink(dest); // Delete the file async. (But we don't check the result)
        if (cb) cb(err.message);
    });
};

$('#upgrade-btn').click(function () {
    if (isDownload) {
        return;
    }
    isDownload = true;

    var fileUrl = (os.platform() === 'darwin') ? latestVersion['file-mac'] : latestVersion['file-win'];
    var tmpFile = fileUrl.split("/")[fileUrl.split("/").length - 1];
    var savePath = os.tmpdir() + '/' + Math.random() + tmpFile;
    console.log(fileUrl);
    console.log(savePath);

    // var url = 'https://vtool.club/app/vip-login.exe';
    console.info(appName, appVersion);

    download(fileUrl, savePath, function (result) {

        //下载完成或者异常
        setTimeout(function () {
            if (os.platform() === 'darwin') {
                console.info(appName, appVersion);
                // 挂载
                cp.execSync(`hdiutil attach '${savePath}' -nobrowse`, {
                    stdio: ['ignore', 'ignore', 'ignore']
                });

                // 覆盖原 app
                cp.execSync(`rm -rf '/Applications/${appName}.app' && cp -R '/Volumes/${appName} ${appVersion}/${appName}.app' '/Applications/${appName}.app'`);

                // 卸载挂载的 dmg
                cp.execSync(`hdiutil eject '/Volumes/${appName} ${appVersion}'`, {
                    stdio: ['ignore', 'ignore', 'ignore']
                });

                // 重启
                let result = ipcRenderer.sendSync('appAction', 'relaunch');
                result = ipcRenderer.sendSync('appAction', 'quit');
            }

            if (os.platform() === 'win32') {
                shell.openItem(savePath);
                setTimeout(function () {
                    result = ipcRenderer.sendSync('appAction', 'quit');
                }, 1500)
            }
        }, 2000)

    });

})

function upgradeCancel() {
    // $('.upgrade-box').hide();
    var result = ipcRenderer.sendSync('appAction', 'quit');
}
