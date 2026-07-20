class AdminPanel {
    constructor() {
        this.imagePath = "../images/news/";
        this.placeholder = "../images/placeholder-news.jpg";
        this.init();
    }

    async init() {
        await this.loadDashboardStats();
        this.setupEventListeners();
        this.showSection('dashboard');
    }

    setupEventListeners() {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.showSection(e.currentTarget.getAttribute('href').substring(1));
            });
        });
    }

    showSection(name) {
        document.querySelectorAll('.content-section').forEach(s => s.classList.add('hidden'));
        document.getElementById(name)?.classList.remove('hidden');
        this.loadSectionData(name);
    }

    async loadSectionData(name) {
        if (name === 'news') this.loadNewsTable();
        if (name === 'dashboard') this.loadDashboardStats();
    }

    async loadDashboardStats() {
        try {
            const res = await fetch('../api/manage_news.php?action=stats');
            const result = await res.json();
            if (result.success) {
                document.getElementById('totalNews').textContent = result.data.total_news;
                document.getElementById('totalViews').textContent = result.data.total_views;
            }
        } catch (e) { console.error(e); }
    }

    async loadNewsTable() {
        const res = await fetch('../api/manage_news.php?action=list');
        const result = await res.json();
        const container = document.getElementById('newsTable');
        if (result.success && container) {
            container.innerHTML = result.data.map(n => `
                <tr class="border-b">
                    <td class="p-3">${n.judul}</td>
                    <td class="p-3">${n.kategori}</td>
                    <td class="p-3">
                        <button onclick="editNews(${n.id_berita})" class="text-blue-600 mr-2"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteNews(${n.id_berita})" class="text-red-600"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    }
}

// FUNGSI EDIT BERITA (DENGAN PERBAIKAN GAMBAR)
async function editNews(id) {
    try {
        const res = await fetch(`../api/manage_news.php?action=detail&id=${id}`);
        const result = await res.json();
        
        if (result.success) {
            const data = result.data;
            
            // Isi form input
            document.getElementById('newsId').value = data.id_berita;
            document.getElementById('newsTitle').value = data.judul;
            document.getElementById('newsContent').value = data.isi_berita || data.konten;
            document.getElementById('newsCategory').value = data.id_kategori;
            document.getElementById('newsStatus').value = data.status;
            
            // PERBAIKAN GAMBAR: Tampilkan gambar yang sudah ada
            const previewImg = document.getElementById('imagePreviewEdit'); // ID di HTML Anda
            const currentImgPath = document.getElementById('currentImagePath'); // Field tersembunyi
            
            if (previewImg) {
                const imgName = data.gambar_utama || data.gambar;
                previewImg.src = imgName ? `../images/news/${imgName}` : `../images/placeholder-news.jpg`;
                previewImg.classList.remove('hidden');
            }
            
            if (currentImgPath) {
                currentImgPath.value = data.gambar_utama || data.gambar;
            }

            // Buka Modal Edit (Asumsi Anda punya fungsi ini atau elemen modal)
            document.getElementById('newsModal')?.classList.remove('hidden');
        }
    } catch (e) {
        alert("Gagal mengambil data berita");
    }
}

function closeModal() {
    document.getElementById('newsModal')?.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => { window.adminPanel = new AdminPanel(); });
