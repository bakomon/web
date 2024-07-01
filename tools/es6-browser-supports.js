function supportsES6(engine) {
  if (typeof engine.name === 'undefined' || typeof engine.version === 'undefined') {
    return false;
  }
  if (engine.name in MinimumForES6) {
    return parseInt(engine.version) >= MinimumForES6[engine.name];
  }
  return false;
}

/*
ref:
- https://caniuse.com/es6
- https://www.lambdatest.com/web-technologies/es6
*/
var MinimumForES6 = {
  "Blink": 51,
  "EdgeHTML": 15,
  "Gecko": 54,
  "WebKit": 602
};

window.addEventListener('DOMContentLoaded', function() {
  var ua_parser = new UAParser();
  var ua_result = ua_parser.getResult();
  var mobile = typeof ua_result.device.type !== 'undefined' && ua_result.device.type == 'mobile' || typeof ua_result.browser.name !== 'undefined' && ua_result.browser.name.search(/mobile/i) != -1;
  window['isES6'] = supportsES6(ua_result.engine);

  if (!window.isES6) {
    var browsers = '<div class="t_perfect" style="height:95vh;"><div class="tp_content t_center layer"><p><b>Unsupported browser!</b></p><div class="new_line"></div><p>Please ';
    if (ua_result.engine.name in MinimumForES6) {
      browsers += 'upgrade your browser to latest version.</p>';
    } else {
      browsers += 'use these following browsers to view this page.</p><br/>';
      browsers += '<a href="https://www.google.com/chrome/?standalone=1" target="_blank">Chrome</a>';
      browsers += '<div class="new_line"></div><a href="https://www.mozilla.org/firefox/browsers/" target="_blank">Firefox</a>';
      browsers += '<div class="new_line"></div><a href="https://www.microsoft.com/edge" target="_blank">Edge</a>';
      browsers += '<div class="new_line"></div><a href="https://www.opera.com/download#opera-browser" target="_blank">Opera</a>';
      browsers += '<div class="new_line"></div><a href="https://www.apple.com/safari/" target="_blank">Safari</a>';
      browsers += '<div class="new_line"></div><a href="https://duckduckgo.com/app" target="_blank">DuckDuckGo</a>';
      browsers += '<div class="new_line"></div><a href="https://brave.com/" target="_blank">Brave</a>';
      browsers += '<div class="new_line"></div><a href="https://vivaldi.com/" target="_blank">Vivaldi</a>';
      browsers += '<div class="new_line"></div><a href="https://www.ecosia.org/" target="_blank">Ecosia</a>';
      if (mobile && typeof ua_result.os.name !== 'undefined' && ua_result.os.name.search(/android/i) != -1) {
        browsers += '<div class="new_line"></div><a href="https://play.google.com/store/apps/details?id=com.kiwibrowser.browser" target="_blank">Kiwi</a>';
        browsers += '<div class="new_line"></div><a href="https://play.google.com/store/apps/details?id=com.sec.android.app.sbrowser" target="_blank">Samsung Internet</a>';
        browsers += '<div class="new_line"></div><a href="https://play.google.com/store/apps/details?id=mark.via.gp" target="_blank">Via</a>';
      }
    }
    browsers += '</div></div>';
    
    document.body.innerHTML = browsers;
    document.body.classList.remove('loading', 'lody');
  }
});