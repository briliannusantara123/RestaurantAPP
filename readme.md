<p align="center">
<img align="center" src="http://ForTheBadge.com/images/badges/built-with-love.svg"> <img align="center" src="http://ForTheBadge.com/images/badges/uses-html.svg"> <img align="center" src="http://ForTheBadge.com/images/badges/makes-people-smile.svg"> <img align="center" src="http://ForTheBadge.com/images/badges/built-by-developers.svg">
</p>

# Aplikasi Kasir Restoran 
Kasir Resto adalah Restourant Management yang sebuah Website yang bisa managemnet  sebuah restoran dari mulai dari pelanggan bisa melakukan order, dan bertransaksi dengan kasir yang sudah diatur sistemnya.

Sistem ini dibuat dengan menggunakan framework <a href="https://laravel.com/">Laravel</a> dan <a href="https://www.mysql.com/">MySQL</a>

| Profile  | Keterangan  |
|----------|-------------|
| Kelompok | 5           |
| Kampus   | ITBS        |


## 📌 Fitur dari Kasir Resto

| No  | Fitur                                                      |
|-----|------------------------------------------------------------|
|  1  | Authentikasi Admin & Pelanggan.                            |
|  2  | List & CRUD Users, Data Masakan, Kategori.                 |
|  3  | Chart System (Untuk Order Pelanggan).                      |
|  4  | History / Riwayat Order Pelanggan                          |
|  5  | Invoice dan Transaksi Simple & Mudah Dibaca.               |
|  6  | Sweet Alert 2 Included.                                    |
|  7  | Pendataan Dengan Datatable agar lebih cepat & efisien.     |
|  8  | Chart/Grafik Pendapatan mingguan dengan library Chart.JS.  |
|  9  | 6 Hak Akses (Admin, Kasir, Owner, Waiter, Kitchen, Pelanggan)       |
| 10  | User Settings                                              |

------------
## 💻 Panduan Instalasi Project

1. **Clone Repository**
```bash
git clone https://github.com/briliannusantara123/RestaurantAPP.git
```

```
cd AplikasiKasirRestoran
composer install
npm install
copy .env.example rename->.env
```
2. **Buka ```.env``` lalu ubah baris berikut sesuaikan dengan databasemu yang ingin dipakai**
```
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

3. **Import Database SQL**
```
Project ini tidak menggunakan fitur migration, jadi kita harus mengimport database terlebih dahulu**
```
```
mysql -u root -p
create database laravel;
```

4. **Jalankan bash**
```bash
php artisan key:generate
php artisan config:cache
php artisan storage:link
php artisan route:clear
```

5. **Jalankan website**
```bash
php artisan serve
```

## Jika ada pertanyaan silahkan hubungi saya di email :

```
briliannusantara123@gmail.com
```
