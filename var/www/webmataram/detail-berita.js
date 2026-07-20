/**
 * detail-berita.js - FULL VERSION
 * Stasiun Geofisika Mataram
 * Deskripsi: Mengelola konten utama, berita terkait, dan berita populer.
 */

class DetailBerita {
    constructor() {
        // 1. Ambil Slug dari URL
        const urlParams = new URLSearchParams(window.location.search);
        this.newsSlug = urlParams.get('slug');
        
        // 2. Konfigurasi Global
        this.imagePath = "images/news/";
        this.placeholder = "https://placehold.co/800x450/1e3a8a/ffffff?text=BMKG+Mataram";
        this.apiEndpoint = "api/get_news.php";
        
        this.newsData = null;
        this.init();
    }

    async init() {
        if (!this.newsSlug) {
            this.showError("Slug berita tidak ditemukan.");
            return;
        }

        console.log("🚀 Memulai pemuatan data untuk:", this.newsSlug);

        try {
            // Muat Data Utama Berita
            await this.loadNewsDetail();
            
            // Jika data utama berhasil, muat Sidebar (Terkait & Populer) secara paralel
            if (this.newsData) {
                await Promise.all([
                    this.loadRelatedNews(),
                    this.loadPopularNews()
                ]);
            }
            
            this.setupEventListeners();
        } catch (error) {
            console.error('🚨 Gagal inisialisasi halaman:', error);
            this.showError();
        }
    }

    // --- KONTEN UTAMA ---
    async loadNewsDetail() {
        const response = await fetch(`${this.apiEndpoint}?slug=${encodeURIComponent(this.newsSlug)}`);
        if (!response.ok) throw new Error("Gagal terhubung ke API");
        
        const result = await response.json();
        if (result.success && result.data) {
            this.newsData = result.data;
            this.renderNewsDetail();
        } else {
            throw new Error(result.message || "Berita tidak ditemukan");
        }
    }

    renderNewsDetail() {
        const data = this.newsData;

        // Fungsi bantu update teks/HTML
        const update = (id, val, isHTML = false) => {
            const el = document.getElementById(id);
            if (el) isHTML ? el.innerHTML = val || '' : el.textContent = val || '';
        };

        update('articleTitle', data.judul);
        update('articleCategory', data.kategori || 'Berita');
        update('articleDate', this.formatDate(data.tanggal_publish));
        update('articleAuthor', data.penulis || 'Admin BMKG');
        update('articleViews', (data.views || 0).toLocaleString());
        
        // Render Isi Berita
        const content = data.konten || data.isi_berita || '<p>Konten kosong.</p>';
        update('articleBody', content, true);

        // Render Gambar Utama
        const imgEl = document.getElementById('articleImage');
        if (imgEl) {
            imgEl.src = data.gambar_utama ? `${this.imagePath}${data.gambar_utama}` : this.placeholder;
            imgEl.onerror = () => { imgEl.src = this.placeholder; };
        }

        // Munculkan Artikel & Sembunyikan Loading
        document.getElementById('articleContent')?.classList.remove('hidden');
        document.getElementById('loadingState')?.classList.add('hidden');
        document.title = `${data.judul} - Stageof Mataram`;
    }

    // --- BERITA TERKAIT (SIDEBAR) ---
    async loadRelatedNews() {
        try {
            // Ambil berdasarkan kategori yang sama
            const category = this.newsData.slug_kategori || this.newsData.kategori;
            const response = await fetch(`${this.apiEndpoint}?category=${encodeURIComponent(category)}&limit=4`);
            const result = await response.json();
            
            if (result.success) {
                // Jangan tampilkan berita yang sedang dibaca
                const filtered = result.data.filter(item => item.slug !== this.newsSlug);
                this.renderRelatedNews(filtered);
            }
        } catch (e) { console.warn("Gagal muat berita terkait"); }
    }

    renderRelatedNews(news) {
        const container = document.getElementById('relatedNews');
        if (!container) return;

        if (news.length === 0) {
            container.innerHTML = '<p class="text-xs italic text-slate-400">Belum ada berita terkait lainnya.</p>';
            return;
        }

        container.innerHTML = news.map(item => `
            <a href="detail-berita.html?slug=${item.slug}" class="flex gap-3 group mb-4 border-b border-slate-50 pb-3 last:border-0">
                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden">
                    <img src="${this.imagePath}${item.gambar_utama}" 
                         onerror="this.src='${this.placeholder}'"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                </div>
                <div>
                    <h4 class="text-sm font-bold leading-snug group-hover:text-blue-600 line-clamp-2">${item.judul}</h4>
                    <span class="text-[10px] text-slate-400 uppercase mt-1 block">${this.formatDate(item.tanggal_publish)}</span>
                </div>
            </a>
        `).join('');
    }

    // --- TERPOPULER (SIDEBAR) ---
    async loadPopularNews() {
        try {
            const response = await fetch(`${this.apiEndpoint}?sort=popular&limit=5`);
            const result = await response.json();
            if (result.success) this.renderPopularNews(result.data);
        } catch (e) { console.warn("Gagal muat berita populer"); }
    }

    renderPopularNews(news) {
        const container = document.getElementById('popularNews');
        if (!container) return;

        container.innerHTML = news.map((item, index) => `
            <a href="detail-berita.html?slug=${item.slug}" class="flex items-start gap-4 group mb-4">
                <div class="text-2xl font-black text-slate-200 group-hover:text-blue-500 transition-colors italic">0${index + 1}</div>
                <div>
                    <h4 class="text-sm font-bold leading-tight group-hover:text-blue-600 line-clamp-2">${item.judul}</h4>
                    <p class="text-[9px] text-slate-400 mt-1 uppercase">${(item.views || 0).toLocaleString()} Pembaca</p>
                </div>
            </a>
        `).join('');
    }

    // --- FUNGSI PENDUKUNG ---
    setupEventListeners() {
        // Bisa diisi fungsi Share atau Komentar nanti
        console.log("✅ Event listeners siap.");
    }

    formatDate(dateStr) {
        if (!dateStr) return "-";
        try {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateStr).toLocaleDateString('id-ID', options);
        } catch (e) { return dateStr; }
    }

    showError(msg = "Berita tidak ditemukan.") {
        document.getElementById('loadingState')?.classList.add('hidden');
        const errArea = document.getElementById('errorState');
        if (errArea) {
            errArea.classList.remove('hidden');
            const p = errArea.querySelector('p');
            if (p) p.textContent = msg;
        }
    }
}

// Jalankan saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    window.detailApp = new DetailBerita();
});
