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
var mixFile = ['background.js', 'content-script.js', 'popup.js']


exists(copyDir, targetDir, copy);
// var code = {
//   "file1.js": "function add(first, second) { return first + second; }",
//   "file2.js": "console.log(add(1 + 2, 3 + 4));"
// };
// var options = {
//   toplevel: true,
//   compress: {
//     global_defs: {
//       "@console.log": "alert"
//     },
//     passes: 2
//   },
//   output: {
//     beautify: false,
//     preamble: "/* uglified */"
//   }
// };
// var result = UglifyJS.minify(code, options);
// console.log(result.code);


compressing.zip.compressDir(targetDir, targetDir + "/../ahrefs.zip")
    .then(() => {
      console.log('success');
    })
    .catch(err => {
      console.error(err);
    });
