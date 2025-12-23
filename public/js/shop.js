// Product Filter and Search Functionality
function filterProducts() {
  const searchTerm = document.getElementById('productSearch')?.value.toLowerCase() || '';
  const sortBy = document.getElementById('sortBy')?.value || 'default';
  const productGrid = document.getElementById('productGrid');
  const noResults = document.getElementById('noResults');
  const resultsInfo = document.getElementById('resultsInfo');
  
  if (!productGrid) return;
  
  let products = Array.from(productGrid.querySelectorAll('.product-card'));
  let visibleCount = 0;
  
  // Filter by search term
  products.forEach(card => {
    const name = card.dataset.name || '';
    const matchesSearch = name.includes(searchTerm);
    
    if (matchesSearch) {
      card.classList.remove('hidden');
      card.style.display = '';
      visibleCount++;
    } else {
      card.classList.add('hidden');
      card.style.display = 'none';
    }
  });
  
  // Sort products
  if (sortBy !== 'default') {
    products = products.filter(card => !card.classList.contains('hidden'));
    
    products.sort((a, b) => {
      switch(sortBy) {
        case 'price-low':
          return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
        case 'price-high':
          return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
        case 'name':
          return (a.dataset.name || '').localeCompare(b.dataset.name || '');
        case 'newest':
          return parseInt(b.dataset.created || 0) - parseInt(a.dataset.created || 0);
        default:
          return 0;
      }
    });
    
    // Reorder in DOM
    products.forEach(card => productGrid.appendChild(card));
  }
  
  // Update UI
  if (noResults) {
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
  }
  if (resultsInfo) {
    resultsInfo.textContent = visibleCount === 0 ? 'No products found' : 
      `Showing ${visibleCount} product${visibleCount !== 1 ? 's' : ''}`;
  }
}

function clearFilters() {
  const searchInput = document.getElementById('productSearch');
  const sortSelect = document.getElementById('sortBy');
  
  if (searchInput) searchInput.value = '';
  if (sortSelect) sortSelect.value = 'default';
  
  filterProducts();
}

// Product Carousel Functionality
document.addEventListener('DOMContentLoaded', function() {
  // Initialize all carousels
  document.querySelectorAll('.product-carousel').forEach(carousel => {
    const images = carousel.querySelectorAll('.carousel-img');
    const dots = carousel.querySelectorAll('.dot');
    const prevBtn = carousel.querySelector('.carousel-prev');
    const nextBtn = carousel.querySelector('.carousel-next');
    let currentIndex = 0;
    
    function showImage(index) {
      images.forEach((img, i) => {
        img.classList.toggle('active', i === index);
      });
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });
      carousel.dataset.current = index;
    }
    
    if (prevBtn) {
      prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        showImage(currentIndex);
      });
    }
    
    if (nextBtn) {
      nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        currentIndex = (currentIndex + 1) % images.length;
        showImage(currentIndex);
      });
    }
    
    dots.forEach((dot, index) => {
      dot.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        currentIndex = index;
        showImage(currentIndex);
      });
    });
  });
});

// Add to Cart click handler - Removed to allow specific implementations (guest redirect or cart.js)
/*
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', () => {
    alert("Item added to cart (UI demo)");
  });
});
*/

// Quick View click handler
document.querySelectorAll('.quick-view').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    alert("Quick View popup (UI demo)");
  });
});
