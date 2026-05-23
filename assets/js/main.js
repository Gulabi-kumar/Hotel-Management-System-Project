// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required]');
    
    for(let input of inputs) {
        if(!input.value.trim()) {
            alert(`Please fill in ${input.name}`);
            input.focus();
            return false;
        }
    }
    
    // Password strength validation
    const password = form.querySelector('input[type="password"]');
    if(password && password.value.length < 6) {
        alert('Password must be at least 6 characters long');
        return false;
    }
    
    return true;
}

// Datepicker restrictions for booking
function initializeDatePicker() {
    const today = new Date().toISOString().split('T')[0];
    const checkinInput = document.getElementById('check_in');
    const checkoutInput = document.getElementById('check_out');
    
    if(checkinInput) {
        checkinInput.min = today;
        checkinInput.addEventListener('change', function() {
            if(checkoutInput) {
                checkoutInput.min = this.value;
                if(checkoutInput.value && checkoutInput.value < this.value) {
                    checkoutInput.value = this.value;
                }
            }
        });
    }
}

// Calculate total price
function calculateTotalPrice() {
    const checkin = new Date(document.getElementById('check_in').value);
    const checkout = new Date(document.getElementById('check_out').value);
    const pricePerNight = parseFloat(document.getElementById('price_per_night').value);
    
    if(checkin && checkout && pricePerNight) {
        const timeDiff = checkout.getTime() - checkin.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
        
        if(daysDiff > 0) {
            const totalPrice = daysDiff * pricePerNight;
            document.getElementById('total_price').value = totalPrice.toFixed(2);
            document.getElementById('display_total').textContent = '₹' + totalPrice.toFixed(2);
            document.getElementById('nights_count').textContent = daysDiff;
        }
    }
}

// Room availability check
function checkAvailability(roomId, checkin, checkout) {
    if(!checkin || !checkout) {
        alert('Please select check-in and check-out dates');
        return false;
    }
    
    fetch(`../api/check-availability.php?room_id=${roomId}&checkin=${checkin}&checkout=${checkout}`)
        .then(response => response.json())
        .then(data => {
            if(data.available) {
                document.getElementById('booking-form').style.display = 'block';
            } else {
                alert('Room not available for selected dates');
            }
        });
}

// Image preview for uploads
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    const reader = new FileReader();
    
    reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
    }
    
    if(file) {
        reader.readAsDataURL(file);
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDatePicker();
    
    // Auto-calculate price when dates change
    const dateInputs = document.querySelectorAll('#check_in, #check_out');
    dateInputs.forEach(input => {
        input.addEventListener('change', calculateTotalPrice);
    });
});