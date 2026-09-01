window.addEventListener('load', function() {
    if (window.location.href.indexOf('/brief-1/?send=yes') != -1) {
      gtag('event', 'conversion', {'send_to': 'AW-790752334/7ukxCJapns4DEM7Yh_kC'});
    }
    
      if (window.location.href.indexOf('/brief-1/') != -1) {
        gtag('event', 'conversion', {'send_to': 'AW-790752334/Y89mCOSpns4DEM7Yh_kC'});
    }
    
       jQuery('body').on('mousedown', '[href*="mailto:"]', function() {
      gtag('event', 'conversion', {
        'send_to': 'AW-790752334/N-DvCOepns4DEM7Yh_kC'
      });
    })
  });