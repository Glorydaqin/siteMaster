$(document).ready(function () {
  var bg = chrome.extension.getBackgroundPage();

  chrome.storage.local.get({username: null, password: null}, function (items) {
    console.log(items.username, items.password);

    $("#i1").val(items.username);
    $("#i2").val(items.password);
  });

  $("#dDiv1").on("click", function () {
    //账号登陆
    let username = $("#i1").val();
    let password = $("#i2").val();
    chrome.storage.local.set({username: username, password: password}, function () {
      console.log('本地账号保存成功！');
    });

    let url = "https://vipfor.me/api/login/";
    let data = {username: username, password: password};
    $.post(url, data, function (response) {

    });

    //账号成功则显示 mDivb
    $('#mDivb').show();
    $('#mDiv').hide();
  });

  $(".loginNum").on("click", function () {


    chrome.windows.create({
      url: "https://mangools.com/users/sign_in",
      top: 10,
      width: 300,
      height: 300,
      focused: true
    }, function (window) {

      bg.loginWindowId = window.id;
      bg.loginTabId = window.tabs[0].id;

      console.log(bg.loginWindowId);
      console.log(bg.loginWindowId)
    });

  });

});