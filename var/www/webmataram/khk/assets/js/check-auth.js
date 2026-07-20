document.addEventListener('DOMContentLoaded', function() {
    fetch('api/check_session.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Jika session gagal/kosong, tendang ke login
                window.location.href = 'pintu-masuk-rahasia.html';
            } else {
                console.log("✅ Session aktif:", data.user);
                // Update nama admin di dashboard jika ada elemennya
                const adminNameEl = document.getElementById('adminName');
                if (adminNameEl) adminNameEl.textContent = data.user;
            }
        })
        .catch(error => {
            console.error('Auth Check Error:', error);
        });
});
