/**
 * 解密函数
 * @param str 待解密字符串
 * @returns {string}
 */
function str_decrypt(str) {
    // str = decodeURIComponent(str);

    console.log('str decrypt')
    console.log(str)
    str = window.atob(str);
    var c = String.fromCharCode(str.charCodeAt(0) - str.length);

    for (var i = 1; i < str.length; i++) {
        c += String.fromCharCode(str.charCodeAt(i) - c.charCodeAt(i - 1));
    }
    return c;
}
