# 🏥 Medique

Sistem Antrian Online untuk Fasilitas Kesehatan.

---

## 🚀 Fitur

- 👨‍⚕️ Admin
  - Kelola klinik
  - Kelola dokter
  - Kelola jadwal
- 🧑‍⚕️ Doctor
  - Monitor antrian
  - Panggil pasien
- 👤 Patient
  - Booking antrian

---

## 🛠️ Tech Stack

- Laravel 11
- PHP 8.2
- MySQL
- Tailwind CSS

---

## ⚙️ Cara Install

```bash
git clone https://github.com/USERNAME/medique.git
cd medique
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
