# Pemilihan Ketua Kelas Berbasis Blockchain (Simulasi)

Aplikasi pemilihan ketua kelas menggunakan Laravel + MySQL. Setiap suara direkam sebagai **block** pada rantai hash SHA-256, sehingga perubahan data dapat dideteksi melalui halaman audit publik.

> Prototipe untuk pembelajaran. Blockchain disimulasikan di MySQL (bukan jaringan blockchain terdesentralisasi).

## Fitur

- Admin/panitia: login, pengaturan pemilihan (draf/berlangsung/ditutup), CRUD kandidat, CRUD siswa, generate/cetak kode voting, hasil, audit chain.
- Siswa: vote menggunakan **kode sekali-pakai** tanpa akun/password.
- Struk suara dengan kode unik untuk verifikasi publik.
- Halaman publik: hasil real-time, verifikasi struk, dan audit integritas rantai.
- Pencegahan vote ganda di level database (`votes` unik per pemilihan + siswa).
- Hash-chain tamper-evident: perubahan satu block membuat audit gagal.

## Teknologi

- Laravel 13 (PHP 8.3), Blade, Tailwind CSS (CDN)
- MySQL 8
- Tanpa build step Node (tidak perlu `npm install`)

## Menjalankan

1. Pastikan PHP, Composer, dan MySQL berjalan.
2. Salin konfigurasi environment (sesuaikan kredensial DB):
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
3. Buat database `blockchain_voting` di MySQL (bila belum ada), lalu siapkan data:
   ```bash
   php artisan db:wipe
   php artisan migrate --seed
   php artisan storage:link
   ```
4. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```
5. Buka `http://127.0.0.1:8000`.

> Catatan: pada environment ini `migrate:fresh` sempat tidak menghapus tabel lama; gunakan `php artisan db:wipe` lalu `php artisan migrate --seed`.

## Akun Demo

- Admin: `admin@example.com` / `password`
- Siswa: gunakan kode voting yang tercetak saat seeding, atau buka **Admin → Siswa & Kode → Cetak kode**.

Pemilihan demo berstatus **Berlangsung** dengan 3 kandidat dan 20 siswa.

## Alur Voting

1. Siswa membuka `/vote`, memasukkan kode voting.
2. Sistem memeriksa kode, status pemilihan, dan apakah siswa sudah memilih.
3. Siswa memilih satu kandidat dan mengonfirmasi.
4. Dalam satu transaksi database:
   - vote disimpan dengan `receipt_code`,
   - block baru ditambahkan ke rantai,
   - kode ditandai terpakai (`voted_at`).
5. Siswa menerima struk dan dapat memverifikasinya di `/verifikasi`.

## Cara Kerja Blockchain

- Genesis block (`position = 0`) dibuat saat pemilihan dibuka.
- Setiap block menyimpan: `position`, `block_timestamp`, `voter_hash`, `candidate_id`, `previous_hash`, `nonce`, dan `hash`.
- Identitas pemilih disamarkan: `voter_hash = HMAC-SHA256(student_id, APP_KEY)`.
- Hash block:
  ```
  SHA-256( position | timestamp | voter_hash | candidate_id | previous_hash | nonce )
  ```
- Audit menghitung ulang semua hash, memeriksa `previous_hash`, dan urutan `position`.

### Keterbatasan

- Hash-chain relatif tamper-evident; pengguna dengan akses penuh ke database masih dapat menulis ulang seluruh rantai.
- Untuk proteksi nyata diperlukan smart contract/ledger eksternal.
- Tabel `votes` tetap menyimpan relasi siswa → suara untuk mencegah vote ganda; block hanya menyimpan hash identitas.

## Struktur Data

| Tabel | Fungsi |
|---|---|
| `users` | Admin/panitia |
| `elections` | Data & status pemilihan |
| `candidates` | Kandidat + visi/misi |
| `students` | Siswa + kode voting sekali-pakai |
| `votes` | Suara + kode struk |
| `blocks` | Rantai block (hash-chain) |

## Testing

```bash
php artisan test
```

Test mencakup: vote sah, vote ganda, kode terpakai, pemilihan ditutup, kandidat tidak valid, deteksi tamper, verifikasi publik, auth admin, dan render semua halaman.
