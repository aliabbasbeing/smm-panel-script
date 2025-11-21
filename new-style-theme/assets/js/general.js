/* ===========================
   AI Chatbot (Named Functions)
   =========================== */

/* ------ State ------ */
var AIW = {
  root: null,
  panel: null,
  body: null,
  input: null,
  form: null,
  fab: null,
  closeBtn: null,
  sendBtn: null,
  isOpen: false,
  endpoint: "/chatbot/ask",
  name: "AI Chatbot",
  primary: null,
  STORAGE_KEY: "aiw_history_v1",
  EXPIRE_MS: 60 * 60 * 1000,
  MAX_MSGS: 100,
  DEBOUNCE_MS: 700,
  lastSendAt: 0,
  NORMAL_BOTTOM: 78,
  KB_BOTTOM: 2
};

/* ------ Small Utils ------ */
function aiChatbotQS(sel) { return document.querySelector(sel); }
function aiChatbotScrollToBottom() {
  if (!AIW.body) return;
  AIW.body.scrollTop = AIW.body.scrollHeight;
  requestAnimationFrame(function () {
    AIW.body.scrollTop = AIW.body.scrollHeight;
    setTimeout(function () { AIW.body.scrollTop = AIW.body.scrollHeight; }, 60);
  });
}
function aiChatbotAppendCSRF(fd) {
  var n = aiChatbotQS("meta[name='csrf-name']");
  var h = aiChatbotQS("meta[name='csrf-hash']");
  if (n && h) fd.append(n.content, h.content);
  else if (typeof window.token !== "undefined") fd.append("token", window.token);
}
function aiChatbotNowCanSend() {
  var now = Date.now();
  if (now - AIW.lastSendAt < AIW.DEBOUNCE_MS) return false;
  AIW.lastSendAt = now;
  return true;
}

/* ------ History ------ */
function aiChatbotLoadHistory() {
  try {
    var raw = JSON.parse(localStorage.getItem(AIW.STORAGE_KEY) || "null");
  } catch (e) { raw = null; }
  if (Array.isArray(raw)) {
    raw = { time: Date.now(), msgs: raw };
    localStorage.setItem(AIW.STORAGE_KEY, JSON.stringify(raw));
  }
  if (!raw || !Array.isArray(raw.msgs)) return [];
  if (Date.now() - (raw.time || 0) > AIW.EXPIRE_MS) {
    localStorage.removeItem(AIW.STORAGE_KEY);
    return [];
  }
  return raw.msgs;
}
function aiChatbotSaveHistory(arr) {
  if (!Array.isArray(arr)) arr = [];
  if (arr.length > AIW.MAX_MSGS) arr = arr.slice(arr.length - AIW.MAX_MSGS);
  localStorage.setItem(AIW.STORAGE_KEY, JSON.stringify({ time: Date.now(), msgs: arr }));
}
function aiChatbotRenderHistory() {
  var hist = aiChatbotLoadHistory();
  if (!hist.length) return;
  AIW.body.innerHTML = "";
  for (var i = 0; i < hist.length; i++) {
    aiChatbotAddMessage(hist[i].t, hist[i].w, false);
  }
  aiChatbotScrollToBottom();
}

/* ------ Layout / Position ------ */
function aiChatbotApplyBottom(px) {
  if (!AIW.panel) return;
  AIW.panel.style.bottom =
    "calc(env(safe-area-inset-bottom,0px) + " + Math.max(0, Math.round(px)) + "px)";
}
function aiChatbotIsKBOpen() {
  if (!window.visualViewport) return false;
  var diff = window.innerHeight - visualViewport.height;
  return diff > 120 && document.activeElement && document.activeElement.tagName === "INPUT";
}
function aiChatbotKBOccluded() {
  if (!window.visualViewport) return 0;
  var vv = visualViewport;
  var occluded = window.innerHeight - vv.height - vv.offsetTop;
  return occluded > 0 ? Math.round(occluded) : 0;
}
function aiChatbotRefreshPosition() {
  if (!AIW.panel || !AIW.isOpen) return;
  aiChatbotApplyBottom(aiChatbotIsKBOpen() ? (AIW.KB_BOTTOM + aiChatbotKBOccluded()) : AIW.NORMAL_BOTTOM);
}

/* ------ Show / Hide ------ */
function aiChatbotShow() {
  if (!AIW.panel) return;
  AIW.panel.style.display = "flex";
  AIW.isOpen = true;
  aiChatbotApplyBottom(AIW.NORMAL_BOTTOM);
  aiChatbotScrollToBottom();
}
function aiChatbotHide() {
  if (!AIW.panel) return;
  AIW.panel.style.display = "none";
  AIW.isOpen = false;
  aiChatbotApplyBottom(AIW.NORMAL_BOTTOM);
}
function aiChatbotToggle() { AIW.isOpen ? aiChatbotHide() : aiChatbotShow(); }

