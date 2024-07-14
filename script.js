// Generate random number between two numbers https://stackoverflow.com/a/7228322
function getRndInteger(min, max) { // min and max included
  return Math.floor(Math.random() * (max - min + 1) + min)
}

// https://developer.mozilla.org/en-US/docs/Web/API/Window/beforeunload_event#examples
function beforeUnloadListener(event) {
  event.preventDefault();
  return event.returnValue = '';
}

// Check if string is number https://stackoverflow.com/a/175787
function isNumeric(data) {
  if (typeof data == 'number') return true;
  return !isNaN(data) && !isNaN(parseFloat(data));
}

// Check if browser is Chromium-based https://stackoverflow.com/a/62797156
function isChromium() {
  return !!window.chrome || !!navigator.userAgentData && navigator.userAgentData.brands.some(data => data.brand == 'Chromium');
}

// Toggle multiple classes
function toggleClass(elem, array) {
  // example: toggleClass(document.body, ['mobile', 'no_js']);
  array.forEach(function(item){ elem.classList.toggle(item); });
}

// Uppercase first character
function firstUCase(str) {
  var text = str.replace(str.substring(1, 0), str.substring(1, 0).toUpperCase());
  return text;
}


// Check if an image is loaded (no errors) https://stackoverflow.com/a/1977898
function isImageLoaded(img) {
  if (!img.complete) return false;
  if (img.naturalWidth === 0) return false;
  return true;
}

function modArray(note, array, str) {
  if (note == 'add') array.push(str);
  if (note == 'remove') return array.filter(function(item) { return item !== str });
  return array;
}

