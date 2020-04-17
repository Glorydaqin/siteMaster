var validityDay = 7;

chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
  if (request.cmd == 'AHREFS') {
    window.location.reload();
  }
});

chrome.runtime.sendMessage({
  cmd: 'remote.js'
}, function(response) {
  sessionStorage.res = response
});

$(function() {
  var token = sessionStorage.res;
  token = (token != null && token.length > 10) ? (JSON.parse(token)).loginNum : null;

  var ur = "https://www.xixuanseo.com/s/login.php";

  setInterval(function() {

    if (url.includes("ahrefs.com/account/billing/subscriptions")) {
      $("body").css("display", "none");
    }

    let a, b;
    a = $(".dropdown-item.fixed-link");
    for (let i = 0; i < a.length; i++) {
      if (a.eq(i).text() == "Batch analysis" || a.eq(i).text() == "批次分析" || a.eq(i).text() == "バッチ分析") {
        a.eq(i).css("display", "none");
      }
    }

    a = $("a");
    for (let i = 0; i < a.length; i++) { // 隐藏退出按钮
      if (a.eq(i).prop("href").includes("user/logout")) {
        a.eq(i).parent().css("display", "none");
      }
    }

    if ($("#start_full_export").length > 0) { // 屏蔽完整导出
      $("#start_full_export").css("display", "none");
    }

    if ($("#background_export_count").length > 0) { // 自定义导出限额
      let url = window.location.href;
      if (url.includes("ahrefs_rank_desc") && $("#background_export_count").val() > 200000)
        $("#background_export_count").val("200000");
      else if (url.includes("traffic_desc") && $("#background_export_count").val() > 1000)
        $("#background_export_count").val("1000");
    }

    $(".signout").css("display", "none");
  }, 1 * 1000);
});

document.onkeydown = function() {
  if (window.event && window.event.keyCode == 13) {
    window.event.returnValue = false;
  }
}