/* ------ Messages ------ */
function aiChatbotAddMessage(text, who, save) {
  if (!AIW.body) return;
  var m = document.createElement("div");
  m.className = "aiw-msg " + (who === "user" ? "aiw-user" : "aiw-bot");
  m.textContent = text || "";
  AIW.body.appendChild(m);
  aiChatbotScrollToBottom();
  if (save !== false) {
    var hist = aiChatbotLoadHistory();
    hist.push({ w: who === "user" ? "user" : "bot", t: text || "" });
    aiChatbotSaveHistory(hist);
  }
}
function aiChatbotSendMessage(message) {
  if (!message) return;
  aiChatbotAddMessage(message, "user", true);

  if (AIW.input) {
    AIW.input.value = "";
    setTimeout(function () { try { AIW.input.focus(); } catch (e) {} aiChatbotRefreshPosition(); }, 0);
  }

  var fd = new FormData();
  fd.set("message", message);
  aiChatbotAppendCSRF(fd);

  var xhr = new XMLHttpRequest();
  xhr.open("POST", AIW.endpoint, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState !== 4) return;
    var txt = xhr.responseText || "";
    if (xhr.status >= 200 && xhr.status < 300) {
      var j = null;
      try { j = JSON.parse(txt); } catch (e) {}
      var reply = (j && (j.reply || j.answer)) ? (j.reply || j.answer) : (txt || "No response.");
      aiChatbotAddMessage(reply, "bot", true);
    } else {
      var friendly = xhr.status === 403
        ? "Session expire ho gaya. Page refresh karke dubara try karein."
        : "Maaf kijiye, abhi thoda issue aa gaya. Dubara try karein.";
      aiChatbotAddMessage(friendly, "bot", true);
    }
    setTimeout(aiChatbotRefreshPosition, 60);
  };
  xhr.send(fd);
}
function aiChatbotOnSend(e) {
  if (e) { e.preventDefault(); e.stopImmediatePropagation(); }
  if (!AIW.input) return;
  var msg = (AIW.input.value || "").trim();
  if (!msg || !aiChatbotNowCanSend()) return;
  aiChatbotSendMessage(msg);
}

/* ------ Build UI ------ */
function aiChatbotBuildUI(options) {
  options = options || {};
  AIW.endpoint = options.endpoint || AIW.endpoint;
  AIW.name = options.name || AIW.name;
  AIW.primary = options.primary || null;

  AIW.root = document.createElement("div");
  AIW.root.id = "aichat";
  if (AIW.primary) AIW.root.style.setProperty("--aiw-primary", AIW.primary);

  AIW.root.innerHTML = [
    '<button class="aiw-fab" type="button" aria-label="Open chat" title="Chat">',
    '  <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">',
    '    <path d="M2 5a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3H9l-5 5v-5H5a3 3 0 0 1-3-3V5z"/>',
    '  </svg>',
    '</button>',
    '<div class="aiw-panel" role="dialog" aria-modal="true" aria-labelledby="aiwTitle">',
    '  <div class="aiw-head">',
    '    <span class="aiw-title" id="aiwTitle"></span>',
    '    <button class="aiw-close" type="button" aria-label="Close">',
    '      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">',
    '        <path d="M18.3 5.71L12 12.01l-6.29-6.3-1.41 1.42 6.29 6.29-6.3 6.29 1.42 1.41L12 14.84l6.29 6.29 1.41-1.41-6.29-6.3 6.3-6.29z"/>',
    '      </svg>',
    '    </button>',
    '  </div>',
    '  <div class="aiw-body"></div>',
    '  <div class="aiw-foot">',
    '    <form class="aiw-form" action="' + AIW.endpoint + '" method="post" style="display:flex;gap:8px;width:100%">',
    '      <input class="aiw-input" name="message" type="text" placeholder="Type your message..." autocomplete="off" />',
    '      <button class="aiw-send" type="submit" aria-label="Send">',
    '        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">',
    '          <path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/>',
    '        </svg>',
    '      </button>',
    '    </form>',
    '  </div>',
    '</div>'
  ].join("");

  document.body.appendChild(AIW.root);

  AIW.panel   = AIW.root.querySelector(".aiw-panel");
  AIW.body    = AIW.root.querySelector(".aiw-body");
  AIW.input   = AIW.root.querySelector(".aiw-input");
  AIW.form    = AIW.root.querySelector(".aiw-form");
  AIW.fab     = AIW.root.querySelector(".aiw-fab");
  AIW.closeBtn= AIW.root.querySelector(".aiw-close");
  AIW.sendBtn = AIW.root.querySelector(".aiw-send");

  AIW.root.querySelector("#aiwTitle").textContent = AIW.name || "AI Chatbot";

  // events
  AIW.fab.addEventListener("click", aiChatbotToggle);
  AIW.closeBtn.addEventListener("click", aiChatbotHide);

  if (window.MutationObserver && AIW.body) {
    var mo = new MutationObserver(function (muts) {
      for (var i = 0; i < muts.length; i++) {
        if (muts[i].addedNodes && muts[i].addedNodes.length) { aiChatbotScrollToBottom(); break; }
      }
    });
    mo.observe(AIW.body, { childList: true });
  }

  if (window.visualViewport) {
    visualViewport.addEventListener("resize", aiChatbotRefreshPosition);
    visualViewport.addEventListener("scroll", aiChatbotRefreshPosition);
  }

  if (AIW.input) {
    AIW.input.addEventListener("focus", function () { setTimeout(aiChatbotRefreshPosition, 60); });
    AIW.input.addEventListener("blur",  function () { setTimeout(aiChatbotRefreshPosition, 60); });
    AIW.input.addEventListener("keydown", function (e) {
      if (e.key === "Enter") { e.preventDefault(); aiChatbotOnSend(); }
    });
  }
  if (AIW.form)  AIW.form.addEventListener("submit", aiChatbotOnSend);
  if (AIW.sendBtn) {
    AIW.sendBtn.addEventListener("click", aiChatbotOnSend);
    AIW.sendBtn.addEventListener("mousedown", function (e) { e.preventDefault(); });
    AIW.sendBtn.addEventListener("touchend", function (e) { e.preventDefault(); aiChatbotOnSend(e); }, { passive: false });
  }

  // defaults
  AIW.panel.style.display = "none";
  AIW.isOpen = false;
  aiChatbotApplyBottom(AIW.NORMAL_BOTTOM);
  aiChatbotRenderHistory();
  window.addEventListener("load", aiChatbotScrollToBottom);
}

