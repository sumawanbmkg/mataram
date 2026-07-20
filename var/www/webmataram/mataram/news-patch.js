window.loadNews = function() {
    fetch('api/news.php')
    .then(r => r.json())
    .then(res => {
        const tbody = document.getElementById('newsTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        if (res.data && res.data.length > 0) {
            res.data.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.judul}</td>
                    <td><span class="badge bg-info">${item.nama_kategori || 'Umum'}</span></td>
                    <td><span class="badge bg-success">${item.status}</span></td>
                    <td>${item.views || 0}</td>
                    <td>${item.tanggal_publish}</td>
                    <td>
                        <div class="btn-group">
                            <a href="news-edit.html?id=${item.id_berita}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="hapusBerita(${item.id_berita})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            // Update info jumlah berita di footer tabel
            const info = document.querySelector('.card-footer');
            if(info) info.innerHTML = `Menampilkan ${res.data.length} berita.`;
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Belum ada berita.</td></tr>';
        }
    });
};

window.hapusBerita = function(id) {
    if (confirm('Yakin ingin menghapus berita ini?')) {
        fetch(`api/news-delete.php?id=${id}`)
        .then(r => r.json())
        .then(res => {
            if(res.success) { alert("Berhasil dihapus"); window.loadNews(); }
        });
    }
};

document.addEventListener('DOMContentLoaded', window.loadNews);
