# Stasiun Geofisika Mataram — Dokumentasi Link & Struktur File

> Server: `vmhosting@10.21.224.146`  
> Web Root: `/var/www/webmataram/`  
> Domain: `http://10.21.224.146/`  
> Dibuat: 2026-07-19

---

## Daftar Semua Halaman HTML

| File | URL | Deskripsi |
|---|---|---|
| `index.html` | `/` atau `/index.html` | Halaman utama — beranda, hero, berita, status layanan |
| `berita.html` | `/berita.html` | Daftar berita dan artikel |
| `detail-berita.html` | `/detail-berita.html?id=N` | Halaman detail berita (query param `id`) |
| `gempabumi.html` | `/gempabumi.html` | Data gempa bumi terkini (sync BMKG) + filter, export CSV |
| `monitoring-gempa.html` | `/monitoring-gempa.html` | Peta interaktif WRS InaTEWS — real-time earthquake monitoring |
| `tsunami.html` | `/tsunami.html` | Informasi tsunami |
| `magnet-bumi.html` | `/magnet-bumi.html` | Data magnet bumi |
| `cuaca-ntb.html` | `/cuaca-ntb.html` | Prakiraan cuaca NTB (sync BMKG) |
| `tanda-waktu.html` | `/tanda-waktu.html` | Informasi tanda waktu BMKG |
| `kontak.html` | `/kontak.html` | Halaman kontak, form, peta lokasi |
| `faq.html` | `/faq.html` | Pertanyaan umum (FAQ) |
| `404.html` | `/404.html` | Halaman not found (custom 404) |
| `mitigasi.html` | `/mitigasi.html` | Informasi mitigasi bencana |
| `petir.html` | `/petir.html` | Informasi petir |

---

## Navigasi — Link per Menu

### Desktop Nav (header)

| Menu | Link | Keterangan |
|---|---|---|
| Beranda | `/#beranda` | Scroll ke section hero |
| Berita | `/berita.html` | ✅ OK |
| Gempa Bumi | `/gempabumi.html` | ✅ OK |
| Tsunami | `/tsunami.html` | ✅ OK (lokal) |
| Magnet Bumi | `/magnet-bumi.html` | ✅ OK (lokal) |
| Tanda Waktu | `/tanda-waktu.html` | ✅ OK |
| Cuaca NTB | `/cuaca-ntb.html` | ✅ OK |
| WRS | `/monitoring-gempa.html` | ✅ OK |
| Kontak | `/kontak.html` | ✅ OK |

### Mobile Nav (hamburger menu)

Sama dengan desktop nav (link sama).

### Hero Section CTA Buttons

| Tombol | Link | Keterangan |
|---|---|---|
| Lihat Data Gempa Bumi | `/gempabumi.html` | ✅ OK |
| Status Tsunami | `https://tsunami.bmkg.go.id` | Eksternal — langsung ke portal tsunami BMKG |

### Footer — Layanan Geofisika BMKG

| Link | Tujuan | Keterangan |
|---|---|---|
| Gempa Bumi | `/gempabumi.html` | ✅ OK |
| Prakiraan Cuaca | `/cuaca-ntb.html` | ✅ OK |
| Tsunami | `/tsunami.html` | ✅ OK |
| WRS Monitoring | `/monitoring-gempa.html` | ✅ OK |
| Magnet Bumi | `/magnet-bumi.html` | ✅ OK |

### Footer — Link BMKG Eksternal

| Link | URL | Keterangan |
|---|---|---|
| BMKG Pusat | `https://www.bmkg.go.id` | ✅ OK |
| Data Terbuka BMKG | `https://data.bmkg.go.id` | ✅ OK |
| Prakiraan Cuaca Seluruh Indonesia | `https://www.bmkg.go.id/cuaca/prakiraan-cuaca` | ✅ OK |
| Twitter/X | `https://twitter.com/stageof_mataram` | ✅ OK |
| Instagram | `https://instagram.com/infogempa_ntb` | ✅ OK |
| YouTube | `https://youtube.com/@stasiungeofisikamataram7031` | ✅ OK |
| Facebook | `https://facebook.com/stasiungeofisika.mataram` | ✅ OK |
| YouTube Playlist Edukasi | `https://www.youtube.com/playlist?list=PL-k2VTQGAjdfzYGzV8Bp-GDgJUvb6eqbd` | ✅ OK |

---

## API Endpoints

| File | URL | Deskripsi |
|---|---|---|
| `api/auth_middleware.php` | — | Middleware JWT auth untuk public API |
| `api/cuaca_proxy.php` | `/api/cuaca_proxy.php?mode=all` | Data cuaca NTB (mode: `all`, `kabupaten`, `kota`, `provinsi`) |
| `api/gempabumi_proxy.php` | `/api/gempabumi_proxy.php?type=terkini` | Proxy data gempa BMKG (type: `terkini`, `terbaru`, `dirasakan`) |
| `api/inatews_proxy.php` | `/api/inatews_proxy.php?mode=all` | Proxy data real-time InaTEWS WRS (mode: `all`, `ntb`) |
| `api/manage_news.php` | — | CRUD berita via public API |

## Admin CMS (KHK)

| Halaman | URL |
|---|---|
| Login | `/khk/pintu-masuk-rahasia.html` |
| Dashboard | `/khk/index.php` |
| Edit Berita | `/khk/news-edit.html?id=N` |

**Kredensial Admin:**
- Username: `superadmin`
- Password: `BmkgAdmin2026!`

## File-file Pendukung

| File | Lokasi |
|---|---|
| `.htaccess` | `/var/www/webmataram/.htaccess` |
| `styles.css` | `/var/www/webmataram/styles.css` |
| `gempabumi.js` | `/var/www/webmataram/gempabumi.js` |
| `cuaca.js` | `/var/www/webmataram/cuaca.js` |
| Folder gambar | `/var/www/webmataram/images/news/` → symlink ke `/home/vmhosting/vm_upload/news/` |

## Struktur Folder

```
/var/www/webmataram/
├── index.html                 # Halaman utama
├── berita.html                # Daftar berita
├── detail-berita.html         # Detail berita
├── gempabumi.html             # Gempa bumi (sync BMKG)
├── monitoring-gempa.html      # WRS InaTEWS map
├── tsunami.html               # Tsunami
├── magnet-bumi.html           # Magnet bumi
├── cuaca-ntb.html             # Cuaca NTB
├── tanda-waktu.html           # Tanda waktu
├── kontak.html                # Kontak
├── faq.html                   # FAQ
├── 404.html                   # 404
├── mitigasi.html              # Mitigasi
├── petir.html                 # Petir
├── styles.css                 # CSS global
├── gempabumi.js               # JS gempa bumi
├── cuaca.js                   # JS cuaca NTB
├── .htaccess                  # Konfigurasi Apache/nginx
├── api/
│   ├── auth_middleware.php
│   ├── cuaca_proxy.php
│   ├── gempabumi_proxy.php
│   ├── inatews_proxy.php
│   └── manage_news.php
├── khk/                       # Admin CMS
│   ├── pintu-masuk-rahasia.html
│   ├── index.php
│   ├── news-edit.html
│   └── api/
└── images/news/ → symlink to /home/vmhosting/vm_upload/news/
```