/* ------ Public init (export) ------ */
function initAIChatbot(opts) {
  opts = opts || {};
  function start() { aiChatbotBuildUI(opts); }
  if (!document.body) { document.addEventListener("DOMContentLoaded", start); }
  else { start(); }
}

/* ------ Optional auto-init via <body data-ai-*> ------ */
document.addEventListener("DOMContentLoaded", function () {
  var b = document.body;
  if (!b) return;
  var ep = b.getAttribute("data-ai-endpoint");
  if (ep) {
    initAIChatbot({
      endpoint: ep,
      name: b.getAttribute("data-ai-name") || "AI Chatbot",
      primary: b.getAttribute("data-ai-primary") || null
    });
  }
});


function General(){
    var self= this;
    this.init= function(){
        //Callback
        self.generalOption();
        self.uploadSettings();
        self.scriptLicense();
        self.users();
        self.add_funds();
       
        self.services();
        if ($("#order_resume").length > 0) {
            self.order();
            self.calculateOrderCharge();
        }
        
        if ($(".sidebar").length > 0) {
            _url = window.location.href;
            _url = _url.split("?t=");
            if(_url.length == 2){
                $('[data-content="'+_url[1]+'"]').trigger("click");
            }
        }        
        
    };

    this.add_funds = function(){
      $(document).on("submit", ".actionAddFundsForm", function(){
        pageOverlay.show();
        event.preventDefault();
        _that         = $(this);
        _action       = PATH + 'add_funds/process';
        _redirect     = _that.data("redirect");
        _data         = _that.serialize();
        _data         = _data + '&' + $.param({token:token});
        $.post(_action, _data, function(_result){
            setTimeout(function(){
              pageOverlay.hide();
            },1500)
            if (is_json(_result)) {
                _result = JSON.parse(_result);

                if (_result.status == 'success' && typeof _result.redirect_url != "undefined") {
                    window.location.href = _result.redirect_url;
                }

                setTimeout(function(){
                    notify(_result.message, _result.status);
                },1500)

                setTimeout(function(){
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                }, 2000)
            }else{
                setTimeout(function(){
                    $(".add-funds-form-content").html(_result);
                }, 100)
            }
        })
        return false;
      })
    }

    this.users = function(){

        $(document).on("click", ".btnEditCustomRate", function(){
            _that = $(this);
            _url = _that.data("action");
            $('#customRate').load(_url, function(){
                $('#customRate').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#customRate').modal('show');
            });
            return false;
        });

        /*----------  View user and back to admin  ----------*/
        $(document).on("click", ".ajaxViewUser" , function(){
            event.preventDefault();
            pageOverlay.show();
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = PATH + 'new_order';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        }) 

        $(document).on("click", ".ajaxBackToAdmin" , function(){
            event.preventDefault();
            pageOverlay.show();
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = PATH + 'admin/users';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        }) 

    }

    this.services = function(){

        /*----------  Get Service details  ----------*/
        $(document).on("click", ".ajaxGetServiceDescription", function(){
            event.preventDefault();
            _that     = $(this);
            _url = PATH + 'services/desc/' + _that.data("ids");
            $('#modal-ajax').load(_url, function(){
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        })


        $(document).on('click', '.check-all', function(){
            _that      = $(this);
            _checkName = _that.data('name');
            $('.'+_checkName+'').prop('checked', this.checked);
        })

        $(document).on("change", ".ajaxChangeServiceType", function(){
            event.preventDefault();
            _that   = $(this);
            _type    = _that.val();
            switch(_type) {
              case "default":
                $("#add_edit_service .dripfeed-form").removeClass("d-none");
                break;  
              default:
                $("#add_edit_service .dripfeed-form").addClass("d-none");
                break;
            }
        })

        $(document).on("click", ".ajaxActionOptions" , function(){
            event.preventDefault();
            _that       = $(this);
            _type       = _that.data("type");

            if ((_type == 'delete' || _type == 'all_deactive' || _type == 'clear_all')) {
                if(!confirm_notice('deleteItems')){
                    return;
                }
            }
            _action     = _that.attr("href");
            _form       = _that.closest('form');
            _ids        = _form.serialize();
            _data       = _ids + '&' +$.param({token:token, type:_type});

            pageOverlay.show();
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = '';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        }) 

        $(document).on("change", ".ajaxChangeSevicesCurrency" , function(){
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _id         = _that.val();

            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = '';
                        reloadPage(_redirect);
                    }
                }, 200);
            },'json')
        }) 


        $(document).on("click", ".ajaxTaskAction", function(event){
            event.preventDefault();
        
            var _that = $(this);
            var _type = _that.data("type") || "";
        
            var _action = _that.attr("href");
            var _form   = _that.closest('form');
            var _id     = _form.length ? _form.serialize() : "";
            var _data   = _id + '&' + $.param({ token: token, type: _type });
        
            _that.hide();
        
            $.post(_action, _data, function(_result){
            }, 'json').fail(function(){
            });
        });




    }

    this.scriptLicense = function(){
        $(document).on("click", ".ajaxUpgradeVersion", function(){
            pageOverlay.show();
            event.preventDefault();
            _that   = $(this);
            _action = _that.attr("href");
            _data   = $.param({token:token});
            $.post(_action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = '';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        })
    }

    this.order = function (){
        _total_quantity = 0;
        _service_price  = 0;

        $(document).on("input", ".ajaxQuantity" , function(){
            _that           = $(this);
            _quantity       = _that.val();
            _service_id     = $("#service_id").val();
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();
            _is_drip_feed   = $("#new_order input[name=is_drip_feed]:checked").val();
            if (_is_drip_feed) {
                _runs           = $("#new_order input[name=runs]").val();
                _interval       = $("#new_order input[name=interval]").val();
                _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                _total_quantity = _quantity;
            }
            _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        // callback ajaxDripFeedRuns
        $(document).on("input", ".ajaxDripFeedRuns" , function(){
            _that           = $(this);
            _runs           = _that.val();
            _service_id     = $("#service_id").val();
            _quantity       = $("#new_order input[name=quantity]").val();
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();
            _is_drip_feed   = $("#new_order input[name=is_drip_feed]:checked").val();
            if (_is_drip_feed) {
                _interval       = $("#new_order input[name=interval]").val();
                _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                _total_quantity = _quantity;
            }
            _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        $(document).on("click", ".is_drip_feed" , function(){
            _that           = $(this);
            _service_id     = $("#service_id").val();
            _quantity       = $("#new_order input[name=quantity]").val();
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();
            if (_that.is(":checked")) {
                _runs           = $("#new_order input[name=runs]").val();
                _interval       = $("#new_order input[name=interval]").val();
                _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                _total_quantity = _quantity;
            }
            _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

    }

    this.calculateOrderCharge = function(){

        // callback ajax_custom_comments
        $(document).on("keyup", ".ajax_custom_comments" , function(){
            _quantity = $("#new_order .order-comments textarea[name=comments]").val();
            if (_quantity == "") {
                _quantity = 0;
            }else{
                _quantity = _quantity.split("\n").filter(line => line.trim() !== "").length;
            }
            _service_id     = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();

            _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        // callback ajax_custom_lists
        $(document).on("keyup", ".ajax_custom_lists" , function(){
            _quantity = $("#new_order .order-usernames-custom textarea[name=usernames_custom]").val();
            console.log(_quantity);
            if (_quantity == "") {
                _quantity = 0;
            }else{
                _quantity = _quantity.split("\n").filter(line => line.trim() !== "").length;
            }

            _service_id     = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();

            _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

    }

    this.generalOption = function(){

        $(document).on("click", ".ajaxToggleItemStatus", function() {
            var _that = $(this),
                _id = _that.data('id'),
                _action = _that.data('action') + _id,
                _status = _that.is(":checked") ? 1 : 0;
        
            if (_id == "") {
                return false;
            }
        
            _that.siblings('.loading-spinner').show();
        
            _data = $.param({ token: token, status: _status });
        
            $.post(_action, _data, function(_result) {
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
        
                    setTimeout(function(){
                        notify(_result.message, _result.status);
                    }, 200);
        
                }
            })
            .always(function() {
                // Hide the spinner after request completion
                _that.siblings('.loading-spinner').hide();
            });
        });

        // Insert hyper-link
        $(document).on('focusin', function(e) {
            if ($(event.target).closest(".mce-window").length) {
              e.stopImmediatePropagation();
            }
        });

        // load ajax-Modal
        $(document).on("click", ".ajaxModal", function(){
            _that = $(this);
            _url = _that.attr("href");
            $('#modal-ajax').load(_url, function(){
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        });

        /*----------  ajaxChangeTicketSubject  ----------*/
        $(document).on("change", ".ajaxChangeTicketSubject", function(){
            event.preventDefault();
            _that   = $(this);
            _type    = _that.val();
            switch(_type) {

              case "subject_order":
                $("#add_new_ticket .subject-order").removeClass("d-none");
                $("#add_new_ticket .subject-payment").addClass("d-none");
                break;  
                              
              case "subject_payment":
                $("#add_new_ticket .subject-order").addClass("d-none");
                $("#add_new_ticket .subject-payment").removeClass("d-none");
                break;

              default:
                $("#add_new_ticket .subject-order").addClass("d-none");
                $("#add_new_ticket .subject-payment").addClass("d-none");
                break;
            }
        })

        // ajaxChangeLanguage
        $(document).on("change", ".ajaxChangeLanguage", function(){
            event.preventDefault();
            _that     = $(this);
            _ids      = _that.val();
            _action   = _that.data("url") + _ids;
            _redirect = _that.data("redirect");
            _data     = $.param({token:token, redirect:_redirect});
            $.post(_action, _data, function(_result){
                pageOverlay.show();
                setTimeout(function () {
                    pageOverlay.hide();
                    location.reload();
                }, 1000);
            },'json')
        })

        // ajaxChangeStatus ticket
        $(document).on("click", ".ajaxChangeStatus", function(){
            event.preventDefault();
            _that   = $(this);
            _action = _that.data("url");
            _status = _that.data("status");
            _data   = $.param({token:token, status:_status});
            $.post(_action, _data, function(_result){
                pageOverlay.show();
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
                if (_status == 'new' || _status == 'unread') {
                    _redirect = PATH + 'tickets';
                }else{
                    _redirect = '';
                }
                reloadPage(_redirect);
            },'json')
        })

        // callback ajaxChange
        $(document).on("change", ".ajaxChange" , function(){
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _id         = _that.val();

            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearch").html(_result);
                }, 100);
            });
        })  

        // callback ajaxChange
        $(document).on("change", ".ajaxChangeOrders" , function(){
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _id         = _that.val();

            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearchOrders").html(_result);
                }, 100);
            });
        }) 

        // callback ajaxChange
        $(document).on("change", ".ajaxChangeTransactions" , function(){
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _id         = _that.val();

            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearchTransactions").html(_result);
                }, 100);
            });
        }) 

        // callback ajaxChange
        $(document).on("change", ".ajaxChangeUsers" , function(){
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _id         = _that.val();

            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearchUsers").html(_result);
                }, 100);
            });
        })
        
    
        $(document).on("change", ".ajaxChangeSelect", function(event){
            pageOverlay.hide();
            event.preventDefault();
            var _that = $(this);
            var _id = _that.val();
        
            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
        
            var _action = _that.data("url") + _id;
            var _data = $.param({token: token});
        
            $.post(_action, _data, function(_result){
                pageOverlay.hide();
                // Reload the page
                location.reload();
            });
        });
        
        $(document).ready(function() {
            function formatOption(option) {
                if (!option.id) { return option.text; }
                var $option = $(
                    '<span><i class="' + $(option.element).data('icon') + '"></i> ' + option.text + '</span>'
                );
                return $option;
            }
        
            $("#dropdownselect").select2({
                templateResult: formatOption,
                templateSelection: formatOption,
                minimumResultsForSearch: Infinity
            
            });
        
            $("#dropdownselectc").select2({
                templateResult: formatOption,
                templateSelection: formatOption,
                minimumResultsForSearch: Infinity
            
            });
        });

 

        // callback ajaxChangeCategory
        $(document).on("change", ".ajaxChangeCategory" , function(){
            event.preventDefault();
            $("#new_order .drip-feed-option").addClass("d-none");
            if ($("#order_resume").length > 0) {
                $("#order_resume input[name=service_name]").val("");
                $("#order_resume input[name=service_min]").val("");
                $("#order_resume input[name=service_max]").val("");
                $("#order_resume input[name=service_price]").val("");
                $("#order_resume textarea[name=service_desc]").val("");
                $("#order_resume #service_desc").val("");

                $("#new_order input[name=service_price]").val("");
                $("#new_order input[name=service_min]").val("");
                $("#new_order input[name=service_max]").val("");
            }
            _that       = $(this);
            _id         = _that.val();
            if (_id == "") {
                return;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                setTimeout(function () {
                    $("#result_onChange").html(_result);
                }, 100);
            });
        })  

