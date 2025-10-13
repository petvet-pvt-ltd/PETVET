// Add to Cart click handler 
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', () => {
    alert("Item added to cart (UI demo)");
  });
});

// Category filtering
document.querySelectorAll('.category-card').forEach(card => {
  card.addEventListener('click', e => {
    e.preventDefault();
    const category = card.dataset.category;

    document.querySelectorAll('.product-card').forEach(product => {
      if (category === 'all' || product.dataset.category === category) {
        product.classList.remove('hidden'); 
      } else {
        product.classList.add('hidden'); 
      }
    });
  });
});

//direct to product page
document.querySelectorAll('.product-card').forEach(card => {
  card.addEventListener('click', function(e) {
    if (!e.target.closest('button') && !e.target.closest('a')) {
      const productId = this.dataset.productId;
      if (productId) {
        window.location.href = `shop-product.php?id=${productId}`;
      } else {
        window.location.href = 'shop-product.php';
      }
    }
  });
});

