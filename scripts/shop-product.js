function changeQty(delta) {
  const qtyInput = document.getElementById("quantity");
  let current = parseInt(qtyInput.value, 10);
  if (isNaN(current)) current = 1;
  const newQty = Math.max(1, current + delta);
  qtyInput.value = newQty;
}

// Add to Cart click handler 
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', () => {
    alert("Item added to cart (UI demo)");
  });
});

//direct to product page
document.querySelectorAll('.product-card').forEach(card => {
  card.addEventListener('click', function(e) {
    if (!e.target.closest('button') && !e.target.closest('a')) {
      window.location.href = 'shop-product.php';
    }
  });
});