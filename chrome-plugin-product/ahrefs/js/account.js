// //监听控制访问
//
// //获取剩余量
// function get_limit() {
//
// }
//
// //检查是否达到限制
// function check_limit(url) {
//   //如果是退出或相关页面。强制不请求
//   if (url.indexOf("api-adaptor/authLogout") !== -1) {
//     alert('limit request!');
//
//     return {cancel: true};
//   }
//
//   return {};
// }
//
// //
// //
// //记录请求
// chrome.webRequest.onBeforeRequest.addListener(function (details) {
//       return check_limit(details.url);
//     },
//     {urls: ["*://ahrefs.com/*", "*://auth.ahrefs.com/*"]},
//     ["blocking"]
// );

//修改header user-agent
chrome.webRequest.onBeforeSendHeaders.addListener(function (details) {
      let ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
      for (let i = 0; i < details.requestHeaders.length; ++i) {
        if (details.requestHeaders[i].name === 'User-Agent') {
          details.requestHeaders[i].value = ua;
          break;
        }
      }
      return {requestHeaders: details.requestHeaders};
    },
    {urls: ["*://ahrefs.com/*", "*://auth.ahrefs.com/*"]},
    ["blocking", "requestHeaders"]
);
