// Booking System JavaScript

class BookingSystem {
    constructor() {
        this.initDatePickers();
        this.initEventListeners();
    }
    
    initDatePickers() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach(input => {
            if (input.id.includes('check') || input.name.includes('check')) {
                input.min = today;
            }
        });
        
        // Date validation for check-in/check-out
        const checkin = document.getElementById('check_in');
        const checkout = document.getElementById('check_out');
        
        if (checkin && checkout) {
            checkin.addEventListener('change', () => {
                checkout.min = checkin.value;
                if (checkout.value && new Date(checkout.value) < new Date(checkin.value)) {
                    checkout.value = checkin.value;
                }
                this.calculateTotal();
            });
            
            checkout.addEventListener('change', () => {
                this.calculateTotal();
            });
        }
    }
    
    initEventListeners() {
        // Room selection change
        const roomSelect = document.getElementById('room_id');
        if (roomSelect) {
            roomSelect.addEventListener('change', () => {
                this.fetchRoomPrice(roomSelect.value);
            });
        }
        
        // Guests selection
        const guestsSelect = document.getElementById('guests');
        if (guestsSelect) {
            guestsSelect.addEventListener('change', this.validateGuests);
        }
    }
    
    async fetchRoomPrice(roomId) {
        if (!roomId) return;
        
        try {
            const response = await fetch(`../api/get-room-price.php?id=${roomId}`);
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('room_price').textContent = data.price;
                this.calculateTotal();
            }
        } catch (error) {
            console.error('Error fetching room price:', error);
        }
    }
    
    calculateTotal() {
        const checkin = document.getElementById('check_in');
        const checkout = document.getElementById('check_out');
        const priceElement = document.getElementById('room_price');
        
        if (!checkin || !checkout || !priceElement) return;
        
        const startDate = new Date(checkin.value);
        const endDate = new Date(checkout.value);
        const price = parseFloat(priceElement.textContent) || 0;
        
        if (startDate && endDate && endDate > startDate) {
            const timeDiff = endDate.getTime() - startDate.getTime();
            const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            if (days > 0) {
                const total = days * price;
                
                // Update display
                const nightsElement = document.getElementById('nights_count');
                const totalElement = document.getElementById('display_total');
                const totalInput = document.getElementById('total_price');
                
                if (nightsElement) nightsElement.textContent = days;
                if (totalElement) totalElement.textContent = total.toFixed(2);
                if (totalInput) totalInput.value = total.toFixed(2);
            }
        }
    }
    
    validateGuests() {
        const guests = parseInt(this.value);
        const roomCapacity = parseInt(document.getElementById('room_capacity')?.value || 4);
        
        if (guests > roomCapacity) {
            alert(`Maximum capacity for this room is ${roomCapacity} guests`);
            this.value = roomCapacity;
        }
    }
    
    // Check room availability
    async checkAvailability(roomId, checkin, checkout) {
        if (!roomId || !checkin || !checkout) {
            alert('Please select room and dates');
            return false;
        }
        
        try {
            const response = await fetch(
                `../api/check-availability.php?room_id=${roomId}&checkin=${checkin}&checkout=${checkout}`
            );
            const data = await response.json();
            
            if (data.available) {
                return true;
            } else {
                alert('Room is not available for selected dates');
                return false;
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            return false;
        }
    }
}

// Image upload preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    }
}

// Form validation
function validateBookingForm() {
    const form = document.getElementById('booking-form');
    const requiredFields = form.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            alert(`Please fill in ${field.name || field.id}`);
            field.focus();
            return false;
        }
    }
    
    // Validate dates
    const checkin = new Date(document.getElementById('check_in').value);
    const checkout = new Date(document.getElementById('check_out').value);
    const today = new Date();
    
    if (checkin < today.setHours(0, 0, 0, 0)) {
        alert('Check-in date cannot be in the past');
        return false;
    }
    
    if (checkout <= checkin) {
        alert('Check-out date must be after check-in date');
        return false;
    }
    
    return true;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    const bookingSystem = new BookingSystem();
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', function() {
            // Add tooltip functionality
        });
    });
    
    // Auto-refresh availability every 5 minutes
    setInterval(() => {
        if (window.location.pathname.includes('booking')) {
            // Refresh room availability
        }
    }, 300000);
});