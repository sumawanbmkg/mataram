/**
 * Cuaca NTB - BMKG Weather Integration
 * Prakiraan cuaca untuk wilayah Nusa Tenggara Barat
 * Sumber: BMKG (https://www.bmkg.go.id/cuaca/prakiraan-cuaca/52)
 */

class CuacaNTB {
    constructor() {
        this.config = {
            proxyUrl: 'api/cuaca_proxy.php',
            updateInterval: 1800000, // 30 menit
        };
        this.state = {
            data: null,
            isLoading: false,
            selectedKab: null,
            lastUpdate: null,
        };
        this.weatherIcons = {
            sunny: '<span class="material-symbols-outlined text-4xl text-amber-400">sunny</span>',
            partly_cloudy: '<span class="material-symbols-outlined text-4xl text-amber-300">partly_cloudy_day</span>',
            cloudy: '<span class="material-symbols-outlined text-4xl text-slate-400">cloud</span>',
            overcast: '<span class="material-symbols-outlined text-4xl text-slate-500">cloud_queue</span>',
            light_rain: '<span class="material-symbols-outlined text-4xl text-blue-400">rainy_light</span>',
            night_cloudy: '<span class="material-symbols-outlined text-4xl text-slate-300">cloudy_night</span>',
            default: '<span class="material-symbols-outlined text-4xl text-slate-400">cloud</span>',
        };
        this.monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        this.init();
    }

    init() {
        this.loadData();
        setInterval(() => this.loadData(), this.config.updateInterval);
    }

    async loadData() {
        if (this.state.isLoading) return;
        this.state.isLoading = true;
        document.getElementById('loading-state')?.classList.remove('hidden');
        document.getElementById('weather-content')?.classList.add('hidden');

        try {
            const res = await fetch(this.config.proxyUrl + '?_=' + Date.now());
            const json = await res.json();
            if (json.success && json.data) {
                this.state.data = json.data;
                this.state.lastUpdate = new Date();
                this.render();
                document.getElementById('error-state')?.classList.add('hidden');
            } else {
                this.showError('Gagal memuat data cuaca');
            }
        } catch(e) {
            console.error('Cuaca fetch error:', e);
            this.showError('Gagal terhubung ke server');
        } finally {
            this.state.isLoading = false;
            document.getElementById('loading-state')?.classList.add('hidden');
            document.getElementById('weather-content')?.classList.remove('hidden');
        }
    }

    showError(msg) {
        const el = document.getElementById('error-state');
        if (el) {
            el.classList.remove('hidden');
            el.querySelector('.error-msg').textContent = msg;
        }
    }

    render() {
        if (!this.state.data) return;
        const d = this.state.data;
        
        // Update header
        document.getElementById('forecast-date').textContent = `${d.hari}, ${d.tanggal}`;
        document.getElementById('data-source').textContent = d.sumber;
        document.getElementById('update-time').textContent = d.pembaruan;

        // Ringkasan
        document.getElementById('suhu-min-val').textContent = d.ringkasan.suhu_min + '°';
        document.getElementById('suhu-max-val').textContent = d.ringkasan.suhu_max + '°';

        // Render cards kabupaten
        this.renderCards(d.kabupaten);

        // Pilih default
        if (!this.state.selectedKab && d.kabupaten.length > 0) {
            this.selectKabupaten(d.kabupaten[0]);
        }
    }

