// Smooth sidebar navigation without flash
(function(){
  const enable = () => {
    document.body.classList.add('page-ready');
    const sidebar = document.getElementById('sidebar');
    if(!sidebar) return;

    // Delegate clicks on internal links
    sidebar.addEventListener('click', function(e){
      const a = e.target.closest('a');
      if(!a) return;
      const url = a.getAttribute('href');
      if(!url || url.startsWith('http') || url.includes('logout.php') || url.startsWith('mailto:')) return; // allow normal
      if(url.indexOf('/PETVET/') === -1) return; // external or rootless
      e.preventDefault();
      navigate(url);
    });
  };

  async function navigate(url){
    try {
      document.body.classList.add('nav-transition');
      const resp = await fetch(url, {credentials:'same-origin'});
      if(!resp.ok) throw new Error('HTTP '+resp.status);
      const html = await resp.text();

      // Parse new HTML
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // Sync new <link rel="stylesheet"> and external <script src> assets that are not yet loaded
      const syncAssets = (parsedDoc) => {
        // Stylesheets
        parsedDoc.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
          const href = link.getAttribute('href');
            if(!href) return;
            if(!document.querySelector(`link[rel="stylesheet"][href='${href}']`)) {
              const clone = link.cloneNode(true);
              document.head.appendChild(clone);
            }
        });
        // External scripts (avoid duplicates; defer execution order not critical for our simple pages)
        parsedDoc.querySelectorAll('script[src]').forEach(scr => {
          const src = scr.getAttribute('src');
          if(!src) return;
          if(!document.querySelector(`script[src='${src}']`)) {
            const s = document.createElement('script');
            s.src = src;
            document.head.appendChild(s);
          }
        });
      };

      // Extract new main-content
      const newMain = doc.querySelector('.main-content');
      const currentMain = document.querySelector('.main-content');
      if(newMain && currentMain){
        // Ensure assets present before swapping (so styles apply immediately after DOM replacement)
        syncAssets(doc);
        // Replace content only
        currentMain.innerHTML = newMain.innerHTML;
        // Update active classes in sidebar
        const newActive = doc.querySelector('.dashboard-sidebar .active');
        if(newActive){
          document.querySelectorAll('.dashboard-sidebar a').forEach(a=>a.classList.remove('active'));
          const targetHref = newActive.getAttribute('href');
          const match = document.querySelector(`.dashboard-sidebar a[href='${targetHref}']`);
          if(match) match.classList.add('active');
        }
        // Update document title if available
        if(doc.title) document.title = doc.title;
        // Execute inline scripts inside new main-content (if any)
        currentMain.querySelectorAll('script').forEach(s=>{
          const ns=document.createElement('script');
          if(s.src){ ns.src = s.src; } else { ns.textContent = s.textContent; }
          document.body.appendChild(ns); ns.remove();
        });
        // Finish transition
        requestAnimationFrame(()=>{
          document.body.classList.remove('nav-transition');
        });
        // Update history
        window.history.pushState({partial:true}, '', url);
      } else {
        // Fallback full navigation
        window.location.href = url;
      }
    } catch(err){
      console.error('Partial nav failed, falling back', err);
      window.location.href = url; // fallback
    }
  }

  window.addEventListener('popstate', () => {
    // On back/forward do full reload (simpler) or could implement partial again
    window.location.reload();
  });

  if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', enable);
  } else {
    enable();
  }
})();
