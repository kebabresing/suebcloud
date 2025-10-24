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

    // Scroll to pricing + highlight Pro CTA when users click 'Mulai Gratis'
    document.querySelectorAll('.to-pricing').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        var target = document.querySelector('#harga');
        if (!target) return;
        var offset = 80; // header height approx
        var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
        if ($){ $('html,body').animate({ scrollTop: top }, 500); }
        else { window.scrollTo({ top: top, behavior: 'smooth' }); }
        // After scroll, focus and highlight Pro CTA
        setTimeout(function(){
          var pro = document.getElementById('pro-cta');
          if (pro){
            try { pro.focus({ preventScroll: true }); } catch(err){ pro.focus(); }
            pro.classList.add('focus-highlight');
            setTimeout(function(){ pro.classList.remove('focus-highlight'); }, 2200);
          }
        }, 520);
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
    if(window.location.hash){ var el = document.querySelector(window.location.hash); if(el) setTimeout(function(){ el.scrollIntoView({behavior:'smooth', block:'start'}); }, 300); }

  });
})();
