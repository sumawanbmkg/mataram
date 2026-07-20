# Stasiun Geofisika Mataram — BMKG NTB

> **Website:** [http://10.21.224.146](http://10.21.224.146)  
> **Domain:** `geofisika-mataram.bmkg.go.id`  
> **Lokasi:** Jl. Adisucipto No. 10, Rembiga, Kota Mataram, NTB 83115  
> **Kontak:** (0370) 7503527 | stageof.mataram@bmkg.go.id

---

## 📋 Daftar Halaman

| Halaman | URL | Deskripsi |
|---|---|---|
| **Beranda** | `/index.html` | Halaman utama dengan hero, berita, status layanan |
| **Berita** | `/berita.html` | Daftar berita & artikel |
| **Detail Berita** | `/detail-berita.html?id=N` | Halaman detail berita |
| **Gempa Bumi** | `/gempabumi.html` | Data gempa bumi terkini (sync BMKG), filter, export CSV |
| **WRS Monitoring** | `/monitoring-gempa.html` | Peta interaktif gempa real-time InaTEWS, auto-refresh 10 menit |
| **Tsunami** | `/tsunami.html` | Informasi peringatan tsunami |
| **Magnet Bumi** | `/magnet-bumi.html` | Data magnet bumi |
| **Cuaca NTB** | `/cuaca-ntb.html` | Prakiraan cuaca NTB (sync BMKG) |
| **Tanda Waktu** | `/tanda-waktu.html` | Informasi tanda waktu BMKG |
| **Kontak** | `/kontak.html` | Halaman kontak, form, peta lokasi |
| **FAQ** | `/faq.html` | Pertanyaan umum |
| **Mitigasi** | `/mitigasi.html` | Informasi mitigasi bencana |
| **Petir** | `/petir.html` | Informasi petir |

## 🔧 Admin CMS (KHK)

| Halaman | URL |
|---|---|
| Login | `/khk/pintu-masuk-rahasia.html` |
| Dashboard | `/khk/index.php` |
| Kelola Berita | `/khk/news.html` |
| Tambah Berita | `/khk/news-create.html` |
| Edit Berita | `/khk/news-edit.html` |

**Kredensial Admin:**
- Username: `superadmin`
- Password: `BmkgAdmin2026!`

## 🌐 API Endpoints

| Endpoint | Deskripsi |
|---|---|
| `api/gempabumi_proxy.php?type=terkini` | Proxy gempa BMKG (terkini/terbaru/dirasakan) |
| `api/inatews_proxy.php?mode=all` | Proxy gempa real-time InaTEWS WRS (all/ntb) |
| `api/cuaca_proxy.php?mode=all` | Data cuaca NTB (all/kabupaten/kota/provinsi) |
| `api/manage_news.php` | CRUD berita (JWT auth) |
| `api/auth_middleware.php` | Middleware JWT authentication |

## 🗄️ Teknologi

- **Frontend:** HTML, Tailwind CSS, JavaScript (Vanilla), Leaflet.js
- **Backend:** PHP 8.x, MySQL/MariaDB
- **Design:** Inter Font, Material Symbols, Dark Mode, Glassmorphism
- **Server:** nginx, Debian 11
- **Data Source:** BMKG API, InaTEWS WRS GeoJSON, Stasiun Geofisika Mataram

## 🚀 Struktur Folder

```
├── .htaccess                    # Konfigurasi nginx security & caching
├── index.html                   # Halaman utama
├── berita.html                  # Daftar berita
├── detail-berita.html           # Detail berita
├── gempabumi.html               # Gempa bumi
├── gempabumi.js                 # JS gempa bumi
├── monitoring-gempa.html        # WRS InaTEWS map
├── cuaca-ntb.html               # Cuaca NTB
├── cuaca.js                     # JS cuaca
├── tsunami.html                 # Tsunami
├── magnet-bumi.html             # Magnet bumi
├── tanda-waktu.html             # Tanda waktu
├── kontak.html                  # Kontak
├── faq.html                     # FAQ
├── 404.html                     # 404
├── mitigasi.html                # Mitigasi
├── petir.html                   # Petir
├── styles.css                   # CSS global
├── api/                         # API proxy endpoints
│   ├── auth_middleware.php
│   ├── gempabumi_proxy.php
│   ├── inatews_proxy.php
│   ├── cuaca_proxy.php
│   └── manage_news.php
├── khk/                         # Admin CMS
│   ├── pintu-masuk-rahasia.html
│   ├── index.php
│   ├── news.html
│   ├── news-create.html
│   ├── news-edit.html
│   ├── dashboard.html
│   └── api/
├── docs/                        # Dokumentasi
├── backups/                     # Backup file
└── images/news/                 # Upload gambar berita
```

## 📄 Lisensi

Hak cipta © Stasiun Geofisika Mataram — Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)
