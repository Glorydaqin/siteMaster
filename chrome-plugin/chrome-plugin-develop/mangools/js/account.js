chrome.webRequest.onBeforeSendHeaders.addListener(function (details) {
        let ua = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36'
        for (let i = 0; i < details.requestHeaders.length; ++i) {
            if (details.requestHeaders[i].name === 'User-Agent') {
                details.requestHeaders[i].value = ua;
                break;
            }
        }
        return {requestHeaders: details.requestHeaders};
    },
    {
        urls: [
            "*://*.kwfinder.com/*",
            "*://*.serpchecker.com/*",
            "*://*.serpwatcher.com/*",
            "*://*.linkminer.com/*",
            "*://*.siteprofiler.com/*",
            "*://mangools.com/*",
            "*://*.mangools.com/*"
        ]
    },
    ["blocking", "requestHeaders"]
);

// chrome.webRequest.onCompleted.addListener(function (detail) {
//         console.log(detail)
//
//     }, {
//         urls: ["https://api.mangools.com/v3/kwfinder/limits"]
//         // urls: ["<all_urls>"]
//     },
//     [ "responseHeaders"]
// );