    renderCards(kabupaten) {
        const container = document.getElementById('kabupaten-cards');
        if (!container) return;
        container.innerHTML = kabupaten.map(k => this.cardHTML(k)).join('');

        // Event listeners for cards
        container.querySelectorAll('.kab-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = parseInt(card.dataset.id);
                const kab = kabupaten.find(k => k.id === id);
                if (kab) this.selectKabupaten(kab);
            });
        });
    }

    cardHTML(k) {
        const icon = this.weatherIcons[k.cuaca_icon] || this.weatherIcons.default;
        const isSelected = this.state.selectedKab && this.state.selectedKab.id === k.id;
        const selectedClass = isSelected ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50';
        
        return `
        <div class="kab-card cursor-pointer rounded-xl p-4 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-all ${selectedClass}" data-id="${k.id}">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-slate-900 dark:text-white text-sm">${k.nama}</h4>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">${k.kecamatan}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="icon-wrapper">${icon}</div>
                    <div>
                        <div class="text-lg font-bold text-slate-900 dark:text-white">${k.suhu_min}° / ${k.suhu_max}°</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">${k.cuaca}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-slate-400">Angin</div>
                    <div class="text-sm font-medium text-slate-700 dark:text-slate-300">${k.kecepatan_angin} km/h</div>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                ${k.potensi_hujan > 50 ? 
                    '<span class="text-xs px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full font-medium">Hujan</span>' :
                    k.potensi_hujan > 20 ?
                    '<span class="text-xs px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full font-medium">Berawan</span>' :
                    '<span class="text-xs px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full font-medium">Cerah</span>'
                }
                <span class="text-xs text-slate-400">${k.potensi_hujan}% hujan</span>
            </div>
        </div>`;
    }

    selectKabupaten(kab) {
        this.state.selectedKab = kab;
        
        // Update active card styling
        document.querySelectorAll('.kab-card').forEach(c => {
            c.classList.toggle('ring-2', parseInt(c.dataset.id) === kab.id);
            c.classList.toggle('ring-blue-500', parseInt(c.dataset.id) === kab.id);
            c.classList.toggle('bg-blue-50', parseInt(c.dataset.id) === kab.id);
            c.classList.toggle('dark:bg-blue-900/20', parseInt(c.dataset.id) === kab.id);
            c.classList.toggle('bg-white', parseInt(c.dataset.id) !== kab.id);
        });

        // Detail panel
        const detail = document.getElementById('detail-panel');
        if (!detail) return;

        const icon = this.weatherIcons[kab.cuaca_icon] || this.weatherIcons.default;
        const kotaDetail = this.state.data.kota.find(k => k.id === kab.id);

        detail.innerHTML = `
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">${kab.nama}</h3>
                    <p class="text-slate-500 dark:text-slate-400">Kec. ${kab.kecamatan}</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold text-slate-900 dark:text-white">${kab.suhu_max}°</div>
                    <div class="text-sm text-slate-500">Terasa seperti ${kab.suhu_max - 2}°</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl mb-6">
                <div class="icon-wrapper">${icon}</div>
                <div>
                    <div class="text-lg font-semibold text-slate-900 dark:text-white">${kab.cuaca}</div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">${kab.keterangan}</div>
                </div>
                <div class="ml-auto text-right">
                    <div class="text-xs text-slate-500">Potensi Hujan</div>
                    <div class="text-lg font-bold text-blue-600">${kab.potensi_hujan}%</div>
                </div>
            </div>

            <h4 class="font-semibold text-slate-900 dark:text-white mb-3">Prakiraan Per Periode</h4>
            <div class="grid grid-cols-3 gap-3 mb-6">
                ${kotaDetail ? ['pagi','siang','malam'].map((periode, i) => {
                    const p = kotaDetail[periode];
                    const timeLabel = ['Pagi (07-12)', 'Siang (12-18)', 'Malam (18-06)'][i];
                    const pIcon = this.weatherIcons[p.icon] || this.weatherIcons.default;
                    return `
                    <div class="text-center p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700">
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">${timeLabel}</div>
                        <div class="flex justify-center my-1">${pIcon}</div>
                        <div class="text-sm font-medium text-slate-900 dark:text-white">${p.cuaca}</div>
                        <div class="text-xs text-slate-500">${p.suhu}</div>
                    </div>`;
                }).join('') : ''}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                    <div class="text-xs text-slate-500">Kelembapan</div>
                    <div class="text-lg font-semibold text-slate-900 dark:text-white">${kab.kelembapan_min}% - ${kab.kelembapan_max}%</div>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                    <div class="text-xs text-slate-500">Kecepatan Angin</div>
                    <div class="text-lg font-semibold text-slate-900 dark:text-white">${kab.kecepatan_angin} km/jam</div>
                    <div class="text-xs text-slate-400">Arah: ${kab.arah_angin}</div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                <a href="https://www.bmkg.go.id/cuaca/prakiraan-cuaca/52" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 text-sm hover:underline inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                    Lihat di BMKG.go.id
                </a>
            </div>
        </div>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.cuacaNTB = new CuacaNTB();
});
