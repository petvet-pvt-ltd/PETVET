/**
 * Simple Shopping Cart for PetVet
 * Handles adding items to cart using LocalStorage
 */

function addToCart(productId) {
    // In a real app, this would add to a session or database cart
    // For now, we'll just show a success message
    
    // You can implement per-clinic cart logic here
    // const clinicId = document.querySelector('.clinic-header-card').dataset.clinicId;
    // const cartKey = `cart_${clinicId}`;
    
    alert('Product added to cart! (Functionality to be fully implemented)');
    
    // Visual feedback
    const btn = event.target;
    const originalText = btn.innerText;
    btn.innerText = 'Added ✓';
    btn.style.background = '#10b981';
    btn.style.color = 'white';
    
    setTimeout(() => {
        btn.innerText = originalText;
        btn.style.background = '';
        btn.style.color = '';
    }, 2000);
}
