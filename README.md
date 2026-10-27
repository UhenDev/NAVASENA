# NAVASENA MANAGEMENT — Sistem Informasi Keuangan Angkatan

**NAVASENA MANAGEMENT** adalah sistem informasi pengelolaan iuran, arus kas, data siswa, dan pertanggungjawaban keuangan angkatan berbasis web yang dirancang khusus untuk memenuhi standar akurasi perhitungan uang, transparansi, keamanan data, dan kemudahan operasional panitia/pengurus angkatan.

Aplikasi ini dibangun menggunakan **PHP Native (PHP 8.2+)**, **MySQL/MariaDB**, **HTML5/Vanilla CSS3**, dan **Vanilla JavaScript** tanpa framework backend besar, tanpa Bootstrap, dan tanpa data dummy fiktif.

---

## 1. SPESIFIKASI DAN ATURAN KEUANGAN

1. **Target Siswa**: &plusmn;492 Siswa Aktif.
2. **Kewajiban Iuran per Siswa**: **Rp250.000**, dengan rincian:
   - Album Fisik Kenangan: Rp200.000
   - Tambahan Acara Graduation: Rp50.000
3. **Target Total Penerimaan**: Rp123.000.000 (492 siswa &times; Rp250.000).
4. **Jadwal 3 Tahap Cicilan Resmi**:
   - **Tahap 1 (DP Pertama)**: Rp100.000 (Jatuh tempo: 1 Desember 2026).
   - **Tahap 2 (Cicilan Kedua)**: Rp100.000 (April/Mei 2027 &mdash; dapat diatur fleksibel di menu Pengaturan).
   - **Tahap 3 (Pelunasan)**: Rp50.000 (Menjelang kelulusan 2027 &mdash; dapat diatur fleksibel).
5. **Invarian & Integritas Finansial**:
   - *Target Total* = Jumlah Siswa Aktif &times; Rp250.000.
   - *Pembayaran Sah* = Hanya transaksi yang telah berstatus **terverifikasi**.
   - *Sisa Tagihan Siswa* = `MAX(0, Kewajiban - Pembayaran Sah)`.
   - Transaksi **Menunggu Verifikasi** **TIDAK PERNAH** dihitung sebagai uang sah dan tidak masuk kas.
   - Transaksi **Ditolak** atau **Dibatalkan** tidak menambah penerimaan dan meninggalkan jejak audit (*audit reversal*).
   - *Saldo Kas Aktual* = Total Kas Masuk Sah &minus; Total Kas Keluar Sah.

---

## 2. PERSYARATAN PERANGKAT LUNAK

- **Web Server**: Apache (XAMPP / standalone) atau PHP Built-in Server.
- **PHP**: Versi 8.1 atau 8.2+ dengan ekstensi `pdo_mysql`, `mbstring`, `session`.
- **Database**: MySQL 8.0+ atau MariaDB 10.4+.
- **Browser Modern**: Chrome, Firefox, Edge, Safari.

---

## 3. PANDUAN INSTALASI & MENJALANKAN DI XAMPP

Proyek ini telah dibuat di:
`C:\Users\HENDRIK WILDANSYAH\.gemini\antigravity-ide\scratch\navasena`
dan telah dibuatkan junction direktori otomatis ke webroot XAMPP di:
`C:\xampp2\htdocs\navasena`

### Langkah 1: Jalankan Apache dan MySQL pada XAMPP Control Panel
Buka XAMPP Control Panel dan pastikan modul **Apache** dan **MySQL** dalam status running (Hijau).

