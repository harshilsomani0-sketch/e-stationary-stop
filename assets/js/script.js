// ADD THIS CODE TO assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live-search-input');
    const searchResults = document.getElementById('search-results');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = searchInput.value;

            if (query.length > 2) {
                fetch(`/e-stationary-stop/live_search.php?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = ''; // Clear previous results
                        if (data.length > 0) {
                            searchResults.style.display = 'block';
                            data.forEach(product => {
                                const item = document.createElement('a');
                                item.href = `/e-stationary-stop/product_details.php?id=${product.id}`;
                                item.className = 'search-result-item';
                                item.innerHTML = `
                                    <img src="/e-stationary-stop/assets/images/${product.image_url}" alt="${product.name}" width="40">
                                    <span>${product.name}</span>
                                `;
                                searchResults.appendChild(item);
                            });
                        } else {
                            searchResults.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                searchResults.style.display = 'none';
            }
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchResults.contains(event.target) && !searchInput.contains(event.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
});
// ADD THIS CODE TO THE BOTTOM of assets/js/script.js

document.addEventListener('click', function(event) {
    // Check if a wishlist button was clicked
    if (event.target.classList.contains('wishlist-btn')) {
        event.preventDefault();
        const button = event.target;
        const productId = button.dataset.productId;

        // Prepare data to send
        const formData = new FormData();
        formData.append('product_id', productId);

        fetch('/e-stationary-stop/toggle_wishlist.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error' && data.message === 'login_required') {
                // Redirect to login if not logged in
                window.location.href = '/e-stationary-stop/login.php';
            } else if (data.status === 'added') {
                // Mark as active (wishlisted)
                button.classList.add('active');
                button.title = 'Remove from Wishlist';
            } else if (data.status === 'removed') {
                // Mark as inactive
                button.classList.remove('active');
                button.title = 'Add to Wishlist';
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
// ADD THIS TO assets/js/script.js
// Live validation for password matching on the registration form
document.addEventListener('DOMContentLoaded', function() {
    const regForm = document.getElementById('registration-form');
    if (regForm) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const message = document.getElementById('password-match-message');

        const validatePasswords = function() {
            if (password.value === '' && confirmPassword.value === '') {
                message.textContent = '';
                return;
            }
            if (password.value === confirmPassword.value) {
                message.textContent = 'Passwords match!';
                message.style.color = 'green';
            } else {
                message.textContent = 'Passwords do not match.';
                message.style.color = 'red';
            }
        };

        password.addEventListener('keyup', validatePasswords);
        confirmPassword.addEventListener('keyup', validatePasswords);
    }
});
// Live preview for product customization
document.addEventListener('DOMContentLoaded', function() {
    const customTextInput = document.getElementById('custom-text-input');
    const livePreviewText = document.getElementById('live-preview-text');

    if (customTextInput && livePreviewText) {
        customTextInput.addEventListener('keyup', function() {
            if (customTextInput.value.trim() !== '') {
                livePreviewText.textContent = customTextInput.value;
            } else {
                livePreviewText.textContent = 'Your Text Here';
            }
        });
    }
});
// --- LOGIC FOR PROMOTIONAL MODAL ---
document.addEventListener('DOMContentLoaded', function() {
    const promoOverlay = document.getElementById('promo-modal-overlay');
    const closeButton = document.getElementById('close-promo-modal');
    
    // Function to show the modal
    const showPromoModal = function() {
        if (promoOverlay) {
            promoOverlay.classList.add('active');
        }
    };

    // Function to hide the modal
    const hidePromoModal = function() {
        if (promoOverlay) {
            promoOverlay.classList.remove('active');
        }
    };
    
    // Check if the URL has our promo flag
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('promo')) {
        showPromoModal();
        // Clean the URL so the modal doesn't show again on refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Event listeners to close the modal
    if (closeButton) {
        closeButton.addEventListener('click', hidePromoModal);
    }
    if (promoOverlay) {
        promoOverlay.addEventListener('click', function(event) {
            if (event.target === promoOverlay) {
                hidePromoModal();
            }
        });
    }
});
// --- AJAX LOGIC FOR ADDING TO CART ---
document.addEventListener('submit', function(event) {
    // Check if the submitted form is an "add to cart" form
    if (event.target.matches('.add-to-cart-form')) {
        // Prevent the default form submission (which causes a page reload)
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const button = form.querySelector('button[type="submit"]');
        const originalButtonText = button.textContent;

        // Change button text to give user feedback
        button.textContent = 'Adding...';
        button.disabled = true;

        // Send the form data to the server using fetch
        fetch('/e-stationary-stop/add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update the mini-cart count in the header
                const cartCountSpan = document.getElementById('cart-item-count');
                if (cartCountSpan) {
                    cartCountSpan.textContent = data.item_count;
                }
                
                // Provide visual feedback on the button
                button.textContent = 'Added!';
                setTimeout(() => {
                    button.textContent = originalButtonText;
                    button.disabled = false;
                }, 1500); // Revert back after 1.5 seconds

            } else {
                // Handle errors
                alert(data.message); // Simple alert for errors
                button.textContent = originalButtonText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            button.textContent = originalButtonText;
            button.disabled = false;
        });
    }
});
// --- AJAX LOGIC FOR FULL CART PAGE ---
document.addEventListener('DOMContentLoaded', function() {
    const cartBody = document.getElementById('cart-body');
    if (cartBody) {
        cartBody.addEventListener('click', function(event) {
            // Handle REMOVE button clicks
            if (event.target.classList.contains('remove-from-cart-btn')) {
                const button = event.target;
                const itemId = button.dataset.itemId;
                
                const formData = new FormData();
                formData.append('item_id', itemId);

                fetch('/e-stationary-stop/remove_from_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Remove the row from the table
                        document.getElementById(`cart-row-${itemId}`).remove();
                        // Update totals
                        document.getElementById('grand-total').textContent = '$' + data.grand_total;
                        document.getElementById('cart-item-count').textContent = data.item_count;
                        
                        // If cart is now empty, show message
                        if (data.item_count === 0) {
                            document.getElementById('cart-container').innerHTML = '<p>Your cart is empty.</p><a href="products.php" class="btn">Continue Shopping</a>';
                        }
                    }
                });
            }
        });

        cartBody.addEventListener('change', function(event) {
            // Handle QUANTITY input changes
            if (event.target.classList.contains('cart-quantity-input')) {
                const input = event.target;
                const itemId = input.dataset.itemId;
                const quantity = input.value;

                const formData = new FormData();
                formData.append('item_id', itemId);
                formData.append('quantity', quantity);

                fetch('/e-stationary-stop/update_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update the UI with new values from the server
                        document.getElementById(`subtotal-${itemId}`).textContent = '$' + data.item_subtotal;
                        document.getElementById('grand-total').textContent = '$' + data.grand_total;
                        document.getElementById('cart-item-count').textContent = data.item_count;
                    }
                });
            }
        });
    }
});
// --- AJAX LOGIC FOR APPLYING COUPON ---
document.addEventListener('DOMContentLoaded', function() {
    const couponForm = document.getElementById('coupon-form');
    if (couponForm) {
        couponForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const couponInput = document.getElementById('coupon-input');
            const couponMessage = document.getElementById('coupon-message');
            
            const formData = new FormData();
            formData.append('coupon_code', couponInput.value);

            fetch('/e-stationary-stop/apply_coupon.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                couponMessage.textContent = data.message;
                if (data.status === 'success') {
                    couponMessage.style.color = 'green';
                    // Update UI with discount info
                    document.getElementById('discount-row').style.display = 'table-row';
                    document.getElementById('discount-amount').textContent = '-$' + data.discount_amount;
                    document.getElementById('grand-total').textContent = '$' + data.new_grand_total;
                } else {
                    couponMessage.style.color = 'red';
                    // Hide discount row if coupon is invalid
                    document.getElementById('discount-row').style.display = 'none';
                }
            });
        });
    }
});
// --- AMBIENT AUTH FEATURES ---

// 1. Toggle Password Visibility Function
function togglePass(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// 2. Generic AJAX Form Handler
function handleAuthForm(formId, url) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop page reload

        const formData = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        const msgBox = document.getElementById('auth-message');

        // UI Updates
        btn.textContent = "Processing...";
        btn.disabled = true;
        msgBox.style.display = 'none';
        msgBox.className = '';

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            msgBox.style.display = 'block';
            msgBox.textContent = data.message;
            
            if (data.status === 'success') {
                msgBox.classList.add('success');
                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                msgBox.classList.add('error');
                btn.textContent = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            msgBox.style.display = 'block';
            msgBox.textContent = "An error occurred. Please try again.";
            msgBox.classList.add('error');
            btn.textContent = originalText;
            btn.disabled = false;
        });
    });
}

// Initialize Handlers
document.addEventListener('DOMContentLoaded', function() {
    // Check if we are on login or register page
    handleAuthForm('login-form', 'login.php');
    handleAuthForm('register-form', 'register.php');
});
// --- 10. Dark/Light Mode Toggle ---
    const themeToggleBtn = document.getElementById('theme-toggle');
    const body = document.body;

    // Check LocalStorage for saved preference
    const currentTheme = localStorage.getItem('theme');
    
    // Apply saved theme on load
    if (currentTheme === 'dark') {
        body.classList.add('dark-mode');
        if(themeToggleBtn) themeToggleBtn.textContent = '☀️'; // Sun icon for dark mode
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            
            // Update Icon and Save Preference
            if (body.classList.contains('dark-mode')) {
                themeToggleBtn.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            } else {
                themeToggleBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            }
        });
    }