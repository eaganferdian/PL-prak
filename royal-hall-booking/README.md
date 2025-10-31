# Royal Hall Booking System

Sistem pemesanan ruangan bergaya kerajaan yang memungkinkan pengguna untuk memesan ruangan kastil untuk berbagai keperluan.

## Fitur Utama

### 1. Manajemen Booking

- Daftar booking dengan pencarian dan filter
- Penyortiran berdasarkan tanggal dan biaya
- Pagination server-side
- Status booking (pending, approved, rejected, completed)
- Soft delete dengan fitur restore
- Royal Archives (Recycle Bin) untuk booking yang dihapus

### 2. Manajemen Ruangan

- Daftar ruangan kastil
- Detil kapasitas dan fasilitas
- Cek ketersediaan ruangan
- Harga per jam

### 3. Sistem Pengguna

- Login dan registrasi
- Role admin dan user biasa
- Validasi server-side untuk semua form

### 4. Fitur Admin

- Approval/reject booking
- Manajemen ruangan dan fasilitas
- Akses ke semua booking
- Restore booking dari arsip

## Struktur Database

### Users Table

- id (Primary Key)
- username (Unique)
- email (Unique)
- password
- full_name
- role (admin/user)
- created_at

### Rooms Table

- id (Primary Key)
- name
- description
- capacity
- location
- image_path
- hourly_rate
- is_active
- created_at

### Bookings Table

- id (Primary Key)
- user_id (Foreign Key)
- room_id (Foreign Key)
- purpose
- booking_date
- start_time
- end_time
- total_cost
- status (pending/approved/rejected/completed)
- admin_notes
- is_deleted
- deleted_at
- created_at

### Facilities Table

- id (Primary Key)
- name
- icon
- description
- created_at

### Room Facilities Table

- room_id (Foreign Key)
- facility_id (Foreign Key)

## Validasi Form

### Booking Form

- Validasi tanggal (tidak boleh di masa lalu)
- Validasi waktu (minimal 1 jam)
- Validasi ketersediaan ruangan
- Validasi purpose (wajib diisi)
- Kalkulasi biaya otomatis

## Fitur Keamanan

- Password hashing
- Form validation
- SQL injection prevention
- CSRF protection
- Role-based access control

## Cara Penggunaan

### Login Admin

- Username: admin
- Password: password

### Login User Demo

- Username: knight_arthur
- Password: password

## Requirement

- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Browser modern

## Instalasi

1. Import file `config/database.sql` ke MySQL
2. Sesuaikan konfigurasi database di `config/config.php`
3. Pastikan folder memiliki permission yang tepat
4. Akses melalui web browser

## Pengembangan Selanjutnya

- [ ] Sistem pembayaran
- [ ] Notifikasi email
- [ ] Laporan booking
- [ ] Galeri foto ruangan
- [ ] Sistem review dan rating
