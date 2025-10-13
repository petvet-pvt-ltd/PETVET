// Add to Cart click handler 
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', () => {
    alert("Item added to cart (UI demo)");
  });
});

// Quick View click handler
document.querySelectorAll('.quick-view').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    alert("Quick View popup (UI demo)");
  });
});
