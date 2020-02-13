let username = null;
let password = null;

let loginWindowId = null;
let loginTabId = null;


function sendMessageToContentScript(message, callback) {
  chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
    chrome.tabs.sendMessage(tabs[0].id, message, function (response) {
      if (callback) callback(response);
    });
  });
}

chrome.runtime.onMessage.addListener(function (msg, sender, sendResponse) {
      if (!msg.action) {
        msg.action = msg.text
      }
      if (msg.action == "openLogin") {
        // alert(JSON.stringify(msg))
        console.log(msg)
        return msg
      }
    }
);