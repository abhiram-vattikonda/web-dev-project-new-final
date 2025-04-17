// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Image preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// Filter listings
function filterListings() {
    const category = document.getElementById('category-filter').value;
    const location = document.getElementById('location-filter').value;
    const minPrice = document.getElementById('min-price-filter').value;
    const maxPrice = document.getElementById('max-price-filter').value;

    const listings = document.querySelectorAll('.listing-card');
    
    listings.forEach(listing => {
        const listingCategory = listing.dataset.category;
        const listingLocation = listing.dataset.location;
        const listingPrice = parseFloat(listing.dataset.price);

        const categoryMatch = !category || listingCategory === category;
        const locationMatch = !location || listingLocation.toLowerCase().includes(location.toLowerCase());
        const minPriceMatch = !minPrice || listingPrice >= parseFloat(minPrice);
        const maxPriceMatch = !maxPrice || listingPrice <= parseFloat(maxPrice);

        if (categoryMatch && locationMatch && minPriceMatch && maxPriceMatch) {
            listing.style.display = 'block';
        } else {
            listing.style.display = 'none';
        }
    });
}

// Date range validation for bookings
function validateDateRange() {
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);
    const today = new Date();

    if (startDate < today) {
        alert('Start date cannot be in the past');
        return false;
    }

    if (endDate <= startDate) {
        alert('End date must be after start date');
        return false;
    }

    return true;
}
/*
// Calculate total price
function calculateTotalPrice() {
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);
    const pricePerDay = parseFloat(document.getElementById('price-per-day').value);

    if (!startDate || !endDate || !pricePerDay) return;

    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const totalPrice = diffDays * pricePerDay;

    document.getElementById('total_price').textContent = `$${totalPrice.toFixed(2)}`;
}

// Calculate total price for rental
function calculateRentalPrice() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const pricePerDay = parseFloat(document.getElementById('price_per_day').value);

    if (!startDate || !endDate || !pricePerDay) return;

    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const totalPrice = diffDays * pricePerDay;

    document.getElementById('total_price').value = totalPrice.toFixed(2);
}*/

// Initialize date pickers
function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];

    dateInputs.forEach(input => {
        input.min = today;
        input.addEventListener('change', calculateTotalPrice);
    });
}

// Toggle mobile menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Initialize tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    initDatePickers();

    // Initialize tooltips
    initTooltips();

    // Add event listeners for form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form.id)) {
                e.preventDefault();
            }
        });
    });

    // Add event listeners for image preview
    const imageInputs = document.querySelectorAll('input[type="file"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.dataset.preview;
            if (previewId) {
                previewImage(this, previewId);
            }
        });
    });

    // Add event listeners for filters
    const filterInputs = document.querySelectorAll('#category-filter, #location-filter, #min-price-filter, #max-price-filter');
    filterInputs.forEach(input => {
        input.addEventListener('change', filterListings);
    });

    // Add event listeners for rental price calculation
    const startDateInputs = document.querySelectorAll('input[name="start_date"]');
    const endDateInputs = document.querySelectorAll('input[name="end_date"]');

    startDateInputs.forEach(input => {
        input.addEventListener('change', calculateRentalPrice);
    });

    endDateInputs.forEach(input => {
        input.addEventListener('change', calculateRentalPrice);
    });
}); 