// Basic client-side validation (e.g., for dates)
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const checkIn = form.querySelector('input[name="check_in"]');
            const checkOut = form.querySelector('input[name="check_out"]');
            if (checkIn && checkOut && new Date(checkOut.value) <= new Date(checkIn.value)) {
                alert('Check-out must be after check-in');
                e.preventDefault();
            }
        });
    });
});

// Load available rooms for selected dates (for checkin page)
document.addEventListener('DOMContentLoaded', function () {
    const checkIn = document.querySelector('input[name="check_in"]');
    const checkOut = document.querySelector('input[name="check_out"]');
    const roomSelect = document.getElementById('room_id');

    async function loadRooms() {
        if (!checkIn || !checkOut || !roomSelect) return;
        if (!checkIn.value || !checkOut.value) return;
        try {
            const resp = await fetch(`available_rooms.php?check_in=${encodeURIComponent(checkIn.value)}&check_out=${encodeURIComponent(checkOut.value)}`);
            const data = await resp.json();
            roomSelect.innerHTML = '';
            if (data.error) {
                const opt = document.createElement('option');
                opt.textContent = 'Error loading rooms';
                roomSelect.appendChild(opt);
                return;
            }
            if (!data.rooms || data.rooms.length === 0) {
                const opt = document.createElement('option');
                opt.textContent = 'No rooms available for selected dates';
                roomSelect.appendChild(opt);
                return;
            }
            data.rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = `${r.room_number} - ${r.type} ($${r.price})`;
                roomSelect.appendChild(opt);
            });
        } catch (e) {
            roomSelect.innerHTML = '<option>Error loading rooms</option>';
        }
    }

    if (checkIn && checkOut && roomSelect) {
        checkIn.addEventListener('change', loadRooms);
        checkOut.addEventListener('change', loadRooms);
    }
});