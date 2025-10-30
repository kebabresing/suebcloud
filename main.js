// main.js - uses jQuery when available, falls back to vanilla JS
(function(){
  function ready(fn){
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function(){
    var $ = window.jQuery;
    var mobileBtn = document.getElementById('mobile-menu-btn');
    var mobileMenu = document.getElementById('mobile-menu');

    // Mobile menu toggle
    if (mobileBtn && mobileMenu){
      mobileBtn.addEventListener('click', function(){
        var expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', String(!expanded));
        mobileMenu.classList.toggle('active');
      });
    }

    // FAQ accordion
    function toggleAccordion(trigger){
      var content = trigger.nextElementSibling;
      document.querySelectorAll('.accordion-content').forEach(function(c){ if(c !== content) c.classList.remove('active'); });
      document.querySelectorAll('.faq-trigger svg').forEach(function(svg){ if(svg !== trigger.querySelector('svg')) svg.style.transform = 'rotate(0deg)'; });
      content.classList.toggle('active');
      var icon = trigger.querySelector('svg');
      if(icon) icon.style.transform = content.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
    }
    document.querySelectorAll('.faq-trigger').forEach(function(t){ t.addEventListener('click', function(){ toggleAccordion(this); }); });

    // Smooth scroll for anchors (jQuery animate if present)
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor){
      anchor.addEventListener('click', function(e){
        var href = this.getAttribute('href');
        if (href.length > 1 && document.querySelector(href)){
          e.preventDefault();
          var target = document.querySelector(href);
          if($){ $('html, body').animate({ scrollTop: $(target).offset().top - 20 }, 450); }
          else { target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
          if (mobileMenu) mobileMenu.classList.remove('active');
        }
      });
    });

    // Reveal on scroll
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
      var ro = new IntersectionObserver(function(entries){
        entries.forEach(function(e){ if(e.isIntersecting) e.target.classList.add('in-view'); });
      }, { threshold: 0.12 });
      reveals.forEach(function(el){ ro.observe(el); });
    } else { reveals.forEach(function(el){ el.classList.add('in-view'); }); }

    // Testimonial carousel (if present)
    var car = document.querySelector('.testimonial-carousel');
    if (car){
      var track = car.querySelector('.testimonial-track');
      var slides = track ? Array.from(track.children) : [];
      var idx = 0;
      var dots = car.querySelectorAll('.testimonial-dot');
      function go(i){
        if (!track) return;
        idx = (i + slides.length) % slides.length;
        var w = slides[0].getBoundingClientRect().width + parseFloat(getComputedStyle(track).gap || 16);
        track.style.transform = 'translateX(' + (-idx * w) + 'px)';
        if (dots.length) { dots.forEach(function(d){ d.classList.remove('active'); }); if (dots[idx]) dots[idx].classList.add('active'); }
      }
      var auto = setInterval(function(){ go(idx+1); }, 4000);
      car.addEventListener('mouseenter', function(){ clearInterval(auto); });
      car.addEventListener('mouseleave', function(){ auto = setInterval(function(){ go(idx+1); }, 4000); });
      if (dots.length) dots.forEach(function(d,i){ d.addEventListener('click', function(){ go(i); }); });
      // initial
      setTimeout(function(){ go(0); }, 60);
    }

    // Modal open/close (data-modal-target="#id")
    document.querySelectorAll('[data-modal-target]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var sel = this.getAttribute('data-modal-target');
        var modal = document.querySelector(sel);
        if (modal) modal.classList.add('active');
      });
    });
    document.querySelectorAll('.modal-backdrop').forEach(function(b){
      b.addEventListener('click', function(e){ if (e.target === b) b.classList.remove('active'); });
      var close = b.querySelector('[data-modal-close]'); if (close) close.addEventListener('click', function(){ b.classList.remove('active'); });
    });

    // Back-to-top button
    var back = document.createElement('button'); back.className = 'back-to-top'; back.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>';
    document.body.appendChild(back);
    function checkScroll(){ if (window.scrollY > 300) back.classList.add('visible'); else back.classList.remove('visible'); }
    window.addEventListener('scroll', checkScroll); checkScroll();
    back.addEventListener('click', function(){ if($) $('html,body').animate({scrollTop:0}, 400); else window.scrollTo({top:0, behavior:'smooth'}); });

  // DigitalOcean status widget
  initDigitalOceanStatus();

  function highlightProCta(delay){
    var wait = typeof delay === 'number' ? delay : 520;
    setTimeout(function(){
      var pro = document.getElementById('pro-cta');
      if (!pro) return;
      try { pro.focus({ preventScroll: true }); }
      catch(err){ if (typeof pro.focus === 'function') pro.focus(); }
      pro.classList.add('focus-highlight');
      setTimeout(function(){ pro.classList.remove('focus-highlight'); }, 2200);
    }, wait);
  }

  function goToPricing(behavior, highlightDelay){
    var target = document.querySelector('#harga');
    if (!target){
      window.location.href = 'index.html#harga';
      return false;
    }
    var focusEl = document.getElementById('pro-cta') || target;
    if (focusEl && typeof focusEl.scrollIntoView === 'function'){
      try {
        focusEl.scrollIntoView({ behavior: behavior || 'smooth', block: 'center' });
      } catch (err){
        var topFallback = focusEl.getBoundingClientRect().top + window.pageYOffset;
        window.scrollTo({ top: Math.max(topFallback, 0), behavior: behavior || 'smooth' });
      }
    } else {
      var top = focusEl ? focusEl.getBoundingClientRect().top + window.pageYOffset : target.getBoundingClientRect().top + window.pageYOffset;
      window.scrollTo({ top: Math.max(top, 0), behavior: behavior || 'smooth' });
    }
    highlightProCta(typeof highlightDelay === 'number' ? highlightDelay : undefined);
    return true;
  }

  // Scroll to pricing + highlight Pro CTA when users click 'Mulai Gratis'
  document.querySelectorAll('.to-pricing').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      goToPricing('smooth', 520);
    });
  });

    // Contact form AJAX + client validation + live char count
    var contact = document.getElementById('contact-form');
    if (contact){
      var submitBtn = contact.querySelector('button[type=submit]');
      var textarea = contact.querySelector('textarea[name=message]');
      // live count
      if (textarea){
        var counter = document.createElement('div'); counter.className = 'text-sm text-gray-500 mt-1'; counter.id = 'message-count'; textarea.parentNode.appendChild(counter);
        function updateCount(){ counter.textContent = textarea.value.length + ' karakter'; }
        textarea.addEventListener('input', updateCount); updateCount();
      }

      contact.addEventListener('submit', function(e){
        if (contact.getAttribute('data-ajax') !== 'true') return;
        e.preventDefault();
        var formData = new FormData(contact);
        // basic client-side validation
        var name = (formData.get('name')||'').trim();
        var email = (formData.get('email')||'').trim();
        var message = (formData.get('message')||'').trim();
        var errs = [];
        if (!name) errs.push('Nama harus diisi.');
        if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) errs.push('Email tidak valid.');
        if (!message) errs.push('Pesan tidak boleh kosong.');
        var feedbackBox = contact.querySelector('.form-feedback');
        if (!feedbackBox){ feedbackBox = document.createElement('div'); feedbackBox.className = 'form-feedback mb-4'; contact.insertBefore(feedbackBox, contact.firstChild); }
        if (errs.length){ feedbackBox.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded">' + errs.map(function(x){ return '<div>'+x+'</div>'; }).join('') + '</div>'; return; }

        // submit (use jQuery if available)
        if ($ && $.ajax){
          $.ajax({ url: contact.action, method: 'POST', data: formData, processData:false, contentType:false, headers:{ 'X-Requested-With':'XMLHttpRequest' }, success: function(res){ handleResponse(res, feedbackBox, contact); }, error: function(){ feedbackBox.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded">Terjadi kesalahan jaringan.</div>'; } });
        } else {
          // fetch
          fetch(contact.action, { method:'POST', body: formData, headers:{ 'X-Requested-With':'XMLHttpRequest' } }).then(function(r){ return r.json(); }).then(function(res){ handleResponse(res, feedbackBox, contact); }).catch(function(){ feedbackBox.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded">Terjadi kesalahan jaringan.</div>'; });
        }
      });
      function handleResponse(res, feedbackBox, form){
        if (res.success){ feedbackBox.innerHTML = '<div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded">Terima kasih — pesan berhasil dikirim.</div>'; form.reset(); var mc = document.getElementById('message-count'); if (mc) mc.textContent = '0 karakter'; }
        else { feedbackBox.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded">' + (res.errors && res.errors.length ? res.errors.map(function(e){ return '<div>'+e+'</div>'; }).join('') : 'Terjadi kesalahan.') + '</div>'; }
      }
    }

  // small enhancement: smooth focus when navigated via hash at load
  if (window.location.hash){
    var hash = window.location.hash;
    if (hash === '#harga'){
      setTimeout(function(){ goToPricing('smooth', 600); }, 300);
    } else {
      var el = document.querySelector(hash);
      if (el){ setTimeout(function(){ el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 300); }
    }
  }

  });
})();

function initDigitalOceanStatus(){
  var widget = document.getElementById('do-status-widget');
  if (!widget) return;

  var statusBanner = widget.querySelector('[data-status-message]');
  var regionList = widget.querySelector('[data-region-list]');
  var incidentList = widget.querySelector('[data-incident-list]');
  var endpoint = 'https://status.digitalocean.com/api/v2/summary.json';

  setBanner('Mengambil status layanan DigitalOcean…');
  if (regionList) regionList.innerHTML = '<p class="text-sm text-gray-500 col-span-full">Memuat data region…</p>';
  if (incidentList) incidentList.innerHTML = '<p class="text-sm text-gray-500">Memuat ringkasan insiden…</p>';

  fetchJSON(endpoint).then(function(summary){
    return Promise.all([
      Promise.resolve(summary),
      fetchJSON('https://status.digitalocean.com/api/v2/incidents.json').catch(function(){ return null; }),
      fetchJSON('https://status.digitalocean.com/api/v2/scheduled-maintenances.json').catch(function(){ return null; })
    ]);
  }).then(function(results){
    var summary = results[0];
    var incidentsData = results[1];
    var maintenanceData = results[2];
    renderStatus(summary, incidentsData, maintenanceData);
  }).catch(function(err){
    console.error('DigitalOcean status fetch failed', err);
    setBanner('Gagal memuat status DigitalOcean. Coba muat ulang atau cek langsung di status.digitalocean.com.', 'error');
    if (regionList) regionList.innerHTML = '<p class="text-sm text-red-600 col-span-full">Tidak dapat memuat data region.</p>';
    if (incidentList) incidentList.innerHTML = '<p class="text-sm text-red-600">Tidak dapat memuat detail insiden.</p>';
  });

  function fetchJSON(url){
    return fetch(url).then(function(res){
      if (!res.ok) throw new Error('HTTP ' + res.status + ' for ' + url);
      return res.json();
    });
  }

  function renderStatus(data, incidentsPayload, maintPayload){
    if (!data || !data.status){
      setBanner('Data status tidak tersedia.', 'error');
      return;
    }

    var indicator = (data.status.indicator || 'none').toLowerCase();
    var description = data.status.description || 'Status tidak diketahui';
    setBanner('Status DigitalOcean: ' + description, mapIndicator(indicator));

  var components = Array.isArray(data.components) ? data.components.filter(function(comp){ return !comp.group; }) : [];
    renderRegions(components);

    if (incidentList){
      renderIncidents(incidentList, data, incidentsPayload, maintPayload);
    }
  }

  function renderIncidents(target, summary, incidentsPayload, maintPayload){
    var incidentsSource = [];
    if (incidentsPayload && Array.isArray(incidentsPayload.incidents)){
      incidentsSource = incidentsPayload.incidents.slice();
    } else if (Array.isArray(summary.incidents)){
      incidentsSource = summary.incidents.slice();
    }

    var maintSource = [];
    if (maintPayload && Array.isArray(maintPayload.scheduled_maintenances)){
      maintSource = maintPayload.scheduled_maintenances.slice();
    } else if (Array.isArray(summary.scheduled_maintenances)){
      maintSource = summary.scheduled_maintenances.slice();
    }

    var activeIncidents = incidentsSource.filter(function(inc){ return (inc.status || '').toLowerCase() !== 'resolved'; });
    var upcomingMaint = maintSource.filter(function(item){
      var st = (item.status || '').toLowerCase();
      return st !== 'completed';
    });

    target.innerHTML = '';

    if (!activeIncidents.length && !upcomingMaint.length){
      var recent = incidentsSource.slice(0, 3);
      if (recent.length){
        recent.forEach(function(inc){ target.appendChild(createIncidentItem(inc, 'incident', true)); });
      } else {
        target.innerHTML = '<p class="text-sm text-gray-500">Tidak ada insiden atau pemeliharaan yang sedang berlangsung.</p>';
      }
      return;
    }

    activeIncidents.slice(0, 4).forEach(function(inc){ target.appendChild(createIncidentItem(inc, 'incident', false)); });
    upcomingMaint.slice(0, 3).forEach(function(item){ target.appendChild(createIncidentItem(item, 'maintenance', false)); });
  }

  function renderRegions(components){
    if (!regionList) return;

    if (!components.length){
      regionList.innerHTML = '<p class="text-sm text-gray-500 col-span-full">DigitalOcean belum menyediakan data region terperinci.</p>';
      return;
    }

    var regions = {};
    components.forEach(function(comp){
      var code = extractRegionCode(comp.name) || extractRegionFromGroup(comp);
      if (!code) return;
      if (!regions[code]){
        regions[code] = { code: code, components: [], maxSeverity: -1, updatedAt: null };
      }
      var region = regions[code];
  var score = severityScore(comp.status);
      region.maxSeverity = Math.max(region.maxSeverity, score);
      region.components.push(comp);
      if (comp.updated_at && (!region.updatedAt || comp.updated_at > region.updatedAt)){
        region.updatedAt = comp.updated_at;
      }
    });

    var keys = Object.keys(regions).sort();
    if (!keys.length){
      regionList.innerHTML = '<p class="text-sm text-gray-500 col-span-full">Tidak ada data region yang dapat ditampilkan.</p>';
      return;
    }

    regionList.innerHTML = '';
    keys.forEach(function(code){
      regionList.appendChild(createRegionCard(regions[code]));
    });
  }

  function extractRegionCode(name){
    if (!name) return '';
    var upper = name.toUpperCase();
    var match = upper.match(/\b([A-Z]{2,3}\d)\b/);
    if (!match) match = upper.match(/^([A-Z]{2,3}\d)/);
    return match ? match[1] : '';
  }

  function extractRegionFromGroup(component){
    if (!component) return '';
    if (component.group){
      return extractRegionCode(component.name);
    }
    if (component.group_id){
      return extractRegionCode(component.group_id);
    }
    return '';
  }

  function createRegionCard(region){
    var card = document.createElement('div');
    card.className = 'do-region-card';

    var header = document.createElement('h4');
    header.textContent = region.code;
    var chip = document.createElement('span');
    chip.className = 'do-chip';
    var variant = variantFromScore(region.maxSeverity);
    chip.setAttribute('data-variant', variant);
    chip.textContent = variantLabel(variant);
    header.appendChild(chip);
    card.appendChild(header);

    var impacted = region.components.filter(function(comp){ return comp.status !== 'operational'; });
    var summary = document.createElement('p');
    if (!region.components.length){
      summary.textContent = 'Tidak ada layanan tercatat untuk region ini.';
    } else if (!impacted.length){
      summary.textContent = 'Semua layanan berjalan normal.';
    } else {
      summary.textContent = 'Ada ' + impacted.length + ' layanan yang terpengaruh.';
    }
    card.appendChild(summary);

    if (impacted.length){
      var list = document.createElement('ul');
      list.className = 'text-xs text-gray-600 space-y-1';
      impacted.slice(0, 3).forEach(function(comp){
        var item = document.createElement('li');
        item.textContent = comp.name + ' — ' + statusLabel(comp.status);
        list.appendChild(item);
      });
      if (impacted.length > 3){
        var more = document.createElement('li');
        more.className = 'text-xs text-gray-400';
        more.textContent = '+' + (impacted.length - 3) + ' layanan lainnya';
        list.appendChild(more);
      }
      card.appendChild(list);
    }

    var meta = document.createElement('div');
    meta.className = 'do-region-meta';
    var services = document.createElement('span');
    services.textContent = region.components.length + ' layanan';
    meta.appendChild(services);
    if (region.updatedAt){
      var updated = document.createElement('span');
      updated.textContent = formatDate(region.updatedAt);
      meta.appendChild(updated);
    }
    card.appendChild(meta);

    return card;
  }

  function variantFromScore(score){
    if (score <= 0) return 'ok';
    if (score <= 2) return 'warning';
    return 'error';
  }

  function setBanner(message, state){
    if (!statusBanner) return;
    statusBanner.textContent = message;
    if (state){ statusBanner.setAttribute('data-state', state); }
    else { statusBanner.removeAttribute('data-state'); }
  }

  function mapIndicator(value){
    switch (value){
      case 'none': return 'ok';
      case 'maintenance': return 'warning';
      case 'minor': return 'warning';
      case 'major':
      case 'critical':
        return 'error';
      default: return undefined;
    }
  }

  function severityScore(status){
    var scores = {
      operational: 0,
      under_maintenance: 1,
      degraded_performance: 2,
      partial_outage: 3,
      major_outage: 4
    };
    return scores[status] || 0;
  }

  function statusLabel(status){
    var map = {
      operational: 'Operational',
      degraded_performance: 'Degraded',
      partial_outage: 'Partial outage',
      major_outage: 'Major outage',
      under_maintenance: 'Maintenance'
    };
    return map[status] || status;
  }

  function variantLabel(variant){
    switch (variant){
      case 'ok': return 'Normal';
      case 'warning': return 'Waspada';
      case 'error': return 'Gangguan';
      default: return 'Info';
    }
  }

  function createIncidentItem(item, type, historical){
    var link = item.shortlink || item.url || buildStatusLink(item, type);
    var wrapper = document.createElement(link ? 'a' : 'div');
    wrapper.className = 'do-incident-card border border-gray-100 rounded-xl px-4 py-3 bg-gray-50/60';
    if (link){
      wrapper.href = link;
      wrapper.target = '_blank';
      wrapper.rel = 'noopener noreferrer';
    }

    var header = document.createElement('div');
    header.className = 'flex items-center justify-between gap-3';

    var title = document.createElement('h4');
    title.className = 'text-sm font-semibold text-gray-800';
    title.textContent = item.name || item.title || (type === 'incident' ? 'Insiden' : 'Pemeliharaan');

  var chip = document.createElement('span');
    chip.className = 'do-chip';
    var statusValue = type === 'incident' ? item.impact || 'minor' : item.status || 'scheduled';
    chip.setAttribute('data-variant', impactVariant(statusValue));
  chip.textContent = chipLabel(statusValue, type, historical);

    header.appendChild(title);
    header.appendChild(chip);
    wrapper.appendChild(header);

    if (item.started_at || item.scheduled_for){
      var timing = document.createElement('p');
      timing.className = 'text-xs text-gray-500 mt-1';
      var start = item.started_at || item.scheduled_for;
      var end = item.resolved_at || item.completed_at || item.scheduled_until;
      timing.textContent = formatDateRange(start, end);
      wrapper.appendChild(timing);
    }

    if (Array.isArray(item.incident_updates) && item.incident_updates.length){
      var latest = item.incident_updates[0];
      var body = document.createElement('p');
      body.className = 'text-sm text-gray-600 mt-2';
      body.textContent = latest.body || '';
      wrapper.appendChild(body);
    }

    return wrapper;
  }

  function buildStatusLink(item, type){
    if (!item || !item.id) return '';
    var slug = (type === 'maintenance') ? 'maintenances' : 'incidents';
    return 'https://status.digitalocean.com/' + slug + '/' + item.id;
  }

  function impactVariant(value){
    var normalized = (value || '').toLowerCase();
    if (normalized === 'none' || normalized === 'resolved' || normalized === 'completed') return 'ok';
    if (normalized === 'minor' || normalized === 'scheduled' || normalized === 'monitoring') return 'warning';
    return 'error';
  }

  function chipLabel(value, type, historical){
    if (type === 'maintenance'){
      var maintenanceMap = {
        scheduled: 'Dijadwalkan',
        in_progress: 'Berlangsung',
        verifying: 'Verifikasi',
        completed: 'Selesai'
      };
      return maintenanceMap[value] || 'Maintenance';
    }
    var impactMap = {
      none: historical ? 'Selesai' : 'Normal',
      minor: 'Minor impact',
      major: 'Major impact',
      critical: 'Critical',
      resolved: historical ? 'Selesai' : 'Resolved',
      monitoring: 'Monitoring'
    };
    return impactMap[value] || value;
  }

  function formatDate(value){
    if (!value) return '-';
    try {
      return new Date(value).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch (err){
      return value;
    }
  }

  function formatDateRange(start, end){
    if (!start) return '';
    var formattedStart = formatDate(start);
    if (!end) return formattedStart;
    return formattedStart + ' – ' + formatDate(end);
  }
}