// Escape
function escape(note, str) {
  if (note == 'json') return str.replace(/["\&\t\b\f\r\n]/g, '\\$&');
  if (note == 'regex') return str.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
  if (note == 'html') return str.replace(/&/g, '&#38;').replace(/</g, '&#60;').replace(/>/g, '&#62;').replace(/\'/g, '&#39;').replace(/\"/g, '&#34;');
}

// Extract data from hash, example: "#/page/1" use "getHash('page')" to get "1"
function getHash(k) {
  // var rgx = new RegExp('#(?:.*)\\/'+ k +'\\/([^\\/]+)', 'i');
  var rgx = new RegExp(k +'\\/([^\\/]+)', 'i');
  var mtch = window.location.hash.match(rgx);
  var str = mtch ? mtch[1] : null;
  return str;
}

// Trigger "input" event https://stackoverflow.com/a/35659572
function triggerEvent(evnt, elem) {
  var event = new Event(evnt, {
    bubbles: true,
    cancelable: true,
  });
  elem.dispatchEvent(event);
}

// Position (X,Y) element https://stackoverflow.com/a/28222246
function getOffset(element) {
  var rect = element.getBoundingClientRect();
  var pos = {};
  pos.top = rect.top + window.scrollY;
  pos.right = rect.right + window.scrollX;
  pos.bottom = rect.bottom + window.scrollY;
  pos.left = rect.left + window.scrollX;
  return pos;
}

// Get first {n} data from Array https://stackoverflow.com/a/50930772
function firstArray(array, length, last) {
  return array.filter(function(item, index) {
    if (last) {
      return index >= length && index < array.length;
    } else {
      return index < length;
    }
  });
}

function genArray(json) {
  var arr = [];
  for (var key in json) {
    arr.push(json[key]);
  }
  return arr;
}

function genJSON(array, param) {
  var json = {};
  for (index in array) {
    var name = param ? array[index][param] : index;
    json[name] = array[index];
  }
  return json;
}

// Local Storage
function local(prop, name, val) {
  var methods = prop == 'get' ? 'getItem' : prop == 'set' ? 'setItem' : prop == 'remove' ? 'removeItem' : 'clear';
  if (prop == 'set') return localStorage[methods](name, val);
  if (prop == 'get' || prop == 'remove') return localStorage[methods](name);
  if (prop == 'clear') return localStorage[methods]();
}

// Sorting JSON by values https://stackoverflow.com/a/9188211
function sortBy(array, prop, asc) {
  return array.sort(function(a, b) {
    if (asc) {
      return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
    } else {
      return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
    }
  });
}

// Date toLocaleString() with local format, ref: https://www.w3schools.com/jsref/jsref_tolocalestring.asp
function dateLocal(date) {
  var date_lang = 'id-ID';
  var date_format = {
    // timeZone: 'Asia/Jakarta',
    hour12: false,
    dateStyle: 'full',
    timeStyle: 'long'
  };
  return new Date(date).toLocaleString(date_lang, date_format);
}

function keyEvent(event, code) {
  // Based on the US standard 101 keyboard https://www.toptal.com/developers/keycode/table
  var list = {"Enter":13,"Shift":16,"Control":17,"Alt":18,"ArrowLeft":37,"ArrowUp":38,"ArrowRight":39,"ArrowDown":40,"a":65,"c":67,"i":73,"j":74,"r":82,"s":83,"u":85,"v":86,"x":88,"F12":123};

  var key, prop = '';
  if (event.code) {
    prop = event.code in list ? event.code : event.key;
    key = list[prop];
  } else {
    key = event.keyCode;
  }

  return key == code || prop.toLowerCase() == String(code).toLowerCase() || new RegExp(`^${escape('regex', prop)}$`).test(code);
}

// Simple querySelector https://codepen.io/sekedus/pen/oKYOEK
function el(e, l, m) {
  var elem, parent = l != 'all' && l != 'xpath' && (l || l === null) ? l : document;
  if (parent === null) {
    elem = parent;
    console.error('selector: '+ e +' => parent: '+ parent);
  } else {
    if ((m || l) == 'xpath') {
      // https://stackoverflow.com/a/14284815
      elem = document.evaluate(e, parent, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
    } else {
      elem = ((m || l) == 'all') ? parent.querySelectorAll(e) : parent.querySelector(e);
    }
  }
  return elem;
}

// Check document (DOM) status https://codepen.io/sekedus/pen/ZEMzorv
function loadListener(type, callback) {
  type = type == 'initial' ? 0 : type == 'dom' ? 3 : 4; //4 = complete/load
  var load_chk = setInterval(function() {
    var ready = document.readyState;
    var state = ready == 'uninitialized' ? 0 : ready == 'loading' ? 1 : ready == 'loaded' ? 2 : ready == 'interactive' ? 3 : 4;
    if (state >= type) {
      clearInterval(load_chk);
      callback();
    }
  }, 100);
}

// Validation of file extension before upload https://stackoverflow.com/a/4237161
function fileValidate(elem, accept) {
  if (elem.type == 'file') {
    var file_name = elem.value;
    if (file_name.length > 0) {
      var valid = false;
      for (var i = 0; i < accept.length; i++) {
        if (file_name.substr(file_name.length - accept[i].length, accept[i].length).toLowerCase() == accept[i].toLowerCase()) {
          valid = true;
          break;
        }
      }

      if (!valid) return false;
    }
  }
  return true;
}

// Add script to head https://codepen.io/sekedus/pen/QWKYpVR
function addScript(options, callback) {
  // data, id, info, boolean, parent
  if (!('data' in options)) return;
  var js_new = document.createElement('script');
  if ('id' in options) js_new.id = options.id;
  if ('async' in options) js_new.async = options.async;
  if ('defer' in options) js_new.defer = options.defer;
  if ('html' in options && options.html == true) {
    js_new.type = 'text/javascript';
    js_new.innerHTML = options.data;
  } else {
    if (callback) {
      js_new.onerror = callback(true);
      js_new.onload = callback(false);
    }
    js_new.src = options.data;
  }
  var parent = 'parent' in options && options.parent.tagName ? options.parent : document.querySelector('head');
  parent.appendChild(js_new);
}

// Copy to clipboard https://stackoverflow.com/a/30810322
function copyToClipboard(text, elem) {
  var msg, elm = elem || document.body; /* parent element for textarea */
  var copyTextarea = document.createElement('textarea');
  copyTextarea.value = text;
  elm.appendChild(copyTextarea);
  copyTextarea.focus();
  copyTextarea.select();

  try {
    var successful = document.execCommand('copy');
    msg = successful ? true : false;
  } catch (err) {
    msg = false;
    console.log('Oops, unable to copy ', err);
  }

  elm.removeChild(copyTextarea);
  return msg;
}

// Timestamp to relative time https://stackoverflow.com/a/6109105
function timeDifference(date) {
  var msPerMinute = 60 * 1000;
  var msPerHour = msPerMinute * 60;
  var msPerDay = msPerHour * 24;
  var msPerMonth = msPerDay * 30;
  var msPerYear = msPerDay * 365;
  var elapsed = new Date() - new Date(date);

  if (elapsed < msPerMinute) {
    return Math.round(elapsed / 1000) + ' seconds ago';
  } else if (elapsed < msPerHour) {
    return Math.round(elapsed / msPerMinute) + ' minutes ago';
  } else if (elapsed < msPerDay) {
    return Math.round(elapsed / msPerHour) + ' hours ago';
  } else if (elapsed < msPerMonth) {
    return Math.round(elapsed / msPerDay) + ' days ago';
  } else if (elapsed < msPerYear) {
    return Math.round(elapsed / msPerMonth) + ' months ago';
  } else {
    return Math.round(elapsed / msPerYear) + ' years ago';
  }
}

// Remove element https://codepen.io/sekedus/pen/ZEYRyeY
function removeElem(elem, index) {
  var elmn = typeof elem === 'string' ? document.querySelectorAll(elem) : elem;
  if (!elmn || (elmn && elmn.length == 0)) {
    console.error('!! ERROR: removeElem(), elem = ', elem);
    return;
  }
  // if match 1 element & have specific index
  if (elmn && !elmn.length && index) {
    console.error('!! ERROR: use querySelectorAll() for specific index');
    return;
  }

  elmn = index ? (index == 'all' ? elmn : elmn[index]) : (typeof elem == 'string' || elmn.length ? elmn[0] : elmn);

  if (elmn.length && index == 'all') {
    for (var i = 0; i < elmn.length; i++) {
      elmn[i].parentElement.removeChild(elmn[i]);
    }
  } else {
    elmn.parentElement.removeChild(elmn);
  }
}

// Detect mobile device https://stackoverflow.com/a/22327971
function isMobile() {
  var ua = navigator.userAgent || navigator.vendor || window.opera;
  return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(ua) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(ua.substr(0,4)));
}

// Cookies with custom timer https://codepen.io/sekedus/pen/xxYeZZj
var cookies = {
  set: function(name, value, interval) {
    var expires = '';
    if (interval) {
      var date = new Date();
      var timer = interval.indexOf('|') != -1 ? Number(interval.split('|')[1]) : 1;

      if (interval.search(/(year|month)s?/i) != -1) {
        var year_add = interval.search(/years?/i) != -1 ? timer : 0;
        var month_add = interval.search(/months?/i) != -1 ? timer : 0;
        date.setFullYear(date.getFullYear() + year_add, date.getMonth() + month_add);
      } else {
        var date_num = interval.search(/weeks?/i) != -1 ? (timer*7*24*60*60) : interval.search(/days?/i) != -1 ? (timer*24*60*60) : interval.search(/hours?/i) != -1 ? (timer*60*60) : interval.search(/minutes?/i) != -1 ? (timer*60) : timer; // default = second
        date.setTime(date.getTime() + (date_num * 1000));
      }
      expires = '; expires='+ date.toGMTString();
    }
    // if no interval, timer = session
    document.cookie = name +'='+ value + expires+'; path=/';
  },
  get: function(name) {
    // https://www.quirksmode.org/js/cookies.html
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  },
  remove: function(name) {
    document.cookie = name +'=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    document.cookie = name +'=; Max-Age=0; path=/; domain='+ window.location.hostname;
  },
};

// #===========================================================================================#

// Filter Array data with multiple value https://stackoverflow.com/a/10870500
function bmf_filterBy(note, array, param) {
  return array.filter(function(item) {
    if (note == 'history' && !('bookmarked' in item)) item['bookmarked'] = 'false';
    for (var i in param) {
      // match or indexOf
      if (!item[i].toString().match(param[i])) return null;
    }
    return item;
  });
}

function bmf_get_id(title) {
  var titleId = title.replace(/&#{0,1}[a-z0-9]+;/gi, '').replace(/\([^\)]+\)/g, '');
  // titleId = titleId.replace(/\s((bahasa|sub(title)?)\s)?indo(nesiaa?)?/i, '').replace(/(baca|read|download)\s/i, '').replace(/\s?(man(ga|hwa|hua)|[kc]omi[kc]s?|series|novel|anime)\s?/i, '\x20');
  titleId = titleId.replace(/(\.|\t)+/g, '\x20').replace(/\s+/g, '\x20').replace(/[^\s\w\-]/g, '').replace(/\s+$/g, '').replace(/\s+/g, '-').toLowerCase();
  return titleId;
}

// Get URL Variables https://codepen.io/sekedus/pen/jOpNmja
function bmf_getParam(param, url) {
  var result = [];
  var loc = url ? new URL(url) : window.location;
  var query = loc.search.substring(1).split('&');
  for (var i = 0; i < query.length; i++) {
    var pair = query[i].split('=');
    if (pair[0] == param) {
      if (pair.length == 1) {
        return true;
      } else {
        result.push(decodeURIComponent(pair[1].replace(/\+/g, ' ')));
      }
    }
  }
  return result.length == 0 ? false : result;
}

function bmf_connectionNotif(e) {
  bmv_connection = e.type;
  if (bmv_connection == 'online' && !el('#connection')) return;

  var c_el;
  if (el('#connection')) {
    c_el = el('#connection');
  } else {
    c_el = document.createElement('div');
    c_el.id = 'connection';
    document.body.appendChild(c_el);

    c_el.addEventListener('click', function() {
      if (bmv_connection != 'online') {
        toggleClass(this, ['red', 'bgrey', 'hide', 'pulse']);
        if (this.classList.contains('hide')) {
          this.setAttribute('title', this.innerHTML);
          this.innerHTML = '<svg data-name="mdi/globe-remove" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m14.46 15.88l1.42-1.42L18 16.59l2.12-2.12l1.42 1.41L19.41 18l2.13 2.12l-1.42 1.42L18 19.41l-2.12 2.13l-1.42-1.42L16.59 18l-2.12-2.12M20 12c0-3.36-2.07-6.23-5-7.41V5c0 1.1-.9 2-2 2h-2v2c0 .55-.45 1-1 1H8v2h6c.5 0 .9.35 1 .81c-1.8 1.04-3 2.98-3 5.19c0 1.5.54 2.85 1.44 3.9L12 22C6.5 22 2 17.5 2 12S6.5 2 12 2s10 4.5 10 10l-.1 1.44c-.56-.48-1.2-.85-1.9-1.1V12m-9 7.93V18c-1.1 0-2-.9-2-2v-1l-4.79-4.79C4.08 10.78 4 11.38 4 12c0 4.08 3.06 7.44 7 7.93Z"/></svg>';
        } else {
          this.innerHTML = this.getAttribute('title');
        }
      }
    });
  }

  var c_msg = bmv_connection == 'online' ? 'Kembali Online.' : 'Tidak ada koneksi internet. Pastikan Wi-Fi atau data seluler aktif, lalu muat ulang halaman.';
  // var c_msg = bmv_connection == 'online' ? 'Internet connected.' : 'No internet connection. Make sure Wi-Fi or mobile data is turned on, then reload the page.';
  c_el.className = bmv_connection == 'online' ? 'green' : 'red';
  c_el.innerHTML = c_msg;
  if (bmv_connection == 'online') {
    setTimeout(function() {
      if (bmv_connection == 'online') removeElem(c_el);
    }, 1500);
    if (!bmv_page_loaded) wl.reload();
  }
}

// loadXMLDoc (XMLHttpRequest) https://codepen.io/sekedus/pen/vYGYBNP
function bmf_loadXMLDoc(info, url, callback) {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      var response = this.responseText;
      if (this.status == 200) {
        if ('parse' in info) {
          var resHTML = new DOMParser();
          response = resHTML.parseFromString(response, 'text/html');
        }
      } else {
        var err_msg = '❗❗ ERROR: bmf_loadXMLDoc';
        if ('timeout' in info) err_msg += ` timed out (${info.timeout}).`;
        err_msg += ' status = '+ this.status +', url = '+ url;
        console.error(err_msg);
      }
      var data = {"code": this.status, "response": response};
      if (typeof err_msg !== 'undefined') data.error = err_msg;
      callback(info.note, data);
    }
  };
  xhr.open('GET', url, true);
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  if ('timeout' in info) xhr.timeout = info.timeout;
  xhr.send();
}

// Lazy Loading Images
function bmf_lazyLoad(elem, note) {
  if (!elem) return;
  var lz_check_point, lz_images = elem;

  if (!el('#lz_check_point')) {
    lz_check_point = document.createElement('div');
    lz_check_point.id = 'lz_check_point';
    lz_check_point.style.cssText = 'position:fixed;top:0;bottom:0;left:-2px;';
    document.body.appendChild(lz_check_point);
  } else {
    lz_check_point = el('#lz_check_point');
  }

  function lazyReset() {
    bmv_chk_lazy = false;
    bmv_lazy_error = false;
    bmv_lazy_skip = false;
  }

  function lazyNext() {
    bmv_dt_lazy.splice(0, 1); //remove first image
    if (bmv_dt_lazy.length > 0) lazyQueue();
  }

  function lazyUrl(elem, url, callback) {
    if (bmv_load_cdn || url == '') {
      callback(url);
      return;
    }
    if (is_mobile && bmv_dt_settings.img_resize) {
      var lz_width = bmv_current == 'chapter' ? window.screen.width : (window.screen.width / 2);
      var lz_params = '?quality='+ bmv_dt_settings.resize_quality;
      if (bmv_current == 'chapter') lz_params += '&index='+ elem.dataset.index;
      lz_params += '&width='+ (lz_width + 50);
      lz_params += '&ref='+ encodeURIComponent(bmv_lazy_referer);
      lz_params += '&name='+ bmv_dt_settings.source.site +'_'+ bmf_get_id(elem.title);
      lz_params += '&imageUrl='+ encodeURIComponent(url);

      bmf_loadXMLDoc({note:`xhr/${bmv_current}/lazyload`, timeout:30000}, `${api_path}/tools/image-resize/${lz_params}`, function(n, res) {
        if (res.code == 200 && res.response != '') {
          try {
            var img_data = JSON.parse(res.response);
            if (img_data.status == 'success') {
              url = img_data.img_url;
            } else {
              throw new Error(img_data.error_message);
            }
          } catch(e) {
            console.error(`!! Error: ${e}, ${url}`);
          }
        }
        if ('error' in res) console.error(res.error);
        callback(url);
      });
    } else {
      if (elem.dataset.ref == 'true') {
        url = `${api_path}/tools/img_ref.php?ref=${encodeURIComponent(bmv_lazy_referer)}&url=`+ encodeURIComponent(url);
        if (bmv_current == 'chapter' && elem.parentElement.tagName == 'A') elem.parentElement.href = url;
      }
      callback(url);
    }
  }

  function lazyQueue(single) {
    if (bmv_chk_lazy) return;

    var lz_elem = single ? single.elem : bmv_dt_lazy[0].elem;
    var lz_img = new Image();
    var lz_2nd = 'la2yloading';

    var lz_wait;
    if (bmv_current == 'chapter') {
      lz_img = lz_elem;
      lz_2nd = 'lazyload3d lazyshow';

      // Get image dimensions before image has fully loaded https://stackoverflow.com/a/6575319
      var lz_loaded = false;
      lz_img.addEventListener('load', function() { lz_loaded = true; }, true);
      lz_wait = setInterval(function() {
        if (lz_loaded && lz_img.height > 0) {
          clearInterval(lz_wait);
          lz_elem.style.removeProperty('min-height');
          lz_elem.style.minHeight = lz_img.height +'px';
        }
      }, 0);

      if (el('.ch_menu')) el('.cm_ld_current').innerHTML = 'LZ ('+ lz_elem.dataset.index +')';
    }

    var imgs = single ? single.img : bmv_dt_lazy[0].img;
    lz_elem.className = lz_elem.className.replace('lazy1oad', lz_2nd);
    lz_img.onerror = function() { bmv_lazy_error = true; };
    bmv_chk_lazy = true;

    if (!single && isImageLoaded(lz_elem) && lz_elem.classList.contains('lazyload3d')) {
      if (bmv_current == 'chapter') {
        clearInterval(lz_wait);
        lz_elem.style.removeProperty('min-height');
      }
      lazyReset();
      lazyNext();
    } else {
      lazyUrl(lz_elem, imgs, function(url) {
        lz_img.src = url;

        var skip_time = bmv_current == 'chapter' && lz_elem.dataset.ref == 'false' && !is_mobile ? 60000 : 5000;
        var skip_img = setTimeout(function() { bmv_lazy_skip = true; }, skip_time);

        var wait_img = setInterval(function() {
          if (isImageLoaded(lz_img) || bmv_lazy_error || bmv_lazy_skip) {
            clearInterval(wait_img);
            clearTimeout(skip_img);

            // repeat, if error
            if (bmv_lazy_error && url != '' && (lz_img.src.match(bmv_rgx_cdn) || lz_elem.dataset.ref == 'false')) {
              lazyReset();
              if (lz_img.src.match(bmv_rgx_cdn)) { //cdn is true
                var err_url = lz_img.src.replace(bmv_rgx_cdn, '');
                if (bmv_str_cdn == 'imagecdn') err_url = decodeURIComponent(err_url.replace(/^(https?:)?\/\//, ''));
                var img_elem = single ? single : bmv_dt_lazy[0];
                img_elem.img = err_url;
              } else if (lz_elem.dataset.ref == 'false') {
                lz_elem.dataset.ref = 'true';
              }

              if (single) {
                lazyQueue(single);
              } else {
                lazyQueue();
              }
            } else {
              lz_elem.className = lz_elem.className.replace('la2yloading', 'lazyload3d');
              lz_elem.classList.remove('loading', 'loge');
              lz_elem.classList[bmv_lazy_error ? 'add' : 'remove']('no-image');
              lz_elem.style.removeProperty('min-height');

              if (bmv_current == 'chapter') {
                if (bmv_lazy_error) clearInterval(lz_wait);
              } else {
                lz_elem.src = lz_img.src;
                // lz_elem.removeAttribute('data-src');
                setTimeout(function() { lz_elem.classList.add('lazyshow'); }, 100); //transition
              }
              lz_elem.parentElement.classList.add('lazy-loaded');

              lazyReset();
              if (!single) lazyNext();
            }
          }
        }, 100);
      });
    }
  }

  function lazyPos(img) {
    var lz_top = (getOffset(img).top + img.offsetHeight) > getOffset(lz_check_point).top;
    var lz_bottom = (getOffset(img).bottom - img.offsetHeight) < getOffset(lz_check_point).bottom;
    return lz_top && lz_bottom;
  }

  function lazyLegacy(elem, index = 0) {
    var lz_chk1 = lazyPos(elem) && !elem.classList.contains('lazyload3d');

    var lz_next = false;
    if (bmv_current == 'chapter' && 'length' in lz_images) {
      // load next image, top to bottom
      var lz_chk3 = lazyPos(lz_images[index]) && lz_images[index].classList.contains('lazyload3d');
      var lz_chk4 = lz_images[index+1] && !lazyPos(lz_images[index+1]) && lz_images[index+1].classList.contains('lazy1oad');
      if (lz_chk3 && lz_chk4) {
        lz_images[index].parentElement.classList.add('load-next');
        elem = lz_images[index+1];
        lz_next = true;
      }
    }

    if (lz_chk1 || note == 'single' || note == 'multi' || lz_next) {
      elem.classList.remove('lazyload3d', 'lazyshow', 'no-image');
      elem.classList.add('loading', 'loge', 'lazy1oad');
      elem.setAttribute('data-ref', 'false');
      var img = elem.dataset.src;

      if (bmv_current == 'chapter') {
        img = bmv_load_cdn && bmv_str_cdn == 'imagecdn' ? encodeURIComponent(img) : img.replace(/^(https?:)?\/\//, '');
        if (bmv_chk_cdn) img = img.replace(bmv_rgx_cdn, '').replace(/\/[fhwq]=[^\/]+/, '');
        if (bmv_load_cdn) img = bmv_str_cdn_url + img;
        img = wl.protocol +'//'+ img;

        // remove location.search ?=
        if (img.search(/(pending\-load|cdn\.statically\.io|cdn\.imagesimple\.co)/) != -1) img = img.replace(/\?(.*)/g, '');

        // google images (blogger, gdrive, gphotos)
        if (bmv_load_gi) {
          var sNum = el('.cm_size').innerHTML;
          img = img.replace(/\/([swh]\d+)(?:-[\w]+[^\/]*)?\//, '/'+ sNum +'/');
          img = img.replace(/=[swh](\d+).*/, '='+ sNum);
          if (img.indexOf('docs.google') != -1) img = 'https://lh3.googleusercontent.com/d/'+ img.match(/.*id=([^&]+)/)[1] +'='+ sNum;
        }
      }

      if (note == 'single') {
        if (bmv_current == 'chapter') {
          elem.style.minHeight = '750px';
          elem.removeAttribute('src');
        }
        lazyQueue({"elem": elem, "img": img});
      } else {
        if (!bmv_dt_lazy.some(function(item) {return item.img == img})) {
          bmv_dt_lazy.push({"elem": elem, "img": img});
          lazyQueue();
        }
      }
    }
  }

  if ('length' in lz_images) {
    lz_images.forEach(lazyLegacy);
  } else {
    lazyLegacy(lz_images);
  }
}

// #===========================================================================================#

// Element selector meta tags
function bmf_emc(m, c) {
  el('meta['+ m +']').setAttribute('content', c);
}

function bmf_meta_tags(note, data) {
  var d_desc = 'Baca dan download komik, manga, manhwa, manhua one shot bahasa indonesia online full page, terlengkap, gratis, loading cepat dan update setiap hari.';
  var d_key = 'baca komik, baca manga, baca manhwa, baca manhua, komik one piece, komik black clover, komik jujutsu kaisen, komik boruto, komik edens zero, baca manga android';

  var mt_page = getHash('page') ? ` \u2013 Laman ${getHash('page')}` : '';
  var mt_title = bmv_current == 'latest' ? 'Bakomon'+ mt_page +' \u2013 Baca Komik Bahasa Indonesia Online' : el('h1').textContent +' | Bakomon';
  var mt_url = wl.href;
  var mt_img = 'https://'+ wl.hostname +'/images/cover.png';
  var mt_desc = 'Baca komik dan baca manga terbaru bahasa indonesia online, bisa full page, loading cepat dan update setiap hari.';

  if (bmv_current == 'series') {
    mt_img = data.cover.replace(/\?.*/, '');
    mt_desc = data.desc.length > 87 ? data.desc.substring(0, 87) + '...' : data.desc;
  }

  if (bmv_current == 'chapter') {
    if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') mt_title = `[${data.current.replace(/[-\s]((bahasa?[-\s])?indo(nesiaa?)?|full)/, '')}] `+ mt_title;
    d_desc = mt_desc = 'Baca download komik '+ data.title +' volume batch bahasa indonesia pdf rar zip terlengkap.';
    d_key = d_key + ', '+ data.title +' Chapter '+ data.current;
  }

  document.title = bmv_current == 'latest' ? 'Bakomon'+ mt_page +' \u2013 Baca Komik, Manga, Manhwa, Manhua Bahasa Indonesia Online' : mt_title;
  bmf_emc('name="description"', d_desc);
  bmf_emc('name="keywords"', d_key);
  bmf_emc('itemprop="name"', mt_title);
  bmf_emc('itemprop="description"', mt_desc);
  bmf_emc('itemprop="image"', mt_img);
  bmf_emc('property="og:title"', mt_title);
  bmf_emc('property="og:url"', mt_url);
  bmf_emc('property="og:image"', mt_img);
  bmf_emc('property="og:description"', mt_desc);
  bmf_emc('name="twitter:title"', mt_title);
  bmv_current == 'latest' ? bmf_emc('property="og:type"', 'website') : bmf_emc('property="og:type"', 'article');
}

// #===========================================================================================#

function bmf_member_notif(info, opt) {
  clearTimeout(bmv_mnotif_timeout);

  if (info == 'reset') {
    el('.member .m-notif').classList.remove('error');
    el('.member .m-notif').classList.add('no_items');
    return;
  }

  var m_info = info.replace(/.*\//, '');
  var m_msg = m_info.replace(/\-/g, ' ');
  if (opt && 'message' in opt) m_msg = opt.message;

  if (info.indexOf('error') != -1) {
    if (m_info == 'wrong-password') {
      m_msg = 'Katasandi yang Kamu masukkan salah.';
      if (!fbase_login) m_msg += ' <a href="#/member/forgot">Lupa katasandi?</a>';
    }
    if (m_info == 'user-not-found') m_msg = 'Email tidak terdaftar. Periksa lagi atau <a href="#/member/signup">daftar akun baru.</a>';
    if (m_info == 'email-already-in-use') m_msg = 'Email sudah terdaftar. <a href="#/member/login">Login disini.</a>';
    if (m_info == 'confirm-password') m_msg = 'Konfirmasi katasandi <b>TIDAK SAMA</b>';
    m_msg = '<b>Error:</b> '+ m_msg +'';
    el('.member .m-notif').classList.add('error');
  } else {
    if (m_info == 'email-verification') {
      m_msg = '<span class="success">Link verifikasi email terkirim ke <b>'+ fbase_user.email +'</b></span>. Silahkan cek folder "Kotak Masuk" atau "Spam" di email.';
    } else {
      m_msg = '<span class="success">'+ m_msg +'</b></span>';
    }
  }
  el('.member .m-notif').innerHTML = m_msg;
  el('.member .m-notif').classList.remove('no_items');
  document.body.scrollIntoView();

  if (opt && 'timer' in opt) {
    var milliseconds = isNumeric(opt.timer) ? opt.timer : Number(opt.timer);
    bmv_mnotif_timeout = setTimeout(function() {
      if (el('.member .m-notif')) el('.member .m-notif').classList.add('no_items');
    }, milliseconds);
  }
}

function bmf_member_valid(note, elem, elem_c) {
  var m_valid = false;
  var m_msg = elem.validationMessage;
  // console.log(elem.validity);

  if (elem.checkValidity()) {
    m_valid = true;

    if (note == 'cover') {
      if (!fileValidate(elem, ['png', 'gif', 'jpg', 'jpeg'])) {
        m_valid = false;
        m_msg = 'file-format-not-supported';
      }
    }
    if (note == 'pass-c') {
      if (elem.value != elem_c.value) {
        m_valid = false;
        m_msg = 'confirm-password';
      }
    }
  }

  if (!m_valid) bmf_member_notif('error/'+ m_msg);
  return m_valid;
}

function bmf_member_hibp(note, user, callback) {
  if (user.email.search(new RegExp(user.pass, 'i')) != -1 || user.pass.search(new RegExp(user.name, 'i')) != -1) {
    if (callback) callback(true);
  } else {
    bmf_loadXMLDoc({note:`xhr/${bmv_current}/${note}`}, `${api_path}/tools/hibp.php?pass=${btoa(user.pass)}`, function(n, data) {
      var pwned, hibp = JSON.parse(data.response);
      if (hibp.pawned > 0) pwned = true;
      if (callback) callback(pwned);
    });
  }
}

function bmf_email_verification(note, user, callback) {
  // Default expiration time: https://github.com/firebase/firebase-js-sdk/issues/1884#issuecomment-545598830
  fbase.auth().languageCode = 'id';
  user.sendEmailVerification({
    url: `${bmv_homepage}#/member/profile`
  }).then(() => {
    cookies.set('bmv_signup_verify', 'true', 'hour|1');
    if (bmv_prm_slug == 'profile') bmf_member_notif(`success/${note}/email-verification`);
    if (callback) callback();
  }).catch(function(error) {
    bmf_member_notif('error/verification/'+ error.code);
    if (bmv_prm_slug == 'profile') el('.m-profile .m-detail').classList.remove('loading', 'loge');
    console.error('!! Error: Firebase sendEmailVerification, code: '+ error.code +', message: '+ error.message);
    // alert('!! Error: Firebase sendEmailVerification(\n'+ error.message);
  });
}

function bmf_bmhs_key(e) {
  if (keyEvent(e, 13)) {
    if (el('.ms-form .ms-field') == e.target) el('.ms-form .ms-search').click();
    if (el('.m-pagination .bmhs-goto') == e.target) bmf_bmhs_nav_goto(el('.m-pagination .bmhs-goto').value);
  }
}

function bmf_bmhs_change(info, total) {
  var m_info = info.split('|');
  var m_path = bmf_fbase_path(`check/${m_info[0]}`);
  bmf_fbase_db_get(`series/${m_info[0]}/${m_info[1]}`, m_path, function(res) {
    var m_length = res.val() ? Number(res.val().length) : 0;
    if (m_info[1] == 'remove') m_length = m_length > 0 ? (m_length - 1) : m_length;
    if (m_info[1] == 'set') m_length = m_length + 1;
    if (typeof total === 'number') m_length = total;
    var m_chk_data = bmf_fbase_gen(`${m_info[0]}|check`, {length: m_length});
    bmf_fbase_db_change(`series/${m_info[0]}/check`, m_path, 'set', m_chk_data);
  });
}

function bmf_bmhs_length(note) {
  var m_path = bmf_fbase_path(`check/${bmv_prm_slug}`);
  bmf_fbase_db_get(note, m_path, function(res) {
    var m_length = res.val() ? Number(res.val().length) : 0;
    var m_sort = bmv_prm_slug == 'bookmark' ? 'bm_added' : 'hs_update';
    if (m_length > bmv_max_bmhs) {
      var new_data = genArray(bmv_dt_bmhs); //convert to Array
      new_data = sortBy(new_data, m_sort); //sot by "m_sort" (desc)
      new_data = firstArray(new_data, bmv_max_bmhs, 'last'); //get last after {bmv_max_bmhs} data

      for (var i in new_data) {
        bmf_bmhs_remove(`series/${new_data[i].slug}`);
      }
    }
  });
}

function bmf_bmhs_remove_split(data) {
  if (bmv_prm_slug == 'bookmark') {
    if ('hs_visited' in data) {
      data.bookmarked = 'false';
      return data;
    }
  } else { //history
    if ('bookmarked' in data && data.bookmarked == 'true') {
      data.history = 'false';
      data.hs_visited = {};
      return data;
    }
  }
  return {};
}

function bmf_bmhs_remove(note) {
  if (note.indexOf('reset') != -1) {
    bmv_dt_delete = [];
    if (note.indexOf('toggle') == -1) {
      el('.m-delete-btn').classList.remove('sh-close');
      el('.m-delete-all').classList.add('no_items');
      el('.nav-delete').classList.add('no_items');
    }
    el('.m-select-all input').checked = false;
  } else {
    var m_note = `member/${bmv_prm_slug}/${note}`;
    var m_path = bmf_fbase_path(note);

    bmf_fbase_db_get(m_note, m_path, function(res) {
      var series = res.val();
      var new_series = {};

      if (note == 'series') { //all
        for (var i in series) {
          new_series[series[i].slug] = bmf_bmhs_remove_split(series[i]);
        }
        bmv_dt_bmhs = null;
      } else {
        new_series = bmf_bmhs_remove_split(series);
        delete bmv_dt_bmhs[series.slug];
      }

      bmf_fbase_db_change(m_note, m_path, 'set', new_series, function() {
        var m_length = genArray(bmv_dt_bmhs).length;
        bmf_bmhs_change(`${bmv_prm_slug}|remove`, m_length);
        el('.member .m-total').innerHTML = m_length +'/'+ bmv_max_bmhs;

        if (m_length > 0) {
          if (bmv_dt_delete.length == 1) {
            removeElem(el(`.member .m-list li[data-slug="${series.slug}"]`));
            bmv_dt_delete = [];
          } else {
            bmf_bmhs_remove('reset');
            bmf_member_bmhs_data(`${bmv_prm_slug}/delete-all`);
          }
        } else { //empty
          el('.member .m-total').classList.add('no_items');
          el('.member .m-delete').classList.add('no_items');
          el('.member .m-nav').classList.add('no_items');
          el('.member .m-list').classList.remove('loading', 'loge');
          el('.member .m-list').innerHTML = `<div class="flex f_middle f_center full" style="min-height:230px;">${firstUCase(bmv_prm_slug)} Kosong</div>`;
          el('.member .m-pagination').classList.add('no_items');
        }
      });
    });
  }
}

function bmf_bmhs_fnc(note) {
  if (note == 'first') {
    el('.post-header h1 span').classList.add('m-total', 'btn');
    if (is_mobile) el('.member .m-delete').classList.add('m-space-v', 'full');
    el('.member .m-delete').classList.remove('no_items');
    el('.member .m-nav').classList.remove('no_items');

    el('.member .m-delete-btn').addEventListener('click', function() {
      bmf_bmhs_remove('reset/toggle');
      this.classList.toggle('sh-close');
      el('.m-delete-all').classList.toggle('no_items');
      el('.nav-delete').classList.toggle('no_items');
      el('.member .m-list .delete', 'all').forEach(function(item) {
        item.classList.toggle('no_items');
        el('input', item).checked = false;
      });
    });

    el('.member .m-sort').addEventListener('change', function() {
      bmf_bmhs_remove('reset');
      bmf_member_bmhs_data(`${bmv_prm_slug}/sort`);
    });

    el('.member .m-filter').addEventListener('change', function() {
      this.dataset.value = this.value;
      bmf_bmhs_remove('reset');
      bmf_member_bmhs_data(`${bmv_prm_slug}/filter`);
    });

    el('.m-delete-select').addEventListener('click', function() {
      if (bmv_dt_delete.length == 0) return;
      var str_confirm = '';
      var m_slug = '';
      for (var i in bmv_dt_delete) {
        m_slug += '\n👉 '+ bmv_dt_delete[i];
      }
      str_confirm = `Hapus series ini dari ${bmv_prm_slug}?${m_slug}`;
      if (confirm(str_confirm)) {
        for (var j in bmv_dt_delete) {
          el(`.member .m-list li[data-slug="${bmv_dt_delete[j]}"]`).classList.add('loading', 'loge');
          bmf_bmhs_remove(`series/${bmv_dt_delete[j]}`);
        }
      }
    });

    el('.m-select-all input').addEventListener('input', function() {
      var all_chk = this.checked;
      el('.member .m-list .delete input', 'all').forEach(function(item) {
        item.checked = all_chk ? true : false;
        triggerEvent('change', item);
      });
    });

    el('.ms-form .ms-reset').addEventListener('click', function() {
      this.classList.add('no_items');
      bmf_bmhs_remove('reset');
      el('.member .m-filter').disabled = false;
      bmf_member_bmhs_data(`${bmv_prm_slug}/reset`);
    });

    el('.ms-form .ms-search').addEventListener('click', function() {
      if (el('.ms-form input').value == '') return;
      bmf_bmhs_remove('reset');
      el('.ms-form .ms-reset').classList.remove('no_items');
      el('.member .m-filter').disabled = true;
      bmf_member_bmhs_data(`${bmv_prm_slug}/search`);
    });

    // bmhs: delete all
    el('.m-delete-all').addEventListener('click', function() {
      el('.m-confirm').classList.remove('no_items');
      el('.m-confirm input').focus();
    });

    el('.m-confirm .dc-remove').addEventListener('click', function() {
      if (el('.m-confirm input').value == `delete-all-${bmv_prm_slug}`) {
        el('.m-confirm').classList.add('no_items');
        el('.member .m-list').classList.add('loading', 'loge');
        bmf_bmhs_remove('series');
      }
    });

    el('.m-confirm .dc-cancel').addEventListener('click', function() {
      el('.m-confirm').classList.add('no_items');
    });
  }

  if (bmv_prm_slug == 'history') {
    el('.member .m-history .m-hs-list .detail ul', 'all').forEach(function(item) {
      if (item.scrollHeight > item.clientHeight) item.classList.add('overflowing');
    });
  }

  el('.member .m-list .delete', 'all').forEach(function(item) {
    item.addEventListener('change', function(e) {
      var m_slug = this.parentElement.dataset.slug;
      var m_note = el('input', this).checked ? 'add' : 'remove';
      bmv_dt_delete = modArray(m_note, bmv_dt_delete, m_slug);

      if (e.isTrusted) {
        this.parentElement.classList.add('highlighted');
        var all_chk = bmv_dt_delete.length == el('.member .m-list .delete', 'all').length;
        el('.m-select-all input').checked = all_chk ? true : false;
      }
    });
  });
}

function bmf_bmhs_filter(note, data, value) {
  if (value == '') return data;
  var m_val = value.split('|');
  var m_filter = JSON.parse(`{"${m_val[0]}": "${m_val[1]}"}`);
  if (note.indexOf('/search') != -1) m_filter[Object.keys(m_filter)] = new RegExp(m_val[1], 'gi');
  return  bmf_filterBy(bmv_prm_slug, data, m_filter);
}

function bmf_bmhs_sort(note, data, value) {
  var m_val = value.split('|');
  var m_chk = m_val[1] && m_val[1] == 'asc' ? true : false;
  return sortBy(data, m_val[0], m_chk);
}

function bmf_bmhs_nav_goto(page) {
  if (page == bmhs_current) return;
  document.body.scrollIntoView();
  bmhs_current = Number(page);
  bmf_bmhs_set(`page-${bmhs_current}`);
}

function bmf_bmhs_nav_html() {
  var str_nav = '';
  var n_num = parseInt(bmhs_nav_max / 2);
  var n_start = bmhs_current - n_num;
  var n_end = bmhs_current + n_num;

  if (bmhs_current < (bmhs_nav_max+2)) {
    n_start = 1;
    n_end = (bmhs_nav_max+2);
  }

  if (bmhs_current > (bmhs_length - (bmhs_nav_max+2))) {
    n_start = bmhs_length - (bmhs_nav_max+1);
    n_end = bmhs_length;
  }

  if (n_start < 1) n_start = 1;

  if (bmhs_length <= (bmhs_nav_max+4)) {
    n_start = 1;
    n_end = bmhs_length;
  }

  str_nav += '<ul class="flex radius">';

  // "prev" button
  str_nav += '<li';
  if (bmhs_current != 1) str_nav += ' data-page="'+ (bmhs_current-1) +'"';
  str_nav += '><div class="btn bmhs-prev '+ (bmhs_current == 1 ? 'disabled' : 'bmhs-nav') +'" title="Prev">&#10094;</div></li>';

  // add '...'
  if (n_start > 1) {
    str_nav += '<li data-page="1"><div class="btn bmhs-nav">1</div></li>';
    str_nav += '<li><div class="bmhs-lg">...</div></li>';
  }

  for (var i = n_start; i <= n_end; i++) {
    str_nav += '<li';
    if (bmhs_current != i) str_nav += ' data-page="'+ i +'"';
    str_nav += '><div class="btn '+ (bmhs_current == i ? 'selected' : 'bmhs-nav');
    if (/^\d{2,}$/.test(i)) str_nav += ' bmhs-lg';
    str_nav += '">'+ i +'</div></li>';
  }

  // add '...'
  if (n_end < bmhs_length) {
    str_nav += '<li><div class="bmhs-lg">...</div></li>';
    str_nav += '<li data-page="'+ bmhs_length +'"><div class="btn bmhs-nav';
    if (/^\d{2,}$/.test(bmhs_length)) str_nav += ' bmhs-lg';
    str_nav += '">'+ bmhs_length +'</div></li>';
  }

  // "next" button
  str_nav += '<li';
  if (bmhs_current != bmhs_length) str_nav += ' data-page="'+ (bmhs_current+1) +'"';
  str_nav += '><div class="btn bmhs-next '+ (bmhs_current == bmhs_length ? 'disabled' : 'bmhs-nav') +'" title="Next">&#10095;</div></li>';

  str_nav += '</ul>';
  str_nav += '<div class="bmhs-pagestats flex f_middle f_center"><span class="bmhs-count">Page '+ bmhs_current +' of '+ bmhs_length +'</span><input class="bmhs-goto no_arrow" type="number" min="1" max="'+ bmhs_length +'" title="Goto.."></div>';

  el('.member .m-pagination').innerHTML = str_nav;

  el('.m-pagination .bmhs-nav', 'all').forEach(function(item) {
    item.addEventListener('click', function() {
      bmf_bmhs_nav_goto(this.parentElement.dataset.page);
    });
  });

  el('.m-pagination .bmhs-goto').addEventListener('input', function(e) {
    if (this.value == '') return;
    var goto = Number(this.value);
    if (goto < Number(e.target.min)) this.value = e.target.min;
    if (goto > Number(e.target.max)) this.value = e.target.max;
  });
}

function bmf_bmhs_html(note) {
  var m_empty = '<div class="flex f_middle f_center full" style="min-height:130px;">'+ (note.indexOf('search') != -1 ? 'Tidak ditemukan' : firstUCase(bmv_prm_slug) +' Kosong') +'</div>';

  if ((bmhs_arr && bmhs_arr.length == 0) || note == 'empty') {
    el('.member .m-list').innerHTML = m_empty;
  } else {
    bmhs_arr = bmf_bmhs_sort(note, bmhs_arr, el('.member .m-sort').value);
    var m_newtab = bmv_dt_settings.link.indexOf(bmv_prm_slug) != -1;

    var str_bmhs = '';
    str_bmhs += '<div class="post post-list';
    if (bmv_prm_slug == 'history') str_bmhs += ' full-cover';
    str_bmhs += '"><ul class="flex_wrap">';

    for (var i = (bmhs_current-1) * bmhs_max; i < (bmhs_current * bmhs_max) && i < bmhs_arr.length; i++) {
      var slug = 'slug_alt' in bmhs_arr[i] && bmv_dt_settings.source.site in bmhs_arr[i].slug_alt ? bmhs_arr[i].slug_alt[bmv_dt_settings.source.site] : bmhs_arr[i].slug;
      var title = bmhs_arr[i].title == '' ? 'untitled' : bmhs_arr[i].title;
      if (bmv_prm_slug == 'bookmark') {
        str_bmhs += '<li class="flex f_column" data-slug="'+ bmhs_arr[i].slug +'">';
        str_bmhs += '<div class="cover f_grow">';
        str_bmhs += '<a href="#/series/'+ slug;
        if (m_newtab) str_bmhs += '" target="_blank';
        str_bmhs += '"><img style="min-height:225px;" class="radius full_img loading loge lazy1oad" data-src="'+ bmhs_arr[i].cover +'" alt="'+ title +'" title="'+ title +'" referrerpolicy="no-referrer"></a>';
        str_bmhs += '<span class="type m-icon btn radius '+ bmhs_arr[i].type +'"></span>';
        if (bmhs_arr[i].status == 'completed') str_bmhs += '<span class="completed m-icon btn red radius">completed</span>';
        if (bmhs_arr[i].history == 'true') {
          var m_visited = genArray(bmhs_arr[i].hs_visited);
          m_visited = sortBy(m_visited, 'added'); //descanding
          str_bmhs += '<span class="last-read m-icon btn green nowrap" title="Last Read">ch. '+ m_visited[0].number +'</span>';
        }
        str_bmhs += '</div>'; //.cover
        str_bmhs += '<div class="title"><a href="#/series/'+ slug;
        if (m_newtab) str_bmhs += '" target="_blank';
        str_bmhs += '"><h3 class="hd-title clamp">'+ title +'</h3></a></div>';
      } else { //history
        str_bmhs += '<li class="flex f_between" data-slug="'+ bmhs_arr[i].slug +'">';
        str_bmhs += '<div class="cover">';
        str_bmhs += '<a href="#/series/'+ slug;
        if (m_newtab) str_bmhs += '" target="_blank';
        str_bmhs += '"><img style="min-height:130px;" class="radius full_img loading loge lazy1oad" data-src="'+ bmhs_arr[i].cover +'" alt="'+ title +'" title="'+ title +'" referrerpolicy="no-referrer"></a>';
        if (bmhs_arr[i].bookmarked == 'true') str_bmhs += '<span class="bookmarked m-icon btn green" title="Bookmarked"><svg data-name="fa/bookmark" xmlns="http://www.w3.org/2000/svg" width="0.84em" height="1em" viewBox="0 0 1280 1536"><path fill="currentColor" d="M1164 0q23 0 44 9q33 13 52.5 41t19.5 62v1289q0 34-19.5 62t-52.5 41q-19 8-44 8q-48 0-83-32l-441-424l-441 424q-36 33-83 33q-23 0-44-9q-33-13-52.5-41T0 1401V112q0-34 19.5-62T72 9q21-9 44-9h1048z"/></svg></span>';
        if (bmhs_arr[i].status && bmhs_arr[i].status.search(/completed?|tamat/i) != -1) str_bmhs += '<span class="completed m-icon btn red radius">completed</span>';
        str_bmhs += '</div>'; //.cover
        str_bmhs += '<div class="detail">';
        str_bmhs += '<div class="title"><a href="#/series/'+ slug;
        if (m_newtab) str_bmhs += '" target="_blank';
        str_bmhs += '"><h3 class="hd-title nowrap">'+ title +'</h3></a></div>';
        str_bmhs += '<ul class="">';
        var m_visited = genArray(bmhs_arr[i].hs_visited);
        m_visited = sortBy(m_visited, 'added'); //descanding
        for (var j = 0; j < m_visited.length; j++) {
          var chk_site = m_visited[[j]].site != bmv_dt_settings.source.site;
          var m_url = slug +'/'+ m_visited[j].number +'/';
          if (bmv_dt_settings.ch_url) m_url = m_url + encodeURIComponent('url='+ m_visited[j].url +'&');
          m_url = m_url + encodeURIComponent('site='+ m_visited[j].site);
          str_bmhs += '<li class="flex"><';
          if (chk_site) {
            str_bmhs += 'span title="source: '+ m_visited[j].site +'"';
          } else {
            str_bmhs += 'a href="#/chapter/'+ m_url +'"';
            if (m_newtab) str_bmhs += ' target="_blank"';
          }
          str_bmhs += ' class="f_grow f_clamp">Chapter '+ m_visited[j].number +'</'
          str_bmhs += chk_site ? 'span' : 'a';
          str_bmhs += '><span class="time-ago" title="'+ dateLocal(m_visited[j].added) +'">'+ timeDifference(m_visited[j].added) +'</span></li>';
        }
        str_bmhs += '</ul>';
        str_bmhs += '</div>'; //.detail
      }
      str_bmhs += '<label class="delete m-icon btn red no_items"><input type="checkbox"></label>';
      str_bmhs += '</li>';
    }

    str_bmhs += '</ul></div>';
    el('.member .m-list').innerHTML = str_bmhs;
    el('.post-header h1 span').innerHTML = bmhs_arr.length +'/'+ bmv_max_bmhs;

    bmf_bmhs_fnc(note);
    if (note == 'first') bmf_bmhs_length(`member/${bmv_prm_slug}/check`); //if length more than {bmv_max_bmhs}, remove
    bmf_lazyLoad(el('img.lazy1oad', 'all')); //first load
    document.addEventListener('scroll', bmf_page_scroll);
  }
}

function bmf_bmhs_set(note) {
  bmhs_length = Math.ceil(bmhs_arr.length / bmhs_max);
  bmhs_current = note.search(/page-\d/) == -1 || bmhs_current < 1 ? 1 : bmhs_current;
  if (bmhs_current > bmhs_length) bmhs_current = bmhs_length;

  bmf_bmhs_html(note);
  if (note.indexOf('search') != -1 && bmhs_arr.length <= 0) {
    el('.member .m-pagination').classList.add('no_items');
  } else {
    bmf_bmhs_nav_html();
    el('.member .m-pagination').classList.remove('no_items');
  }
}

function bmf_member_bmhs_data(note) {
  el('.member .m-list').classList.remove('loading', 'loge');

  if (bmv_dt_bmhs) {
    bmhs_arr = genArray(bmv_dt_bmhs);
    bmf_bmhs_change(`${bmv_prm_slug}|update`, bmhs_arr.length); //update "check" length on database
    var m_filter = note.indexOf('/search') != -1 ? `title|${el('.ms-form input').value}` : el('.member .m-filter').value;
    bmhs_arr = bmf_bmhs_filter(note, bmhs_arr, m_filter);
    bmf_bmhs_set(note);
  } else { //empty
    bmf_bmhs_change(`${bmv_prm_slug}|remove`, 0); //reset to default
    bmf_bmhs_html('empty');
  }
}

function bmf_profile_db_change(note, data, callback) {
  var c_data = JSON.parse(`{"${note}": "${data}"}`);
  fbase.database().ref(bmf_fbase_path('profile')).update(c_data, function(error) {
    if (error) {
      console.error('!! Error: Firebase '+ info +' bmf_profile_db_change, code: '+ error.code +', message: '+ error.message);
      // alert('!! Error: Firebase '+ info +' bmf_profile_db_change(\n'+ error.message);
    } else {
      callback();
    }
  });
}

function bmf_profile_change(note, info, data, callback) {
  var c_info = info.split('/');
  var c_data = note == 'email' || note == 'password' ? data : JSON.parse(`{"${c_info[1]}": "${data}"}`);

  fbase_user[c_info[0]](c_data).then(function() {
    if (note == 'password') {
      callback();
    } else {
      bmf_profile_db_change(note, data, callback);
    }
    if (note == 'email') bmf_email_verification('reauth', fbase_user);
  }).catch(function(error) {
    console.error('!! Error: Firebase '+ [c_info[0]] +' bmf_profile_change, code: '+ error.code +', message: '+ error.message);
    // alert('!! Error: Firebase '+ [c_info[0]] +' bmf_profile_change(\n'+ error.message);
  });
}

function bmf_profile_save(parent) {
  var pr_data = parent.dataset.edit;
  var m_input = el('input[name]', parent);
  var m_info = pr_data == 'name' ? 'updateProfile' : `update${firstUCase(pr_data)}`;
  m_info += '/'+ (pr_data == 'name' ? 'displayName' : pr_data);

  bmf_profile_change(pr_data, m_info, m_input.value, function() {
    bmf_member_notif(`success/profile_save/${pr_data}-saved`, {timer: 3000, message: 'Berhasil disimpan.'});
    if (pr_data == 'email') {
      el('.m-email').classList.remove('verified');
      el('.m-email .m-input').classList.add('f_top');
      el('.m-email .m-verified').classList.add('not', 'block');
      el('.m-email .m-verified').innerHTML = bmv_settings.l10n.profile.verified_not;
    }
    el('.m-profile .m-detail').classList.remove('loading', 'loge');
    el('.m-edit', parent).classList.remove('no_items');
    el('.m-save', parent).parentElement.classList.add('no_items');
    el('.m-profile .m-'+ pr_data).classList.remove('edit');
    if (pr_data == 'password') {
      el('.m-pass-c', parent).value = '';
      el('.m-pass-n', parent).value = '';
      el('.m-pass-n', parent).parentElement.classList.add('no_items');
      el('input[type=text]', parent).classList.remove('no_items');
    } else {
      m_input.dataset.value = m_input.value;
      m_input.readOnly = true;
    }
  });
}

function bmf_member_profile_fnc() {
  // member: edit button
  el('.m-profile .m-edit', 'all').forEach(function(item) {
    item.addEventListener('click', function() {
      bmf_member_notif('reset');

      var parent = this.parentElement;
      var pr_data = parent.dataset.edit;
      this.classList.add('no_items');
      el('.m-profile .m-'+ pr_data).classList.add('edit');
      if (pr_data != 'cover' && el('.m-save', parent)) el('.m-save', parent).parentElement.classList.remove('no_items');

      if (pr_data == 'cover') {
        el('.m-upload', parent).classList.remove('no_items');
      }

      if (pr_data == 'name' || pr_data == 'email') {
        el('input', parent).readOnly = false;
        el('input', parent).select();

        // https://stackoverflow.com/a/7445389
        // if (pr_data == 'name') {
        //   el('input', parent).focus();
        //   var length = el('input', parent).value.length;
        //   el('input', parent).setSelectionRange(length, length);
        // } else {
        //   el('input', parent).select();
        // }
      }

      if (pr_data == 'password') {
        el('input[type=text]', parent).classList.add('no_items');
        el('.m-pass-n', parent).parentElement.classList.remove('no_items');
        el('.m-pass-n', parent).focus();
      }

      if (pr_data == 'delete') {
        parent.classList.add('wBox', 'active', 'layer');
        el('.m-delete .m-label').classList.add('no_items');
        el('.m-dl-notif', parent).classList.remove('no_items');
        el('.m-dl-export', parent).classList.remove('no_items');
        el('.m-dl-export a', parent).classList.remove('disabled');
      }
    });
  });

  el('.m-delete .m-dl-export a').addEventListener('click', function() {
    this.classList.add('disabled');
  });

  // member: cancel button
  el('.m-profile .m-cancel', 'all').forEach(function(item) {
    item.addEventListener('click', function() {
      bmf_member_notif('reset');
      if (document.getSelection().toString() != '') document.getSelection().collapseToEnd();

      var parent = this.parentElement.parentElement;
      var pr_data = parent.dataset.edit;
      el('.m-edit', parent).classList.remove('no_items');
      el('.m-profile .m-'+ pr_data).classList.remove('edit');

      if (pr_data == 'cover') {
        el('.m-file', parent).value = '';
        el('.m-upload span').innerHTML = 'Choose File...';
        el('.m-upload', parent).classList.add('no_items');
      }

      if (pr_data != 'cover') {
        this.parentElement.classList.add('no_items');
      }

      if (pr_data == 'name' || pr_data == 'email') {
        el('input', parent).value = el('input', parent).dataset.value;
        el('input', parent).readOnly = true;
      }

      if (pr_data == 'password') {
        el('.m-pass-c', parent).value = '';
        el('.m-pass-n', parent).value = '';
        el('.m-pass-n', parent).parentElement.classList.add('no_items');
        el('input[type=text]', parent).classList.remove('no_items');
      }

      if (pr_data == 'delete') {
        parent.classList.remove('wBox', 'active', 'layer');
        el('.m-delete .m-label').classList.remove('no_items');
        el('.m-dl-notif', parent).classList.add('no_items');
        el('.m-dl-export', parent).classList.add('no_items');
      }
    });
  });

  function bmf_rsave(data, text, color) {
    el('.m-reauth .r-save').innerHTML = bmv_settings.l10n.member[text];
    el('.m-reauth .r-save').className = 'r-save btn '+ color +' f_grow';
    el('.m-reauth .r-save').dataset.active = data;
    el('.m-reauth').classList.remove('no_items');
    el('.m-reauth input').focus();
  }

  // member: save button
  el('.m-profile .m-save', 'all').forEach(function(item) {
    item.addEventListener('click', function() {
      bmf_member_notif('reset');

      var parent = this.parentElement.parentElement;
      var pr_data = parent.dataset.edit;
      el('.m-reauth input').placeholder = pr_data == 'password' ? 'Old Password' : 'Password';

      if (pr_data == 'delete') {
        bmf_rsave(pr_data, 'delete', 'red');
        return;
      }

      var in_elem = el('input[name]', parent);
      if (!bmf_member_valid(pr_data, in_elem)) return;

      if (pr_data == 'cover' && el('.m-file', parent).files.length > 0) {
        parent.classList.add('loading', 'loge');
        var file = el('.m-file', parent).files[0];

        var storage_ref = fbase.storage().ref('users').child(fbase_user.uid +'-profile');
        storage_ref.put(file).then(function() {
          storage_ref.getDownloadURL().then(function(url) {
            bmf_profile_change(pr_data, 'updateProfile/photoURL', url, function() {
              parent.classList.remove('loading', 'loge');
              el('.m-file', parent).value = '';
              el('.m-cover label span').innerHTML = 'Choose File...';
              el('.m-edit', parent).classList.remove('no_items');
              el('.m-upload', parent).classList.add('no_items');
              el('img', parent).src = url;
            });
          });
        }).catch(function(error) {
          bmf_member_notif('error/profile-upload/'+ error.code);
          parent.classList.remove('loading', 'loge');
          console.error('!! Error: Firebase storage(), code: '+ error.code +', message: '+ error.message);
          // alert('!! Error: Firebase storage(\n'+ error.message);
        });
      }

      if (pr_data == 'name' || pr_data == 'email') {
        if (in_elem.value == in_elem.dataset.value) return;

        if (pr_data == 'name') {
          el('.m-profile .m-detail').classList.add('loading', 'loge');
          bmf_profile_save(parent);
        } else {
          bmf_rsave(pr_data, 'save', 'green');
        }
      }

      if (pr_data == 'password') {
        if (bmf_member_valid('pass', in_elem) && bmf_member_valid('pass-c', in_elem, el('.m-pass-c', parent))) {
          el('.m-profile .m-detail').classList.add('loading', 'loge');
          var d_user = {"name":fbase_user.displayName,"email":fbase_user.email,"pass":in_elem.value};
          bmf_member_hibp('profile/change-password', d_user, function(pawned) {
            if (pawned) {
              bmf_member_notif('error/profile/change-password', {message: bmv_settings.l10n.member.pass_safe});
            } else {
              bmf_rsave(pr_data, 'save', 'green');
            }
            el('.m-profile .m-detail').classList.remove('loading', 'loge');
          });
        }
      }
    });
  });

  el('.m-cover .m-file').addEventListener('change', function() {
    if (this.files.length > 0) {
      el('.m-cover label span').innerHTML = this.files[0].name;
    } else {
      el('.m-cover label span').innerHTML = 'Choose File...';
    }
  });

  if (el('.m-email .m-verified.not')) {
    el('.m-email .m-verified.not').addEventListener('click', function() {
      if (this.classList.contains('wait')) return;
      el('.m-profile .m-detail').classList.add('loading', 'loge');
      bmf_email_verification('verify', fbase_user, function() {
        if (el('.notif .not_verified')) removeElem('.notif .not_verified');

        el('.m-profile .m-detail').classList.remove('loading', 'loge');
        el('.m-email .m-verified.not').innerHTML = 'Kirim ulang link verifikasi email dalam (<b>60</b>) detik</b>';
        el('.m-email .m-verified.not').classList.add('wait');

        var timeleft = 60;
        var verifyTimer = setInterval(function() {
          timeleft--;
          el('.m-email .m-verified.not b').innerHTML = timeleft;
          if (timeleft <= 0) {
            clearInterval(verifyTimer);
            el('.m-email .m-verified.not').classList.remove('wait');
            el('.m-email .m-verified.not').innerHTML = bmv_settings.l10n.profile.verified_not;
          }
        }, 1000);
      });
    });
  }

  // member: reauthenticate save
  el('.m-reauth .r-save').addEventListener('click', function() {
    bmf_member_notif('reset');

    if (bmf_member_valid('pass', el('.m-reauth input'))) {
      var credential = firebase.auth.EmailAuthProvider.credential(fbase_user.email, el('.m-reauth input').value);
      el('.m-reauth').classList.add('no_items');
      el('.m-reauth input').value = ''; //reset
      el('.m-profile .m-detail').classList.add('loading', 'loge');

      fbase_user.reauthenticateWithCredential(credential).then(function() {
        var a_data = el('.m-reauth .r-save').dataset.active;
        if (a_data == 'delete') {
          // remove user data from storage
          bmf_fbase_storage_delete(fbase_user.uid, function() {
            console.warn(`Firebase: user ${fbase_user.email} removed from storage`);
            // remove user data from database
            bmf_fbase_db_remove('remove-user', `users/${fbase_user.uid}`, function() {
              console.warn(`Firebase: user ${fbase_user.email} removed from database`);
              // remove user from users list
              fbase_user.delete().then(function() {
                console.warn(`Firebase: user account ${fbase_user.email} removed`);
                wl.hash = '#/latest';
              }).catch(function(error) {
                console.error('!! Error: Firebase user delete(), code: '+ error.code +', message: '+ error.message);
                alert('!! Error: Firebase user delete(\n'+ error.message);
              });
            });
          });
        } else {
          bmf_profile_save(el(`.m-profile .m-${a_data} .m-input`));
        }
      }).catch(function(error) {
        bmf_member_notif('error/reauth/'+ error.code);
        el('.m-profile .m-detail').classList.remove('loading', 'loge');
        console.error('!! Error: Firebase reauthenticateWithCredential, code: '+ error.code +', message: '+ error.message);
        // alert('!! Error: Firebase reauthenticateWithCredential(\n'+ error.message);
      });
    }
  });

  el('.m-reauth .r-cancel').addEventListener('click', function() {
    el('.m-reauth input').value = '';
    el('.m-reauth').classList.add('no_items');
  });
}

function bmf_settings_changed(elem) {
  // note: 'theme' is excluded
  var st_changed;
  var st_id = (elem.id || elem.name).replace(/^st-/, '').replace(/-/g, '_');
  var st = bmv_dt_settings[st_id];

  if (elem.type == 'radio') {
    el(`#${elem.name} input`, 'all').forEach(function(item){ item.parentElement.classList.remove('highlighted'); });
    elem = el(`#${elem.name} input:checked`);
    if (st_id == 'source') st = bmv_dt_settings.source.site;
    st_changed = elem.value != st;
  } else if (elem.type == 'checkbox') {
    if (st_id == 'link') {
      st_changed = elem.checked && st.indexOf(elem.value) == -1 || !elem.checked && st.indexOf(elem.value) != -1;
    } else {
      st_changed = elem.checked !== st;
    }
  } else {
    st_changed = elem.value != st;
  }
  return st_changed;
}

function bmf_settings_fill(settings) {
  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') {
    el(`.st-source input[value="${settings.source.site}"]`).checked = true;
    el('.st-sr-copy input').checked = settings.sr_copy;
    el('.st-sr-list input').checked = settings.sr_list;
    el('.st-ch-menu input').checked = settings.ch_menu;
    el('.st-ch-index input').checked = settings.ch_index;
    el('.st-src-link input').checked = settings.src_link;
    el('.st-cm-load input').checked = settings.cm_load;

    el(`.st-cdn input[value="${settings.cdn}"]`).checked = true;
    if (!el('.st-ch-menu input').checked) {
      el('#st-cdn').classList.add('disabled');
      el('.st-cdn input', 'all').forEach(function(item){ item.disabled = true; });
    }
  }

  el(`.st-theme input[value="${settings.theme}"]`).checked = true;

  el('.st-cache input').value = settings.cache;
  el('#st-hs-stop').checked = settings.hs_stop;
  el('#st-img-resize').checked = settings.img_resize;
  el('#st-ch-url').checked = settings.ch_url;

  el('.st-quality input').value = settings.resize_quality;
  if (!el('#st-img-resize').checked) {
    el('.st-quality').classList.add('disabled');
    el('.st-quality input').disabled = true;
  }

  var link_list = settings.link.split(', ');
  link_list.forEach(function(item) {
    el(`.st-link input[value="${item}"]`).checked = true;
  });
}

function bmf_member_settings_fnc() {
  el('.st-reset button').disabled = false;
  el('.st-save button').disabled = false;
  el('.st-control').classList.remove('loading', 'loge');

  if (!(bmv_dt_settings.source.site in bmv_settings.source)) bmv_dt_settings.source = bmv_settings.default.source;
  bmf_settings_fill(bmv_dt_settings);

  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') {
    el('.st-ch-menu input').addEventListener('input', function() {
      var cdn_el = el(`.st-cdn input[value="${bmv_dt_settings.cdn}"]`);
      cdn_el.checked = true;
      triggerEvent('change', cdn_el);
      el('#st-cdn').classList.toggle('disabled');
    });
  }

  el('.st-theme input', 'all').forEach(function(item) {
    item.addEventListener('input', function(event) {
      bmv_dt_settings.theme = this.value;
      bmf_toggle_dark(event);
    });
  });

  el('.st-cache input').addEventListener('input', function(e) {
    var cache = Number(this.value);
    if (cache < Number(e.target.min)) this.value = e.target.min;
    if (cache > Number(e.target.max)) this.value = e.target.max;
  });

  el('#st-img-resize').addEventListener('input', function() {
    el('.st-quality').classList.toggle('disabled');
    el('.st-quality input').value = bmv_dt_settings.resize_quality;
    triggerEvent('change', el('.st-quality input'));
    el('.st-quality input').disabled = this.checked ? false : true;
  });

  el('.st-quality input').addEventListener('input', function(e) {
    var quality = Number(this.value);
    if (quality < Number(e.target.min)) this.value = e.target.min;
    if (quality > Number(e.target.max)) this.value = e.target.max;
  });

  el('.st-control input', 'all').forEach(function(item) {
    item.addEventListener('change', function(e) {
      if (bmf_settings_changed(e.target)) {
        el('.st-save button').classList.add('pulse');
        this.parentElement.classList.add('highlighted');
        window.addEventListener('beforeunload', beforeUnloadListener);
      } else {
        this.parentElement.classList.remove('highlighted');
        if (!el('.st-control .highlighted')) {
          el('.st-save button').classList.remove('pulse');
          window.removeEventListener('beforeunload', beforeUnloadListener);
        }
      }
    });
  });

  el('.st-reset button').addEventListener('click', function() {
    el('.m-confirm').classList.remove('no_items');
    el('.m-confirm input').focus();
  });

  el('.m-confirm .dc-remove').addEventListener('click', function() {
    if (el('.m-confirm input').value == `reset-all-${bmv_prm_slug}`) {
      el('.m-confirm').classList.add('no_items');
      var st_new = bmf_update_settings('reset', bmv_settings.default);
      bmf_settings_fill(st_new);

      el('.st-reset button').disabled = true;
      el('.st-save button').classList.add('pulse');
      el('.st-control').classList.add('loading', 'loge');
      window.addEventListener('beforeunload', beforeUnloadListener);
    }
  });

  el('.m-confirm .dc-cancel').addEventListener('click', function() {
    el('.m-confirm').classList.add('no_items');
  });

  el('.st-save button').addEventListener('click', function() {
    this.disabled = true;
    this.classList.remove('pulse');
    bmf_member_notif('reset');
    el('.st-reset button').disabled = true;
    el('.st-control').classList.add('loading', 'loge');

    if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') {
      var st_source = bmv_settings.source[el('.st-source input:checked').value];
      bmv_dt_settings['source']['type'] = st_source.type;
      bmv_dt_settings['source']['site'] = st_source.site;
      bmv_dt_settings['sr_copy'] = el('.st-sr-copy input').checked;
      bmv_dt_settings['sr_list'] = el('.st-sr-list input').checked;
      bmv_dt_settings['ch_menu'] = el('.st-ch-menu input').checked;
      bmv_dt_settings['ch_index'] = el('.st-ch-index input').checked;
      bmv_dt_settings['cdn'] = el('.st-cdn input:checked').value;
      bmv_dt_settings['src_link'] = el('.st-src-link input').checked;
      bmv_dt_settings['cm_load'] = el('.st-cm-load input').checked;
    }

    bmv_dt_settings['theme'] = el('.st-theme input:checked').value;
    bmv_dt_settings['cache'] = el('.st-cache input').value;
    bmv_dt_settings['resize_quality'] = el('.st-quality input').value;
    bmv_dt_settings['hs_stop'] = el('#st-hs-stop').checked;
    bmv_dt_settings['img_resize'] = el('#st-img-resize').checked;
    bmv_dt_settings['ch_url'] = el('#st-ch-url').checked;

    var link_list = [];
    el('.st-link input:checked', 'all').forEach(function(item) {
      link_list.push(item.value);
    });
    bmv_dt_settings['link'] = link_list.join(', ');

    fbase.database().ref(bmf_fbase_path('settings')).update(bmv_dt_settings, function(error) {
      el('.st-control').classList.remove('loading', 'loge');
      if (error) {
        console.error('!! Error: Firebase "save" bmf_member_settings_fnc, code: '+ error.code +', message: '+ error.message);
        alert('!! Error: Firebase "save" bmf_member_settings_fnc(\n'+ error.message);
      } else {
        if (bmv_dt_settings.theme == 'system') {
          local('remove', 'bmv_theme');
        } else{
          local('set', 'bmv_theme', bmv_dt_settings.theme);
        }
        var local_time = new Date().getTime();
        bmv_dt_settings['update'] = local_time;
        local('set', 'bmv_user_settings', JSON.stringify(bmv_dt_settings));
        bmv_dt_settings = bmf_update_settings('update', bmv_dt_settings);
        bmf_fbase_db_change('settings|check', bmf_fbase_path('check/settings'), 'set', {update: local_time});
        bmf_member_notif('success/settings', {timer: 3000, message: 'Pengaturan telah disimpan.'});
        el('.st-control .highlighted', 'all').forEach(function(item){ item.classList.remove('highlighted'); });
        window.removeEventListener('beforeunload', beforeUnloadListener);
        document.body.scrollIntoView();
        el('.st-save button').disabled = false;
        el('.st-reset button').disabled = false;
      }
    });
  });
}

function bmf_member_settings_html() {
  // Display "settings" html
  var str_settings = '';
  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') {
    str_settings += '<div class="st-adv bg2 layer">';
    str_settings += '<div class="st-source st-list" id="st-source">';
    str_settings += '<h2>API Sources</h2>';
    for (var site in bmv_settings.source) {
      str_settings += `<label class="radio" title="${bmv_settings.source[site].type}"><input type="radio" name="st-source" value="${site}"><span></span>${site}`;
      if ('lang' in bmv_settings.source[site]) str_settings += ` (${bmv_settings.source[site].lang})`;
      str_settings += '</label>';
    }
    str_settings += '</div>'; //.st-source
    str_settings += '<div class="st-series st-list">';
    str_settings += '<h2>Advanced Series</h2>';
    str_settings += '<label class="checkbox st-sr-copy"><input type="checkbox" id="st-sr-copy"><span></span>Show <code>sr_copy</code></label>';
    str_settings += '<label class="checkbox st-sr-list"><input type="checkbox" id="st-sr-list"><span></span>Show <code>sr_list</code> (mobile)</label>';
    str_settings += '</div>'; //.st-series
    str_settings += '<div class="st-chapters st-list">';
    str_settings += '<h2>Advanced Chapter</h2>';
    str_settings += '<label class="checkbox st-ch-menu"><input type="checkbox" id="st-ch-menu"><span></span>Show <code>ch_menu</code></label>';
    str_settings += '<label class="checkbox st-ch-index"><input type="checkbox" id="st-ch-index"><span></span>Show <code>ch_index</code></label>';
    str_settings += '</div>'; //.st-chapters
    str_settings += '<div class="st-cdn st-list" id="st-cdn">';
    str_settings += '<h2>CDN for chapter</h2>';
    str_settings += '<label class="radio"><input type="radio" name="st-cdn" value="default"><span></span>Default</label>';
    str_settings += '<label class="radio"><input type="radio" name="st-cdn" value="not"><span></span>Remove CDN (not)</label>';
    str_settings += '<label class="radio"><input type="radio" name="st-cdn" value="statically"><span></span>statically.io</label>';
    str_settings += '<label class="radio"><input type="radio" name="st-cdn" value="wp"><span></span>wp.com</label>';
    str_settings += '<label class="radio"><input type="radio" name="st-cdn" value="imagecdn"><span></span>imagecdn.app</label>';
    str_settings += '<label class="radio"><input type="radio" name="st-cdn" value="imageoptim"><span></span>imageoptim.com</label>';
    str_settings += '</div>'; //.st-cdn
    str_settings += '<div class="st-src-link st-list">';
    str_settings += '<h2>Source Link</h2>';
    str_settings += '<label class="checkbox"><input type="checkbox" id="st-src-link"><span></span>Show <code>src_link</code></label>';
    str_settings += '</div>'; //.st-src-link
    str_settings += '<div class="st-comments st-list">';
    str_settings += '<h2>Comments</h2>';
    str_settings += '<label class="checkbox st-cm-load"><input type="checkbox" id="st-cm-load"><span></span>Auto Load (chapter)</label>';
    str_settings += '</div>'; //.st-comments
    str_settings += '</div>'; //.st-adv
  }
  str_settings += '<div class="st-general bg2 layer radius">';
  str_settings += '<div class="st-theme st-list" id="st-theme">';
  str_settings += '<h2>Tema Situs</h2>';
  str_settings += '<label class="radio"><input type="radio" name="st-theme" value="system"><span></span>System</label>';
  str_settings += '<label class="radio"><input type="radio" name="st-theme" value="light"><span></span>Terang</label>';
  str_settings += '<label class="radio"><input type="radio" name="st-theme" value="dark"><span></span>Gelap</label>';
  str_settings += '</div>'; //.st-theme
  str_settings += '<div class="st-cache st-list">';
  str_settings += '<h2>Cache Timer</h2>';
  str_settings += '<div><input type="number" id="st-cache" min="10" max="60" step="5" placeholder="30"> minutes (api data)</div>';
  str_settings += '</div>'; //.st-cache

  str_settings += '<div class="st-chapter st-list">';
  str_settings += '<h2>Chapter</h2>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-img-resize"><span></span>Image Resize (mobile)</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-hs-stop"><span></span>Berhenti merekam histori bacaan</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-ch-url"><span></span>Gunakan <code>url</code> pada link chapter</label>';
  str_settings += '</div>'; //.st-chapter
  str_settings += '<div class="st-quality st-list">';
  str_settings += '<h2>Image Resize Quality</h2>';
  str_settings += '<div><input type="number" id="st-quality" min="0" max="100" placeholder="50"> percent</div>';
  str_settings += '</div>'; //.st-quality

  str_settings += '<div class="st-link st-list">';
  str_settings += '<h2>Buka link di tab baru</h2>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="latest"><span></span>Link di Beranda</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="search"><span></span>Link di Pencarian</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="series"><span></span>Link di Series</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="chapter-bc"><span></span>Link di Chapter (breadcrumb)</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="chapter-img"><span></span>Link di Chapter (gambar)</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="chapter-np"><span></span>Link di Chapter (next-prev)</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="bookmark"><span></span>Link di Bookmark (member)</label>';
  str_settings += '<label class="checkbox"><input type="checkbox" id="st-link" value="history"><span></span>Link di History (member)</label>';
  str_settings += '</div>'; //.st-link
  str_settings += '</div>'; //.st-general
  el('.st-control').innerHTML = str_settings;
  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') el('.m-settings').classList.add('m-pro');

  bmf_fbase_db_get('member/settings', bmf_fbase_path('settings'), function(res) {
    if (res.exists()) bmv_dt_settings = bmf_update_settings('member/database', res.val());
    bmf_member_settings_fnc();
  });
}

// signup, login, forgot
function bmf_member_form_fnc() {
  var m_parent = el('.member .content');

  // prevent form submit
  if (el('form', m_parent)) {
    el('.member .m-form').addEventListener('submit', function(e) {
      e.preventDefault();
    });
  }

  if (bmv_prm_slug == 'login') {
    el('.form-login .m-submit').addEventListener('click', function() {
      bmf_member_notif('reset');

      var m_email = el('.form-login .m-email');
      var m_pass = el('.form-login .m-pass');

      if (bmf_member_valid('email', m_email) && bmf_member_valid('pass', m_pass)) {
        m_parent.classList.add('loading', 'loge');

        fbase.auth().signInWithEmailAndPassword(m_email.value, m_pass.value).then(function(user) {
          if (wl.href.indexOf('continue=') != -1) {
            wl.hash = bmf_getParam('continue', wl.href.replace(/\/#/, ''))[0];
          } else {
            wl.hash = '#/member/profile';
          }
        }).catch(function(error) {
          var m_msg = error.code == 'auth/user-disabled' ? `Akun dengan email <b>${m_email.value}</b> telah dinonaktifkan.<br><a href="#/contact" target="_blank">Hubungi Admin</a> untuk mengaktifkan kembali.` : null;
          bmf_member_notif('error/login/'+ error.code, {message: m_msg});
          m_parent.classList.remove('loading', 'loge');
          console.error('!! Error: Firebase signInWithEmailAndPassword, code: '+ error.code +', message: '+ error.message);
          // alert('!! Error: Firebase signInWithEmailAndPassword(\n'+ error.message);
        });
      }
    });
  }

  if (bmv_prm_slug == 'forgot') {
    el('.form-forgot .m-submit').addEventListener('click', function() {
      bmf_member_notif('reset');
      var m_email = el('.form-forgot .m-email');

      if (bmf_member_valid('email', m_email)) {
        m_parent.classList.add('loading', 'loge');

        fbase.auth().languageCode = 'id';
        fbase.auth().sendPasswordResetEmail(m_email.value, {url:`${bmv_homepage}#/member/login`}).then(function() {
          m_parent.classList.remove('loading', 'loge');
          bmf_member_notif('success/forgot/password-reset', {message:'<span class="success">Link reset password terkirim ke <b>'+ m_email.value +'</b></span>\nSilahkan cek folder "Kotak Masuk" atau "Spam" di email.'});
        }).catch(function(error) {
          console.error('!! Error: Firebase sendPasswordResetEmail, code: '+ error.code +', message: '+ error.message);
          alert('!! Error: Firebase sendPasswordResetEmail(\n'+ error.message);
        });
      }
    });
  }

  if (bmv_prm_slug == 'signup') {
    el('.form-signup .m-submit').addEventListener('click', function() {
      bmf_member_notif('reset');

      var m_name = el('.form-signup .m-name');
      var m_email = el('.form-signup .m-email');
      var m_pass = el('.form-signup .m-pass');
      var m_pass_c = el('.form-signup .m-pass-c');

      if (bmf_member_valid('name', m_name) && bmf_member_valid('email', m_email) && bmf_member_valid('pass', m_pass) && bmf_member_valid('pass-c', m_pass, m_pass_c)) {
        m_parent.classList.add('loading', 'loge');

        var d_user = {"name":m_name.value,"email":m_email.value,"pass":m_pass.value};
        bmf_member_hibp('signup', d_user, function(pawned) {
          if (pawned) {
            bmf_member_notif('error/signup', {message: bmv_settings.l10n.member.pass_safe});
            m_parent.classList.remove('loading', 'loge');
          } else {
            fbase.auth().createUserWithEmailAndPassword(m_email.value, m_pass.value).then(function(res) {
              fbase_user = res.user;

              fbase_user.updateProfile({ displayName: m_name.value }).catch(function(error) {
                console.error('!! Error: Firebase (signup) set name to displayName, code: '+ error.code +', message: '+ error.message);
                alert('!! Error: Firebase (signup) set name to displayName\n'+ error.message);
              });

              var m_profile = bmf_fbase_gen('signup', {name: m_name.value, email: m_email.value});
              fbase.database().ref(bmf_fbase_path('profile')).set(m_profile, function(error) {
                if (error) {
                  console.error('!! Error: Firebase (signup) set profile to database, code: '+ error.code +', message: '+ error.message);
                  alert('!! Error: Firebase (signup) set profile to database\n'+ error.message);
                } else {
                  bmf_email_verification('signup', fbase_user, function() {
                    wl.hash = '#/member/profile';
                  });
                }
              });
            }).catch(function(error) {
              bmf_member_notif('error/signup/'+ error.code);
              m_parent.classList.remove('loading', 'loge');
              console.error('!! Error: Firebase createUserWithEmailAndPassword, code: '+ error.code +', message: '+ error.message);
              // alert('!! Error: Firebase createUserWithEmailAndPassword(\n'+ error.message);
            });
          }
        });
      }
    });
  }
}

function bmf_member_fnc() {
  if (fbase_login) {
    if (bmv_prm_slug == 'profile') {
      bmf_member_profile_fnc();
      if (cookies.get('bmv_signup_verify') && !fbase_user.emailVerified) {
        if (el('.notif .not_verified')) removeElem('.notif .not_verified');
        bmf_member_notif('success/signup/email-verification');
      }
    }
    if (bmv_prm_slug == 'bookmark' || bmv_prm_slug == 'history') {
      var order_by = bmv_prm_slug == 'bookmark' ? 'bookmarked' : bmv_prm_slug;
      bmf_fbase_db_get(`member${bmv_prm_slug}`, bmf_fbase_path('series'), function(res) {
        bmv_dt_bmhs = res.val();
        bmf_member_bmhs_data('first');
        document.addEventListener('keyup', bmf_bmhs_key);
      }, `equal|${order_by}|true`);
    }
    if (bmv_prm_slug == 'settings') {
      var wait_tier = setInterval(function() {
        if ('tier' in fbase_user) {
          clearInterval(wait_tier);
          bmf_member_settings_html();
        }
      }, 100);
    }
  } else {
    bmf_member_form_fnc();
  }
}

function bmf_build_member() {
  // Display "member" page
  var str_member = '';

  str_member += '<div class="post-header flex_wrap f_middle">';
  str_member += '<h1 class="title">Member: '+ firstUCase(bmv_prm_slug) +' <span></span></h1>';
  str_member += '<span class="f_grow"></span>';
  if (fbase_login) {
    if (bmv_prm_slug == 'bookmark' || bmv_prm_slug == 'history') {
      str_member += '<div class="m-delete flex f_middle no_items">';
      str_member += '<span class="m-delete-btn btn btn-icon red" title="Delete"><svg data-name="bx/trash" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 20a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8h2V6h-4V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v2H3v2h2zM9 4h6v2H9zM8 8h9v12H7V8z"/><path fill="currentColor" d="M9 10h2v8H9zm4 0h2v8h-2z"/></svg></span>';
      str_member += '<span class="m-delete-all m-space-h btn red no_items">'+ bmv_settings.l10n.member.delete_all +'</span>';
      str_member += '</div>'; //.m-delete
    }
    if (bmv_prm_slug == 'settings') {
      str_member += '<div class="flex f_middle">';
      str_member += '<div class="st-reset m-space-h" title="Reset all settings to default"><button class="btn btn-icon" disabled><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3s-3 1.331-3 3s1.329 3 3 3z"/><path fill="currentColor" d="M20.817 11.186a8.94 8.94 0 0 0-1.355-3.219a9.053 9.053 0 0 0-2.43-2.43a8.95 8.95 0 0 0-3.219-1.355a9.028 9.028 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a6.961 6.961 0 0 1 2.502 1.053a7.005 7.005 0 0 1 1.892 1.892A6.967 6.967 0 0 1 19 13a7.032 7.032 0 0 1-.55 2.725a7.11 7.11 0 0 1-.644 1.188a7.2 7.2 0 0 1-.858 1.039a7.028 7.028 0 0 1-3.536 1.907a7.13 7.13 0 0 1-2.822 0a6.961 6.961 0 0 1-2.503-1.054a7.002 7.002 0 0 1-1.89-1.89A6.996 6.996 0 0 1 5 13H3a9.02 9.02 0 0 0 1.539 5.034a9.096 9.096 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9.09 9.09 0 0 0 1.814-.183a9.014 9.014 0 0 0 3.218-1.355a8.886 8.886 0 0 0 1.331-1.099a9.228 9.228 0 0 0 1.1-1.332A8.952 8.952 0 0 0 21 13a9.09 9.09 0 0 0-.183-1.814z"/></svg></button></div>';
      str_member += '<div class="st-save"><span class="st-content"><button class="btn" disabled>'+ bmv_settings.l10n.member.save +'</button></span></div>';
      str_member += '</div>';
    }
  }
  str_member += '</div>'; //.post-header
  str_member += '<div class="content '+ (fbase_login ? 'login' : 'not-login bg2 layer radius') +'">';
  str_member += '<div class="m-notif m-text t_center'+ (fbase_login ? ' bg2 layer radius' : '') +' no_items"></div>';
  if (fbase_login) {
    if (bmv_prm_slug == 'bookmark' || bmv_prm_slug == 'history') {
      str_member += '<div class="m-nav flex_wrap full radius no_items">';
      str_member += '<select class="m-sort mn-menu'+ (is_mobile ? ' f_grow' : '') +'"><option value="" disabled>Sort by</option>';
      if (bmv_prm_slug == 'bookmark') {
        str_member += '<option value="bm_added" selected>Added</option>';
      } else { //history
        str_member += '<option value="hs_update" selected>Update</option>';
      }
      str_member += '<option value="title|asc">A-Z</option><option value="title">Z-A</option>';
      str_member += '</select>'; //.m-sort
      str_member += '<select class="m-filter mn-menu'+ (is_mobile ? ' f_grow' : '') +'" data-value="">';
      str_member += '<option value="" selected disabled>Filter by</option><option value="">All</option>';
      if (bmv_prm_slug == 'bookmark') {
        str_member += '<option value="status|ongoing">Ongoing</option><option value="status|completed">Completed</option><option disabled>---------------</option>';
        for (var i in bmv_genres) {
          str_member += '<option value="genre|'+ bmv_genres[i] +'">'+ firstUCase(bmv_genres[i]) +'</option>';
        }
      } else { //history
        str_member += '<option value="bookmarked|true">Bookmarked</option><option value="bookmarked|false">Not Bookmarked</option>';
      }
      str_member += '</select>'; //.m-filter
      str_member += '<div class="nav-delete flex no_items">';
      str_member += '<div class="m-delete-select mn-menu btn red t_center">'+ bmv_settings.l10n.member.delete +'</div>';
      str_member += '<div class="m-select-all mn-menu flex f_middle"><label><input type="checkbox">'+ bmv_settings.l10n.member.select_all +'</label></div>';
      str_member += '</div>'; //.nav-delete
      if (!is_mobile) str_member += '<span class="f_grow"></span>';
      str_member += '<div class="ms-form flex f_middle">';
      str_member += '<span class="ms-reset btn red radius mn-menu no_items">'+ bmv_settings.l10n.member.reset +'</span>';
      str_member += '<input class="ms-field radius f_grow" type="search" placeholder="'+ bmv_settings.l10n.title +'..." value=""/>';
      str_member += '<span class="ms-search btn"><svg data-name="zondicons/search" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><path fill="currentColor" d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33l-1.42 1.42l-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/></svg></span>';
      str_member += '</div>'; //.ms-form
      str_member += '</div>'; //.m-nav
    }
    str_member += '<div class="m-'+ bmv_prm_slug +' flex_wrap';
    if (bmv_prm_slug == 'profile') {
      str_member += ' f_between">';
      str_member += '<div class="m-cover" data-edit="cover">';
      str_member += '<img class="full_img loading loge radius" src="'+ (fbase_user.photoURL ? fbase_user.photoURL : 'https://www.gravatar.com/avatar/?d=mp&s=160') +'" alt="Profile Picture">';
      str_member += '<button class="m-edit btn full m-space-v">'+ bmv_settings.l10n.member.edit +'</button>';
      str_member += '<div class="m-upload flex_wrap no_items">';
      str_member += '<label class="m-space-v full"><input class="m-file no_items" type="file" name="avatar" accept="image/png, image/jpeg, image/gif" required><span class="nowrap block">Choose File...</span></label>';
      str_member += '<button class="m-save btn">'+ bmv_settings.l10n.member.save +'</button>';
      str_member += '<button class="m-cancel btn selected">'+ bmv_settings.l10n.member.cancel +'</button>';
      str_member += '</div>'; //.m-upload
      str_member += '</div>'; //.m-cover
      str_member += '<div class="m-detail bg2 layer radius">';
      str_member += '<div class="m-id mp-list flex_wrap">';
      str_member += '<div class="m-label f_grow">UID</div>';
      str_member += '<div class="m-input"><div class="m-id-i">'+ fbase_user.uid +'</div></div>';
      str_member += '</div>'; //.m-id
      str_member += '<div class="m-name mp-list flex_wrap">';
      str_member += '<div class="m-label f_grow">'+ bmv_settings.l10n.form.name +'</div>';
      str_member += '<div class="m-input flex_wrap" data-edit="name">';
      str_member += '<div class="m-name-i f_grow">';
      var m_name = fbase_user.displayName ? fbase_user.displayName : '';
      str_member += '<input class="full" type="text" name="log" placeholder="Name" value="'+ m_name +'" data-value="'+ m_name +'" required readonly>';
      if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') str_member += '<span class="m-pro">pro</span>';
      str_member += '</div>'; //.m-name-i
      str_member += '<button class="m-edit btn">'+ bmv_settings.l10n.member.edit +'</button>';
      str_member += '<div class="flex no_items"><button class="m-save btn m-space-h">'+ bmv_settings.l10n.member.save +'</button><button class="m-cancel btn selected">'+ bmv_settings.l10n.member.cancel +'</button></div>';
      str_member += '</div>'; //.m-input
      str_member += '</div>'; //.m-name
      str_member += '<div class="m-email mp-list mp-list flex_wrap'+ (fbase_user.emailVerified ? ' verified' : '') +'">';
      str_member += '<div class="m-label f_grow">'+ bmv_settings.l10n.form.email +'</div>';
      str_member += '<div class="m-input flex_wrap'+ (fbase_user.emailVerified ? '' : ' f_top') +'" data-edit="email">';
      str_member += '<div class="m-email-i f_grow">';
      str_member += '<input class="full" type="email" name="email" placeholder="Email" value="'+ fbase_user.email +'" data-value="'+ fbase_user.email +'" required readonly>';
      if (fbase_user.emailVerified) {
        str_member += '<span class="m-verified" title="Email Verified">&#10004;</span>';
      } else {
        str_member += '<span class="m-verified not block">'+ bmv_settings.l10n.profile.verified_not +'</span>';
      }
      str_member += '</div>'; //.m-email-i
      str_member += '<button class="m-edit btn">'+ bmv_settings.l10n.member.edit +'</button>';
      str_member += '<div class="flex no_items"><button class="m-save btn m-space-h">'+ bmv_settings.l10n.member.save +'</button><button class="m-cancel btn selected">'+ bmv_settings.l10n.member.cancel +'</button></div>';
      str_member += '</div>'; //.m-input
      str_member += '</div>'; //.m-email
      str_member += '<div class="m-password mp-list flex_wrap">';
      str_member += '<div class="m-label f_grow">'+ bmv_settings.l10n.form.password +'</div>';
      str_member += '<div class="m-input flex_wrap f_top" data-edit="password">';
      str_member += '<input class="f_grow" type="text" placeholder="••••••••" value="" readonly>';
      str_member += '<div class="f_grow no_items"><input class="m-pass-n full" type="password" name="password" placeholder="New Password" value="" minlength="8" required><input class="m-pass-c m-space-v full" type="password" name="password" placeholder="Confirm New Password" value="" minlength="8" required></div>';
      str_member += '<button class="m-edit btn">'+ bmv_settings.l10n.member.edit +'</button>';
      str_member += '<div class="flex no_items"><button class="m-save btn m-space-h">'+ bmv_settings.l10n.member.save +'</button><button class="m-cancel btn selected">'+ bmv_settings.l10n.member.cancel +'</button></div>';
      str_member += '</div>'; //.m-input
      str_member += '</div>'; //.m-password
      str_member += '<div class="m-delete mp-list m-trigger flex_wrap">';
      str_member += '<div class="m-label f_grow">'+ bmv_settings.l10n.profile.delete_account +'</div>';
      str_member += '<div class="m-input flex_wrap" data-edit="delete">';
      str_member += '<button class="m-edit btn red selected">'+ bmv_settings.l10n.profile.delete_permanent +'</button>';
      str_member += '<div class="m-dl-notif full t_center no_items">'+ bmv_settings.l10n.profile.delete_notif +'</div>';
      str_member += `<div class="m-dl-export full t_center m-space-v no_items">${bmv_settings.l10n.profile.export_account}, <a href="${api_path}/firebase/backup.php?tz=${encodeURIComponent(timezone)}&uid=${fbase_user.uid}&export" download>${bmv_settings.l10n.profile.export_all}</a></div>`;
      str_member += '<div class="flex f_center full no_items"><button class="m-save btn red m-space-h">'+ bmv_settings.l10n.member.delete +'</button><button class="m-cancel btn selected">'+ bmv_settings.l10n.member.cancel +'</button></div>';
      str_member += '</div>'; //.m-input
      str_member += '</div>'; //.m-delete
      str_member += '</div>'; //.m-detail
    } else if (bmv_prm_slug == 'bookmark') {
      str_member += '">';
      str_member += '<div class="full"><div class="m-text t_center bg2 layer radius">'+ firstUCase(bmv_prm_slug) +' '+ bmv_settings.l10n.bmhs.info +' <b>'+ bmv_max_bmhs +'</b></div></div>';
      str_member += '<div class="m-bm-list m-list full loading loge"><div class="flex f_middle f_center full" style="min-height:225px;"></div></div>';
      str_member += '<div class="m-bm-pagination m-pagination flex_wrap f_center f_middle full"></div>';
    } else if (bmv_prm_slug == 'history') {
      str_member += '">';
      str_member += '<div class="m-hs-list m-list full loading loge"><div class="flex f_middle f_center full" style="min-height:225px;"></div></div>';
      str_member += '<div class="m-hs-pagination m-pagination flex_wrap f_center f_middle full"></div>';
    } else { //settings
      str_member += '">';
      str_member += '<div class="st-control full in-check loading loge">';
      str_member += '<div style="min-height:'+ bmv_half_screen +'px;"></div>';
      str_member += '</div>'; //.st-control
    }
    str_member += '</div>';
    if (bmv_prm_slug == 'profile') str_member += '<div class="m-reauth flex f_perfect no_items"><div class="fp_content"><div class="fp_content"><input type="password" name="password" placeholder="Password" autocomplete="off"><div class="flex full f_between m-space-v"><button class="r-save btn f_grow" data-active=""></button><button class="r-cancel btn selected f_grow" style="margin-left:15px;">'+ bmv_settings.l10n.member.cancel +'</button></div></div></div>';
    var dc_text = bmv_prm_slug == 'settings' ? 'reset' : 'delete';
    if (bmv_prm_slug.search(/bookmark|history|settings/) != -1) str_member += '<div class="m-confirm flex f_perfect no_items"><div class="fp_content wBox bg2 layer"><div class="fp_content"><p><b>Are you absolutely sure?</b></p><p class="m-space-v">This action will <b>permanently</b> '+ dc_text +' all '+ bmv_prm_slug +' data and <b>cannot</b> be undone.</p><p>Please type <b class="no_select">'+ dc_text +'-all-'+ bmv_prm_slug +'</b> to confirm.</p><input class="full m-space-v" type="text" name="verify" placeholder="'+ dc_text +'-all-'+ bmv_prm_slug +'" autocomplete="off"><div class="flex full f_between"><button class="dc-remove btn red f_grow">'+ bmv_settings.l10n.member[bmv_prm_slug == 'settings' ? 'reset' : 'delete'] +'</button><button class="dc-cancel btn selected f_grow" style="margin-left:15px;">'+ bmv_settings.l10n.member.cancel +'</button></div></div></div>';
  } else {
    str_member += '<form class="m-form form-'+ bmv_prm_slug +'" onsubmit="return false">';
    str_member += '<div class="form-email">';
    if (bmv_prm_slug == 'forgot') str_member += '<div class="m-text t_center">'+ bmv_settings.l10n.member.forgot_info +'</div>';
    if (bmv_prm_slug == 'signup') str_member += '<div class="m-group flex_wrap"><label for="name">'+ bmv_settings.l10n.form.name +'</label><input type="text" name="log" class="m-name full" id="name" placeholder="Name" value="" required></div>';
    str_member += '<div class="m-group flex_wrap"><label for="email">'+ bmv_settings.l10n.form.email +'</label><input type="email" class="m-email full" id="email" placeholder="Email" value="" required></div>';
    if (bmv_prm_slug != 'forgot') str_member += '<div class="m-group flex_wrap"><label for="password">'+ bmv_settings.l10n.form.password +'</label><input type="password" class="m-pass full" id="password" placeholder="Password" value="" minlength="8" required></div>';
    if (bmv_prm_slug == 'login') {
      str_member += '<button type="submit" class="m-submit btn full">'+ bmv_settings.l10n.member.login +'</button>';
      str_member += '<div class="m-text t_center">'+ bmv_settings.l10n.member.forgot_link +'</div>';
      str_member += '<div class="m-text t_center">'+ bmv_settings.l10n.member.signup_link +'</div>';
    } else if (bmv_prm_slug == 'forgot') {
      str_member += '<button type="submit" class="m-submit btn full">'+ bmv_settings.l10n.form.submit +'</button>';
      str_member += '<div class="m-text t_center">'+ bmv_settings.l10n.member.signup_link +'</div>';
    } else { //signup
      str_member += '<div class="m-group flex_wrap"><label for="c-password">'+ bmv_settings.l10n.form.pass_confirm +'</label><input type="password" class="m-pass-c full" id="c-password" placeholder="Confirm Password" value="" minlength="8" required></div>';
      str_member += '<button type="submit" class="m-submit btn full">'+ bmv_settings.l10n.member.signup +'</button>';
      str_member += '<div class="m-text t_center">'+ bmv_settings.l10n.member.login_link +'</div>';
    }
    str_member += '</div>'; //.form-email
    str_member += '</form>';
  }
  str_member += '</div>'; //.content
  bmv_el_post.innerHTML = str_member;
  if (!fbase_login) el('.main').classList.add('wBox');

  bmf_member_fnc();
}

// #===========================================================================================#

function bmf_window_stop() {
  // window.stop(); //bug: firebase error
  el('.cm_stop').classList.add('no_items');
  el('.cm_reload').classList.remove('no_items');
}

function bmf_chapter_key(e) {
  var is_edit = ['input', 'textarea'].indexOf(document.activeElement.tagName.toLowerCase()) !== -1;
  if (!is_edit && !is_mobile) {
    if (keyEvent(e, 37) && el('.chapter .btn.prev')) el('.chapter .btn.prev').click(); //key: left arrow
    if (keyEvent(e, 39) && el('.chapter .btn.next')) el('.chapter .btn.next').click(); //key: right arrow
  }
}

function bmf_chapter_direction() {
  // Manga (Japanese) - Read from right to left.
  // Manhua (Chinese) - Read from right to left.
  // Manhwa (Korean) - Read from left to right.
  var dir = bmv_el_post.dataset.type == 'manhwa' ? 'ltr' : 'rtl';
  var dir_txt = dir == 'ltr' ? 'Kiri ke Kanan' : 'Kanan ke Kiri';
  if (!el('.direction')) {
    var dir_el = document.createElement('div');
    dir_el.classList.add('direction', 'flex', 'f_perfect', dir);
    dir_el.style.zIndex = '1';
    dir_el.innerHTML = '<div class="dir-info fp_content t_center radius"><div class="dir-image"><img class="full" alt="' + dir_txt + '" src="./images/read-' + dir + '.png" title="' + dir_txt + '" alt="' + dir_txt + '"></div><div class="dir-text">' + dir_txt + '</div></div>';
    document.body.appendChild(dir_el);
  }
  setTimeout(function() { removeElem('.direction') }, 1500);
  cookies.set(bmv_zoom_id, 'true', 'hour|1');
}

function bmf_chapter_zoom(slug, type) {
  // set "zoom" for chapter
  bmv_zoom = local('get', 'bmv_zoom') ? JSON.parse(local('get', 'bmv_zoom')) : {}; //check localStorage
  if (slug in bmv_zoom === false && type != '') bmv_zoom[slug] = type;
  local('set', 'bmv_zoom', JSON.stringify(bmv_zoom));
}

function bmf_menu_key(e) {
  if (e.altKey) {
    if (keyEvent(e, 82)) el('.cm_reload').click(); //"alt & r" for reload page
    if (keyEvent(e, 88)) el('.cm_stop').click(); //"alt & x" for stop page loading
    if (keyEvent(e, 65)) el('.cm_ld_img').click(); //"alt & a" for load all
  }

  if (e.shiftKey) {
    if (keyEvent(e, 38)) el('.cm_zoom .cm_zm_plus').click(); //"shift & up" zoom +
    if (keyEvent(e, 40)) el('.cm_zoom .cm_zm_less').click(); //"shift & down" zoom -
  }

  if (keyEvent(e, 13)) {
    // enter to load
    if (el('.cm_ld_all') === document.activeElement) el('.cm_ld_img').click();
  }
}

function bmf_menu_cdn(elem) {
  if (el('.cm_cdn.cm_active')) el('.cm_cdn.cm_active').classList.remove('cm_active');
  elem.classList.add('cm_active');
  if (bmv_chk_gi) {
    el('.cm_size').innerHTML = 's15000';
    el('.cm_size').click();
  }
}

function bmf_menu_cdn_url(source, elem, url) {
  bmv_str_cdn_url = elem.dataset.cdn ? elem.dataset.cdn : url;
  bmv_load_cdn = true;
  bmv_str_cdn = source;
  bmf_menu_cdn(elem);
}

function bmf_menu_fnc(img_list) {
  if (el('.cm_prev')) {
    el('.cm_prev').classList.remove('no_items');
    el('.cm_prev2').classList.remove('no_items');
  }

  if (el('.cm_next')) {
    el('.cm_next').classList.remove('no_items');
    el('.cm_next2').classList.remove('no_items');
  }

  el('.cm_toggle').addEventListener('click', function() {
    this.classList.toggle('db_danger');
    el('.ch_menu').classList.toggle('cm_shide');
    el('.cm_pause2').classList.toggle('no_items');
    if (is_mobile) {
      el('.cm_bg').classList.toggle('no_items');
      if (el('.cm_next')) toggleClass(el('.cm_next'), ['no_hover', 'mobile_left_hand']);
    }
    el('.cm_tr2 .cm_td1').classList.toggle('no_items');
  });

  if (is_mobile) {
    el('.cm_bg').addEventListener('click', function() {
      el('.cm_toggle').click();
    });
  }

  el('.cm_load input', 'all').forEach(function(item) {
    item.addEventListener('click', function() {
      this.select();
    });
    item.addEventListener('input', function() {
      if (this.value > img_list.length) this.value = img_list.length;
      if (this.classList.contains('cm_ld_all')) { //.cm_ld_all
        if (isNumeric(this.value)) {
          this.classList.add('cm_all_active');
          el('.cm_load2').classList.add('cm_n_active');
        }
        if (!isNumeric(this.value) && this.value != 'all') {
          this.value = 'all';
          this.classList.remove('cm_all_active');
          el('.cm_load2').classList.remove('cm_n_active');
        }
      }
    });
  });

  el('.cm_load .cm_ld_pause').addEventListener('click', function() {
    this.classList.toggle('cm_danger');
    el('.cm_pause2').classList.toggle('cm_danger');
    bmv_chk_pause = bmv_chk_pause ? false : true;
    el('.cm_ld_img').disabled = bmv_chk_pause ? true : false;
    el('.cm_load2').disabled = bmv_chk_pause ? true : false;
    el('.cm_ld_all').disabled = bmv_chk_pause ? true : false;
    el('.cm_ld_reset').disabled = bmv_chk_pause ? true : false;
    if (el('.cm_ld_all').classList.contains('cm_all_active')) el('.cm_ld_reset').click();
    if (el('.cm_fr_btn').classList.contains('cm_active')) el('.cm_fr_btn').click();
    el('.cm_fr_btn').disabled = bmv_chk_pause ? true : false;
  });

  // Load all images
  el('.cm_load .cm_ld_img').addEventListener('click', function() {
    // "img_list" from bmf_menu_fnc() parameter
    if (isNumeric(el('.cm_ld_all').value)) {
      var ld_index = Number(el('.cm_ld_all').value);
      if (is_mobile && isImageLoaded(el(`#reader [data-index="${ld_index}"] img`))) el(`#reader [data-index="${ld_index}"]`).scrollIntoView();
      bmf_lazyLoad(img_list[ld_index - 1], 'single');
    } else {
      if (!bmv_chk_from) bmv_loaded_img = true;
      var ld_index = bmv_chk_from && el('.cm_fr_min').value != '' ? (Number(el('.cm_fr_min').value) - 1) : 0;
      var ld_length = bmv_chk_from && el('.cm_fr_max').value != '' ? Number(el('.cm_fr_max').value) : img_list.length;
      for (var i = ld_index; i < ld_length; i++) { bmf_lazyLoad(img_list[i], 'multi'); }
      if (bmv_chk_from) el(`#reader [data-index="${ld_index+1}"]`).scrollIntoView();
    }
    var fr_last = bmv_chk_from && el('.cm_fr_max').value == img_list.length;
    if (is_mobile && !el('.ch_menu').classList.contains('cm_shide') && (!bmv_chk_from || (fr_last))) el('.cm_toggle').click();
  });

  el('.cm_load .cm_ld_reset').addEventListener('click', function() {
    el('.cm_ld_all').value = 'all';
    el('.cm_ld_all').classList.remove('cm_all_active');
    el('.cm_load2').classList.remove('cm_n_active');
  });

  el('.cm_load .cm_fr_btn').addEventListener('click', function() {
    var ld_from = this.classList.contains('cm_active');
    el('.cm_load .cm_ld_reset').click();
    el('.cm_ld_all').disabled = ld_from && !bmv_chk_pause ? false : true;
    el('.cm_ld_reset').disabled = ld_from && !bmv_chk_pause ? false : true;
    el('.cm_fr_min').disabled = ld_from ? true : false;
    el('.cm_fr_max').disabled = ld_from ? true : false;
    if (ld_from) {
      el('.cm_fr_min').value = '1';
      el('.cm_fr_max').value = el('.cm_fr_max').dataset.value;
    }
    bmv_chk_from = ld_from ? false : true;
    this.classList.toggle('cm_active');
    el('.cm_load2').classList.toggle('cm_fr_active');
  });

  if (bmv_chk_gi) {
    el('.cm_size').addEventListener('click', function() {
      this.innerHTML = this.innerHTML == bmv_str_gi ? 's15000' : bmv_str_gi;
      bmv_load_gi = this.innerHTML == bmv_str_gi ? false : true;
    });
  }

  el('.cdn_statically').addEventListener('click', function() {
    // https://statically.io/docs/using-images/
    if (this.classList.contains('cm_active')) return;
    bmf_menu_cdn_url('statically', this, 'cdn.statically.io/img/');
  });

  el('.cdn_wp').addEventListener('click', function() {
    // https://developer.wordpress.com/docs/photon/
    if (this.classList.contains('cm_active')) return;
    var wp_num = getRndInteger(0, 2);
    bmf_menu_cdn_url('wp', this, `i${wp_num}.wp.com/`);
  });

  el('.cdn_imagecdn').addEventListener('click', function() {
    // https://imagecdn.app/docs
    if (this.classList.contains('cm_active')) return;
    bmf_menu_cdn_url('imagecdn', this, 'imagecdn.app/v2/image/');
  });

  el('.cdn_imageoptim').addEventListener('click', function() {
    if (this.classList.contains('cm_active')) return;
    bmf_menu_cdn_url('imageoptim', this, `img.gs/${imageoptim_username}/full/`);
  });

  el('.cdn_not').addEventListener('click', function() {
    if (this.classList.contains('cm_active')) return;
    bmv_load_cdn = false;
    bmv_str_cdn = '';
    bmf_menu_cdn(this);
  });

  if (bmv_chk_cdn) {
    if (bmv_dt_settings.cdn == 'default') el(`.cm_cdn.cdn_${bmv_str_cdn}`).classList.add('cm_active');
    el(`.cm_cdn.cdn_${bmv_str_cdn}`).setAttribute('data-cdn', bmv_str_cdn_url);
  }

  if (bmv_dt_settings.cdn != 'default') el(`.cm_cdn.cdn_${bmv_dt_settings.cdn}`).click();

  el('.cm_zoom button', 'all').forEach(function(item) {
    item.addEventListener('click', function() {
      var load_zm;
      if (item.classList.contains('cm_zm_reset')) {
        var zm_type = bmv_el_post.dataset.type || 'manga';
        if (bmv_el_post.dataset.type) {
          bmv_zoom[bmv_zoom_id] = zm_type;
        } else {
          delete bmv_zoom[bmv_zoom_id];
        }
        load_zm = bmv_zm_size[zm_type];
        if (load_zm > window.screen.width) load_zm = window.screen.width;
      } else {
        var zm_min = 240;
        var zm_max = is_mobile ? window.screen.width : ((window.screen.width * 50) / 100);
        load_zm = Number(el('.cm_zoom input').value);
        if (item.classList.contains('cm_zm_plus')) {
          load_zm += 50;
          if (load_zm > zm_max) load_zm = zm_max;
        } else {
          load_zm += -50
          if (load_zm < zm_min) load_zm = zm_min;
        }
        bmv_zoom[bmv_zoom_id] = load_zm;
      }
      bmv_el_images.style.setProperty('max-width', load_zm +'px', 'important');
      el('.cm_zoom input').value = load_zm;
      local('set', 'bmv_zoom', JSON.stringify(bmv_zoom));
    });
  });

  el('.cm_load2').addEventListener('click', function() {
    el('.cm_ld_img').click();
  });

  el('.cm_pause2').addEventListener('click', function() {
    el('.cm_ld_pause').click();
  });

  el('.cm_reload').addEventListener('click', function() {
    wl.reload();
  });

  el('.cm_stop').addEventListener('click', bmf_window_stop);

  // back to top
  el('.cm_top').addEventListener('click', function() {
    document.body.scrollIntoView();
  });

  // scroll to bottom
  el('.cm_bottom').addEventListener('click', function() {
    //document.body.scrollIntoView(false);
    window.scrollTo(0, bmv_el_images.scrollHeight);
  });

  document.addEventListener('keyup', bmf_menu_key);
  loadListener('load', bmf_window_stop); //stop page after html and js "_reader" loaded
  // if (el('.cm_reload').classList.contains('no_items')) el('.cm_stop').click(); //auto stop page after html and js "_reader" loaded
  if (bmv_settings.remove_statically && bmv_chk_cdn) el('.cm_cdn').click(); //auto remove cdn from statically
}

function bmf_chapter_menu() {
  var cm_size;
  if (bmv_zoom_id in bmv_zoom) {
    cm_size = isNumeric(bmv_zoom[bmv_zoom_id]) ? bmv_zoom[bmv_zoom_id] : bmv_zm_size[bmv_zoom[bmv_zoom_id]];
  } else {
    cm_size = bmv_zm_size['manga'];
  }
  if (cm_size > window.screen.width) cm_size = window.screen.width;
  bmv_el_images.style.setProperty('max-width', cm_size +'px', 'important');

  // Add "chapter" menu
  var str_menu = '';
  if (is_mobile) str_menu += '<div class="cm_bg no_items"></div>';
  str_menu += '<div class="ch_menu ';
  if (is_mobile) str_menu += 'cm_shide ';
  str_menu += 'flex_wrap f_bottom bg2">';
  str_menu += '<div class="cm_tr1 flex_wrap">';
  str_menu += '<div class="cm_others cm_line w100 flex_wrap">';
  str_menu += '<button class="cdn_statically cm_cdn cm_btn btn">Statically</button>';
  str_menu += '<button class="cdn_wp cm_cdn cm_btn btn">WP</button>';
  str_menu += '<button class="cdn_imagecdn cm_cdn cm_btn btn">ImageCDN</button>';
  str_menu += '<button class="cdn_imageoptim cm_cdn cm_btn btn">ImageOptim</button>';
  str_menu += '<button class="cdn_not cm_cdn cm_btn btn">not</button>';
  if (bmv_chk_gi) str_menu += '<div class="cm_size cm_btn btn">'+ bmv_str_gi +'</div>';
  str_menu += '</div>'; //.cm_others
  if (el('.ch-nav .prev') || el('.ch-nav .next')) {
    var np_newtab = bmv_dt_settings.link.indexOf(`${bmv_current}-np`) != -1;
    str_menu += '<div class="cm_nav cm_line w100 flex">';
    if (el('.ch-nav .prev')) {
      str_menu += '<a class="cm_prev cm_btn btn no_items" title="arrow left &#x25C0;" href="'+ el('.ch-nav .prev').href;
      if (np_newtab) str_menu += '" target="_blank';
      str_menu += '">&#9666; Prev</a>';
    }
    if (el('.ch-nav .next')) {
      str_menu += '<a class="cm_next cm_btn btn no_items'+ (is_mobile ? ' no_hover mobile_left_hand' : '') +'" title="arrow right &#x25B6;" href="'+ el('.ch-nav .next').href;
      if (np_newtab) str_menu += '" target="_blank';
      str_menu += '">Next &#9656;</a>';
    }
    str_menu += '</div>'; //.cm_nav
  }
  str_menu += '<div class="cm_home cm_line w100"><a class="cm_btn btn" href="./">Homepage</a></div>';
  str_menu += '<div class="cm_load cm_line flex_wrap">';
  str_menu += '<div class="flex f_middle">';
  str_menu += '<span class="cm_ld_current cm_text" title="Current image loading">LZ (0)</span>';
  str_menu += '<button class="cm_ld_pause cm_btn btn btn_circle btn_icon no_hover" title="Pause images from loading"><svg data-name="mdi/play-pause" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M3 5v14l8-7m2 7h3V5h-3m5 0v14h3V5"/></svg></g></svg></button>';
  str_menu += '</div>';
  str_menu += '<div class="flex">';
  str_menu += '<button class="cm_ld_img cm_btn btn" title="alt + a">Load</button>';
  str_menu += '<input class="cm_ld_all cm_input" value="all" type="text">';
  str_menu += '<button class="cm_ld_reset cm_btn btn btn_circle btn_icon" title="Reset">&#8634;</button>';
  str_menu += '</div>';
  str_menu += '<div class="cm_from flex f_middle w100">';
  str_menu += '<button class="cm_fr_btn cm_btn btn no_hover" title="Load images from [index]">From</button>';
  str_menu += '<div class="cm_fr_num">';
  str_menu += '<input class="cm_fr_min cm_input no_arrows" type="number" value="1"  disabled>';
  str_menu += '<span>-</span>';
  str_menu += '<input class="cm_fr_max cm_input no_arrows" type="number" value="'+ bmv_dt_chapter.images.length +'" data-value="'+ bmv_dt_chapter.images.length +'" disabled>';
  str_menu += '</div>';// .cm_fr_num
  str_menu += '</div>';// .cm_from
  str_menu += '</div>';// .cm_load
  str_menu += '<div class="cm_zoom flex w100"><button class="cm_zm_plus cm_btn btn" title="shift + up">+</button><button class="cm_zm_less cm_btn btn" title="shift + down">-</button><input style="width:50px;" class="cm_input no_arrow" value="'+ cm_size +'" type="number" readonly><button class="cm_zm_reset cm_btn btn btn_circle btn_icon" title="Reset zoom">&#8634;</button></div>';
  str_menu += '</div>';// .cm_tr1
  str_menu += '<div class="cm_tr2 flex f_bottom">';
  str_menu += '<div class="cm_td1'+ (is_mobile ? '' : ' no_items') +'">';
  if (el('.ch-nav .next')) {
    str_menu += '<a class="cm_next2 cm_btn btn flex f_center f_middle no_items" href="'+ el('.ch-nav .next').href;
    if (np_newtab) str_menu += '" target="_blank';
    str_menu += '">&#9656;</a>';
  }
  if (el('.ch-nav .prev')) {
    str_menu += '<a class="cm_prev2 cm_btn btn flex f_center f_middle no_items" href="'+ el('.ch-nav .prev').href;
    if (np_newtab) str_menu += '" target="_blank';
    str_menu += '">&#9666;</a>';
  }
  str_menu += '<button class="cm_load2 cm_btn btn flex f_center f_middle" title="alt + a">&#671;</button>';
  str_menu += '</div>';// .cm_td1
  str_menu += '<div class="cm_td2">';
  str_menu += '<div class="cm_pause2 cm_btn btn flex f_center f_middle no_hover';
  if (!is_mobile) str_menu += ' no_items';
  str_menu += '"><svg data-name="mdi/play-pause" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M3 5v14l8-7m2 7h3V5h-3m5 0v14h3V5"/></svg></g></svg></div>';
  str_menu += '<div class="cm_rest"><div class="cm_reload cm_btn btn flex f_center f_middle no_items" title="alt + r">&#8635;</div><div class="cm_stop cm_btn btn flex f_center f_middle" title="alt + x">&#10007;</div></div>';
  str_menu += '<div class="cm_top cm_btn btn flex f_center f_middle">&#9652;</div>';
  str_menu += '<div class="cm_bottom cm_btn btn flex f_center f_middle">&#9662;</div>';
  str_menu += '<div class="cm_toggle cm_btn btn flex f_center f_middle no_select'+ (is_mobile ? ' no_hover' : '') +'">&#174;</div>';
  str_menu += '</div>';// .cm_td2
  str_menu += '</div>';// .cm_tr2
  str_menu += '</div>';// .ch_menu
  el('.chapter ._reader').innerHTML = str_menu;

  bmf_menu_fnc(el('#reader img', 'all'));
}

// Find element on middle visible screen (viewport) https://stackoverflow.com/a/26466655
function bmf_chapter_middle() {
  var viewportHeight = document.documentElement.clientHeight;
  var found = [];
  var opts = {
    elements: el('#reader .ch-index .sticky', 'all'),
    class: 'active',
    zone: [40, 40] // percentage distance from top & bottom
  };

  for (var i = opts.elements.length; i--;) {
    var elm = opts.elements[i];
    var pos = elm.getBoundingClientRect();
    var topPerc = pos.top / viewportHeight * 100;
    var bottomPerc = pos.bottom / viewportHeight * 100;
    var middle = (topPerc + bottomPerc)/2;
    var inViewport = middle > opts.zone[1] && middle < (100-opts.zone[1]);

    elm.classList.toggle(opts.class, inViewport);

    if (inViewport) found.push(elm);
  }
}

function bmf_chapter_history(slug) {
  // generate & send chapter data to firebase realtime database
  if (fbase_login && !bmv_dt_settings.hs_stop) {
    var c_path = bmf_fbase_path(`series/${slug}`);
    var c_data = bmv_dt_settings.source.type.search(/eastheme|koidezign/) != -1 ? '/hs_visited' : ''; //different path for cover

    bmf_fbase_db_get('chapter/history', `${c_path + c_data}`, function(res) {
      if (!bmv_dt_chapter) return;
      var ch_data = bmf_fbase_gen('history|info|set', bmv_dt_chapter);
      var ch_visited = {};

      if (res.exists()) {
        // update history, get first {bmv_max_hv} data
        ch_visited = res.val();
        if (bmv_dt_settings.source.type.search(/eastheme|koidezign/) == -1) ch_visited = 'hs_visited' in ch_visited ? ch_visited.hs_visited : {};
        ch_visited[bmv_dt_chapter.current] = { //add current chapter
          number: bmv_dt_chapter.current,
          url: new URL(bmv_dt_chapter.source).pathname,
          added: new Date().getTime(),
          site: bmv_dt_settings.source.site //only for info (visited chapter)
        };
        ch_visited = genArray(ch_visited); //convert to Array
        ch_visited = sortBy(ch_visited, 'added'); //sort by "added" (desc)
        ch_visited = firstArray(ch_visited, bmv_max_hv); //get first {bmv_max_hv} data
        ch_visited = genJSON(ch_visited, 'number'); //convert to JSON
      } else {
        ch_visited[bmv_dt_chapter.current] = { //add current chapter
          number: bmv_dt_chapter.current,
          url: new URL(bmv_dt_chapter.source).pathname,
          added: new Date().getTime(),
          site: bmv_dt_settings.source.site //only for info (visited chapter)
        };
        bmf_bmhs_change('history|set');
      }

      if (bmv_dt_chapter.slug != slug) {
        var slug_alt = 'slug_alt' in ch_data ? ch_data.slug_alt : {};
        slug_alt[bmv_dt_settings.source.site] = bmv_dt_chapter.slug;
        ch_data.slug_alt = slug_alt;
      }
      ch_data.slug = slug;
      ch_data.hs_visited = ch_visited;
      var cover_chk = bmv_dt_settings.source.type.search(/eastheme|koidezign/) == -1 && res.exists() && 'cover' in bmv_dt_chapter && bmv_dt_chapter.cover == '';
      if (cover_chk) ch_data.cover = res.val().cover;
      bmf_fbase_db_change('chapter/history/set', c_path, 'update', ch_data); //use "update" to keep "other data" from being deleted
    });
  }
}

function bmf_chapter_nav(note, data) {
  if (!bmv_dt_chapter || data.code != 200) return;
  var json = JSON.parse(data.response);
  bmv_dt_series = json;
  bmf_fbase_slug('chapter', {"slug": bmv_dt_chapter.slug, "title": bmv_dt_series.title}, bmf_chapter_history);

  if (json.status_code == 200) {
    // Chapter navigation
    bmf_chapter_zoom(json.slug, json.detail.type);
    bmv_el_post.setAttribute('data-type', json.detail.type);

    var lists = json.chapter; //JSON data
    var str_ch_nav = '';
    str_ch_nav += '<select name="index">';
    for (var i = 0; i < lists.length; i++) {
      str_ch_nav += '<option value="'+ lists[i].number +'"';
      if (lists[i].number.replace(/[\.\s\t\-]+/g, '-').toLowerCase() == bmv_dt_chapter.current) { str_ch_nav += ' selected="selected"'; }
      str_ch_nav += '>Chapter '+ lists[i].number.replace(/[-\s]((bahasa?[-\s])?indo(nesiaa?)?|full)/, '') +'</option>';
    }
    str_ch_nav += '</select>';

    // Display chapter navigation
    el('.ch-nav .ch-number', 'all')[0].innerHTML = str_ch_nav;
    el('.ch-nav .ch-number', 'all')[1].innerHTML = str_ch_nav;

    // Select option navigation
    el('.chapter select', 'all').forEach(item => {
      item.addEventListener('change', function() {
        var num = this.selectedIndex;
        wl.hash = '#/chapter/'+ bmf_series_chapter_link('nav/select', this.options[num].value);
      });
    });
  }
}

function bmf_chapter_fnc() {
  // if (bmv_settings.direction && !cookies.get(bmv_zoom_id)) bmf_chapter_direction(); //rtl or ltr
  document.addEventListener('keyup', bmf_chapter_key); //Left and right keyboard navigation

  el('#reader .ch-images', 'all').forEach(function(item) {
    el('.ch-index .btn', item).addEventListener('click', function(e) {
      e.preventDefault();
      if (e.target == e.currentTarget) item.classList.toggle('right');
    });
  });
}

function bmf_build_chapter_nav(data) {
  var np_newtab = bmv_dt_settings.link.indexOf(`${bmv_current}-np`) != -1;
  var str_ch_nav = '';

  str_ch_nav += '<span class="ch-number">Loading..</span>';
  str_ch_nav += '<span class="f_grow"></span>';
  if ('number' in bmv_dt_chapter.prev) {
    str_ch_nav += '<a class="prev ctrl btn radius" href="'+ '#/chapter/'+ bmf_series_chapter_link('nav/prev', bmv_dt_chapter.prev, bmv_dt_chapter.slug);
    if (np_newtab) str_ch_nav += '" target="_blank';
    str_ch_nav += '">&#x25C0;&#160;&#160;'+ bmv_settings.l10n.prev +'</a>';
  }
  if ('number' in bmv_dt_chapter.next) {
    str_ch_nav += '<a class="next ctrl btn radius" href="'+ '#/chapter/'+ bmf_series_chapter_link('nav/next', bmv_dt_chapter.next, bmv_dt_chapter.slug);
    if (np_newtab) str_ch_nav += '" target="_blank';
    str_ch_nav += '">'+ bmv_settings.l10n.next +'&#160;&#160;&#x25B6;</a>';
  }

  return str_ch_nav;
}

function bmf_build_chapter(data) {
  // Display "chapter" page
  bmv_dt_chapter = data;
  bmv_zoom_id = data.slug;
  var images = data.images;
  var ch_current = data.current.replace(/[-\s]((bahasa?[-\s])?indo(nesiaa?)?|full)/, '');
  var ch_title = data.title +' Chapter '+ ch_current;
  var bc_newtab = bmv_dt_settings.link.indexOf(`${bmv_current}-bc`) != -1;
  var img_newtab = bmv_dt_settings.link.indexOf(`${bmv_current}-img`) != -1;

  var str_chapter = '';
  str_chapter += '<div class="ch-header layer"><h1 class="hidden_items">'+ ch_title +'</h1>';
  str_chapter += '<div class="breadcrumb'+ (is_mobile ? ' bg2 t_center' : '') +'"><a href="#/latest';
  if (bc_newtab) str_chapter += '" target="_blank';
  str_chapter += '">'+ bmv_settings.l10n.homepage +'</a> &#62; <a href="'+ bmv_series_list;
  if (bc_newtab) str_chapter += '" target="_blank';
  str_chapter += '">Series</a> &#62; <a href="#/series/'+ data.slug +'" title="'+ data.title;
  if (bc_newtab) str_chapter += '" target="_blank';
  str_chapter += '"><span class="bc-title'+ (is_mobile ? '' : ' nowrap') +'">'+ data.title +'</span></a> &#62; Chapter '+ ch_current +'</div></div>';
  str_chapter += '<div class="ch-nav flex layer">'+ bmf_build_chapter_nav(bmv_dt_chapter) +'</div>';
  str_chapter += '<div id="reader" class="max_chapter flex_wrap';
  if (images.length > 0) {
    str_chapter += '">';
    for (var i = 0; i < images.length; i++) {
      var img_attr = ch_title +' - '+ (i + 1);
      images[i] = images[i].replace(/^\s+/, '').replace(/^(%20)+/, '');
      str_chapter += '<a class="ch-images full" data-index="'+ (i + 1) +'" href="'+ images[i] +'#'+ (i + 1);
      if (img_newtab) str_chapter += '" target="_blank';
      str_chapter += '"><img style="min-height:750px;" class="full_img loading loge lazy1oad" data-index="'+ (i + 1) +'" data-src="'+ (images[i] +'#'+ (i + 1)) +'" title="'+ img_attr +'" alt="'+ img_attr +'" referrerpolicy="no-referrer">';
      if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f' && bmv_dt_settings.ch_index) str_chapter += '<div class="ch-index"><div class="sticky"><div class="btn">'+ (i + 1) +'</div></div></div>';
      str_chapter += '</a>';

      if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f' && bmv_dt_settings.ch_menu) {
        // Check CDN
        if (!bmv_chk_cdn && images[i].match(bmv_rgx_cdn)) {
          var cdn_match = images[i].match(bmv_rgx_cdn);
          bmv_chk_cdn = true;
          bmv_load_cdn = true;
          bmv_str_cdn = cdn_match[2];
          bmv_str_cdn_url = cdn_match[1];
        }

        // Check Google images
        if (!bmv_chk_gi && images[i].match(bmv_rgx_gi)) {
          bmv_chk_gi = true;
          bmv_str_gi = images[i].match(bmv_rgx_gi);
          bmv_str_gi = bmv_str_gi[1] || bmv_str_gi[2];
          bmv_str_gi = Number(bmv_str_gi.replace(/[swh]/,''));
          bmv_str_gi = bmv_str_gi == 0 || bmv_str_gi > 800 ? 's'+ bmv_str_gi : 's1600';
        }
      }
    }
  } else {
    str_chapter += ' f_middle f_center" style="height:50vh;">Tidak ada Chapter';
  }
  str_chapter += '</div>';
  str_chapter += '<div class="ch-nav flex layer">'+ bmf_build_chapter_nav(bmv_dt_chapter) +'</div>';
  str_chapter += '<div id="disqus_thread"><div class="full t_center"><button class="disqus-trigger btn bgrey">'+ bmv_settings.l10n.comment_btn +'</button></div></div>';
  str_chapter += '<div class="_reader" style="position: relative;"></div>';
  bmv_el_post.innerHTML = str_chapter;

  bmf_chapter_fnc();
  bmv_el_images = el('#reader');
  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') {
    if (bmv_dt_settings.ch_menu) bmf_chapter_menu();
    if (bmv_dt_settings.ch_index) document.addEventListener('scroll', bmf_chapter_middle); //ch-index
  }
  bmf_loadXMLDoc({note:`xhr/${bmv_current}/nav`}, `${api_path}/api/?source=${bmv_dt_settings.source.site}&index=series&slug=${data.slug}&cache=${bmv_dt_settings.cache}`, bmf_chapter_nav); //get data from series for type & chapter list
}

// #===========================================================================================#

function bmf_series_chapter_number(check) {
  var ch_arr = bmv_dt_series.chapter;
  for (var i = 0; i < ch_arr.length; i++) {
    if (ch_arr[i].number == check) return ch_arr[i];
  }
  return false;
}

function bmf_series_chapter_link(note, data, slug) {
  if (note.search(/\/select/i) != -1) data = bmf_series_chapter_number(data);
  var ch_link = (slug || bmv_dt_series.slug) +'/'+ data.number.replace(/[\.\s\t\-]+/g, '-').toLowerCase();
  if (bmv_dt_settings.ch_url) ch_link += '/'+ encodeURIComponent('url='+ data.url);
  if (note == 'visited') ch_link += (bmv_dt_settings.ch_url ? '&' : '/')+ encodeURIComponent('site='+ data.site);
  return ch_link;
}

function bmf_series_chapter_list(note, data) {
  // Display chapter list
  var s_newtab = bmv_dt_settings.link.indexOf(bmv_current) != -1;
  var str_lists = '';

  if (data.length > 0) {
    if (note == 'visited') str_lists += '<div class="ch-title">'+ bmv_settings.l10n.series.visited +'</div>';
    str_lists += '<ul class="flex_wrap">';
    for (var i in data) {
      var chk_site = note == 'visited' && data[i].site != bmv_dt_settings.source.site;
      str_lists += '<li><';
      if (chk_site) {
        str_lists += 'span title="source: '+ data[i].site +'"';
      } else {
        str_lists += 'a href="#/chapter/'+ bmf_series_chapter_link(note, data[i]) +'"';
        if (s_newtab) str_lists += ' target="_blank"';
      }
      str_lists += ' class="chapter full radius">Chapter '+ data[i].number.replace(/[-\s]((bahasa?[-\s])?indo(nesiaa?)?|full)/, '') +'</';
      str_lists += chk_site ? 'span' : 'a';
      str_lists += '></li>';
    }
    str_lists += '</ul>';
  } else {
    str_lists += '<div class="flex f_middle f_center" style="height:25vh;">Tidak ada Chapter</div>';
  }
  el(`.series .${note}-list`).innerHTML = str_lists;
}

function bmf_series_fnc(slug) {
  var s_bm = el('.info-left .bookmark');

  el('.info-left img').addEventListener('error', function() {
    if (this.dataset.src != '' && this.dataset.ref == 'false') {
      this.src = `${api_path}/tools/img_ref.php?ref=${encodeURIComponent(bmv_lazy_referer)}&url=`+ encodeURIComponent(this.dataset.src);
    } else {
      this.classList.remove('loading', 'loge');
      this.classList.add('no-image');
    }
  });
  el('.info-left img').src = el('.info-left img').dataset.src;

  // init check bookmark
  if (fbase_login) {
    var s_path = bmf_fbase_path(`series/${slug}`);
    bmf_fbase_db_get('series/bookmark', `${s_path}/bookmarked`, function(res) {
      if (res.val() == 'true') {
        s_bm.classList.remove('wait');
        s_bm.classList.add('marked', 'red');
        s_bm.removeAttribute('disabled');

        // update bookmark
        var data = bmf_fbase_gen('bookmark|info|update', bmv_dt_series);
        if (bmv_dt_series.slug != slug) {
          var slug_alt = 'slug_alt' in data ? data.slug_alt : {};
          slug_alt[bmv_dt_settings.source.site] = bmv_dt_series.slug;
          data.slug_alt = slug_alt;
        }
        data.slug = slug;
        bmf_fbase_db_change('series/bookmark', s_path, 'update', data);
      } else {
        s_bm.classList.remove('wait', 'marked', 'red');
        s_bm.removeAttribute('disabled');
      }
    });

    if (bmv_dt_series.chapter.length > 0) {
      bmf_fbase_db_get('series/bookmark/visited', `${s_path}/hs_visited`, function(res) {
        if (res.exists()) {
          var hs_data = genArray(res.val());
          hs_data = sortBy(hs_data, 'added');

          var hs_list = [];
          for (var i in hs_data) {
            hs_list.push(hs_data[i]);
            var ch_number = bmv_dt_settings.ch_url ? `*="/${hs_data[i].number}/` : `$="/${hs_data[i].number}`;
            var ch_visited = el(`.chapter-list a[href${ch_number}"]`);
            if (ch_visited) ch_visited.classList.add('visited');
          }

          el('.series .visited-list').classList.add('ch-list', 'bg2', 'radius');
          bmf_series_chapter_list('visited', hs_list); //Build visited list

          if (bmv_dt_series.cover != '' && !s_bm.classList.contains('marked')) {
            bmf_fbase_db_change('series/cover', `${s_path}`, 'update', {cover:bmv_dt_series.cover});
          }
        }
      });
    }
  } else {
    s_bm.classList.remove('wait', 'marked', 'red');
    s_bm.removeAttribute('disabled');
  }

  s_bm.addEventListener('click', function() {
    if (fbase_login) {
      var s_path = bmf_fbase_path(`series/${slug}`);
      this.classList.add('wait');

      if (this.classList.contains('marked')) {
        if (confirm(`Hapus series ini dari bookmark?\n👉 ${slug}`)) {
          var r_note = 'series/bookmark/remove';
          var hs_path = bmf_fbase_path(`series/${slug}/hs_visited`);
          bmf_fbase_db_check(r_note, hs_path, function(res) {
            if (res) {
              bmf_fbase_db_change(r_note, s_path, 'update', {bookmarked: 'false'}, function() {
                s_bm.classList.remove('wait', 'marked', 'red');
              });
            } else {
              bmf_fbase_db_remove(r_note, s_path, function() {
                s_bm.classList.remove('wait', 'marked', 'red');
              });
            }
            bmf_bmhs_change('bookmark|remove');
          });
        }
      } else {
        var bm_path = bmf_fbase_path('check/bookmark');
        bmf_fbase_db_get('series/bookmark/check', bm_path, function(res) {
          var bm_length = res.val() ? Number(res.val().length) : 0;
          if (bm_length >= bmv_max_bmhs) {
            s_bm.classList.remove('wait');
            alert(`Total bookmark telah melampaui kuota (${bmv_max_bmhs}), coba hapus bookmark lain.`);
          } else {
            var bbmhs_arr = bmf_fbase_gen('bookmark|info|set', bmv_dt_series);
            bmf_fbase_db_change('series/bookmark/set', s_path, 'update', bbmhs_arr, function() { //use "update" to keep "history" from being deleted
              s_bm.classList.remove('wait');
              s_bm.classList.add('marked', 'red');
            });

            bmf_bmhs_change('bookmark|set');
          }
        });
      }
    } else {
      wl.hash = '#/member/login/?continue='+ encodeURIComponent(wl.hash);
    }
  });

  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f') {
    if (el('.series .s-copy')) {
      el('.series .s-copy').addEventListener('click', function() {
        if (copyToClipboard(el('.series h1').textContent, this)) {
          this.classList.add('green');
          setTimeout(() => { this.classList.remove('green') }, 1000);
        }
      });
    }

    if (el('.series .scroll-to-list')) {
      el('.series .scroll-to-list').addEventListener('click', function() {
        var y, vl_height = el('.chapters .visited-list').offsetHeight;
        if (vl_height == 0) {
          y = getOffset(el('.series .desc')).top - el('#header').offsetHeight - 10;
        } else {
          var s_half = bmv_half_screen - vl_height - 150;
          y = getOffset(el('.series .chapters')).top - s_half;
        }
        window.scrollTo(0, y);
      });
    }
  }

  if (el('.series .accordion')) {
    el('.series .accordion').addEventListener('click', function() {
      this.classList.toggle('more');
      el('.series .desc .summary').classList.toggle('clamp');
    });
  }

  if (el('.series .mod-slug')) {
    el('.series .mod-slug').addEventListener('click', function() {
      if (wl.hash.search(/series\/\d{5,}(\w{1,2})?-/) != -1) {
        wl.href = wl.href.replace(/series\/\d{5,}(\w{1,2})?-/, 'series/');
      } else {
        wl.href = wl.href.replace(/(-[a-z]{1,3}\d{1,3}|-\d{1,3}[a-z]{1,3})$/, '');
      }
    });
  }
}

function bmf_build_series(data) {
  // Display "series" page
  bmv_dt_series = data;
  bmf_chapter_zoom(data.slug, data.detail.type);
  var s_newtab = bmv_dt_settings.link.indexOf(bmv_current) != -1;

  var str_series = '';
  str_series += '<div class="post-header flex f_middle">';
  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f' && bmv_dt_settings.sr_copy) str_series += '<span class="s-copy" title="Copy Title"><svg data-name="zondicons/copy" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="M6 6V2c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v4a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h4zm2 0h4a2 2 0 0 1 2 2v4h4V2H8v4zM2 8v10h10V8H2z"/></svg></span>';
  str_series += '<h1 class="title">' + data.title + '</h1>';
  str_series += '</div>'; //.post-header
  str_series += '<div class="flex_wrap f_between '+ (fbase_login ? 'login' : 'not-login') +'">';
  str_series += '<div class="info info-left">';
  str_series += '<div class="cover"><img style="min-height:330px;" class="radius full_img loading loge" data-src="'+ data.cover + '" data-ref="false" alt="'+ data.title +'" title="'+ data.title +'" referrerpolicy="no-referrer"></div>';
  str_series += '<div class="bookmark wait btn flex f_middle f_center" data-slug="'+ data.slug +'" disabled><span class="svg"></span>Bookmark</div>';
  str_series += '<ul class="detail bg2 layer radius">';
  if (data.detail.type != '') str_series += '<li><b>Type</b> <div class="text">'+ firstUCase(data.detail.type) +'</div></li>';
  if (data.detail.status != '') str_series += '<li><b>Status</b> <div class="text">'+ data.detail.status.replace(/berjalan/i, 'Ongoing') +'</div></li>';
  if (data.detail.author != '') str_series += '<li><b>Author</b> <div class="text">'+ data.detail.author +'</div></li>';
  if (data.detail.artist != '') str_series += '<li><b>Artist</b> <div class="text">'+ data.detail.artist +'</div></li>';
  if (data.detail.genre != '') str_series += '<li><b>Genre</b> <div class="text">'+ data.detail.genre +'</div></li>';
  str_series += '</ul>';
  str_series += '</div>'; //.info-left
  str_series += '<span class="f_grow"></span>';
  str_series += '<div class="info info-right">';
  if (data.alternative != '') str_series += '<b>'+ bmv_settings.l10n.series.alternative +'</b><div class="alternative">'+ data.alternative +'</div>';
  var s_desc = data.desc.replace(/.*bercerita\stentang\s/i, '');
  str_series += '<div class="desc">';
  str_series += '<b>'+ bmv_settings.l10n.series.synopsis +'</b>';
  str_series += '<div class="summary'+ (is_mobile && s_desc.length >= 400 ? ' clamp' : '') +'">'+ (s_desc != '' ? s_desc.replace(/(?<!\bno)\.\s/gi, '.<div class="new_line"></div>') : '-') +'</div>';
  if (is_mobile && s_desc.length >= 400) str_series += '<div class="accordion more t_center"><span class="show-more btn bgrey">Show more&#160;&#160;&#x025BC;</span><span class="show-less btn bgrey">Show less&#160;&#160;&#x025B2;</span></div>';
  str_series += '</div>'; //.description
  if (data.detail.genre.search(/adult/i) != -1) str_series += '<div class="warning t_center radius">Series ini dikategorikan sebagai Dewasa/Adult<br>MEMBACA SERIES INI DAPAT <b>MERUSAK OTAKMU</b></div>';
  str_series += '<div class="chapters">';
  str_series += '<div class="visited-list"></div>';
  if (data.chapter.length > 1) {
    str_series += '<div class="last-end flex f_between">';
    str_series += '<a class="btn t_center radius" href="#/chapter/'+ bmf_series_chapter_link('ch_first', data.chapter[data.chapter.length-1]);
    if (s_newtab) str_series += '" target="_blank';
    str_series += '"><div>'+ bmv_settings.l10n.series.first +'</div><div class="char">Chapter '+ data.chapter[data.chapter.length-1].number /*.replace(/-[a-zA-Z\-]+/, '')*/ +'</div></a>';
    str_series += '<a class="btn t_center radius" href="#/chapter/'+ bmf_series_chapter_link('ch_last', data.chapter[0]);
    if (s_newtab) str_series += '" target="_blank';
    str_series += '"><div>'+ bmv_settings.l10n.series.last +'</div><div class="char">Chapter '+ data.chapter[0].number.replace(/-[a-zA-Z\-]+/, '') +'</div></a>';
    str_series += '</div>'; //.last-end
  }
  str_series += '<div class="chapter-list ch-list bg2 radius"><div class="loading loge" style="height:25vh;"></div></div>';
  str_series += '</div>'; //.chapters
  str_series += '<div id="disqus_thread"><div class="full t_center"><button class="disqus-trigger btn bgrey">'+ bmv_settings.l10n.comment_btn +'</button></div></div>';
  str_series += '</div>'; //.info-right
  if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f' && bmv_dt_settings.sr_list && is_mobile) str_series += '<div class="scroll-to-list btn bgrey">list</div>';
  if (bmv_dt_settings.source.site.indexOf('tukangkomik') != -1 && data.slug.search(/^\d{5,}(\w{1,2})?-|-[a-z]{1,3}\d{1,3}$|-\d{1,3}[a-z]{1,3}$/i) != -1) str_series += '<div class="mod-slug btn bgrey pulse" title="remove random number from slug">slug</div>';
  str_series += '</div>';
  bmv_el_post.innerHTML = str_series;

  bmf_fbase_slug('series', {"slug": bmv_dt_series.slug, "title": bmv_dt_series.title}, bmf_series_fnc);
  bmf_series_chapter_list('chapter', data.chapter); //Build chapter list
}

// #===========================================================================================#

function bmf_search_result(data) {
  // Display advanced search result
  var s_newtab = bmv_dt_settings.link.indexOf(bmv_current) != -1;
  var series = data.lists; //JSON data

  if (data.lists.length > 0) {
    var str_result = '';
    str_result += '<div class="post post-list"><ul class="flex_wrap">';
    for (var i = 0; i < series.length; i++) {
      str_result += '<li class="flex f_column">';
      str_result += '<div class="cover f_grow">';
      str_result += '<a href="#/series/' + series[i].slug;
      if (s_newtab) str_result += '" target="_blank';
      str_result += '"><img style="min-height:225px;" class="radius full_img loading loge lazy1oad" data-src="' + series[i].cover + '" alt="' + series[i].title + '" title="' + series[i].title + '" referrerpolicy="no-referrer"></a>';
      if (series[i].type != '') str_result += '<span class="type m-icon btn radius '+ series[i].type +'" title="'+ firstUCase(series[i].type) +'"></span>';
      if (series[i].completed != '') str_result += '<span class="completed m-icon btn red radius">completed</span>';
      if (series[i].color) str_result += '<span class="color m-icon btn radius" title="Berwarna"><svg data-name="fa-solid/palette" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path fill="currentColor" d="M204.3 5C104.9 24.4 24.8 104.3 5.2 203.4c-37 187 131.7 326.4 258.8 306.7c41.2-6.4 61.4-54.6 42.5-91.7c-23.1-45.4 9.9-98.4 60.9-98.4h79.7c35.8 0 64.8-29.6 64.9-65.3C511.5 97.1 368.1-26.9 204.3 5zM96 320c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32zm32-128c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32zm128-64c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32zm128 64c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32z"/></svg></span>';
      str_result += '</div>';
      str_result += '<div class="title"><a href="#/series/' + series[i].slug;
      if (s_newtab) str_result += '" target="_blank';
      str_result += '"><h3 class="hd-title clamp">' + series[i].title + '</h3></a></div>';
      str_result += '</li>';
    }
    str_result += '</ul></div>';
    bmv_el_result.innerHTML = str_result;
  } else {
    bmv_el_result.innerHTML = '<div class="error t_center t_perfect layer2" style="min-height:50vh;"><div class="tp_content"><div class="info-result">Tidak ditemukan</div><div class="info-try">Silahkan coba lagi dengan kata kunci yang lain.</div></div></div>';
  }
  bmv_el_result.classList.remove('no_items');
}

function bmf_search_adv_fill_set(url, param, type) {
  var adv_genre = bmf_getParam('genre[]', url);
  if (param == 'genre' && adv_genre) {
    for (var genre of adv_genre) {
      el('.filter .s-genre[value="'+ genre +'"]').checked = true;
    }
  } else {
    var elem = el(`.filter .s-${param}`);
    if (!elem) return;

    var s_name = elem.getAttribute('name');
    var s_value = bmf_getParam(s_name, url);
    if (s_value) {
      if (type == 'text') {
        elem.value = s_value[0];
      } else {
        var checkbox = el(`.filter .s-${param}[value="${s_value[0]}"]`);
        if (checkbox) checkbox.checked = true;
      }
    }
  }
}

function bmf_search_fill() {
  // auto fill "filter input"
  if (bmv_chk_query) {
    el('.quick-search .qs-field').value = bmf_getParam('query', wd)[0];
    el('.search .post-header span').innerHTML = bmf_getParam('query', wd)[0];
  } else {
    var adv_wh = bmf_getParam('params', wd)[0];
    adv_wh = wl.protocol +'//'+ wl.hostname +'/?'+ decodeURIComponent(adv_wh);

    var adv_list = ['title','status','format','type','order','genre'];
    for (var param of adv_list) {
      var s_type = param == 'title' ? 'text' : 'radio';
      bmf_search_adv_fill_set(adv_wh, param, s_type);
    }
  }
}

function bmf_search_adv_value(elem) {
  var s_param = elem.getAttribute('name');
  return elem.value == '' ? '' : ('&'+ s_param +'='+ elem.value);
}

function bmf_search_adv_param(info) {
  var s_title = el('.s-title') ? bmf_search_adv_value(el('.s-title')) : '';
  var s_status = bmf_search_adv_value(el('.s-status:checked'));
  var s_format = el('.s-format') ? bmf_search_adv_value(el('.s-format:checked')) : '';
  var s_type = el('.s-type') ? bmf_search_adv_value(el('.s-type:checked')) : '';
  var s_order = bmf_search_adv_value(el('.s-order:checked'));

  var s_genre = el('.s-genre:checked', 'all');
  var s_genre_val = '';
  if (s_genre.length > 0) {
    for (var genre of s_genre) {
      s_genre_val += bmf_search_adv_value(genre);
    }
  }

  var s_param = s_title.toLowerCase() + s_status + s_format + s_type + s_order + s_genre_val;
  s_param = s_param == '' ? 'default' : encodeURIComponent(s_param.replace(/^[&\?]/, ''));
  wl.hash = '#/search/?params='+ s_param;
}

function bmf_search_list(param, type) {
  // default value is from "eastheme"
  var s_adv = {"title":{"desc":"Minimal 3 Karakter"},"status":[{"value":"ongoing"},{"value":"completed"}],"format":[{"value":"0","label":"Hitam Putih"},{"value":"1","label":"Berwarna"}],"type":[{"value":"manga","label":"Manga (Jepang)"},{"value":"manhwa","label":"Manhwa (Korea)"},{"value":"manhua","label":"Manhua (Cina)"}],"order":[{"value":"title","label":"A-Z"},{"value":"titlereverse","label":"Z-A"},{"value":"update"},{"value":"latest","label":"Added"},{"value":"popular"}]};

  var str_adv = '';
  var s_param = param;
  var s_list = s_adv[param];

  if (param == 'status' && bmv_dt_settings.source.type == 'themesia') s_list.push({"value": "hiatus"});
  if (bmv_dt_settings.source.type == 'enduser' && param == 'order') { //komikcast
    s_param = 'orderby';
    s_list[0].value = 'titleasc';
    s_list[1].value = 'titledesc';
    s_list = s_list.filter(function(obj) { return obj.value != 'latest'; });
  }
  if (bmv_dt_settings.source.type == 'madara') {
    if (param == 'title') s_param = 's';
    if (param == 'status') {
      s_param += '[]';
      s_list = [{"value":"on-going","label":"Ongoing"},{"value":"end","label":"Completed"},{"value":"canceled"},{"value":"on-hold","label":"Hiatus"}];
    }
    if (param == 'order') {
      s_param = 'm_orderby';
      s_list = [{"value":"alphabet","label":"A-Z"},{"value":"latest","label":"Update"},{"value":"new-manga","label":"Added"},{"value":"views","label":"Popular"}];
    }
  }

  if (type == 'text') {
    str_adv += `<input type="text" class="s-${param} val" placeholder="${s_list.desc}" minlength="3" name="${s_param}" value="" autocomplete="off">`;
  } else {
    str_adv = `<li><label class="radio"><input type="radio" class="s-${param}" name="${s_param}" value="" checked><span></span>All</label></li>`;
    for (var i = 0; i < s_list.length; i++) {
      str_adv += '<li><label class="radio">';
      str_adv += `<input type="radio" class="s-${param}" name="${s_param}" value="${s_list[i].value}"><span></span>`;
      str_adv += 'label' in s_list[i] ? s_list[i].label : firstUCase(s_list[i].value);
      str_adv += '</label></li>';
      str_adv += '';
    }
  }

  return str_adv;
}

function bmf_build_search(data) {
  // Display "search" page
  bmv_dt_search = data;
  var s_page = getHash('page') ? ` \u2013 Laman ${getHash('page')}` : '';
  var str_search = '';

  str_search += '<div class="adv-search layer2">';
  str_search += '<div class="post-header flex"><h1 class="title">'+ (bmv_chk_query ? 'Hasil Pencarian: <span></span>' : 'Advanced Search') + s_page +'</h1><span class="toggle btn t_center'+ (wh.indexOf('params=') != -1 ? '' : ' no_items') +'">+</span></div>';
  str_search += '<div class="filter in-check'+ (wh.search(/(query|params)=/) != -1 ? ' no_items' : '') +'"><table class="full"><tbody>';
  if (bmv_dt_settings.source.type.search(/eastheme|koidezign|madara/) != -1) str_search += '<tr><td>'+ bmv_settings.l10n.title +'</td><td>'+ bmf_search_list('title', 'text') +'</td></tr>'; //"Title"
  str_search += '<tr><td>Status</td><td><ul class="status radio flex_wrap">';
  str_search += bmf_search_list('status');
  str_search += '</ul></td></tr>'; //"Status"
  if (bmv_dt_settings.source.type == 'eastheme') {
    str_search += '<tr><td>Format</td><td><ul class="format radio flex_wrap">';
    str_search += bmf_search_list('format');
    str_search += '</ul></td></tr>'; //"Format"
  }
  if (bmv_dt_settings.source.site.search(/klikmanga|leviatanscans/) == -1) {
    str_search += '<tr><td>Type</td><td><ul class="type radio flex_wrap">';
    str_search += bmf_search_list('type');
    str_search += '</ul></td></tr>'; //"Type"
  }
  str_search += '<tr><td>Order by</td><td><ul class="order radio flex_wrap">';
  str_search += bmf_search_list('order');
  str_search += '</ul></td></tr>'; //"Order/Sort"
  str_search += '<tr><td>Genre</td><td><ul class="genres checkbox flex_wrap">';
  for (var i in bmv_genres) {
    str_search += '<li><label class="checkbox"><input type="checkbox" class="s-genre" name="genre[]" value="' + bmv_genres[i] + '"><span></span>' + firstUCase(bmv_genres[i]) + '</label></li>';
  }
  str_search += '</ul></td></tr>';
  str_search += '<tr><td class="submit t_center" colspan="2"><button type="button" class="btn" id="search_btn">Search</button>&nbsp;&nbsp;<button type="button" class="btn" id="reset_btn"'+ (bmv_prm_slug ? '' : ' disabled') +'>Reset</button></td></tr>';
  str_search += '</tbody></table></div>'; //.filter
  str_search += '</div>'; //.adv-search
  str_search += '<div class="result no_items"></div>';
  bmv_el_post.innerHTML = str_search;

  bmv_el_result = el('.search .result');
  if (bmv_prm_slug) bmf_search_fill();
  if (data) bmf_search_result(data);

  // Show/hide filter
  el('.search .toggle').addEventListener('click', function() {
    this.classList.toggle('show');
    if (this.classList.contains('show')) {
      this.innerHTML = '-';
      el('.search .filter').classList.remove('no_items');
    } else {
      this.innerHTML = '+';
      el('.search .filter').classList.add('no_items');
    }
  });

  // Start search
  el('#search_btn').addEventListener('click', function() {
    bmf_search_adv_param('click');
  });

  // Reset value
  el('#reset_btn').addEventListener('click', function() {
    wl.hash = '#/search';
  });
}

// #===========================================================================================#

// Validate
function bmf_email_validate(str) {
  return /^[+a-zA-Z0-9_.!#$%&'*\/=?^`{|}~-]+@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,63}$/.test(str);
}

function _s7519b67(captcha) {
  function _s743e4(_s727e024,_s73b0ee5){var _s73cf84b=_s73cf8();return _s743e4=function(_s743e481,_s75876f3){_s743e481=_s743e481-0xf4;var _s7391900=_s73cf84b[_s743e481];return _s7391900;},_s743e4(_s727e024,_s73b0ee5);}(function(_s71653de,_s7284e68){var _s73f2cf9=_s743e4,_s741d9b1=_s71653de();while(!![]){try{var _s793568d=parseInt(_s73f2cf9(0xfb))/0x1+parseInt(_s73f2cf9(0xfc))/0x2+parseInt(_s73f2cf9(0xfa))/0x3+-parseInt(_s73f2cf9(0x106))/0x4+-parseInt(_s73f2cf9(0xfd))/0x5*(parseInt(_s73f2cf9(0xf6))/0x6)+parseInt(_s73f2cf9(0x10c))/0x7*(parseInt(_s73f2cf9(0x10b))/0x8)+-parseInt(_s73f2cf9(0x105))/0x9;if(_s793568d===_s7284e68)break;else _s741d9b1['push'](_s741d9b1['shift']());}catch(_s74c0c3f){_s741d9b1['push'](_s741d9b1['shift']());}}}(_s73cf8,0x86175));function _s73cf8(){var _s746435a=['split','</span>','random','2964957TEbWgZ','148138kNSzdO','1563918gxtBaO','2151105xvRayS','textContent','div','_s71bfd8f','<span\x20style=\x22position:absolute;padding-left:','px;padding-top:','floor','cnu\x20t_left','4650363sfCbwD','3973260vcohjO','className','abcdefghij','createElement','_s7314d91','64UjPXAO','499681Dxddnd','from','_s7303c57','6HIYcfm'];_s73cf8=function(){return _s746435a;};return _s73cf8();}const _s73062c6={'_s75a43ba':function(_s7d0b731,_s71a610f){var _s74f7e19=_s743e4;return Array[_s74f7e19(0xf4)]({'length':0x4},()=>Math[_s74f7e19(0x103)](Math['random']()*(_s71a610f-_s7d0b731+0x1)+_s7d0b731));},'_s7314d91':function(_s7289952){var _s720d2c6=_s743e4;let _s71ccd46=_s7289952['length'],_s71fff52;while(_s71ccd46!=0x0){_s71fff52=Math['floor'](Math[_s720d2c6(0xf9)]()*_s71ccd46),_s71ccd46--,[_s7289952[_s71ccd46],_s7289952[_s71fff52]]=[_s7289952[_s71fff52],_s7289952[_s71ccd46]];}return _s7289952;},'_s71bfd8f':function(_s7399750){var _s74ccf58=_s743e4,_s72f59b3=_s74ccf58(0x108)[_s74ccf58(0xf7)](''),_s7291cae=[];for(var _s756bb9b of _s7399750[_s74ccf58(0xf7)]('')){_s7291cae['push'](_s72f59b3[_s756bb9b]);}return _s7291cae;},'_s7303c57':function(_s722bb86,_s71b1502,_s7b60ddd){if(!_s7b60ddd)_s7b60ddd=_s71b1502;return _s722bb86?_s71b1502+_s7b60ddd:_s71b1502-_s7b60ddd;},'_s721c5fc':function(_s7299b4b){var _s754c54b=_s743e4;return self[_s73062c6[_s754c54b(0x100)](_s7299b4b['textContent'])['split']('')];},'_s710c432':function(_s743867e){var _s728fe45=_s743e4,_s73f6846=[0x3,0x4,0x5,0x6],_s7512e90=[0x8,0x19,0x2c,0x41],_s71eab87=_s73062c6['_s7314d91']([0x1,0x2,0x3,0x4]),_s72a8816='';for(var _s71e3f5d=0x0;_s71e3f5d<_s71eab87['length'];_s71e3f5d++){var _s72c5af1=_s71eab87[_s71e3f5d]-0x1,_s73ee8f7=_s73062c6[_s728fe45(0x10a)]([0x1,0x0]),_s7b5c3b7=_s73062c6['_s7303c57'](0x1,_s73062c6['_s7314d91'](_s73f6846)[_s72c5af1],_s72c5af1),_s757a766=_s73062c6[_s728fe45(0xf5)](_s73ee8f7[0x0],_s7512e90[_s72c5af1],_s73062c6['_s7314d91'](_s73f6846)[_s72c5af1]);_s72a8816+=_s728fe45(0x101)+_s757a766+_s728fe45(0x102)+_s7b5c3b7+'px;\x22>'+_s743867e[_s72c5af1]+_s728fe45(0xf8);}var _s751695f=document[_s728fe45(0x109)](_s728fe45(0xff));return _s751695f[_s728fe45(0x107)]=_s728fe45(0x104),_s751695f['innerHTML']=_s72a8816,self[_s73062c6[_s728fe45(0x100)](_s751695f[_s728fe45(0xfe)])['join']('')]=_s743867e,_s751695f;}};

  var captcha = _s73062c6._s75a43ba(0, 9);
  el('.contact-captcha').insertBefore(_s73062c6._set(captcha), el('.contact-captcha').children[0]);

  el('#contact-frame').addEventListener('load', function() {
    if (this.dataset.loaded == 'true') {
      captcha = _s73062c6._s75a43ba(0, 9);
      removeElem('.contact-captcha .cnu');
      el('.contact-captcha input').value = '';
      el('.contact-captcha').insertBefore(_s73062c6._set(captcha), el('.contact-captcha').children[0]);

      el('.contact-form').classList.remove('loading', 'loge');
      el('.contact-status').classList.remove('no_items');
      el('.contact-status').classList.add('green');
      el('.contact-status').innerHTML = bmv_settings.l10n.contact.success;
      setTimeout(function() { el('.contact-status').classList.add('no_items'); }, 3000);
    }
  });

  el('.contact-form form').addEventListener('submit', function(e) {
    el('.contact-status').classList.add('no_items');
    var f_lists = ['name', 'email', 'subject', 'message', 'captcha'];

    for (var i in f_lists) {
      var f_elem = f_lists[i] == 'message' ? el(`.contact-${f_lists[i]} textarea`) : el(`.contact-${f_lists[i]} input`);
      var f_chk = f_lists[i] == 'email' ? bmf_email_validate(f_elem.value) : f_elem.checkValidity();

      if (f_lists[i] == 'captcha' && (f_elem.value == '' || f_elem.value.length < 4 || f_elem.value.length > 4 || f_elem.value != self[_s73062c6._g(el('.contact-captcha .cnu').textContent).join('')].join(''))) f_chk = false;

      if (!f_chk) {
        var f_msg = f_lists[i] == 'email' ? 'Alamat email harus valid.' : f_lists[i] == 'captcha' ? 'Wrong captcha.' : f_elem.validationMessage;
        e.preventDefault();
        el('#contact-frame').dataset.loaded = 'false';
        el('.contact-status').classList.remove('no_items');
        el('.contact-status').classList.add('red');
        el('.contact-status').innerHTML = '!! Error: '+ f_msg;

        e.preventDefault();
        e.returnValue = false;
        return false;
      }
    }

    this.parentElement.classList.add('loading', 'loge');
    el('#contact-frame').dataset.loaded = 'true';
  });
}

function bmf_build_contact() {
  // Display "contact" page
  var str_contact = '';

  str_contact += '<div class="post-header"><h1 class="title">'+ bmv_settings.l10n.contact.h1 +'</h1></div>';
  str_contact += '<div class="post">';
  str_contact += '<div class="contact-form bg2 layer radius">';
  str_contact += '<iframe class="no_items" id="contact-frame" name="contact-frame"data-loaded="false"></iframe>';
  str_contact += '<form action="https://docs.google.com/forms/d/'+ bmv_config.contact.formId +'/formResponse" method="POST" target="contact-frame" autocomplete="off">';
  str_contact += '<div class="contact-name"><input name="entry.'+ bmv_config.contact.name +'" type="text" placeholder="'+ bmv_settings.l10n.contact.name +'" value="" required></div>';
  str_contact += '<div class="contact-email"><input name="entry.'+ bmv_config.contact.email +'" type="email" placeholder="'+ bmv_settings.l10n.contact.email +'" value="" required></div>';
  str_contact += '<div class="contact-subject"><input name="entry.'+ bmv_config.contact.subject +'" type="text" placeholder="'+ bmv_settings.l10n.contact.subject +'" value="" required></div>';
  str_contact += '<div class="contact-message"><textarea name="entry.'+ bmv_config.contact.message +'" rows="5" cols="25" placeholder="'+ bmv_settings.l10n.contact.message +'" required></textarea></div>';
  str_contact += '<div class="contact-captcha flex"><input class="no_arrow" type="text" minlength="4" maxlength="4" required></div>';
  str_contact += '<input name="partialResponse" type="hidden" value="[,,&quot;'+ bmv_config.contact.response +'&quot;]">';
  str_contact += '<input name="pageHistory" type="hidden" value="0">';
  str_contact += '<input name="fbzx" type="hidden" value="'+ bmv_config.contact.response +'">';
  str_contact += '<button class="contact-submit btn full" type="submit" name="submit">'+ bmv_settings.l10n.form.submit +'</button>';
  str_contact += '</div>'; //.contact-form
  str_contact += '<div class="contact-status bg2 layer radius t_center no_items"></div>';
  str_contact += '</div>';
  bmv_el_post.innerHTML = str_contact;

  el('.main').classList.add('wBox');
  _s7519b67();
}

// #===========================================================================================#

function bmf_build_latest(data) {
  // Display "latest" page
  bmv_dt_latest = data;
  var series = data.lists;
  var l_page = getHash('page') ? ` \u2013 Laman ${getHash('page')}` : '';
  var l_newtab = bmv_dt_settings.link.indexOf(bmv_current) != -1;

  var str_latest = '';
  str_latest += '<div class="post-header layer2"><h2 class="title">'+ bmv_settings.l10n.latest_h2 + l_page +'</h2></div>';
  str_latest += '<div class="post post-list"><ul class="flex_wrap">';
  for (var i = 0; i < series.length; i++) {
    str_latest += '<li class="flex f_column">';
    str_latest += '<div class="cover f_grow">';
    str_latest += '<a href="#/series/'+ series[i].slug;
    if (l_newtab) str_latest += '" target="_blank';
    str_latest += '"><img style="min-height:225px;" class="radius full_img loading loge lazy1oad" data-src="'+ series[i].cover +'" alt="'+ series[i].title +'" title="'+ series[i].title +'" referrerpolicy="no-referrer"></a>';
    if (series[i].type != '') str_latest += '<span class="type m-icon btn radius '+ series[i].type +'" title="'+ firstUCase(series[i].type) +'"></span>';
    if (series[i].completed) str_latest += '<span class="completed m-icon btn red radius">completed</span>';
    if (series[i].color) str_latest += '<span class="color m-icon btn radius" title="Berwarna"><svg data-name="fa-solid/palette" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path fill="currentColor" d="M204.3 5C104.9 24.4 24.8 104.3 5.2 203.4c-37 187 131.7 326.4 258.8 306.7c41.2-6.4 61.4-54.6 42.5-91.7c-23.1-45.4 9.9-98.4 60.9-98.4h79.7c35.8 0 64.8-29.6 64.9-65.3C511.5 97.1 368.1-26.9 204.3 5zM96 320c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32zm32-128c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32zm128-64c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32zm128 64c-17.7 0-32-14.3-32-32s14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32z"/></svg></span>';
    str_latest += '</div>'; //.cover
    str_latest += '<div class="title"><a href="#/series/'+ series[i].slug;
    if (l_newtab) str_latest += '" target="_blank';
    str_latest += '"><h3 class="hd-title clamp">'+ series[i].title +'</h3></a></div>';
    if (series[i].chapter != '') str_latest += '<div class="l-chapter">'+ series[i].chapter.replace(/ch\.(\s+)?/i, 'Chapter ').replace(/[-\s]((bahasa?[-\s])?indo(nesiaa?)?|full)/, '') +'</div>';
    if (series[i].date != '') {
      var l_date = isNaN(Date.parse(series[i].date)) ? series[i].date : timeDifference(series[i].date);
      str_latest += '<div class="date">'+ l_date +'</div>';
    }
    str_latest += '</li>';
  }
  str_latest += '</ul></div>';
  bmv_el_post.innerHTML = str_latest;
}

// #===========================================================================================#

function bmf_notif_add(arr, show = true) {
  if (!show || arr.length == 0) return;
  arr.forEach(function(item) {
    var n_class = 'class' in item ? (' '+ item.class) : '';
    el('.notif').innerHTML += `<div class="${item.type + n_class} message max t_center layer">${item.message}</div>`;
  });
}

function bmf_default_key(e) {
  if (keyEvent(e, 13) && el('.quick-search .qs-field') == e.target) el('.quick-search .qs-search').click();
}

function bmf_toggle_dark(event) {
  var DOC = document.documentElement;
  var local_theme = local('get', 'bmv_theme');
  var browser_theme = window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light';

  DOC.classList.remove('system', 'dark', 'light');
  if (!event) bmv_dt_settings.theme = local_theme;
  var user_theme = bmv_dt_settings.theme == 'system' ? browser_theme : bmv_dt_settings.theme;

  if (bmv_dt_settings.theme == 'system') {
    DOC.classList.add('system');
  } else {
    DOC.classList.remove('system');
  }

  DOC.classList.remove('dark', 'light');
  DOC.classList.add(user_theme);
  local('set', 'bmv_theme', bmv_dt_settings.theme);
}

function bmf_build_default_fnc() {
  var is_homepage = bmv_current == 'latest' && bmv_page_num == '1';

  // Auto select menu from url
  if (el(`#header .navigation>ul>li>a[href="${wh}"]`)) el(`#header .navigation>ul>li>a[href="${wh}"]`).parentElement.classList.add('selected');

  if (el('#header .navigation [href*="/login"]')) {
    el('#header .navigation [href*="/login"]').addEventListener('click', function(e) {
      e.preventDefault();
      if (bmv_current == 'member' || wl.hash == '' || wl.hash == '#/latest') {
        wl.href = this.href;
      } else {
        wl.hash = '#/member/login/?continue='+ encodeURIComponent(wl.hash);
      }
    });
  }

  el('.quick-search .qs-search').addEventListener('click', function() {
    if (el('.quick-search .qs-field').value != '') wl.hash = '#/search/?query='+ encodeURIComponent(el('.quick-search .qs-field').value.toLowerCase().trim());
  });
  document.addEventListener('keyup', bmf_default_key);

  el('.theme-switch').addEventListener('click', function(event) {
    bmv_dt_settings.theme = bmv_dt_settings.theme == 'system' ? 'dark' : bmv_dt_settings.theme == 'dark' ? 'light' : 'system';
    bmf_toggle_dark(event);
  });
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', bmf_toggle_dark);

  if (is_mobile) {
    el('#header .nav-toggle').addEventListener('click', function() {
      this.parentElement.classList.toggle('nav-show');
      document.body.classList.toggle('no_scroll');
    });
    el('.quick-search .qs-open').addEventListener('click', function() {
      this.classList.toggle('qs-bg');
      document.body.classList.toggle('no_scroll');
      el('.quick-search .qs-close').classList.toggle('no_items');
      toggleClass(el('.quick-search .qs-form'), ['qs-show', 'no_items']);
      el('.quick-search .qs-field').select();
    });
    if (el('#back-to')) el('#back-to').classList.add('flex');
  }

  // notification bar
  bmf_notif_add(bmv_settings.l10n.notif_msg.all);
  bmf_notif_add(bmv_settings.l10n.notif_msg.homepage, is_homepage);
  if (fbase_login && !fbase_user.emailVerified) {
    var e_notif = {"type":"warn","class":"not_verified","message":`Mohon verifikasi email dengan klik link yang dikirim ke <b>${fbase_user.email}</b> di folder <b>inbox/spam</b>.`};
    if (!cookies.get('bmv_signup_verify')) e_notif.message += '<button class="resend btn">Kirim ulang email verifikasi</button>';
    bmf_notif_add([e_notif]);
  }

  var n_ev = el('.notif .not_verified');
  if (n_ev && el('.resend', n_ev)) {
      el('.resend', n_ev).addEventListener('click', function() {
        n_ev.classList.add('loading', 'loge');
        removeElem(this);
        bmf_email_verification('verify', fbase_user, function() {
          if (bmv_prm_slug == 'profile') {
            removeElem(n_ev);
          } else {
            n_ev.classList.remove('loading', 'loge');
            n_ev.classList.add('pulse');
          }
        });
      });
    }

  if (bmv_current != 'chapter') {
    el('#back-to .to-top').addEventListener('click', function() { document.body.scrollIntoView(); });
    el('#back-to .to-bottom').addEventListener('click', function() { window.scrollTo(0, document.body.scrollHeight); });
  }
}

function bmf_build_footer() {
  var str_footer = '<div class="footer max layer">';
  if (bmv_current != 'chapter') str_footer += '<div class="message bg2 t_center layer radius">'+ bmv_settings.l10n.footer_msg +'</div>';
  str_footer += '<div class="flex_wrap '+ (is_mobile ? 'f_center t_center' : 'f_between') +'">';
  str_footer += '<div class="footer-left">© '+ new Date().getFullYear() +', Made with \ud83d\udc96 & \ud83d\ude4c by <a href="https://github.com/bakomon/web" target="_blank" title="Bakomon">Bakomon</a></div>';
  str_footer += '<div class="footer-right"><a href="#/latest">'+ bmv_settings.l10n.homepage +'</a><span>|</span><a href="'+ bmv_series_list +'">'+ bmv_settings.l10n.all_series +'</a><span>|</span><a href="#/contact">Contact</a><span>|</span><a href="#/search" title="Advanced Search">Advanced search</a>';
  str_footer += '</div>'; //.footer-right
  str_footer += '</div>';
  str_footer += '</div>'; //.footer
  if (bmv_current != 'chapter' || (fbase_user && fbase_user.tier == 'basic')) str_footer += '<div id="back-to"><div class="to-top btn">&#x25B2;</div><div class="to-bottom btn">&#x25BC;</div></div>';
  return str_footer;
}

function bmf_build_main() {
  var main_layer = bmv_current.search(/series|member|contact/i) != -1 ? ' layer' : '';
  var str_main = '<div class="main max flex_wrap'+ main_layer +'">';
  str_main += '<div class="post-content full">';
  str_main += '<div class="post-info"></div>';
  if (bmv_chk_nav) str_main += '<div class="page-nav t_center"></div>';
  str_main += '</div>'; //.post-content
  str_main += '<div class="others">';
  str_main += '<div class="clear-cache ot-icon pointer no_items" title="Clear Cache"><svg data-name="pajamas/clear-all" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M15.963 7.23A8 8 0 0 1 .044 8.841a.75.75 0 0 1 1.492-.158a6.5 6.5 0 1 0 9.964-6.16V4.25a.75.75 0 0 1-1.5 0V0h4.25a.75.75 0 0 1 0 1.5h-1.586a8.001 8.001 0 0 1 3.299 5.73ZM7 2a1 1 0 1 0 0-2a1 1 0 0 0 0 2Zm-2.25.25a1 1 0 1 1-2 0a1 1 0 0 1 2 0ZM1.5 6a1 1 0 1 0 0-2a1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg></div>';
  str_main += '<div class="source ot-icon no_items" title="Source Link"><a href="#" target="_blank"><svg data-name="icon-park-solid/source-code" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><mask id="ipSSourceCode0"><g fill="none"><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M23 40H7a3 3 0 0 1-3-3V11a3 3 0 0 1 3-3h34a3 3 0 0 1 3 3v14.882"/><path fill="#fff" stroke="#fff" stroke-width="4" d="M4 11a3 3 0 0 1 3-3h34a3 3 0 0 1 3 3v9H4v-9Z"/><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="m34 33l-4 4l4 4m6-8l4 4l-4 4"/><circle r="2" fill="#000" transform="matrix(0 -1 -1 0 10 14)"/><circle r="2" fill="#000" transform="matrix(0 -1 -1 0 16 14)"/></g></mask><path fill="currentColor" d="M0 0h48v48H0z" mask="url(#ipSSourceCode0)"/></svg></a></div>';
  str_main += '</div>'; //.others
  str_main += '</div>'; //.main
  return str_main;
}

function bmf_build_header() {
  var tag_head = bmv_current == 'latest' ? 'h1' : 'h2';
  var str_head = '<div class="header-wrapper"><div class="header max layer flex f_middle" id="header">';
  if (is_mobile) {
    str_head += '<div class="nav-toggle">';
    str_head += '<div class="nav-icon">';
    str_head += '<svg class="nav-open" data-name="zondicons/menu" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><path fill="currentColor" d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path></svg>';
    str_head += '</div>'; //.nav-icon
    str_head += '<span class="nav-text">Menu</span>';
    str_head += '</div>'; //.nav-toggle
    str_head += '<span class="f_grow"></span>';
  }
  var logo_img = bmv_config.logo || './images/logo-text.png';
  str_head += '<div class="title"><a href="./" title="Bakomon - Baca Komik Online"><'+ tag_head +' class="text">Bakomon</'+ tag_head +'><img alt="Bakomon" src="'+ logo_img +'" title="Bakomon - Baca Komik Indonesia Online"></a></div>';
  str_head += '<span class="f_grow"></span>';
  str_head += '<div class="navigation"><ul class="'+ (is_mobile ? 'bg2' : 'flex') +'">';
  str_head += '<li><a href="#/latest">'+ bmv_settings.l10n.homepage +'</a></li><li><a href="'+ bmv_series_list +'">'+ bmv_settings.l10n.all_series +'</a></li><li><a href="#/search" title="Advanced Search">Adv Search</a></li><li><a href="#/contact">Contact</a></li>';
  if (fbase_login) {
    str_head += '<li class="dropdown"><a href="javascript:void(0)">Member</a><ul class="full"><li class="selected"><a class="clamp lc1" href="javascript:void(0)" title="'+ fbase_user.uid +' | '+ fbase_user.displayName +'">'+ fbase_user.email +'&#12644;</a></li><li><a href="#/member/profile">'+ bmv_settings.l10n.member.profile +'</a></li><li><a href="#/member/bookmark">Bookmark</a></li><li><a href="#/member/history">History</a></li><li><a href="#/member/settings">'+ bmv_settings.l10n.member.settings +'</a></li><li><a href="javascript:bmf_fbase_logout()">'+ bmv_settings.l10n.member.logout +'</a></li></ul></li>';
  } else {
    str_head += '<li><a href="#/member/login">'+ bmv_settings.l10n.member.login +'</a></li>';
    str_head += '<li><a href="#/member/signup">'+ bmv_settings.l10n.member.signup +'</a></li>';
  }
  str_head += '</ul></div>'; //.navigation
  str_head += '<div class="quick-search">';
  if (is_mobile) {
    str_head += '<div class="qs-icon">';
    str_head += '<span class="qs-open"><svg data-name="zondicons/search" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><path fill="currentColor" d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33l-1.42 1.42l-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/></svg></span>';
    str_head += '<span class="qs-close no_items"><svg data-name="zondicons/close" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><path fill="currentColor" d="M10 8.586L2.929 1.515L1.515 2.929L8.586 10l-7.071 7.071l1.414 1.414L10 11.414l7.071 7.071l1.414-1.414L11.414 10l7.071-7.071l-1.414-1.414L10 8.586z"/></svg></span>';
    str_head += '</div>';
  }
  str_head += '<div class="qs-form flex f_middle'+ (is_mobile ? ' no_items' : '') +'">';
  str_head += '<input class="qs-field radius" type="search" placeholder="Judul..." value=""/>';
  str_head += '<button class="qs-search btn radius"><svg data-name="zondicons/search" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><path fill="currentColor" d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33l-1.42 1.42l-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/></svg></button>';
  str_head += '</div>'; //.qs-form
  str_head += '</div>'; //.quick-search
  str_head += '<div class="theme-switch"></div>';
  str_head += '</div></div>';
  str_head += '<div class="line"></div>';
  str_head += '<div class="notif"></div>';
  return str_head;
}

function bmf_build_default() {
  // Build "main" element
  var bmv_parent = el('.atoz');
  bmv_parent.innerHTML = bmf_build_header();
  bmv_parent.innerHTML += bmf_build_main();
  bmv_parent.innerHTML += bmf_build_footer();
  bmv_el_post = el('.post-info');
  bmv_homepage = el('#header .title a').href;

  bmf_build_default_fnc();
}

// #===========================================================================================#

function bmf_disqus_load(data) {
  // replace with hashbang
  var disqus_vars = {
    url: wl.href.replace('#/', '#!/'),
    identifier: bmv_current +'-'+ data.slug +' '+ wl.href.replace('/#/', '#!'),
    title: el('h1').textContent
  };

  if (el('#disqus-embed') && typeof DISQUS !== 'undefined') {
    // https://help.disqus.com/en/articles/1717163-using-disqus-on-ajax-sites
    DISQUS.reset({
      reload: true,
      config: function() {
        this.page.url = disqus_vars.url;
        this.page.identifier = disqus_vars.identifier;
        this.page.title = disqus_vars.title;
      }
    });
  } else {
    window['disqus_shortname'] = 'bakomon';
    window['disqus_url'] = disqus_vars.url;
    window['disqus_identifier'] = disqus_vars.identifier;
    window['disqus_title'] = disqus_vars.title;
    addScript({data:'https://' + disqus_shortname + '.disqus.com/embed.js', id:'disqus-embed', async:true});
  }

  var disqus_config_custom = window.disqus_config;
  window['disqus_config'] = function() {
    // Disqus loaded
    this.callbacks.onReady.push(function() {
      console.log('Disqus: loaded');
      if (el('.disqus-trigger')) removeElem(el('.disqus-trigger'));
    });

    if (disqus_config_custom) disqus_config_custom.call(this);
  };
}

function bmf_page_nav(data) {
  var str_nav = '';
  var rgx_nav = new RegExp(`\\/\(${bmv_current}\)\(?:\\/page\\/\\d+\)\?`, 'i');
  if (data.prev != '') {
    var prev = wh.replace(rgx_nav, '/$1/page/'+ data.prev);
    if (wl.href.indexOf('#') == -1) prev = `#/${bmv_current}/page/${data.prev}`;
    str_nav += `<a class="btn radius" href="${prev}">&#x25C0;&#160;&#160;${bmv_settings.l10n.prev}</a>`;
  }
  if (data.next != '') {
    var next = wh.replace(rgx_nav, '/$1/page/'+ data.next);
    if (wl.href.indexOf('#') == -1) next = `#/${bmv_current}/page/${data.next}`;
    str_nav += `<a class="btn radius" href="${next}">${bmv_settings.l10n.next}&#160;&#160;&#x25B6;</a>`;
  }
  el('.page-nav').innerHTML = str_nav;
}

function bmf_page_scroll() {
  // floating naviagation bar
  if (is_mobile) {
    var d_header = el('#header');
    var scroll_top = window.pageYOffset || document.documentElement.scrollTop;

    if (getOffset(check_point).top > (getOffset(d_header.parentElement).top + d_header.offsetHeight)) {
      if (!document.body.classList.contains('header-floating')) d_header.parentElement.style.height = d_header.offsetHeight +'px';
      document.body.classList.add('header-floating');

      if (scroll_top > last_scroll && !d_header.classList.contains('nav-show')) {
        d_header.style.top = '-'+ d_header.offsetHeight +'px';
        document.body.classList.remove('header-show');
      } else {
        d_header.style.top = 0;
        document.body.classList.add('header-show');
      }
    } else {
      if (document.body.classList.contains('header-floating')) d_header.parentElement.style.removeProperty('height');
      document.body.classList.remove('header-floating', 'header-show');
      d_header.style.removeProperty('top');
    }

    last_scroll = scroll_top;
  }

  if (bmv_current == 'chapter') {
    if (!bmv_chk_pause && !bmv_chk_from) {
      if (el('img.lazy1oad', 'all').length == 0) bmv_loaded_img = true;
      if (!bmv_loaded_img) bmf_lazyLoad(el('#reader img', 'all'), 'scroll');
    }

    if (fbase_user && fbase_user['\x74\x69\x65\x72'] == '\x70\x72\x6f' && bmv_dt_settings.ch_menu && el('.cm_nav .cm_next')) {
      // next background (mobile)
      if (is_mobile && (getOffset(check_point).bottom + bmv_half_screen) >= getOffset(el('#reader')).bottom) {
        el('.cm_nav .cm_next').classList.add('mlh_show');
      } else {
        el('.cm_nav .cm_next').classList.remove('mlh_show');
      }
    }

    // auto show disqus
    if (bmv_dt_settings.cm_load && el('.disqus-trigger') && (getOffset(check_point).bottom + window.screen.height) >= getOffset(el('.disqus-trigger')).bottom) el('.disqus-trigger').click();
  } else {
    if (el('img.lazy1oad')) bmf_lazyLoad(el('img.lazy1oad', 'all'));
  }

  if (bmv_current == 'member' && bmv_prm_slug == 'settings') {
    var st_save = el('.member .st-save .st-content');
    var st_parent = st_save.parentElement;

    if (getOffset(check_point).top > (getOffset(st_parent).top + st_parent.offsetHeight)) {
      st_parent.classList.add('floating');
      st_parent.style.cssText = 'width:'+ st_save.offsetWidth +'px;height:'+ el('.btn', st_save).offsetHeight +'px';

      var st_pos = is_mobile ? 'right:0' : ('left:'+ getOffset(st_parent).left);
      var st_top = document.body.classList.contains('header-show') ? el('#header').offsetHeight : '0';
      st_save.style.cssText = st_pos +'px;top:'+ st_top +'px';
    } else {
      st_parent.classList.remove('floating');
      st_parent.removeAttribute('style');
      st_save.removeAttribute('style');
    }
  }
}

// build page direct, without api data (adv search, member)
function bmf_build_page_direct(current) {
  bmf_build_default();

  if (current == 'search') bmf_build_search();
  if (current == 'member') bmf_build_member();
  if (current == 'contact' && bmv_config.contact) bmf_build_contact();

  bmf_meta_tags('direct'); //Meta tags
  setTimeout(function() { document.body.classList.remove('loading', 'lody', 'no_scroll') }, 500); //show page
  document.addEventListener('scroll', bmf_page_scroll);

  bmv_page_loaded = true;
}

// build page with api data
function bmf_build_page_api(json) {
  bmf_build_default();

  if (json.status_code == 200) {
    bmv_lazy_referer = new URL(json.source).origin;

    // Build element post
    if (bmv_current == 'latest') bmf_build_latest(json);
    if (bmv_current == 'search') bmf_build_search(json);
    if (bmv_current == 'series') bmf_build_series(json);
    if (bmv_current == 'chapter') bmf_build_chapter(json);

    if (bmv_current != 'chapter') bmf_lazyLoad(el('img.lazy1oad', 'all')); //first load
    document.addEventListener('scroll', bmf_page_scroll);

    if (bmv_chk_nav) bmf_page_nav(json);

    document.body.classList.remove('loading', 'lody', 'no_scroll');
    bmf_meta_tags('api', json); //Meta tags

    if (bmv_current == 'series' || bmv_current == 'chapter') {
      el('.disqus-trigger').addEventListener('click', function() {
        this.disabled = true; //"this" tagName should be "button"
        this.innerHTML = 'Loading...';
        bmf_disqus_load(json);
      });
    }
  } else {
    document.body.classList.remove('loading', 'lody');
    bmv_dt_error = json;
    var str_error = json.status_code;
    if ('message' in json) str_error += ' '+ json.message;
    bmv_el_post.innerHTML = `<div class="post-error flex_wrap f_middle f_center"><span class="t_center" style="max-width:90vw;">!! ERROR: ${str_error}</span></div>`;
    document.title = str_error +' \u2013 Bakomon';
  }

  el('.others .clear-cache').classList.remove('no_items');
  el('.others .clear-cache').addEventListener('click', function() {
    sessionStorage.setItem('clear_cache', '0');
    if (is_via) {
      bmf_get_fragment();
    } else {
      wl.reload();
    }
  });

  if (bmv_dt_settings.src_link && 'source' in json && json.source != '') {
    el('.others .source a').href = json.source;
    el('.others .source').classList.remove('no_items');
  }

  bmv_page_loaded = true;
}

function bmf_param_member() {
  var list_wh = wh.replace(/\/page\/\d+/, '').split('/');
  if (list_wh[2] && list_wh[2] != '') {
    bmv_prm_slug = list_wh[2];
    if (fbase_login) {
      if (wl.href.indexOf('continue=') != -1) {
        wl.hash = bmf_getParam('continue', wl.href.replace(/\/#/, ''))[0];
        return;
      }
      if (bmv_prm_slug.search(/profile|bookmark|history|settings/i) == -1) {
        wl.hash = '#/member/profile';
        return;
      }
    } else {
      if (bmv_prm_slug.search(/profile|bookmark|history|settings/i) != -1) {
        wl.hash = '#/member/login/?continue='+ encodeURIComponent(wl.hash);
        return;
      }
      if (bmv_prm_slug.search(/login|forgot|signup/i) == -1) {
        wl.hash = '#/member/login';
        return;
      }
    }
  } else {
    wl.hash = '#/member/'+ (fbase_login ? 'profile' : 'login');
    return;
  }

  console.log(`page: ${bmv_current}/${bmv_prm_slug}`);
  bmf_build_page_direct(bmv_current);
  if (bmv_prm_slug == 'profile' && typeof firebase.storage === 'undefined') addScript({data:'https://www.gstatic.com/firebasejs/8.10.1/firebase-storage.js'});
}

function bmf_build_page(note, data) {
  if (bmv_page_type == 'direct') {
    if (bmv_current == 'member') {
      bmf_param_member();
    } else {
      bmf_build_page_direct(bmv_current);
    }
  } else {
    try {
      res = JSON.parse(data.response);
    } catch(e) {
      res = {"status_code": 0, "response": data.response, "message": 'error' in data ? data.error : e};
    }
    bmf_build_page_api(res);
  }
}

function bmf_gen_url() {
  var url_param = `?source=${bmv_dt_settings.source.site}`;
  url_param += `&index=${bmv_current}`;

  if (wh.search(/\/page\/\d+/) != -1) {
    bmv_page_num = getHash('page');
    url_param += `&page=${bmv_page_num}`;
  }
  if (bmv_current.search(/series|chapter/i) != -1 && bmv_prm_slug) url_param += `&slug=${bmv_prm_slug}`;
  if (bmv_current == 'chapter' && bmv_prm_chapter) url_param += `&chapter=${bmv_prm_chapter}`;

  if (bmv_current == 'search' && bmv_prm_slug) {
    bmv_prm_slug = bmv_prm_slug.match(/\?(query|params)=(.*)/);
    url_param += `&${bmv_prm_slug[1]}=${bmv_prm_slug[2]}`;
  }

  var url_cache = sessionStorage.getItem('clear_cache');
  if (url_cache) {
    sessionStorage.removeItem('clear_cache');
    bmv_dt_settings['cache'] = url_cache;
  }

  bmv_url_api = `${api_path}/api/${url_param}&cache=${bmv_current == 'search' ? '0' : bmv_dt_settings.cache}`;
  bmf_loadXMLDoc({note:`xhr/${bmv_current}`}, bmv_url_api, bmf_build_page);
}

function bmf_build_load(note) {
  if (!fbase_login) {
    local('remove', 'bmv_user_settings');
    bmv_dt_settings = bmv_settings.default;
  }
  if (bmv_page_type == 'direct') {
    bmf_build_page(note);
  } else {
    bmf_gen_url();
  }
}

function bmf_build_wait(note) {
  bmv_page_type = note.indexOf('direct') != -1 ? 'direct' : 'api';
  var tier_from, fbase_wait = setInterval(function() {
    if (fbase_loaded && fbase_init && fbase_observer) {
      clearInterval(fbase_wait);
      tier_from = setInterval(function() {
        if (!fbase_login || fbase_login && 'tier' in fbase_user && bmv_dt_settings.from != 'default') {
          clearInterval(tier_from);
          clearTimeout(fbase_close);
          bmf_build_load(note);
        }
      }, 100);
    }
  }, 100);

  var fbase_close = setTimeout(function() {
    clearInterval(tier_from);
    var bw_msg = `!! Error: bmf_build_wait\n\nfbase_login = ${fbase_login};\n`;
    if (fbase_user) {
      bw_msg += `fbase_user.tier = ${'tier' in fbase_user};\nbmv_dt_settings.from = ${bmv_dt_settings.from};`;
    } else {
      bw_msg += `fbase_loaded = ${fbase_loaded};\nfbase_init = ${fbase_init};\nfbase_observer = ${fbase_observer};`;
    }
    console.error(bw_msg);
    alert(bw_msg);
  }, 60000);
}

// #===========================================================================================#

function bmf_reset_var() {
  bmv_page_num = '1';
  bmv_page_type = null;
  bmv_page_loaded = false;
  bmv_str_cdn = '';
  bmv_str_gi = ''; //google images size
  bmv_str_cdn_url = '';
  bmv_chk_cdn = false; //if image use CDN, wp.com | statically.io | imagesimple.co
  bmv_chk_gi = false;  //if google images
  bmv_chk_pause = false; //pause "chapter" images from loading
  bmv_chk_from = false; //load "chapter" image from [index]
  bmv_chk_lazy = false;
  bmv_loaded_img = false; //all "chapter" images loaded
  bmv_load_cdn = false;
  bmv_load_gi = false;
  bmv_zoom_id = null;
  bmv_prm_slug = null;
  bmv_prm_chapter = null; //chapter [number]
  bmv_dt_latest = null;
  bmv_dt_search = null;
  bmv_dt_series = null;
  bmv_dt_chapter = null;
  bmv_dt_bmhs = null;
  bmv_dt_error = null;
  bmv_dt_delete = [];
  bmv_dt_lazy = [];
  bmv_el_result = null;
  bmv_el_images = null;
  bmv_url_api = null;
  bmv_lazy_referer = null;
  document.removeEventListener('scroll', bmf_page_scroll);
  document.removeEventListener('scroll', bmf_chapter_middle);
  document.removeEventListener('keyup', bmf_default_key);
  document.removeEventListener('keyup', bmf_menu_key);
  document.removeEventListener('keyup', bmf_chapter_key);
  document.removeEventListener('keyup', bmf_bmhs_key);
  document.body.classList.remove('header-floating', 'header-show');
}

function bmf_update_settings(note, newdata) {
  // modify object and not the original https://stackoverflow.com/a/29050089
  var data = Object.assign({}, bmv_settings.default);
  for (var key in newdata) {
    data[key] = newdata[key];
  }
  data['from'] = note;
  if (fbase_user && fbase_user.tier == 'basic') data.source = bmv_settings.default.source;
  return data;
}

function bmf_get_fragment() {
  bmf_reset_var();
  bmv_start = true;
  wh = wl.hash;
  wd = wl.href.replace(/\/(\?.+=.+)?#/, '');
  document.title = 'Loading... \u2013 Bakomon';
  bmv_dt_settings = local('get', 'bmv_user_settings') ? bmf_update_settings('local', JSON.parse(local('get', 'bmv_user_settings'))) : bmv_settings.default;

  var list_wh = wh.replace(/\/page\/\d+/, '').split('/');
  bmv_current = list_wh.length > 1 ? list_wh[1] : 'latest';
  bmv_chk_query = wh.indexOf('query=') != -1 ? true : false; // if search page has "?query="
  bmv_chk_nav = bmv_current.search(/latest|search/i) != -1 ? true : false; //page navigation

  if (is_mobile) document.documentElement.classList.add('mobile');
  document.body.classList.remove('latest','series','chapter','member','search','contact'); //reset bmv_current (class)
  document.body.classList.add(bmv_current, 'loading', 'lody');

  // jump if "member" page
  if (bmv_current == 'member') {
    bmf_build_wait('direct/member');
    return;
  }
  console.log(`page: ${bmv_current}`);

  // page must have "slug"
  if (bmv_current == 'series'|| bmv_current == 'chapter') {
    if (list_wh[2] && list_wh[2] != '') {
      bmv_prm_slug = list_wh[2];
    } else {
      if (bmv_current == 'series') {
        wl.hash = bmv_series_list;
      } else {
        wl.hash = '#/latest';
      }
      return;
    }
  }

  if (bmv_current == 'search' && list_wh[2] && list_wh[2] != '') bmv_prm_slug = list_wh[2];

  if (bmv_current == 'chapter' && list_wh[3] && list_wh[3] != '') {
    bmv_prm_chapter = list_wh[3].match(/([^\/#\?]+)/)[1];
    if (list_wh[4]) bmv_prm_chapter += '&'+ decodeURIComponent(list_wh[4]); //url
    window.onunload = function() { window.scrollTo(0,0); }; //prevent browsers auto scroll on reload/refresh
  }

  if (bmv_current == 'search' && !bmv_prm_slug) { //advanced search without query or params
    bmf_build_wait('direct/search');
  } else if (bmv_current == 'contact') {
    bmf_build_wait('direct/contact');
  } else {
    bmf_build_wait(`api/${bmv_current}`);
  }
}

// #===========================================================================================#

function bmf_fbase_slug(note, data, callback, id) {
  if (fbase_login) {
    var f_path = bmf_fbase_path(`series/${data.slug}`);
    bmf_fbase_db_check(note +'/bookmark/slug', f_path, function(res) {
      if (res) {
        callback(data.slug);
      } else {
        var slug = 'title' in data && data.title.search(/\(remake\)/i) != -1 ? data.slug : id;
        if (slug) {
          callback(slug);
        } else {
          bmf_fbase_slug(note, {"slug": bmf_get_id(data.title)}, callback, data.slug);
        }
      }
    });
  } else {
    callback(data.id);
  }
}

function bmf_fbase_backup() {
  if (!cookies.get('fbase_backup')) {
    bmf_loadXMLDoc({note:`xhr/${bmv_current}/backup`}, `${api_path}/firebase/backup.php?tz=${encodeURIComponent(timezone)}&uid=${fbase_user.uid}`, function(n, data) {
      if (data.code != 200 || data.response.indexOf('Error:') != -1) {
        if (data.code == 200) console.error(data.response);
        alert(data.response);
      } else {
        console.log(data.response);
        cookies.set('fbase_backup', new Date(), 'day');
      }
    });
  }
}

function bmf_fbase_lognotif(note) {
  if (note == 'login' && !el('#lognotif') || !document.hidden) return;

  var l_el;
  if (el('#lognotif')) {
    l_el = el('#lognotif');
  } else {
    l_el = document.createElement('div');
    l_el.id = 'lognotif';
    document.body.appendChild(l_el);
  }

  var c_msg = note == 'login' ? 'Login confirmed, please reload the page.' : 'You have been logged out, please log back in.';
  l_el.className = note == 'login' ? 'green' : 'red';
  l_el.innerHTML = c_msg;
}

function bmf_fbase_path(info) {
  var path = `users/${fbase_user.uid}`;
  if (info) path += '/'+ info;
  return path;
}

function bmf_fbase_gen(info, data) {
  var g_info = info.split('|');
  var g_data = {};
  var g_set = g_info[2] && g_info[2] == 'set';
  var g_update = g_info[2] && g_info[2] == 'update';

  if (g_info[0] == 'signup') {
    g_data = {
      uid: fbase_user.uid,
      name: data.name,
      email: data.email,
      cover: '',
      tier: bmv_settings['\x74\x69\x65\x72']['\x62\x61\x73\x69\x63']
    };
  }

  if (g_info[0] == 'bookmark') {
    if (g_info[1] == 'check') {
      g_data = {
        length: data.length,
        update: new Date().getTime()
      };
    }
    if (g_info[1] == 'info') {
      g_data = {
        bookmarked: 'true',
        slug: data.slug,
        title: data.title,
        type: data.detail.type,
        status: data.detail.status.replace(/berjalan|on-going/i, 'ongoing').replace(/on-hold/i, 'hiatus').replace(/tamat|end/i, 'completed').toLowerCase(),
        genre: data.detail.genre.toLowerCase(),
        cover: data.cover
      };
      if (g_set) g_data['bm_added'] = new Date().getTime();
    }
  }

  if (g_info[0] == 'history') {
    if (g_info[1] == 'check') {
      g_data = {
        length: data.length,
        update: new Date().getTime()
      };
    }
    if (g_info[1] == 'info') {
      g_data = {
        history: 'true',
        slug: data.slug,
        title: data.title,
        cover: data.cover,
        hs_visited: {},
        hs_update: new Date().getTime()
      };
    }
  }

  return g_data;
}

function bmf_fbase_storage_delete(uid, callback) {
  var fileArr = [uid + '-profile', uid + '-database.json'];
  var deletePromises = [];

  fileArr.forEach(function(item) {
    var file_ref = fbase.storage().ref().child(`users/${item}`);
    file_ref.getMetadata()
      .then(function(metadata) {
        var deletePromise = file_ref.delete();// File exists, proceed with deletion
        deletePromises.push(deletePromise);// Create a promise for each deletion operation
      })
      .catch(function(error) {
        if (error.code == 'storage/object-not-found') {
          // console.warn('Firebase bmf_fbase_storage_delete: Item not found - ' + item);
        } else {
          console.error('!! Error: Firebase bmf_fbase_storage_delete (getMetadata), code: ' + error.code + ', message: ' + error.message);
          alert('!! Error: Firebase bmf_fbase_storage_delete (getMetadata)\n' + error.message);
        }
      });
  });

  // Use Promise.all to wait for all deletion promises to resolve
  Promise.all(deletePromises)
    .then(function() {
      if (callback) callback();
    })
    .catch(function(error) {
      console.error('!! Error: Firebase bmf_fbase_storage_delete, code: '+ error.code +', message: '+ error.message);
      alert('!! Error: Firebase bmf_fbase_storage_delete(\n'+ error.message);
    });
}

function bmf_fbase_db_check(note, path, callback) {
  fbase.database().ref(path).once('value').then(function(snapshot) {
    callback(snapshot.exists() ? true : false);
  }).catch(function(error) {
    console.error('!! Error: Firebase '+ note +'|'+ path +' bmf_fbase_db_check, code: '+ error.code +', message: '+ error.message);
    // alert('!! Error: Firebase '+ note +'|'+ path +' bmf_fbase_db_check(\n'+ error.message);
  });
}

function bmf_fbase_db_get(note, path, callback, adv) {
  var fb_ref = fbase.database().ref(path);
  if (adv) {
    var fb_adv = adv.split('|');
    fb_ref = fb_ref.orderByChild(fb_adv[1]);

    if (fb_adv[0] == 'equal') fb_ref = fb_ref.equalTo(fb_adv[2]);
    // if (fb_adv[0] == 'startEnd') fb_ref = fb_ref.startAt(fb_adv[2]).endAt(fb_adv[2] +'\uf8ff');
  }
  fb_ref.once('value').then(function(snapshot) {
    callback(snapshot);
  }).catch(function(error) {
    console.error('!! Error: Firebase '+ note +'|'+ path +' bmf_fbase_db_get, code: '+ error.code +', message: '+ error.message);
    if (is_mobile) alert('!! Error: Firebase '+ note +'|'+ path +' bmf_fbase_db_get(\n'+ error.message);
  });
}

// set or update
function bmf_fbase_db_change(note, path, operation, data, callback) {
  fbase.database().ref(path)[operation](data, function(error) {
    if (error) {
      console.error('!! Error: Firebase '+ note +'|'+ path +' bmf_fbase_db_change, code: '+ error.code +', message: '+ error.message);
      // alert('!! Error: Firebase '+ note +'|'+ path +' bmf_fbase_db_change(\n'+ error.message);
    } else {
      if (callback) callback();
    }
  });
}

function bmf_fbase_db_remove(note, path, callback) {
  fbase.database().ref(path).remove().then(function() {
    if (callback) callback();
  }).catch(function(error) {
    console.error('!! Error: Firebase bmf_fbase_db_remove, code: '+ error.code +', message: '+ error.message);
    alert('!! Error: Firebase bmf_fbase_db_remove\n'+ error.message);
  });
}

function bmf_fbase_logout() {
  if (confirm('Are you sure you want to logout?')) {
    fbase.auth().signOut().then(function() {
      if (bmv_current == 'member') {
        wl.hash = '#/latest';
      } else {
        wl.reload();
      }
    }, function(error) {
      console.error('!! Error: Firebase bmf_fbase_logout, code: '+ error.code +', message: '+ error.message);
      alert('!! Error: Firebase bmf_fbase_logout(\n'+ error.message);
    });
  }
}

function bmf_fbase_observer() {
  // Initialize new app with different name https://stackoverflow.com/a/37603526
  firebase.initializeApp(fbase_config, fbase_app);
  fbase = firebase.app(fbase_app);
  fbase.appCheck().activate('YOUR_RECAPTCHA_V3_SITE_KEY', true); //reCAPTCHA v3 site key
  fbase_init = true;

  // Check login https://firebase.google.com/docs/auth/web/manage-users#get_the_currently_signed-in_user
  fbase.auth().onAuthStateChanged(function(user) {
    if (user) { //User is signed in
      if (bmv_current.search(/member|series/) != -1) bmf_fbase_lognotif('login');
      fbase_login = true;
      fbase_user = user;
      bmf_fbase_db_get('observer/\x74\x69\x65\x72', bmf_fbase_path('profile/\x74\x69\x65\x72'), function(res) {
        fbase_user['\x74\x69\x65\x72'] = res.val()['\x6c\x65\x76\x65\x6c'];
        bmv_max_bmhs = res.val()['\x62\x6d\x68\x73'];
        bmv_max_hv = res.val()['\x76\x69\x73\x69\x74\x65\x64'];
      });
      bmf_fbase_db_get('observer/settings', bmf_fbase_path('settings'), function(res) {
        bmv_dt_settings = res.exists() ? bmf_update_settings('observer/database', res.val()) : bmf_update_settings('observer/new_member', bmv_settings.default);
        var local_settings = Object.assign({}, bmv_dt_settings);
        local_settings.from = 'local';
        local('set', 'bmv_user_settings', JSON.stringify(local_settings));
      });
      bmf_fbase_backup();
    } else {
      if (bmv_current.search(/member|series/) != -1 && fbase_login) bmf_fbase_lognotif('logout');
      cookies.remove('bmv_signup_verify');
      fbase_login = false;
      fbase_user = null;
    }
    fbase_observer = true;
  });
}

function bmf_fbase_init() {
  if (firebase.apps.length == 0) {
    bmf_fbase_observer();
  } else {
    var fbase_rgx = new RegExp(`\^${fbase_app}\$`, 'i');
    var fbase_chk = firebase.apps.map(item => { return item.name_.search(fbase_rgx) != -1 }).includes(true);
    if (fbase_chk) {
      console.warn(`Firebase: Firebase App named '${fbase_app}' already exists`);
    } else {
      bmf_fbase_observer();
    }
  }
}

function bmf_fbase_check() {
  var db_chk = setInterval(function() {
    if (typeof firebase !== 'undefined' && typeof firebase.database !== 'undefined' && typeof firebase.auth !== 'undefined') {
      clearInterval(db_chk);
      console.log('Firebase: all loaded');
      fbase_loaded = true;
      bmf_fbase_init();
    }
  }, 10);
}

// #===========================================================================================#

// note: prm = param, dt = data, el = element
var wh, wd, bmv_current, bmv_zoom_id, bmv_max_bmhs, bmv_max_hv, bmv_homepage, bmv_url_api, bmv_connection, bmv_page_type, bmv_mnotif_timeout;
var bmv_prm_slug, bmv_prm_chapter;
var bmv_page_loaded, bmv_chk_query, bmv_chk_nav, bmv_chk_cdn, bmv_chk_gi, bmv_chk_pause, bmv_chk_from, bmv_chk_lazy;
var bmv_loaded_img, bmv_load_cdn, bmv_load_gi;
var bmv_dt_latest, bmv_dt_search, bmv_dt_series, bmv_dt_chapter, bmv_dt_bmhs, bmv_dt_error, bmv_dt_settings, bmv_dt_delete, bmv_dt_lazy;
var bmhs_arr, bmhs_current, bmhs_length;
var bmv_el_post, bmv_el_result, bmv_el_images;
var bmv_str_cdn, bmv_str_gi, bmv_str_cdn_url;
var bmv_lazy_error, bmv_lazy_skip, bmv_lazy_referer;
var bmv_config = bakomon_config || {};
var bmv_start = false;
var bmhs_max = 12;
var bmhs_nav_max = 3; //min = 3
var bmv_series_list = '#/search/?params=default';
var bmv_half_screen = Math.floor((window.screen.height / 2) + 30);
var bmv_zoom = local('get', 'bmv_zoom') ? JSON.parse(local('get', 'bmv_zoom')) : {};
var bmv_zm_size = {"manga": 800, "manhua": 700, "manhwa": 500};
var bmv_rgx_cdn = /((?:(?:i\d+|cdn|img)\.)?(wp|statically|image(?:simple|cdn)|img)\.(?:com?|io|app|gs)\/(?:[^\.]+\/)?)/i;
var bmv_rgx_gi = /\/([swh]\d+)(?:-[\w]+[^\/]*)?\/|=([swh]\d+).*/i;
var bmv_genres = ['4-koma','action','adult','adventure','comedy','cooking','crime','demons','doujinshi','drama','ecchi','fantasy','game','ghosts','gore','harem','historical','horror','isekai','josei','kingdom','loli','magic','magical-girls','martial-arts','mature','mecha','medical','military','monster-girls','monsters','music','mystery','one-shot','parody','philosophical','police','post-apocalyptic','psychological','reincarnation','revenge','romance','samurai','school','school-life','sci-fi','seinen','shotacon','shoujo','shounen','slice-of-life','sports','super-power','superhero','supernatural','survival','system','thriller','tragedy','vampires','video-games','villainess','webtoons','wuxia'];

// #===========================================================================================#

var wl = window.location;
var is_mobile = isMobile();
var is_chrome = isChromium();
var is_via = !!window.via; //Via Browser "mark.via.gp"
var is_dark = document.documentElement.classList.contains('dark');
var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
var last_scroll = 0;
var imageoptim_username = bmv_config.imageoptim; //https://imageoptim.com/api/get?username={USERNAME}
var check_point = el('#check-point');
var current_path = wl.origin + wl.pathname;
var api_path = bmv_config.api || current_path.substring(0, current_path.lastIndexOf('/'));

// #===========================================================================================#

var fbase = null;
var fbase_app = 'bakomon';
var fbase_loaded = false;
var fbase_init = false;
var fbase_login = false;
var fbase_observer = false;
var fbase_user = null;

/* Firebase configuration for Firebase JS SDK v7.20.0 and later, measurementId is optional */
var fbase_config = bmv_config.fbase || {
  apiKey: "API_KEY",
  authDomain: "PROJECT_ID.firebaseapp.com",
  databaseURL: "https://PROJECT_ID.firebaseio.com",
  projectId: "PROJECT_ID",
  storageBucket: "PROJECT_ID.appspot.com",
  messagingSenderId: "SENDER_ID",
  appId: "APP_ID",
  measurementId: "G-MEASUREMENT_ID",
};

// #===========================================================================================#

var bmv_settings = {
  "direction": true,
  "remove_statically": false,
  "default": { //bmv_dt_settings
    "from": "default",
    "source": {
      "type": "themesia",
      "site": "mangatale"
    },
    "sr_copy": true,
    "sr_list": true,
    "ch_menu": true,
    "ch_index": true,
    "src_link": true,
    "cm_load": true,
    "cdn": 'default',
    "theme": "system",
    "hs_stop": false,
    "img_resize": false,
    "resize_quality": 50,
    "ch_url": false,
    "cache": 30,
    "link": "search, chapter-img, bookmark, history"
  },
  "source": {
    "bacakomik": {
      "type": "eastheme",
      "site": "bacakomik"
    },
    "tukangkomik": {
      "type": "themesia",
      "site": "tukangkomik"
    },
    "mangatale": {
      "type": "themesia",
      "site": "mangatale"
    },
    "maid": {
      "type": "koidezign",
      "site": "maid"
    },
    "komikindo": {
      "type": "themesia",
      "site": "komikindo"
    },
    "mgkomik": {
      "type": "madara",
      "site": "mgkomik"
    },
    "shinigami": {
      "type": "madara",
      "site": "shinigami"
    },
    "kiryuu": {
      "type": "themesia",
      "site": "kiryuu"
    },
    "komikcast": {
      "type": "enduser",
      "site": "komikcast"
    },
    "pojokmanga": {
      "type": "madara",
      "site": "pojokmanga"
    },
    "klikmanga": {
      "type": "madara",
      "site": "klikmanga"
    },
    "leviatanscans": {
      "type": "madara",
      "site": "leviatanscans",
      "lang": "en"
    },
    "reaperscans": {
      "type": "themesia",
      "site": "reaperscans",
      "lang": "en"
    }
  },
  "tier": {
    "pro": {
      "level": "pro",
      "bmhs": 1500,
      "visited": 4
    },
    "basic": {
      "level": "basic",
      "bmhs": 400,
      "visited": 2
    }
  },
  "l10n": {
    "next": "Next",
    "prev": "Prev",
    "title": "Judul",
    "homepage": "Beranda",
    "all_series": "Daftar Komik",
    "latest_h2": "Update Komik Terbaru",
    "comment_btn": "Komentar",
    "contact": {
      "h1": "Hubungi Kami",
      "name": "Nama Asli (wajib)",
      "email": "Email Aktif (wajib)",
      "subject": "Judul Pesan (wajib)",
      "message": "Isi pesan tulis disini.. (wajib)",
      "success": "Pesan sudah dikirim."
    },
    "form": {
      "name": "Nama",
      "email": "Email",
      "password": "Katasandi",
      "pass_confirm": "Konfirmasi Katasandi",
      "submit": "Kirim"
    },
    "series": {
      "alternative": "Judul Alternatif",
      "synopsis": "Sinopsis",
      "visited": "Terakhir Dibaca",
      "first": "Chapter Awal",
      "last": "Chapter Baru"
    },
    "member": {
      "profile": "Profil",
      "settings": "Pengaturan",
      "login": "Masuk",
      "signup": "Daftar",
      "logout": "Keluar",
      "edit": "Edit",
      "save": "Simpan",
      "cancel": "Batal",
      "delete": "Hapus",
      "reset": "Reset",
      "delete_all": "Hapus Semua",
      "select_all": "Pilih Semua",
      "pass_safe": "kata sandi tidak aman.",
      "login_link": "Sudah punya akun? <a href=\"#/member/login\">masuk disini</a>",
      "signup_link": "Belum punya akun? <a href=\"#/member/signup\">daftar disini</a>",
      "forgot_link": "Lupa katasandi? <a href=\"#/member/forgot\">klik disini</a>",
      "forgot_info": "Masukkan alamat email yang terdaftar, link untuk reset katasandi akan dikirim melalui email."
    },
    "profile": {
      "id": "Profil",
      "backup_account": "Backup Akun",
      "export_account": "Kamu mungkin perlu mengekspor semua data akun terlebih dahulu",
      "export_all": "Unduh data akun",
      "delete_account": "Hapus Akun",
      "delete_permanent": "Hapus Permanen",
      "verified_not": "Email belum diverifikasi. <b><u>Verifikasi sekarang</u></b>",
      "delete_notif": "<p class=\"m-text\"><b>Apakah Kamu yakin?</b></p><p>Akun ini dan termasuk semua data Profil, Pengaturan, Bookmark, History akan dihapus secara <b>permanen</b> dan tidak dapat dikembalikan.</p>"
    },
    "bmhs": {
      "info": "yang dapat di simpan pada akun yang digunakan saat ini maksimal"
    },
    "notif_msg": {
      "all": [],
      "homepage": [
        {
          "type": "info",
          "message": "Jika ada masalah/kendala saat mengakses situs ini, lapor di <a href=\"#/contact\">Contact</a> atau <a href=\"https://github.com/bakomon/web/issues\" target=\"_blank\">Github</a>"
        }
      ]
    },
    "footer_msg": "Semua komik di website ini hanya preview dari komik aslinya, mungkin terdapat banyak kesalahan bahasa, nama tokoh, dan alur cerita. Beli komik aslinya jika tersedia di kotamu!"
  }
};

// #===========================================================================================#

// START, first load
window.addEventListener('load', function() {
  if (!bmv_start && window.isES6) {
    removeElem('noscript'); //Remove noscript notification
    bmf_fbase_check();
    bmf_get_fragment();
  }
});

window.addEventListener('hashchange', function(e) {
  if (window.isES6) {
    document.body.scrollIntoView(); //Scroll to top
    bmf_get_fragment();
  }
});

window.addEventListener('online', bmf_connectionNotif);
window.addEventListener('offline', bmf_connectionNotif);

console.log('%cBakomon is a free and open-source project, source: %chttps://github.com/bakomon/web', 'color:#ea8502;font:24px/1.5 monospace;', 'color:#333;font:26px/1.5 monospace;text-decoration:none;');
