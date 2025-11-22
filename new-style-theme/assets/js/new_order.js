(function($){
  "use strict";

  // ---------- helpers ----------
  function esc(s){ return $('<div>').text(s==null?'':String(s)).html(); }
  function dehtml(s){ var t=document.createElement('textarea'); t.innerHTML = s||''; return t.value; }

  function waitForSelect2(cb, tries){
    tries = (typeof tries==='number')? tries : 50;
    (function loop(){
      if (window.jQuery && jQuery.fn && jQuery.fn.select2){ try{ cb(); }catch(e){} return; }
      if (tries-- <= 0) return;
      setTimeout(loop, 100);
    })();
  }
  function s2Init($el, opts){
    try{
      if (!$.fn.select2) return;
      if ($el.hasClass('select2-hidden-accessible')) return;
      $el.select2(opts);
    }catch(e){}
  }
  function s2Destroy($el){
    try{
      if ($.fn.select2 && $el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
    }catch(e){}
  }

  // ---------- main ----------
  $(function(){
    var $cat     = $('#dropdowncategories');
    var $svc     = $('#dropdownservices');
    var $search  = $('#svc-search');
    var $results = $('#svc-results');
    var $desc    = $('#service-desc');
    var $avg     = $('#average-time');
    var $qty     = $('#qty-input');
    var $minmax  = $('#qty-minmax');
    var $cardsWrap = $('.platform-cards');

    // config
    var cfgEl = document.getElementById('new-order-config') || {};
    var SEARCH_URL   = (cfgEl.dataset && cfgEl.dataset.searchUrl)     ? cfgEl.dataset.searchUrl     : '';
    var GET_URL      = (cfgEl.dataset && cfgEl.dataset.getServiceUrl) ? cfgEl.dataset.getServiceUrl : '';
    var LIST_URL     = (cfgEl.dataset && cfgEl.dataset.listByCateUrl) ? cfgEl.dataset.listByCateUrl : '';
    var LAST_CATE    = (cfgEl.dataset && cfgEl.dataset.lastCate)      ? String(cfgEl.dataset.lastCate)      : '';
    var LAST_SERVICE = (cfgEl.dataset && cfgEl.dataset.lastService)   ? String(cfgEl.dataset.lastService)   : '';

    // select2 opts
    function formatWithIcon (opt) {
      if (!opt.id) return opt.text;
      var el = opt.element; if(!el) return opt.text;
      var icn = el.getAttribute('data-icon') || '';
      return icn ? $('<span><i class="'+icn+'"></i> '+opt.text+'</span>') : opt.text;
    }
    var S2_CAT_OPTS = { width:'100%', minimumResultsForSearch:Infinity, templateResult:formatWithIcon, templateSelection:formatWithIcon };
    var S2_SVC_OPTS = { width:'100%', minimumResultsForSearch:Infinity, templateResult:formatWithIcon, templateSelection:formatWithIcon };

    waitForSelect2(function(){ s2Init($cat, S2_CAT_OPTS); s2Init($svc, S2_SVC_OPTS); });

    // ---------- master snapshot (page-load ALL categories) ----------
    var ALL_CATS_MASTER = (function(){
      return $cat.find('option').map(function(){
        var $o = $(this);
        return {
          value: String($o.val()||''),
          text:  ($o.text()||'').trim(),
          icon:  $o.attr('data-icon') || $o.data('icon') || ''
        };
      }).get();
    })();
    // optional global
    window.__ALL_CATS_MASTER__ = ALL_CATS_MASTER;

    // ---------- services cache ----------
    var CACHE = {}; // {cateId: [service objects]}
    var BY_ID = {};

    function optionHTML(o){
      return ''+
        '<option value="'+esc(o.id)+'" '+
        'data-cate="'+esc(o.cate_id)+'" '+
        'data-desc="'+esc(o.desc||'')+'" '+
        'data-average="'+esc(o.average||'')+'" '+
        'data-min="'+esc(o.min||'')+'" '+
        'data-max="'+esc(o.max||'')+'" '+
        'data-type="'+esc(o.type||'')+'" '+
        'data-dripfeed="'+esc(o.dripfeed||0)+'" '+
        'data-icon="'+esc(o.icon||'')+'">'+
          'ID:'+esc(o.id)+' '+esc(o.name)+' - '+esc(o.price_text||'')+
        '</option>';
    }

    function renderSideFromOption(optEl){
      if(!optEl){ $desc.html('<em style="opacity:.7;">No description</em>'); if($avg.length) $avg.val('Not enough data'); $minmax.text(''); return; }
      var $o = $(optEl), d = $o.data();
      var safe = esc(dehtml(String(d.desc||''))).replace(/\n/g,'<br>');
      $desc.html( safe || '<em style="opacity:.7;">No description</em>' );
      if($avg.length) $avg.val(d.average || 'Not enough data');
      if (d.min || d.max){
        $minmax.text('Min: '+(d.min||'')+' - Max: '+(d.max||''));
        if($qty.length){
          if(d.min) $qty.attr('min', d.min); else $qty.removeAttr('min');
          if(d.max) $qty.attr('max', d.max); else $qty.removeAttr('max');
          if(!$qty.val()) $qty.attr('placeholder', (d.min||'')+' - '+(d.max||''));
        }
      } else {
        $minmax.text('');
        if($qty.length){ $qty.removeAttr('min').removeAttr('max').attr('placeholder',''); }
      }
    }

    function legacyFireChange(){
      if(!$svc.hasClass('ajaxChangeService')) $svc.addClass('ajaxChangeService');
      if(!$svc.attr('data-url') && GET_URL)   $svc.attr('data-url', GET_URL);
      try{ $svc.trigger('change'); $svc.trigger('change.select2'); $svc.trigger('select2:select'); }catch(e){}
    }

    function build(cateId, selectedId){
      var list = CACHE[cateId] || [];

      waitForSelect2(function(){ s2Destroy($svc); });

      if (!list.length){
        $svc.html('<option value="">No services in this category</option>');
      } else {
        var html = '';
        for (var i=0;i<list.length;i++){ html += optionHTML(list[i]); }
        $svc.html(html);
      }

      waitForSelect2(function(){ s2Init($svc, S2_SVC_OPTS); });

      var pick = null;
      if (selectedId && $svc.find('option[value="'+selectedId+'"]').length){ pick = String(selectedId); }
      else { pick = $svc.find('option:first').val() || ''; }

      if (pick){ $svc.val(pick).trigger('change.select2'); }
      renderSideFromOption($svc.find('option:selected')[0]);
      legacyFireChange();
    }

    function loadServices(cateId, selectedId){
      cateId = String(cateId||'');
      if (!cateId || !LIST_URL){ build('', null); return; }

      // use cache
      if (CACHE[cateId]){ build(cateId, selectedId); return; }

      $.ajax({
        url: LIST_URL,
        method: 'GET',
        dataType: 'json',
        headers: {'X-Requested-With':'XMLHttpRequest'},
        data: { cate_id: cateId }
      }).done(function(resp){
        var list = (resp && resp.data && Array.isArray(resp.data)) ? resp.data : [];
        CACHE[cateId] = list;
        for (var i=0;i<list.length;i++){ BY_ID[String(list[i].id)] = list[i]; }
        build(cateId, selectedId);
      }).fail(function(){
        $svc.html('<option value="">Failed to load services</option>');
        waitForSelect2(function(){ s2Init($svc, S2_SVC_OPTS); });
      });
    }

    // events (categories/services)
    $cat.on('change', function(){ loadServices(this.value, null); });
    $svc.on('change', function(){ renderSideFromOption(this.options[this.selectedIndex]); });

    // init (respect last)
    (function init(){
      var initCate = LAST_CATE ? LAST_CATE : String($cat.val()||'');
      if (initCate){ try{ $cat.val(initCate).trigger('change.select2'); }catch(e){} }
      loadServices(initCate, LAST_SERVICE ? LAST_SERVICE : null);
    })();

    // ---------- Platform filter ----------
    function norm(s){ return (s||'').toLowerCase(); }
    function tokenize(s){ return norm(s).split(/[^a-z0-9+#]+/).filter(Boolean); }

    function getKeywordsFromCard(el){
      var ds = el.dataset || {};
      if (ds.keywords && ds.keywords.trim()){
        return ds.keywords.split(',').map(function(k){ return norm(k.trim()); }).filter(Boolean);
      }
      var id = norm(el.id||'');
      var txt = norm($(el).text()||'');
      var kw = [].concat(id ? tokenize(id) : [], txt ? tokenize(txt) : []);
      if (kw.indexOf('twitter') !== -1) kw.push('x');
      if (kw.indexOf('youtube') !== -1) kw.push('yt');
      if (kw.indexOf('instagram') !== -1) kw.push('ig');
      if (kw.indexOf('tiktok') !== -1) kw.push('tt');
      var uniq=[]; for (var i=0;i<kw.length;i++){ if (uniq.indexOf(kw[i])===-1) uniq.push(kw[i]); }
      return uniq;
    }
    function belongsToKeywords(catText, keywords){
      if (!keywords || !keywords.length) return true;
      var t = norm(catText||'');
      for (var i=0;i<keywords.length;i++){ if (!keywords[i]) continue; if (t.indexOf(keywords[i]) !== -1) return true; }
      return false;
    }

    function rebuildCategories(list){
      s2Destroy($cat);
      $cat.empty();
      if (!list || !list.length){
        list = (window.__ALL_CATS_MASTER__ || ALL_CATS_MASTER || []).slice(0);
      }
      for (var i=0;i<list.length;i++){
        var o = list[i];
        $cat.append( $('<option>', { value:o.value, 'data-icon':o.icon }).text(o.text) );
      }
      s2Init($cat);
      // NOTE: yahan auto-select & change NAHIN �� caller decide karega
    }

    // global platform reset
    window.PlatformFilter = {
      resetToAll: function(){
        var allList = (window.__ALL_CATS_MASTER__ || ALL_CATS_MASTER || []).slice(0);
        $cardsWrap.find('.platform-card').removeClass('active');
        var $all = $cardsWrap.find('#all'); if ($all.length) $all.addClass('active');
        rebuildCategories(allList);
      }
    };

    // platform card click
    $cardsWrap.on('click', '.platform-card', function(){
      var $card = $(this);
      $cardsWrap.find('.platform-card').removeClass('active');
      $card.addClass('active');

      var base = (window.__ALL_CATS_MASTER__ || ALL_CATS_MASTER || []).slice(0);
      var isAll = (this.id === 'all') || (this.dataset && this.dataset.all === '1');

      if (isAll){
        rebuildCategories(base);
      } else {
        var keywords = getKeywordsFromCard(this);
        var filtered = base.filter(function(o){ return belongsToKeywords(o.text, keywords); });
        rebuildCategories(filtered.length ? filtered : base);
      }

      // yahan default first option select + services load
      var firstVal = $cat.find('option:first').val();
      if (firstVal != null){
        $cat.val(firstVal);
        try { $cat.trigger('change.select2').trigger('change'); } catch(e) {}
      }
      try { document.getElementById('dropdowncategories').scrollIntoView({behavior:'smooth', block:'center'}); } catch(e) {}
    });

    // ALL ko active mark (agar present)
    var $allCard = $cardsWrap.find('#all');
    if ($allCard.length){ $allCard.addClass('active'); }

    // ---------- Search ----------
    var timer=null;
    function hasSearch(){ return !!SEARCH_URL; }

    $search.on('input', function(){
      var q = $.trim(this.value||'');
      clearTimeout(timer);
      if(!hasSearch()){ $results.hide().empty(); return; }
      if(q.length<1){ $results.hide().empty(); return; }

      timer = setTimeout(function(){
        $.ajax({
          url: SEARCH_URL,
          method: 'GET',
          dataType: 'json',
          headers: {'X-Requested-With':'XMLHttpRequest'},
          data: { q:q, limit:12 }
        }).done(function(resp){
          var rows = (resp && Array.isArray(resp.data)) ? resp.data : [];
          if(!rows.length){
            $results.html('<div class="svc-one" style="opacity:.8;">No results</div>').show();
            return;
          }
          var html = '';
          for (var i=0;i<rows.length;i++){
            var r = rows[i];
            html += ''+
              '<a href="#" class="svc-item" data-id="'+esc(r.id)+'" data-cate="'+esc(r.cate_id)+'">'+
                '<div class="svc-one"><strong>ID:'+esc(r.id)+'</strong> '+esc(r.name)+'</div>'+
                '<div style="font-size:11pt;margin-top:2px;">'+esc(r.category_name||'')+' �� '+esc(r.price_display||r.price_text||'')+'</div>'+
              '</a>';
          }
          $results.html(html).show();
        }).fail(function(){
          $results.html('<div class="svc-one" style="opacity:.8;">Error fetching results</div>').show();
        });
      }, 220);
    });

    $(document).on('click', function(e){
      if (!$(e.target).closest('#svc-search, #svc-results').length) $results.hide();
    });

    $results.on('click', '.svc-item', function(e){
      e.preventDefault();
      var serviceId = String($(this).data('id'));
      var cateId    = String($(this).data('cate'));

      // agar active platform 'all' nahi, to ALL par reset (MASTER se rebuild)
      var $activeCard = $cardsWrap.find('.platform-card.active');
      if ($activeCard.length && $activeCard.attr('id') !== 'all' && window.PlatformFilter && typeof window.PlatformFilter.resetToAll === 'function'){
        window.PlatformFilter.resetToAll();
      } else {
        // ensure master list even if already ALL
        rebuildCategories((window.__ALL_CATS_MASTER__ || ALL_CATS_MASTER || []).slice(0));
      }

      // DOM/Select2 settle ho jaye, phir category set + services load + service preselect
      setTimeout(function(){
        var hasOpt = $cat.find('option[value="'+cateId+'"]').length > 0;
        if (!hasOpt) {
          $cat.prop('selectedIndex', 0);
        } else {
          $cat.val(cateId);
        }

        try { $cat.trigger('change.select2').trigger('change'); } catch(e) {}

        // preselect service in list load
        if (typeof loadServices === 'function') {
          loadServices(hasOpt ? cateId : String($cat.val()||''), serviceId);
        }

        $results.hide().empty();
      }, 0);
    });

    $search.on('keydown', function(e){
      if (e.key === 'Enter'){
        var $first = $results.find('.svc-item').first();
        if ($first.length){ $first.trigger('click'); e.preventDefault(); }
      }
    });

  });
})(jQuery);
