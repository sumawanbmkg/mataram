// Berita Management System - Reads from MariaDB Database
class BeritaManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.currentCategory = "";
        this.currentSearch = "";
        this.sortOrder = "newest";
        this.allNews = [];
        this.init();
    }

    async init() {
        await this.loadNews();
        await this.loadFeaturedNews();
        this.setupEventListeners();
    }

    setupEventListeners() {
        let searchTimeout;
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.currentSearch = e.target.value;
                    this.currentPage = 1;
                    this.loadNews();
                }, 500);
            });
        }

        const categoryFilter = document.getElementById("categoryFilter");
        if (categoryFilter) {
            categoryFilter.addEventListener("change", (e) => {
                this.currentCategory = e.target.value;
                this.currentPage = 1;
                this.loadNews();
            });
        }

        const sortOrder = document.getElementById("sortOrder");
        if (sortOrder) {
            sortOrder.addEventListener("change", (e) => {
                this.sortOrder = e.target.value;
                this.currentPage = 1;
                this.loadNews();
            });
        }
    }

    async loadNews() {
        this.showLoading(true);
        try {
            const news = await this.fetchNewsFromAPI();
            this.allNews = news;
            this.renderNews(news);
            this.updateLoadMoreButton(news.length);
        } catch (error) {
            console.error("Error loading news:", error);
            this.showError("Gagal memuat berita dari database.");
        } finally {
            this.showLoading(false);
        }
    }

    async loadFeaturedNews() {
        try {
            const featuredNews = await this.fetchFeaturedNewsFromAPI();
            this.renderFeaturedNews(featuredNews);
        } catch (error) {
            console.error("Error loading featured news:", error);
        }
    }

    async fetchNewsFromAPI() {
        const params = new URLSearchParams({
            page: this.currentPage,
            limit: this.itemsPerPage,
            kategori: this.currentCategory,
            search: this.currentSearch,
            sort: this.sortOrder
        });

        try {
            const response = await fetch("api/get_news.php?" + params.toString());
            if (!response.ok) {
                throw new Error("API response not ok: " + response.status);
            }
            const data = await response.json();
            if (data.success && data.data) {
                return data.data;
            }
            return [];
        } catch (error) {
            console.error("Error fetching news:", error);
            return [];
        }
    }

    async fetchFeaturedNewsFromAPI() {
        try {
            const response = await fetch("api/get_news.php?featured=true&limit=2");
            if (!response.ok) {
                throw new Error("API response not ok: " + response.status);
            }
            const data = await response.json();
            if (data.success && data.data) {
                return data.data;
            }
            return [];
        } catch (error) {
            console.error("Error fetching featured news:", error);
            return [];
        }
    }

    renderFeaturedNews(news) {
        const container = document.getElementById("featuredNews");
        if (!container) return;

        if (!news || news.length === 0) {
            container.innerHTML = "<p class=\"text-center text-gray-500 dark:text-slate-400 col-span-full py-8\">Tidak ada berita utama</p>";
            return;
        }

        container.innerHTML = news.map(item => {
            const imageUrl = item.gambar_utama || 'images/placeholder-news.jpg';
            const category = item.kategori || 'Berita';
            const author = item.penulis || 'Admin';
            const views = item.views || 0;
            const title = item.judul || 'Tanpa Judul';
            const summary = item.ringkasan || '';
            const slug = item.slug || '#';
            const date = item.tanggal_publish ? this.formatDate(item.tanggal_publish) : '';

            return '<article class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"><div class="relative"><img src="' + imageUrl + '" alt="' + title + '" class="w-full h-64 object-cover" onerror="this.src=\'images/placeholder-news.jpg\'"><div class="absolute top-4 left-4"><span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">UTAMA</span></div></div><div class="p-6"><div class="flex items-center text-sm text-gray-500 dark:text-slate-400 mb-2"><span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mr-2">' + category + '</span><span>' + date + '</span><span class="mx-2">•</span><span>' + views.toLocaleString() + ' views</span></div><h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 line-clamp-2"><a href="detail-berita.html?slug=' + slug + '" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">' + title + '</a></h3><p class="text-gray-600 dark:text-slate-400 mb-4 line-clamp-3">' + summary + '</p><div class="flex items-center justify-between"><span class="text-sm text-gray-500 dark:text-slate-400">Oleh: ' + author + '</span><a href="detail-berita.html?slug=' + slug + '" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm flex items-center gap-1">Baca Selanjutkan <span class="material-symbols-outlined text-sm">arrow_forward</span></a></div></div></article>';
        }).join("");
    }

    renderNews(news) {
        const container = document.getElementById("newsList");
        if (!container) return;

        if (!news || news.length === 0) {
            container.innerHTML = "<p class=\"text-center text-gray-500 dark:text-slate-400 col-span-full py-8\">Tidak ada berita yang ditemukan</p>";
            return;
        }

        container.innerHTML = news.map(item => this.createNewsCard(item)).join("");
    }

    createNewsCard(item) {
        const imageUrl = item.gambar_utama || 'images/placeholder-news.jpg';
        const category = item.kategori || 'Berita';
        const author = item.penulis || 'Admin';
        const views = item.views || 0;
        const title = item.judul || 'Tanpa Judul';
        const summary = item.ringkasan || '';
        const slug = item.slug || '#';
        const date = item.tanggal_publish ? this.formatDate(item.tanggal_publish) : '';

        return '<article class="bg-white dark:bg-slate-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow"><div class="relative"><img src="' + imageUrl + '" alt="' + title + '" class="w-full h-48 object-cover" onerror="this.src=\'images/placeholder-news.jpg\'"><div class="absolute top-3 left-3"><span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">' + category + '</span></div></div><div class="p-4"><div class="flex items-center text-xs text-gray-500 dark:text-slate-400 mb-2"><span class="material-symbols-outlined text-sm mr-1">calendar_today</span><span>' + date + '</span><span class="mx-2">•</span><span class="material-symbols-outlined text-sm mr-1">visibility</span><span>' + views.toLocaleString() + '</span></div><h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 line-clamp-2"><a href="detail-berita.html?slug=' + slug + '" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">' + title + '</a></h3><p class="text-gray-600 dark:text-slate-400 text-sm mb-3 line-clamp-2">' + summary + '</p><div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-slate-700"><span class="text-xs text-gray-500 dark:text-slate-400">' + author + '</span><a href="detail-berita.html?slug=' + slug + '" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm flex items-center gap-1">Baca <span class="material-symbols-outlined text-sm">arrow_forward</span></a></div></div></article>';
    }

    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString("id-ID", {
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        } catch (e) {
            return dateString;
        }
    }

    showLoading(show) {
        const indicator = document.getElementById("loadingIndicator");
        if (indicator) {
            if (show) {
                indicator.classList.remove("hidden");
            } else {
                indicator.classList.add("hidden");
            }
        }
    }

    showError(message) {
        const container = document.getElementById("newsList");
        if (container) {
            container.innerHTML = "<div class=\"col-span-full text-center py-8\"><div class=\"bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6\"><span class=\"material-symbols-outlined text-red-500 text-4xl mb-2\">error</span><p class=\"text-red-600 dark:text-red-400 mb-4\">" + message + "</p><button onclick=\"location.reload()\" class=\"bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 mx-auto\"><span class=\"material-symbols-outlined\">refresh</span> Coba Lagi</button></div></div>";
        }
    }

    updateLoadMoreButton(newsCount) {
        const button = document.getElementById("loadMoreBtn");
        if (button) {
            if (newsCount < this.itemsPerPage) {
                button.style.display = "none";
            } else {
                button.style.display = "inline-flex";
            }
        }
    }

    loadMoreNews() {
        this.currentPage++;
        this.loadNews();
    }
}

// Global functions
function searchNews() {
    if (window.beritaManager && document.getElementById("searchInput")) {
        beritaManager.currentSearch = document.getElementById("searchInput").value;
        beritaManager.currentPage = 1;
        beritaManager.loadNews();
    }
}

function loadNews() {
    if (window.beritaManager) {
        beritaManager.loadNews();
    }
}

function loadMoreNews() {
    if (window.beritaManager) {
        beritaManager.loadMoreNews();
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    window.beritaManager = new BeritaManager();
});
