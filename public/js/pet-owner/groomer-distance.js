// Pet Owner Services (Groomers) - Distance badges (Shop-style UI)
(function () {
  let lastDistanceMap = null;

  function unique(arr) {
    return Array.from(new Set(arr));
  }

  function getSortMode() {
    const el = document.getElementById('sortFilter');
    return el ? String(el.value || '').trim() : '';
  }

  function getServiceType() {
    const el = document.getElementById('serviceTypeInput');
    return el ? String(el.value || '').trim() : '';
  }

  function normalizeName(value) {
    return String(value || '')
      .trim()
      .toLowerCase();
  }

  function sortGrid(mode) {
    const grid = document.querySelector('.providers-grid');
    if (!grid) return;

    const cards = Array.from(
      grid.querySelectorAll(
        'article.provider-card, article.service-card, article.package-card'
      )
    );

    if (cards.length <= 1) return;

    if (mode === 'az') {
      cards.sort((a, b) => {
        const aName = normalizeName(a.dataset.sortName);
        const bName = normalizeName(b.dataset.sortName);
        return aName.localeCompare(bName);
      });
    } else if (mode === 'nearest') {
      cards.sort((a, b) => {
        const aDist = a.dataset.distanceKm ? parseFloat(a.dataset.distanceKm) : Number.POSITIVE_INFINITY;
        const bDist = b.dataset.distanceKm ? parseFloat(b.dataset.distanceKm) : Number.POSITIVE_INFINITY;

        if (aDist !== bDist) return aDist - bDist;

        // Tie-breaker: A–Z
        const aName = normalizeName(a.dataset.sortName);
        const bName = normalizeName(b.dataset.sortName);
        return aName.localeCompare(bName);
      });
    } else {
      return;
    }

    // Re-append in sorted order
    const frag = document.createDocumentFragment();
    cards.forEach((c) => frag.appendChild(c));
    grid.appendChild(frag);
  }

  function clearLoaders() {
    document.querySelectorAll('.groomer-distance, .sitter-distance').forEach((el) => {
      if (el.querySelector('.distance-loader')) el.innerHTML = '';
    });
  }

  function setDistance(el, distanceFormatted) {
    if (!distanceFormatted) {
      el.innerHTML = '';
      return;
    }

    el.innerHTML = `
      <span class="clinic-item-distance">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
        ${distanceFormatted}
      </span>
    `;
  }

  async function loadDistances(userLat, userLng, providerIds, providerType = 'groomers') {
    const endpoint = providerType === 'sitters' 
      ? '/PETVET/api/pet-owner/get-sitters-by-distance.php'
      : '/PETVET/api/pet-owner/get-groomers-by-distance.php';
    
    const url = `${endpoint}?latitude=${encodeURIComponent(
      userLat
    )}&longitude=${encodeURIComponent(userLng)}&ids=${encodeURIComponent(
      providerIds.join(',')
    )}`;

    const response = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error('Distance API failed');

    const data = await response.json();
    const dataKey = providerType === 'sitters' ? 'sitters' : 'groomers';
    if (!data || data.success !== true || !Array.isArray(data[dataKey])) {
      throw new Error('Invalid distance response');
    }

    const map = new Map();
    data[dataKey].forEach((g) => {
      if (!g || g.id === undefined) return;
      map.set(String(g.id), g);
    });

    lastDistanceMap = map;
    window.__petvetGroomerDistances = map;

    // Update distances based on provider type
    const distanceSelector = providerType === 'sitters' ? '.sitter-distance[data-sitter-id]' : '.groomer-distance[data-groomer-id]';
    const idAttribute = providerType === 'sitters' ? 'sitterId' : 'groomerId';
    
    document
      .querySelectorAll(distanceSelector)
      .forEach((el) => {
        const id = String(el.dataset[idAttribute] || '');
        const g = map.get(id);
        setDistance(el, g ? g.distance_formatted : '');
      });

    // Attach distance_km to cards for sorting
    const cardSelector = providerType === 'sitters' ? '[data-sitter-id]' : '[data-groomer-id]';
    document.querySelectorAll(cardSelector).forEach((card) => {
      const gid = String(card.dataset[idAttribute] || '').trim();
      if (!gid || gid === '0') return;
      const g = map.get(gid);
      if (g && g.distance_km !== undefined && g.distance_km !== null) {
        card.dataset.distanceKm = String(g.distance_km);
      } else {
        delete card.dataset.distanceKm;
      }
    });

    return map;
  }

  function ensureDistancesLoaded(providerIds, providerType, onLoaded) {
    if (lastDistanceMap && providerIds.length > 0) {
      if (typeof onLoaded === 'function') onLoaded();
      return;
    }

    if (!navigator.geolocation) {
      clearLoaders();
      return;
    }

    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        try {
          await loadDistances(pos.coords.latitude, pos.coords.longitude, providerIds, providerType);
          if (typeof onLoaded === 'function') onLoaded();
        } catch (e) {
          console.error(e);
          clearLoaders();
        }
      },
      () => {
        clearLoaders();
      },
      {
        enableHighAccuracy: false,
        timeout: 8000,
        maximumAge: 5 * 60 * 1000,
      }
    );
  }

  function initSortControls(providerIds, providerType) {
    const sortEl = document.getElementById('sortFilter');
    if (!sortEl) return;

    // Client-side sorting; don't submit form.
    sortEl.addEventListener('change', () => {
      const mode = getSortMode();
      if (mode === 'az') {
        sortGrid('az');
        return;
      }
      if (mode === 'nearest') {
        // Nearest only makes sense for groomers.
        if (getServiceType() !== 'groomers') return;
        ensureDistancesLoaded(providerIds, providerType, () => sortGrid('nearest'));
      }
    });
  }

  function init() {
    const serviceType = getServiceType();
    
    // Check for both groomers and sitters
    const groomerDistanceEls = Array.from(
      document.querySelectorAll('.groomer-distance[data-groomer-id]')
    );
    const sitterDistanceEls = Array.from(
      document.querySelectorAll('.sitter-distance[data-sitter-id]')
    );
    
    const distanceEls = serviceType === 'sitters' ? sitterDistanceEls : groomerDistanceEls;
    const providerType = serviceType === 'sitters' ? 'sitters' : 'groomers';

    if (distanceEls.length === 0) {
      // Still allow A–Z sorting for non-groomer pages
      const sortEl = document.getElementById('sortFilter');
      if (sortEl && getSortMode() === 'az') {
        sortGrid('az');
      }
      return;
    }

    // Always wire sort controls
    const idAttribute = serviceType === 'sitters' ? 'sitterId' : 'groomerId';
    const providerIds = unique(
      distanceEls
        .map((el) => String(el.dataset[idAttribute] || '').trim())
        .filter(Boolean)
    );
    initSortControls(providerIds, providerType);

    const mode = getSortMode();

    // For sitters, only show distances, don't enable sorting by distance
    if (serviceType === 'sitters') {
      // Load distances for display only
      if (navigator.geolocation && providerIds.length > 0) {
        ensureDistancesLoaded(providerIds, providerType, () => {});
      } else {
        clearLoaders();
      }
      return;
    }

    // Sorting is only intended for groomers in this page.
    if (serviceType !== 'groomers') {
      clearLoaders();
      return;
    }

    // If A–Z selected, sort immediately (but still load distances for display).
    if (mode === 'az') {
      sortGrid('az');
    }

    // If geolocation not supported, hide loaders.
    if (!navigator.geolocation) {
      clearLoaders();
      return;
    }

    if (providerIds.length === 0) {
      clearLoaders();
      return;
    }

    // Load distances (for badge display), then apply selected sort.
    ensureDistancesLoaded(providerIds, providerType, () => {
      const currentMode = getSortMode();
      if (currentMode === 'nearest') sortGrid('nearest');
      else if (currentMode === 'az') sortGrid('az');
    });
  }

  document.addEventListener('DOMContentLoaded', init);
})();
