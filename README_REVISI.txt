REVISI COFNA - 8 OKTOBER 2026 (VERSI TANPA NOMOR HP)

File revisi:
1. 01_revisi_tb_pesanan.sql
2. login.php
3. proses_login.php
4. daftar.php
5. proses_daftar.php
6. proses_pesanan.php
7. checkout_cofna.php
8. kelola_pesanan.php
9. ubah_status_pesanan.php

ATURAN SISTEM:
- Login: Email + Password.
- Daftar akun: Nama + Email + Password.
- Nomor HP TIDAK dipakai dan TIDAK perlu ditambahkan ke tb_user.
- Checkout TIDAK meminta Nama Lengkap.
- Checkout TIDAK meminta Nomor HP.
- Checkout hanya berisi Catatan Pesanan (opsional) dan metode pembayaran.
- Nama dan telepon pada tb_pesanan dibuat NULL agar database lama tetap bisa dipakai.

DATABASE:
Jalankan 01_revisi_tb_pesanan.sql pada database cofna_db melalui phpMyAdmin.

CATATAN:
- checkout_cofna.php adalah halaman checkout yang dipakai.
- Link menuju checkout harus mengarah ke checkout_cofna.php.
- Nomor pesanan tetap dibuat otomatis dengan format CFN-01, CFN-02, dst.