// NOTE: same code, bas event param add kiya hai + .val('') fixes + safety
$(document).on("change", ".ajaxChangeService", function (e) {
  e.preventDefault();

  var _that         = $(this);
  var _id           = _that.val();
  var _dripfeed     = _that.children("option:selected").data("dripfeed") || 0;
  var _service_type = _that.children("option:selected").data("type") || "";

  $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", false);
  $("#new_order .order-usernames-custom").addClass("d-none");
  $("#new_order .order-comments-custom-package").addClass("d-none");

  /*----------  reset quantity  ----------*/
  $("#new_order input[name=service_price]").val(''); // was: val();
  $("#new_order input[name=service_min]").val('');   // was: val();
  $("#new_order input[name=service_max]").val('');   // was: val();

  $("#new_order .order-default-quantity input[name=quantity]").val('');
  var _total_charge = 0;
  var _currency_symbol = $("#new_order input[name=currency_symbol]").val() || '';
  $("#new_order input[name=total_charge]").val(_total_charge);
  $("#new_order .total_charge span").html(_currency_symbol + _total_charge);

  switch(_service_type) {
    case "subscriptions":
      $("#new_order input[name=sub_expiry]").val('');
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order #result_total_charge").addClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-hashtag, #new_order .order-media, #new_order .order-answer-number").addClass("d-none");
      $("#new_order .order-subscriptions").removeClass("d-none");
      break;

    case "custom_comments":
      $("#new_order .order-default-link, #new_order .order-comments, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      $("#new_order .order-default-quantity").removeClass("d-none")
        .find("input[name=quantity]").attr("disabled", true);
      break;

    case "custom_comments_package":
      $("#new_order .order-default-link, #new_order .order-comments-custom-package, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-default-quantity, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "mentions_with_hashtags":
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order .order-usernames, #new_order .order-hashtags, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-username, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "mentions_custom_list":
      $("#new_order .order-default-link, #new_order .order-usernames-custom, #new_order #result_total_charge, #new_order .order-default-quantity").removeClass("d-none");
      $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", true);
      $("#new_order .order-usernames, #new_order .order-comments, #new_order .order-username, #new_order .order-hashtags, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "mentions_hashtag":
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order .order-hashtag, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "mentions_user_followers":
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order .order-username, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "mentions_media_likers":
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order .order-media, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-hashtag, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "package":
      $("#new_order .order-default-link, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-default-quantity, #new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "comment_likes":
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order .order-username, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;

    case "poll":
      $("#new_order .order-default-link, #new_order .order-answer-number, #new_order .order-default-quantity, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions").addClass("d-none");
      break;

    default:
      $("#new_order .order-default-link, #new_order .order-default-quantity, #new_order #result_total_charge").removeClass("d-none");
      $("#new_order .order-comments, #new_order .order-usernames, #new_order .order-hashtags, #new_order .order-username, #new_order .order-hashtag, #new_order .order-media, #new_order .order-subscriptions, #new_order .order-answer-number").addClass("d-none");
      break;
  }

  if (_dripfeed) {
    $("#new_order .drip-feed-option").removeClass("d-none");
  } else {
    $("#new_order .drip-feed-option").addClass("d-none");
  }

  // backend partial
  var _action = (_that.data("url") || "") + _id;   // e.g. /new_order/get_service/123
  var _data   = $.param({ token: (window.token || '') });
  $.post(_action, _data, function (_result) {
    $("#result_onChangeService").html(_result);

    var _service_price = $("#order_resume input[name=service_price]").val();
    var _service_min   = $("#order_resume input[name=service_min]").val();
    var _service_max   = $("#order_resume input[name=service_max]").val();
    $("#new_order input[name=service_price]").val(_service_price);
    $("#new_order input[name=service_min]").val(_service_min);
    $("#new_order input[name=service_max]").val(_service_max);

    setTimeout(function () {
      if (_service_type == "package" || _service_type == "custom_comments_package") {
        var _currency_symbol = $("#new_order input[name=currency_symbol]").val() || '';
        $("#new_order input[name=total_charge]").val(_service_price);
        $("#new_order .total_charge span").html(_currency_symbol + _service_price);
      }
    }, 100);
  });
});


        // callback ajaxSearch
        $(document).on("submit", ".ajaxSearchItem" , function(){
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _data       = _that.serialize();

            _data       = _data + '&' + $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    $("#result_ajaxSearch").html(_result);
                }, 300);
            });
        })

        // callback ajaxSearchItemsKeyUp with keyup and Submit from
        var typingTimer;                //timer identifier
        $(document).on("keyup", ".ajaxSearchItemsKeyUp" , function(){
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                  event.preventDefault();
                  return false;
                }
            });
            event.preventDefault();
            clearTimeout(typingTimer);
            $(".ajaxSearchItemsKeyUp .btn-searchItem").addClass("btn-loading");
            var _that       = $(this),
                _form       = _that.closest('form'),
                _action     = _form.attr("action"),
                _data       = _form.serialize();
            _data       = _data + '&' + $.param({token:token});

            // if ( $("input:text").val().length < 2 ) {
            //     $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
            //     return;
            // }

            typingTimer = setTimeout(function () {
                $.post( _action, _data, function(_result){
                    setTimeout(function () {
                        $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
                        $("#result_ajaxSearch").html(_result);
                    }, 10);
                });
            }, 1500);

        })

        $(document).on("submit", ".ajaxSearchItemsKeyUp" , function(){
            event.preventDefault();
        })

        /*----------  Add a service from API provider  ----------*/
        $(document).on("click", ".ajaxAddService", function(){
            event.preventDefault();
            _that = $(this);
            _serviceid          = _that.data("serviceid");
            _name               = _that.data("name");
            _min                = _that.data("min");
            _max                = _that.data("max");
            _price              = _that.data("price");
            _dripfeed           = _that.data("dripfeed");
            _api_provider_id    = _that.data("api_provider_id");
            _type               = _that.data("type");
            _service_desc       = _that.data("service_desc");
            $("#modal-add-service input[name=dripfeed]").val(_dripfeed);
            $("#modal-add-service input[name=service_id]").val(_serviceid);
            $("#modal-add-service input[name=name]").val(_name);
            $("#modal-add-service input[name=min]").val(_min);
            $("#modal-add-service input[name=max]").val(_max);
            $("#modal-add-service input[name=price]").val(_price);
            $("#modal-add-service input[name=api_provider_id]").val(_api_provider_id);
            $("#modal-add-service input[name=type]").val(_type);
            $("#modal-add-service textarea[name=service_desc]").val(_service_desc);
            $('#modal-add-service').modal('show');
        })
        
        $(document).on("click", ".ajaxAddCategory", function(){
            event.preventDefault();
            _that = $(this);
            _api_id          = _that.data("api_id");
            _api_category    = _that.data("api_category");
            _api_name        = _that.data("api_name");
            $("#modal-add-category input[name=api_id]").val(_api_id);
            $("#modal-add-category input[name=api_category]").val(_api_category);
            $("#modal-add-category input[name=api_name]").val(_api_name);
            $('#modal-add-category').modal('show');
        })

        $(document).on("click", ".ajaxUpdateApiProvider", function(){
            $("#result_notification").html("");
            pageOverlay.show();
            event.preventDefault();
            _that   = $(this);
            _action = _that.attr("href");
            _redirect   = _that.data("redirect");
            _data   = $.param({token:token});
            $.post(_action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        })


        /*----------  Sync Services  ----------*/
        $(document).on("submit", ".actionSyncApiServices", function(){
            $("#result_notification").html("");
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _action     = _that.attr("action");
            _redirect   = _that.data("redirect");

            if ($("#mass_order").hasClass("active")) {
                _data = $("#mass_order").find("input[name!=mass_order]").serialize();
                _mass_order_array = [];
                _mass_orders = $("#mass_order").find("textarea[name=mass_order]").val();
                if (_mass_orders.length > 0 ) {
                    _mass_orders = _mass_orders.split(/\n/);
                    for (var i = 0; i < _mass_orders.length; i++) {
                        // only push this line if it contains a non whitespace character.
                        if (/\S/.test(_mass_orders[i])) {
                            _mass_order_array.push($.trim(_mass_orders[i]));
                        }
                    }
                }
                _data       = _data + '&' + $.param({mass_order:_mass_order_array, token:token});
            }else{
                _data       = _that.serialize();
                _data       = _data + '&' + $.param({token:token});
            }

            $.post(_action, _data, function(_result){
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                    setTimeout(function(){
                        pageOverlay.hide();
                    },2000)
                    setTimeout(function () {
                        notify(_result.message, _result.status);
                    }, 3000);
                }else{
                    setTimeout(function(){
                        pageOverlay.hide();
                        $('#modal-ajax').modal('hide');
                        $("#result_notification").html(_result);
                    },2000)
                }
            })
            return false;
        })

        // callback actionForm
        $(document).on("submit", ".NewOrderActionForm", function (e) {
          e.preventDefault();
        
          var $form = $(this);
          var action = $form.attr("action");
          var redirect = $form.data("redirect");
          var $submitBtn = $form.find('[type="submit"]');
        
          // serialize + ensure CSRF
          var data = $form.serialize();
          var tokenInp = $form.find("input[name=token]").val();
          if (typeof tokenInp === "undefined" && typeof token !== "undefined") {
            data += "&" + $.param({ token: token });
          }
        
          $.ajax({
            type: "POST",
            url: action,
            data: data,
            dataType: "text", // server kabhi HTML bhejta hai, kabhi JSON
            beforeSend: function () {
              pageOverlay.show();
              $submitBtn.prop("disabled", true).addClass("opacity-60 cursor-not-allowed");
            },
            complete: function () {
              pageOverlay.hide();
              $submitBtn.prop("disabled", false).removeClass("opacity-60 cursor-not-allowed");
            },
            success: function (resText) {
              // try JSON fast-path
              var asJson = null;
              try { asJson = JSON.parse(resText); } catch (e) {}
        
              if (asJson && typeof asJson === "object" && asJson.status) {
                // instant notify (no delay)
                notify(asJson.message || (asJson.status === "success" ? "Done" : "Failed"), asJson.status);
        
                if (asJson.status === "success") {
                  // 🚀 No full reload: show receipt/summary inline if available
                  if (asJson.html || asJson.view || asJson.partial_html) {
                    $("#result_notification").html(asJson.html || asJson.view || asJson.partial_html);
                  } else {
                    // minimal inline success UI
                    $("#result_notification").html(
                      '<div class="alert alert-success">Order placed successfully' +
                      (asJson.order_id ? " #"+asJson.order_id : "") + ".</div>"
                    );
                  }
        
                  // optional: update URL without reload (nice UX, back button friendly)
                  if (asJson.order_id) {
                    var newUrl = "<?=cn('new_order')?>/" + asJson.order_id;
                    if (history.replaceState) history.replaceState(null, "", newUrl);
                  }
        
                  // reset only dynamic fields; keep category/service selection if you like
                  // $form[0].reset();
        
                  // agar backend ne explicit redirect force kiya ho to hi redirect karo
                  if (asJson.redirect && asJson.redirect !== "stay" && asJson.redirect !== "no") {
                    window.location.href = asJson.redirect;
                  }
                  // warna wahi par raho — no refresh ✅
                  return;
                } else {
                  // error case without reload
                  return;
                }
              }
        
              // Fallback: server ne HTML diya
              $("#result_notification").html(resText);
            },
            error: function (xhr) {
              var msg = (xhr.responseJSON && xhr.responseJSON.message) || xhr.statusText || "Request failed";
              notify(msg, "error");
            }
          });
        
          return false;
        });

        // callback actionForm
        $(document).on("submit", ".actionForm", function(){
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _redirect   = _that.data("redirect");
            if ($("#mass_order").hasClass("active")) {
                _data = $("#mass_order").find("input[name!=mass_order]").serialize();
                _mass_order_array = [];
                _mass_orders = $("#mass_order").find("textarea[name=mass_order]").val();
                if (_mass_orders.length > 0 ) {
                    _mass_orders = _mass_orders.split(/\n/);
                    for (var i = 0; i < _mass_orders.length; i++) {
                        // only push this line if it contains a non whitespace character.
                        if (/\S/.test(_mass_orders[i])) {
                            _mass_order_array.push($.trim(_mass_orders[i]));
                        }
                    }
                }

                _data       = _data + '&' + $.param({mass_order:_mass_order_array, token:token});
            }else{
                var _token = $(".actionForm").find("input[name=token]").val();
                _data       = _that.serialize();
                if (typeof _token == "undefined") {
                    _data       = _data + '&' + $.param({token:token});
                }
            }
            
            $.post(_action, _data, function(_result){
                setTimeout(function(){
                    pageOverlay.hide();
                },1500)

                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    setTimeout(function(){
                        notify(_result.message, _result.status);
                    },1500)
                    setTimeout(function(){
                        if(_result.status == 'success' && typeof _redirect != "undefined"){
                            reloadPage(_redirect);
                        }
                    }, 2000)
                }else{
                    setTimeout(function(){
                        $("#result_notification").html(_result);
                    }, 1500)
                }
            })
            return false;
        })
        
        $(document).on("submit", ".actionFormLogin", function(event) { // Add `event` parameter
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _redirect   = _that.data("redirect");
            
            var _token = $(".actionFormLogin").find("input[name=token]").val();
                _data       = _that.serialize();
                if (typeof _token == "undefined") {
                    _data       = _data + '&' + $.param({token:token});
                }
            
            
            $.post(_action, _data, function(_result) {
                setTimeout(function() {
                    pageOverlay.hide();
                }, 1500);
            
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
            
                    // Notify message
                    setTimeout(function() {
                        notify(_result.message, _result.status);
                    }, 1500);
            
                    // Redirect if status is success and redirect URL exists
                    if (_result.status === 'success' && _result.redirect) {
                        setTimeout(function() {
                            window.location.href = _result.redirect;
                        }, 2000); // Delay for showing notification
                    }
                } else {
                    setTimeout(function() {
                        $("#result_notification").html(_result);
                    }, 1500);
                }
            });

            return false;
        })

        
    $(document).on("submit", ".actionFormService", function(event) {
        event.preventDefault();
        pageOverlay.show();
        
        var _that       = $(this),
            _action     = _that.attr("action");
        var _token = $(".actionFormService").find("input[name=token]").val();
        var _data = _that.serialize();
        if (typeof _token == "undefined") {
            _data = _data + '&' + $.param({token: token});
        }
        
        $.post(_action, _data, function(_result) {
            setTimeout(function() {
                pageOverlay.hide();
            }, 1500);
            
            if (is_json(_result)) {
                _result = JSON.parse(_result);
                setTimeout(function() {
                    notify(_result.message, _result.status);
                }, 1500);
                
                setTimeout(function() {
                    if (_result.status == 'success') {
                        location.reload();  // Reload the current page on success
                    }
                }, 100);
            } else {
                setTimeout(function() {
                    $("#result_notification").html(_result);
                }, 1500);
            }
        });
        return false;
    });

        // actionFormWithoutToast
        $(document).on("submit", ".actionFormWithoutToast", function(){
            alertMessage.hide();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _data       = _that.serialize();
                _data       = _data + '&' + $.param({token:token});
                _redirect   = _that.data("redirect");

                _that.find(".btn-submit").addClass('btn-loading');
            
            $.post(_action, _data, function(_result){
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    setTimeout(function(){
                        alertMessage.show(_result.message, _result.status);
                    }, 1500)

                    setTimeout(function(){
                        if(_result.status == 'success' && typeof _redirect != "undefined"){
                            reloadPage(_redirect);
                        }
                    }, 2000)

                }else{
                    setTimeout(function(){
                        $("#resultActionForm").html(_result);
                    }, 1500)
                }

                setTimeout(function(){
                    _that.find(".btn-submit").removeClass('btn-loading');
                }, 1500)
            })
            return false;
        })

        // callback Delete item
        $(document).on("click", ".ajaxDeleteItem", function(){
            event.preventDefault();
            if(!confirm_notice('deleteItem')){
                return;
            }
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});

            $.post(_action, _data, function(_result){
                pageOverlay.show();
                if(_result.status =='success'){
                    $(".tr_" + _result.ids).remove();
                }
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
            },'json')
        })

        $(document).on("click", ".ajaxDeleteCate", function(){
            event.preventDefault();
            if(!confirm_notice('deleteItem')){
                return;
            }
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});

            $.post(_action, _data, function(_result){
                pageOverlay.show();
                if(_result.status =='success'){
                    setTimeout(function () {
                        location.reload(); // Refresh the current page
                    }, 2000);
                }
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
            },'json')
        })

        /*----------  callback Change status itme  ----------*/
        $(document).on("click", ".ajaxChangeStatusItem", function(){
            event.preventDefault();
            _that       = $(this);
            _action     = _that.attr("href");
            _status     = _that.data("status");
            _redirect   = _that.data("redirect");
            _data       = $.param({token:token, status:_status});
            $.post(_action, _data, function(_result){
                pageOverlay.show();
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
                if (_result.status == 'success' && typeof _redirect != "undefined") {
                    reloadPage(_redirect);
                }
            },'json')
        })

        // callback ajaxGetContents
        $(document).on("click", ".ajaxGetContents" , function(){
            pageOverlay.show();
            _that       = $(this);
            $(".settings .sidebar li").removeClass("active");
            _that.parent().addClass("active");

            _type       = _that.data("content");
            _action     = _that.attr("href");
            _data       = $.param({token:token, type:_type});
            $.post( _action, _data, function(_result){
                $("#result_get_contents").html(_result);
                history.pushState(null, "", _action.replace("/ajax_get_contents/","?t="));
                setTimeout(function () {
                    pageOverlay.hide();
                }, 300);
            });
            return false;
        }) 

    }

    // Upload media on Settings page
    this.uploadSettings = function () {
        var url = PATH + "file_manager/upload_files";
        $(document).on('click','.settings_fileupload',function(){
            _that = $(this);
            _closest_div = _that.closest('div');
            $('.settings .settings_fileupload').fileupload({
                url: url,
                formData: {token:token},
                dataType: 'json',
                done: function (e, data) {
                if (data.result.status == "success") {
                  _img_link = data.result.link;
                  _closest_div.children('input').val(_img_link);
                }
              },
            });
        });
    }

    // Check post type
    $(document).on("change","input[type=radio][name=email_protocol_type]", function(){
      _that = $(this);
      _type = _that.val();
      if(_type == 'smtp'){
        $('.smtp-configure').removeClass('d-none');
      }else{
        $('.smtp-configure').addClass('d-none');
      }
    });
}
General= new General();
$(function(){
    General.init();
});