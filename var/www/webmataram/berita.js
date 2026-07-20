/**
 * Berita Management System - Stasiun Geofisika Mataram
 * Lokasi File: /var/www/webmataram/berita.js
 * Deskripsi: Mengambil data berita dari MariaDB via PHP API
 */

class BeritaManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.currentCategory = "";
        this.currentSearch = "";
        this.sortOrder = "newest";
        this.imagePath = "images/news/"; // Path folder gambar sesuai instruksi Anda
        this.placeholder = "images/placeholder-news.jpg";
        this.init();
    }

    async init() {
        console.log("📰 BeritaManager Initializing...");
        try {
            // Memuat berita utama dan daftar berita secara pararel
            await Promise.all([
                this.loadFeaturedNews(),
                this.loadNews()
            ]);
            this.setupEventListeners();
        } catch (error) {
            console.error("🚨 Init Error:", error);
        }
    }

    setupEventListeners() {
        const bindEvent = (id, event, callback) => {
            const el = document.getElementById(id);
            if (el) el.addEventListener(event, callback);
        };

        // Pencarian dengan Debounce (500ms)
        let searchTimeout;
        bindEvent("searchInput", "input", (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.currentSearch = e.target.value;
                this.currentPage = 1;
                this.loadNews();
            }, 500);
        });

        // Filter Kategori
        bindEvent("categoryFilter", "change", (e) => {
            this.currentCategory = e.target.value;
            this.currentPage = 1;
            this.loadNews();
        });

        // Pengurutan (Sort)
        bindEvent("sortOrder", "change", (e) => {
            this.sortOrder = e.target.value;
            this.currentPage = 1;
            this.loadNews();
        });
    }

    async loadFeaturedNews() {
        try {
            const response = await fetch("api/get_news.php?featured=true&limit=2");
            const data = await response.json();
            if (data.success) {
                this.renderFeaturedNews(data.data);
            }
        } catch (error) {
            console.warn("⚠️ Featured News gagal dimuat:", error);
        }
    }

    async loadNews() {
        this.showLoading(true);
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                kategori: this.currentCategory,
                search: this.currentSearch,
                sort: this.sortOrder
            });

            const response = await fetch("api/get_news.php?" + params.toString());
            if (!response.ok) throw new Error("Gagal mengambil data dari server");
            
            const data = await response.json();
            const newsItems = (data.success && data.data) ? data.data : [];
            
            this.renderNews(newsItems);
            this.updateLoadMoreButton(newsItems.length);
        } catch (error) {
            console.error("🚨 Load News Error:", error);
            this.showError("Gagal memuat berita. Silakan coba lagi.");
        } finally {
            this.showLoading(false);
        }
    }

    renderFeaturedNews(news) {
        const container = document.getElementById("featuredNews");
        if (!container || !news.length) return;

        container.innerHTML = news.map(item => {
            // Gabungkan path folder dengan nama file dari DB
            const fullImageUrl = item.gambar_utama ? `${this.imagePath}${item.gambar_utama}` : this.placeholder;
            
            return `
                <article class="relative group overflow-hidden rounded-2xl bg-slate-800 shadow-lg">
                    <img src="${fullImageUrl}" 
                         alt="${item.judul}" 
                         onerror="this.src='${this.placeholder}'"
                         class="w-full h-80 object-cover opacity-60 group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute bottom-0 p-6 w-full bg-gradient-to-t from-black/90">
                        <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded font-bold mb-2 inline-block tracking-widest">BERITA UTAMA</span>
                        <h2 class="text-xl md:text-2xl font-bold text-white mb-2 leading-tight">
                            <a href="detail-berita.html?slug=${item.slug}" class="hover:text-blue-400 transition-colors">${item.judul}</a>
                        </h2>
                        <p class="text-slate-300 text-sm line-clamp-2">${item.ringkasan || ''}</p>
                    </div>
                </article>
            `;
        }).join("");
    }

    renderNews(news) {
        const container = document.getElementById("newsList");
        if (!container) return;

        // Jika halaman 1, kosongkan container dulu
        if (this.currentPage === 1) container.innerHTML = "";

        if (!news.length && this.currentPage === 1) {
            container.innerHTML = `<div class="col-span-full text-center py-20 text-slate-500">Tidak ada berita ditemukan.</div>`;
            return;
        }

        news.forEach(item => {
            const fullImageUrl = item.gambar_utama ? `${this.imagePath}${item.gambar_utama}` : this.placeholder;
            
            const card = document.createElement('article');
            card.className = "bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-all";
            card.innerHTML = `
                <div class="relative h-48 overflow-hidden">
                    <img src="${fullImageUrl}" 
                         alt="${item.judul}" 
                         onerror="this.src='${this.placeholder}'"
                         class="w-full h-full object-cover">
                    <div class="absolute top-3 left-3">
                        <span class="bg-blue-600 text-white text-[10px] px-2 py-1 rounded font-bold uppercase tracking-wider">
                            ${item.kategori || 'Berita'}
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg mb-2 line-clamp-2 min-h-[3.5rem]">
                        <a href="detail-berita.html?slug=${item.slug}" class="hover:text-blue-600 transition-colors">${item.judul}</a>
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2 mb-4">${item.ringkasan || ''}</p>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest pt-4 border-t dark:border-slate-700">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> ${this.formatDate(item.tanggal_publish)}</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">visibility</span> ${item.views || 0}</span>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    formatDate(dateStr) {
        if (!dateStr) return "";
        try {
            return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        } catch (e) { return dateStr; }
    }

    showLoading(show) {
        const el = document.getElementById("loadingIndicator");
        if (el) show ? el.classList.remove("hidden") : el.classList.add("hidden");
    }

    showError(msg) {
        const container = document.getElementById("newsList");
        if (container) container.innerHTML = `<div class="col-span-full text-center text-red-500 py-10">${msg}</div>`;
    }

    updateLoadMoreButton(count) {
        const btn = document.getElementById("loadMoreBtn");
        if (btn) btn.style.display = (count >= this.itemsPerPage) ? "inline-flex" : "none";
    }
}

// Inisialisasi Saat DOM Siap
document.addEventListener("DOMContentLoaded", () => {
    window.beritaManager = new BeritaManager();
});

// Fungsi Global untuk Tombol Load More
function loadMoreNews() {
    if (window.beritaManager) {
        window.beritaManager.currentPage++;
        window.beritaManager.loadNews();
    }
}
