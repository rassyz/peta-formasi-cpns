# Peta Formasi

Micro-SaaS yang membantu peserta CPNS memilih formasi yang syaratnya benar-benar mereka penuhi dan peluangnya paling masuk akal. Pencarian formasi gratis untuk semua orang — yang dibuka dengan pass sekali bayar per musim adalah isi detailnya: kualifikasi lengkap, gaji bersumber, riwayat pelamar, checklist berkas, dan cek kelayakan personal.

## Fitur utama

- **Finder Formasi** — cari formasi berdasarkan jurusan atau nama formasi (jabatan, penempatan, instansi), dengan saringan musim, instansi, wilayah pusat/daerah, jenis kebutuhan, dan formasi 0 pelamar; urutan berdasarkan peluang, gaji, atau kuota. Ada halaman khusus per jurusan.
- **Cek Kelayakan** — profil peserta (tanggal lahir, IPK, dokumen ijazah/SKL, akreditasi prodi, jenis kebutuhan) dinilai oleh mesin aturan R-01–R-08 terhadap tanggal acuan musim dan syarat tiap formasi. Hasilnya berstatus _Lolos Syarat_, _Tidak Memenuhi_ (dengan alasan per aturan), atau _Perlu Dicek_, lengkap dengan saringan "hanya yang lolos syarat saya". Khusus pemegang Pass Strategi.
- **Detail Formasi** — kualifikasi pendidikan, syarat kunci dan syarat khusus sesuai pengumuman, rentang gaji beserta sumber dan tanggal verifikasinya, riwayat snapshot pelamar (jumlah MS) dengan meter dan grafik peluang, serta checklist berkas yang diwarisi dari default instansi dan bisa dioverride per formasi.
- **Toolkit Berkas** — kompres PDF dan foto 100% di browser (file tidak pernah diunggah ke server): preset target 200 KB / 500 KB / 1 MB atau nilai khusus, mode pas foto 4×6, dan preset yang bisa terisi otomatis dari tautan pada checklist berkas.
- **Pass & Kode Akses** — tiga produk sekali bayar: Pass Peta Historis (Rp10.000), Pass Strategi (Rp19.000, early bird Rp15.000), dan Bundle Lolos (Rp39.000, termasuk paket latihan SKD). Dijual via Lynk.id dalam bentuk kode akses unik — satu kode untuk satu akun — yang di-redeem peserta; masa berlaku pass mengikuti musimnya.
- **Panel Admin** — dashboard ringkasan, kelola musim (siklus dibuat → aktif → historis → arsip, maksimal satu musim aktif), impor formasi dari spreadsheet dengan checksum total kuota dan deteksi duplikat, kelola formasi (terbit/tarik, kartu contoh maks. 3 per jurusan, input snapshot pelamar, pencarian), generator kode akses per batch, dan daftar peserta read-only untuk keperluan dukungan pembeli.
- **Request Formasi** — pengunjung (termasuk tamu) dapat meminta formasi atau instansi yang belum tersedia.

## Tech stack

| Lapisan             | Teknologi                         |
| ------------------- | --------------------------------- |
| Backend             | PHP, Laravel 13                   |
| Interaktivitas      | Livewire, Alpine.js               |
| Frontend            | Tailwind CSS v4, Vite, font Inter |
| Database            | MariaDB 10.11                     |
| Pemrosesan berkas   | pdf.js + pdf-lib (client-side)    |
| Infrastruktur lokal | Docker Compose                    |

## Menjalankan lokal

Prasyarat: Docker terpasang dan domain lokal `peta-formasi.test` mengarah ke mesin ini (tambahkan `127.0.0.1 peta-formasi.test` ke file hosts bila belum).

```bash
# 1. Bangun dan jalankan container (php + db)
docker compose up -d --build

# 2. Migrasi + data awal (admin, musim CPNS 2024, data contoh BPK)
docker compose exec php php artisan migrate --seed

# 3. Akun peserta contoh (opsional, untuk pengujian pass)
docker compose exec php php artisan db:seed --class=PesertaSeeder

# 4. Build aset frontend — wajib setelah ada perubahan Blade/CSS/JS
docker compose exec php npm run build
```

Buka `https://peta-formasi.test`. Nama database adalah `petaformasi`; kredensial database diatur lewat `.env` di root proyek (satu sumber untuk container php dan db).

## Akun uji

Semua akun di bawah memakai kata sandi `password` dan **hanya untuk pengembangan lokal**.

| Akun                      | Peran                                      |
| ------------------------- | ------------------------------------------ |
| `admin@peta-formasi.test` | Admin — akses panel `/admin`               |
| `budi@peta-formasi.test`  | Peserta contoh (PesertaSeeder, tanpa pass) |
| `sari@peta-formasi.test`  | Peserta contoh (PesertaSeeder, tanpa pass) |
| `andi@peta-formasi.test`  | Peserta contoh (PesertaSeeder, tanpa pass) |

Kode akses contoh musim CPNS 2024 dari seeder: `HIST-2024-DEMO1` dan `HIST-2024-DEMO2` (tipe Historis, belum dipakai). Redeem lewat halaman `/akun/redeem` setelah masuk.

## Struktur proyek

```
peta-formasi/
├── docker-compose.yml        # layanan php & db
├── src/                      # aplikasi Laravel
│   ├── app/
│   │   ├── Http/Controllers/ # beranda, detail formasi, akun, admin
│   │   ├── Livewire/         # Finder, redeem, komponen admin
│   │   ├── Models/           # Musim, Formasi, Jurusan, KodeAkses, ...
│   │   └── Services/         # CekKelayakan (mesin aturan R-01–R-08)
│   ├── database/             # migrasi & seeder
│   └── resources/            # view Blade, CSS (token Tailwind v4), toolkit.js
└── db/data/                  # volume data MariaDB (tidak masuk git)
```

## Catatan data uji

Data yang terpasang dari seeder dan file impor adalah data pengujian berbasis pengumuman CPNS BPK 2024: subset contoh untuk pengembangan (15 formasi) ditambah 28 baris hasil ekstraksi Lampiran 1 pengumuman resmi yang total kuotanya terverifikasi lewat checksum (154). Sebagian angka pada data contoh pengembangan — misalnya satu rentang gaji — adalah penanda tempat (placeholder) dan bukan angka resmi; data produksi dikurasi ulang per musim sebelum diterbitkan.
