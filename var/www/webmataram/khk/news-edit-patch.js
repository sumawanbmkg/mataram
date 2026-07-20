document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const newsId = urlParams.get('id');

    if (!newsId) {
        alert("ID Berita tidak ditemukan di URL!");
        return;
    }

    // 1. Ambil data lama dari server
    fetch(`api/news-detail.php?id=${newsId}`)
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const d = res.data;
            // Isi input teks
            document.querySelector('input[name="judul"]').value = d.judul;
            document.querySelector('select[name="id_kategori"]').value = d.id_kategori;
            if(document.querySelector('select[name="status"]')) {
                document.querySelector('select[name="status"]').value = d.status;
            }

            // Isi TinyMCE (tunggu sampai editor siap)
            if (typeof tinymce !== 'undefined') {
                setTimeout(() => {
                    if (tinymce.activeEditor) {
                        tinymce.activeEditor.setContent(d.isi_berita);
                    } else {
                        document.querySelector('textarea[name="isi"]').value = d.isi_berita;
                    }
                }, 1000);
            }
        } else {
            alert("Gagal memuat data: " + res.message);
        }
    });

    // 2. Fungsi Simpan Perubahan
    window.updateNews = function() {
        const formData = new FormData();
        formData.append('id_berita', newsId);
        formData.append('judul', document.querySelector('input[name="judul"]').value);
        formData.append('id_kategori', document.querySelector('select[name="id_kategori"]').value);
        
        let isi = (typeof tinymce !== 'undefined' && tinymce.activeEditor) 
                  ? tinymce.activeEditor.getContent() 
                  : document.querySelector('textarea[name="isi"]').value;
        formData.append('isi', isi);

        fetch('api/news-update.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(res => {
            if(res.success) {
                alert("Berita Berhasil Diperbarui!");
                window.location.href = 'news.html';
            } else {
                alert("Gagal Update: " + res.message);
            }
        });
    };

    // Pasang fungsi ke tombol
    const btnSave = document.querySelector('.btn-primary');
    if(btnSave) {
        btnSave.type = "button";
        btnSave.onclick = window.updateNews;
    }
});
// Pastikan area dropzone bisa diklik untuk memilih file
document.addEventListener('click', function(e) {
    const dropzone = e.target.closest('.dropzone') || e.target.closest('.upload-area');
    if (dropzone) {
        const fileInput = document.getElementById('imageInput');
        if (fileInput) fileInput.click();
    }
});

// Tampilkan preview saat gambar dipilih
document.addEventListener('change', function(e) {
    if (e.target.id === 'imageInput' && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('previewImage') || document.querySelector('.preview-container img');
            if (preview) preview.src = event.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
