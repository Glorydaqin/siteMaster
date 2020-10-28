chrome.contextMenus.create({
  type: 'normal', // 类型，可选：["normal", "checkbox", "radio", "separator"]，默认 normal
  title: '自动填充ahrefs注册信息', // 显示的文字，除非为“separator”类型否则此参数必需，如果类型为“selection”，可以使用%s显示选定的文本
  contexts: ['page'], // 上下文环境，可选：["all", "page", "frame", "selection", "link", "editable", "image", "video", "audio"]，默认page
  onclick: mock(), // 单击时触发的方法
  // parentId: 1, // 右键菜单项的父菜单项ID。指定父菜单项将会使此菜单项成为父菜单项的子菜单
  documentUrlPatterns: [
    "*://ahrefs.com/",
    "https://ahrefs.onfastspring.com/popup-ahrefs/session/*"
  ]
  // targetUrlPatterns: ['https://ahrefs.com']
});

// function find_date_in_str(content,find = "FirstName") {
//   var n=content.match(/性别\s+<\/div>\s+<div class=\"col-md-4 col-sm-4 col-xs-8">\s+<input type="text"\s+value='([^\']+)'/);
//
// }

function mock() {
  //
  // $.get("https://www.baidu.com", function (data, status) {
  //   alert("Data: " + data + "nStatus: " + status);
  // });

  var info = {
    firstName: "Isaac",
    lastName: 'Chase',
    cardNumber: 4929856413314829,
    cardMM: 12,
    cardYY: 25,
    cardCVC: 909,
    address: '3560 Powder House Road',
    city: 'LOS ANGELES',
    state: 'California',
    zip: 90001,
    phone: '561-713-9867'
  };

  //填充

}
