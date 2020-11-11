var UglifyJS = require("uglify-js");
var fs = require('fs'),
    stat = fs.stat;
const compressing = require('compressing');
/*
05
 * 复制目录中的所有文件包括子目录
06
 * @param{ String } 需要复制的目录
07
 * @param{ String } 复制到指定的目录
08
 */
var copy = function (src, dst) {
    // 读取目录中的所有文件/目录
    fs.readdir(src, function (err, paths) {
        if (err) {
            throw err;
        }

        paths.forEach(function (path) {
            var _src = src + '/' + path,
                _dst = dst + '/' + path,
                readable, writable;

            stat(_src, function (err, st) {
                if (err) {
                    throw err;
                }

                // 判断是否为文件
                if (st.isFile()) {
                    // 创建读取流
                    readable = fs.createReadStream(_src);
                    // 创建写入流
                    writable = fs.createWriteStream(_dst);
                    // 通过管道来传输流
                    readable.pipe(writable);
                }
                // 如果是目录则递归调用自身
                else if (st.isDirectory()) {
                    exists(_src, _dst, copy);
                }
            });
        });
    });
};
// 在复制目录前需要判断该目录是否存在，不存在需要先创建目录
var exists = function (src, dst, callback) {
    fs.exists(dst, function (exists) {
        // 已存在
        if (exists) {
            callback(src, dst);
        }
        // 不存在
        else {
            fs.mkdir(dst, function () {
                callback(src, dst);
            });
        }
    });
};


// 配置项
var copyDir = './chrome-plugin/chrome-plugin-develop/ahrefs';
var targetDir = './chrome-plugin/chrome-plugin-product/ahrefs';
var mixFile = ['background.js', 'content-script.js', 'popup.js'];


exists(copyDir, targetDir, copy);
var code = [];
mixFile.forEach(function (item) {
    code.push(targetDir + "/js/" + item);
})
var options = {
    warnings: false,
    parse: {
        // parse options
    },
    compress: {
        // compress options
    },
    mangle: false,
    //     {
    //     //  (default true) — 传 false就跳过混淆名字。传对象来指定混淆配置mangle options (详情如下).
    //
    //     properties: {
    //         // mangle property options
    //     }   //(default false) — 传一个对象来自定义混淆属性配置mangle property options.
    // },
    output: null, //要自定义就传个对象来指定额外的 输出配置output options. 默认是压缩到最优化。
    sourceMap: false,   //传一个对象来自定义 sourcemap配置source map options.
    nameCache: null, // 如果你要缓存 minify()多处调用的经混淆的变量名、属性名，就传一个空对象{}或先前用过的nameCache对象。 注意:这是个可读/可写属性。minify()会读取这个对象的nameCache状态，并在最小化过程中更新，以便保留和供用户在外部使用。
    toplevel: false, //如果你要混淆（和干掉没引用的）最高作用域中的变量和函数名，就传true。
    ie8: false, //传 true 来支持 IE8.
}
var result = UglifyJS.minify(code, options);
console.log("compile result:");
console.log(result);


// compressing.zip.compressDir(targetDir, targetDir + "/../ahrefs.zip")
//     .then(() => {
//         console.log('success');
//     })
//     .catch(err => {
//         console.error(err);
//     });
