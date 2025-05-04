document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentOptions = document.querySelectorAll('.payment-option input');
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            showPaymentDetails(this.value);
        });
    });

    // Form submission handling
    const checkoutForm = document.querySelector('.checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            const address = document.querySelector('input[name="alamat"]');
            const paymentMethod = document.querySelector('input[name="metode"]:checked');
            
            if (!address.value) {
                showAlert('Please enter your shipping address');
                return;
            }
            
            if (!paymentMethod) {
                showAlert('Please select a payment method');
                return;
            }
            
            // If validation passes, submit the form
            this.submit();
        });
    }

    // Initialize quantity controls
    initQuantityControls();
});

function showPaymentDetails(method) {
    const popup = document.querySelector('.popup-overlay');
    const popupTitle = document.querySelector('.popup-content h3');
    const popupText = document.querySelector('.popup-content p');
    
    let title = '';
    let details = '';
    
    switch(method) {
        case 'DANA':
            title = 'DANA Payment';
            details = 'Please transfer to DANA number: 0812-0967-2345 (a.n Booknest)';
            break;
        case 'BCA':
            title = 'BCA Transfer';
            details = 'Bank Account: 1234567890 (a.n Booknest)';
            break;
        case 'OVO':
            title = 'OVO Payment';
            details = 'Please transfer to OVO number: 0813-0987-6574 (a.n Booknest)';
            break;
        case 'COD':
            title = 'Cash On Delivery';
            details = 'Pay when your order arrives. Our courier will contact you.';
            break;
        default:
            return;
    }
    
    popupTitle.textContent = title;
    popupText.textContent = details;
    popup.classList.add('active');
    
    // Close popup when clicking the button
    const popupBtn = document.querySelector('.popup-btn');
    popupBtn.addEventListener('click', function() {
        popup.classList.remove('active');
    });
    
    // Close popup when clicking outside
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            popup.classList.remove('active');
        }
    });
}

function showAlert(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert-message';
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #ff4444;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }, 3000);
}

function initQuantityControls() {
    const quantityControls = document.querySelectorAll('.quantity-control');
    
    quantityControls.forEach(control => {
        const minusBtn = control.querySelector('.quantity-minus');
        const plusBtn = control.querySelector('.quantity-plus');
        const input = control.querySelector('.quantity-input');
        
        minusBtn.addEventListener('click', () => {
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
                updateCartItem(input);
            }
        });
        
        plusBtn.addEventListener('click', () => {
            let value = parseInt(input.value);
            input.value = value + 1;
            updateCartItem(input);
        });
        
        input.addEventListener('change', () => {
            updateCartItem(input);
        });
    });
}

function updateCartItem(input) {
    const productId = input.dataset.productId;
    const newQuantity = parseInt(input.value);
    
    // Here you would typically make an AJAX call to update the cart
    console.log(`Updating product ${productId} to quantity ${newQuantity}`);
    
    // For demo purposes, we'll just update the displayed subtotal
    const priceElement = input.closest('.product-item').querySelector('.product-price');
    const unitPrice = parseFloat(priceElement.dataset.unitPrice);
    const subtotal = unitPrice * newQuantity;
    
    priceElement.textContent = `Rp${subtotal.toLocaleString('id-ID')}`;
    
    // Update the order totals
    updateOrderTotals();
}

function updateOrderTotals() {
    // This would be more comprehensive in a real implementation
    const subtotals = document.querySelectorAll('.product-price');
    let subtotal = 0;
    
    subtotals.forEach(el => {
        const priceText = el.textContent.replace('Rp', '').replace(/\./g, '');
        subtotal += parseFloat(priceText);
    });
    
    const shipping = 10000;
    const total = subtotal + shipping;
    
    document.querySelector('.subtotal-value').textContent = `Rp${subtotal.toLocaleString('id-ID')}`;
    document.querySelector('.shipping-value').textContent = `Rp${shipping.toLocaleString('id-ID')}`;
    document.querySelector('.total-value').textContent = `Rp${total.toLocaleString('id-ID')}`;
}