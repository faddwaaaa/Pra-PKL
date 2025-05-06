document.addEventListener('DOMContentLoaded', function() {
    // Payment Method Selection
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentModal = document.getElementById('paymentModal');
    const paymentMethodName = document.getElementById('paymentMethodName');
    const accountNumber = document.getElementById('accountNumber');
    const closeModal = document.querySelector('.close-modal');

    // Data rekening untuk masing-masing metode
    const paymentMethods = {
        'bca': {
            name: 'Bank BCA',
            number: '1234567890',
            accountName: 'BOOKNEST ID'
        },
        'dana': {
            name: 'DANA',
            number: '081234567890',
            accountName: 'BOOKNEST ID'
        },
        'ovo': {
            name: 'OVO',
            number: '081234567890',
            accountName: 'BOOKNEST ID'
        }
    };

    // Handle payment method selection
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            const method = this.getAttribute('data-method');
            const methodData = paymentMethods[method];

            // Update modal content
            paymentMethodName.textContent = methodData.name;
            accountNumber.textContent = methodData.number;

            // Show modal
            paymentModal.style.display = 'flex';
        });
    });

    // Close modal
    closeModal.addEventListener('click', function() {
        paymentModal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === paymentModal) {
            paymentModal.style.display = 'none';
        }
    });

    // File upload preview
    const fileInput = document.getElementById('proof');
    const fileName = document.querySelector('.file-name');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
        } else {
            fileName.textContent = 'Format: JPG, PNG, JPEG (max 2MB)';
        }
    });

    // Copy to clipboard function
    window.copyToClipboard = function(element) {
        const textToCopy = document.querySelector(element).textContent;
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Show copied feedback
            const copyBtn = document.querySelector('.btn-copy');
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Tersalin';

            setTimeout(() => {
                copyBtn.innerHTML = originalText;
            }, 2000);
        });
    };

    // Form submission
    const checkoutForm = document.getElementById('checkoutForm');

    checkoutForm.addEventListener('submit', function(e) {
        // Validate form
        const paymentMethod = document.querySelector('input[name="metode_pembayaran"]:checked');

        if (!paymentMethod) {
            e.preventDefault(); // Cegah kirim kalau invalid
            alert('Silakan pilih metode pembayaran');
            return;
        }

        // Kalau valid, form akan terkirim normal ke checkout_process.php
        // Optional: tampilkan loading di tombol
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    });

    // Responsive adjustments
    function handleResponsive() {
        if (window.innerWidth < 768) {
            document.querySelector('.checkout-grid').style.gridTemplateColumns = '1fr';
        } else {
            document.querySelector('.checkout-grid').style.gridTemplateColumns = '1.5fr 1fr';
        }
    }

    window.addEventListener('resize', handleResponsive);
    handleResponsive();
});
