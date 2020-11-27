// 禁止页面刷新
window.onkeydown = function (e) {
    var ev = window.event || e;
    var code = ev.keyCode || ev.which;
    if (code === 82 && (ev.metaKey || ev.ctrlKey)) {
        console.log('flush def')
        return false;
    }
}

//根据不同网站执行不同代码
var web_host = window.location.host;
// 去除二级域名
web_host = web_host.replace('www.', '');
web_host = web_host.replace('app.', '');

var ahrefs_host = ['ahrefs.com'];
var mangools_host = [
    'kwfinder.com',
    'serpchecker.com',
    'serpwatcher.com',
    'linkminer.com',
    'siteprofiler.com',
    'mangools.com'
];
if (ahrefs_host.indexOf(web_host) >= 0) {
    //ahrefs
    console.log('ahrefs');

    document.addEventListener('DOMContentLoaded', function () {
        var head = document.head;
        // 创建 style 元素
        var styleElement = document.createElement('style');
        styleElement.innerHTML =
            "a[href='/user/logout']{display:none !important}" +
            "a[href='/account/my-account']{display:none !important;}" +
            "[class$='subscriptionMessage']{display:none}";
        head.append(styleElement)
    });

} else if (mangools_host.indexOf(web_host) >= 0) {
    //mangools
    console.log('mangools')

    document.addEventListener('DOMContentLoaded', function () {
        var head = document.head;
        // 创建 style 元素
        var styleElement = document.createElement('style');

        //在创建好的style元素中，写上CSS
        styleElement.innerHTML = "button.uk-padding-remove{display:none;}a[href^='https://mangools.com/']{display:none;}";
        //在head 中加上 style 元素
        head.append(styleElement);


        // var timer = setInterval(function () {
        //   if ($(".mg-header>nav>div").css('display') == 'none') {
        //     clearInterval(timer);
        //     return;
        //   }
        //   $(".mg-header>nav>div").css("cssText", 'display:none !important');
        // }, 100);
    });
} else {
    // alert("禁止访问此地址");

}
