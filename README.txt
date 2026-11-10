COFNA - STARTER LARAGON + MYSQL

Struktur:
cofna/
├── config/
│   └── database.php
├── css/
├── js/
├── image_cofna/
├── test_koneksi.php
└── menu.php

PENTING:
1. Database MySQL harus bernama: cofna_db
2. Tabel tb_menu harus sudah dibuat dan berisi data menu.
3. File project ini nantinya diletakkan di Document Root Laragon.
4. Jangan jalankan PHP dengan tombol Run/Debug VS Code.
5. Jalankan Laragon (Apache + MySQL), lalu buka lewat browser.

Tes koneksi:
http://localhost/cofna/test_koneksi.php

Tes data menu:
http://localhost/cofna/menu.php