### Langkah 2: Impor Database SQL
Buka terminal / command prompt atau phpMyAdmin di `http://localhost/phpmyadmin`:
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```
*(Atau buka file `database/schema.sql` lalu `database/seed.sql` di phpMyAdmin tab SQL dan jalankan)*.

Database `navasena_keuangan` dan 9 tabel utama beserta 3 tahapan cicilan dan akun awal akan langsung terkonfigurasi.

### Langkah 3: Buka Aplikasi di Browser
Akses melalui URL:
- **Melalui XAMPP**: [http://localhost/navasena/public/](http://localhost/navasena/public/)
- **Atau via PHP Dev Server**:
  ```bash
  cd "C:\Users\HENDRIK WILDANSYAH\.gemini\antigravity-ide\scratch\navasena"
  php -S 127.0.0.1:8000 -t public
  ```
  Lalu buka [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 4. AKUN AWAL & HAK AKSES PENGGUNA

Sistem menyediakan 3 role dengan kredensial master awal:

| Role | Username | Password Awal | Hak Akses Utama |
|---|---|---|---|
| **Ketua Angkatan** | `ketua` | `PasswordKetua2026!` | Dashboard lengkap, verifikasi pembayaran, kelola siswa, buku kas, laporan, jadwal cicilan, manajemen user & audit logs. |
| **Bendahara** | `bendahara` | `PasswordBendahara2026!` | Kelola siswa, pencatatan pembayaran tunai/transfer, verifikasi pembayaran (jika diizinkan), pencatatan kas keluar, cetak rekap. |
| **Siswa** | `[Nomor Siswa/NIS]` | `[Nomor Siswa/NIS]` | Portal mandiri pribadi: tagihan Rp250.000, riwayat kuitansi sah, progres 3 tahap, jatuh tempo, pengumuman resmi. |

> **PERINGATAN KEAMANAN**: Demi kepatuhan keamanan data, segera ganti password akun master awal melalui menu **Ganti Password** setelah login pertama kali.

---

## 5. PANDUAN OPERASIONAL PENGURUS

### A. Mengimpor Data Siswa dari File Excel / CSV
1. Siapkan file Excel yang disimpan sebagai **CSV UTF-8** (Comma Delimited).
2. Header yang wajib ada:
   - `NIS` (atau `nomor_siswa` / `no_siswa`)
   - `Nama Lengkap` (atau `nama` / `nama_siswa`)
   - `Kelas` (atau `rombel`)
   - Kolom opsional: `Jenis Kelamin`, `Telepon / No HP`, `Email`.
3. Buka menu **Data Siswa** &rarr; klik **Impor CSV / Excel**.
4. Pilih file, centang opsi *Buat Akun Portal Siswa Otomatis*, lalu klik **Mulai Proses Impor**.
5. Sistem akan menampilkan ringkasan jumlah data berhasil, data duplikat yang dilewati, serta rincian baris jika ada data yang tidak lengkap.

### B. Mencatat dan Memverifikasi Pembayaran
1. Klik tombol **+ Catat Pembayaran**.
2. Pilih nama siswa dari dropdown (sisa tagihan siswa langsung tampil otomatis).
3. Masukkan nominal uang yang diterima (misal Rp100.000).
4. Pilih metode **Tunai** atau **Transfer**, masukkan tanggal dan nomor referensi/bank.
5. Alokasi cicilan default disetel **Otomatis** (mengisi tahap tertua yang belum lunas), atau pilih **Manual** jika ada kesepakatan khusus.
6. Klik **Simpan Transaksi Pembayaran**. Status awal transaksi adalah **Menunggu Verifikasi** (nomor kuitansi `KW-YYYYMMDD-XXXX` dibuat secara unik).
7. Pengurus berwenang (Ketua / Bendahara) memeriksa data lalu menekan tombol **Setujui & Sahkan Pembayaran**.
8. Setelah terverifikasi:
   - Sisa tagihan siswa otomatis berkurang dengan akurat.
   - Transaksi otomatis masuk ke **Buku Kas** sebagai Kas Masuk (*Iuran Siswa*).
   - Kuitansi resmi dapat langsung dicetak oleh pengurus atau diunduh oleh siswa di portalnya.

### C. Pembatalan Transaksi (Void & Audit Reversal)
Jika terjadi kesalahan catat pada transaksi yang sudah sah:
1. Buka detail transaksi pembayaran di menu **Pembayaran**.
2. Klik tombol **Batalkan Transaksi (Audit Reversal)**.
3. Masukkan alasan pembatalan wajib.
4. Sistem secara otomatis menandai status transaksi menjadi *Dibatalkan*, membatalkan catatan kas terkait, dan mengembalikan sisa tagihan siswa secara akurat tanpa menghapus data secara permanen (jejak audit tersimpan).

### D. Mengubah Jadwal Cicilan
1. Login sebagai **Ketua Angkatan**.
2. Buka menu **Pengaturan Iuran**.
3. Sesuaikan tanggal jatuh tempo Tahap 2 dan Tahap 3 sesuai penetapan resmi sekolah.
4. Klik **Simpan Seluruh Pengaturan**. Perubahan langsung tersinkronisasi ke dashboard dan portal seluruh siswa.

---

## 6. BACKUP DAN RESTORE DATABASE

### Cara Backup:
Buka command prompt / terminal:
```bash
mysqldump -u root -p navasena_keuangan > "storage/backup_navasena_$(date +%Y%m%d).sql"
```

### Cara Restore:
```bash
mysql -u root -p navasena_keuangan < "storage/backup_navasena_YYYYMMDD.sql"
```

---

## 7. STRUKTUR FOLDER APLIKASI

```
navasena/
├── app/
│   ├── autoload.php             # Native PSR-4 autoloader
│   ├── Controllers/
│   │   ├── AuthController.php       # Login, logout, ganti password
│   │   ├── DashboardController.php  # Dashboard eksekutif pengurus
│   │   ├── StudentController.php    # CRUD siswa, impor CSV, ekspor data
│   │   ├── PaymentController.php    # Pembayaran, kuitansi, verifikasi, void
│   │   ├── CashController.php       # Buku kas masuk & keluar, rekonsiliasi
│   │   ├── ReportController.php     # Rekap siswa, tahap, kas, cetak resmi
│   │   ├── PortalController.php     # Portal mandiri siswa (data terisolasi)
│   │   ├── SettingController.php    # Konfigurasi iuran & jadwal jatuh tempo
│   │   └── UserController.php       # Manajemen akun pengguna & reset password
│   ├── Helpers/
│   │   ├── Auth.php                 # Autentikasi sesi & kontrol role
│   │   ├── CSRF.php                 # Proteksi token CSRF pada formulir
│   │   ├── Database.php             # Koneksi PDO singleton & transaksi DB
│   │   ├── Flash.php                # Flash message notifikasi
│   │   ├── Logger.php               # Jejak rekam audit aktivitas sistem
│   │   ├── Response.php             # Pengalihan, JSON, unduh file CSV BOM
│   │   └── View.php                 # Rendering template, format Rupiah & tanggal
│   └── Services/
│       ├── AllocationService.php    # Logika alokasi cicilan per tahap
│       ├── CalculationService.php   # Mesin hitung keuangan dinamis & metrik
│       └── ImportService.php        # Parser CSV UTF-8 fleksibel & anti-duplikasi
├── config/
│   ├── config.php               # Pengaturan base URL dinamis & sesi aman
│   └── database.php             # Kredensial koneksi database MySQL/MariaDB
├── database/
│   ├── schema.sql               # Skema DDL 9 tabel relasional berindeks
│   └── seed.sql                 # Data awal konfigurasi, tahapan & akun pengurus
├── public/
│   ├── index.php                # Front controller & router HTTP
│   ├── .htaccess                # Mod_rewrite routing Apache
│   └── assets/
│       ├── css/
│       │   ├── main.css             # Sistem desain native CSS profesional
│       │   └── print.css            # Format cetak resmi dokumen pertanggungjawaban
│       ├── js/
│       │   └── app.js               # Interaksi JavaScript vanilla
│       └── img/
│           └── logo.svg             # Logo vektor aplikasi
├── storage/
│   ├── sample_import_siswa.csv  # Berkas contoh template impor siswa
│   ├── logs/                    # Direktori log sistem
│   └── exports/                 # Direktori berkas ekspor
├── scripts/
│   ├── test_system.php          # Suite pengujian otomatis perhitungan keuangan
│   └── test_http_flow.php       # Pengujian E2E HTTP, sesi, dan keamanan role
├── .htaccess                    # Pengarah root ke public/
└── README.md                    # Dokumentasi lengkap sistem
```

---

## 8. HASIL PENGUJIAN OTOMATIS (44/44 PASS)

Sistem telah diuji menggunakan skenario pengujian komprehensif (`scripts/test_system.php` dan `scripts/test_http_flow.php`):
- **Skema & Foreign Key Integrity**: 9 tabel terverifikasi.
- **Autentikasi & Password Hash**: Bcrypt terverifikasi aman.
- **Impor CSV & Validasi Duplikasi**: Lolos uji deteksi duplikasi baris dan nomor siswa.
- **Akurasi Perhitungan Finansial**: Transaksi menunggu verifikasi tidak dihitung sebagai penerimaan sah (0 drift).
- **Alokasi Cicilan Bertahap**: Pembayaran sebagian teralokasi tepat ke DP, Cicilan 2, dan Pelunasan tanpa penghitungan ganda.
- **Void & Audit Reversal**: Pembatalan transaksi sah otomatis membatalkan kas terkait dan mengembalikan sisa tagihan siswa dengan jejak audit.
- **Isolasi Hak Akses**: Siswa hanya dapat mengakses data pribadi miliknya sendiri.
- **Proteksi Role**: Bendahara dan Siswa terisolasi sesuai batas kewenangan masing-masing.

Semua 17 Acceptance Criteria terpenuhi secara lengkap dan siap digunakan dalam kegiatan operasional angkatan nyata.
