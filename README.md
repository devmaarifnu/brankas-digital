# SIPINTER LP MA'ARIF NU PBNU

<p align="center">
  <img src="https://sipinter.maarifnu.or.id/assets/images/logos/Logo_Sipinter_Panjang.png" width="300" alt="LP Ma'arif NU Logo">
</p>

<p align="center">
  <strong>Sistem Informasi Pendataan Terintegrasi Lembaga Pendidikan Ma'arif Nahdlatul Ulama</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=flat-square&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0+-orange?style=flat-square&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.x-purple?style=flat-square&logo=bootstrap" alt="Bootstrap">
  <img src="https://img.shields.io/badge/ApexCharts-3.x-green?style=flat-square" alt="ApexCharts">
</p>

---

## 📋 Deskripsi

SIPINTER LP Ma'arif NU PBNU adalah sistem informasi pendataan terintegrasi yang menghubungkan satuan pendidikan di bawah naungan LP Ma'arif NU dengan kantor pusat PBNU. Sistem ini memfasilitasi pendataan dua arah antara operator sekolah dengan administrator LP Ma'arif NU PBNU secara digital dan real-time.

## ✨ Fitur Utama

### 🏫 **Manajemen Satuan Pendidikan (SATPEN)**
- Registrasi dan validasi satuan pendidikan
- Pengelolaan data sekolah/madrasah
- Verifikasi dokumen kelengkapan
- Status tracking permohonan
- Perpanjangan izin operasional

### 👥 **Manajemen PTK (Pendidik & Tenaga Kependidikan)**
- Pendataan guru dan tenaga kependidikan
- Sertifikasi dan kompetensi
- Status kepegawaian
- Riwayat karir dan pendidikan

### 🎓 **Manajemen Peserta Didik**
- Data statistik peserta didik
- Distribusi berdasarkan jenjang
- Analisis per wilayah dan cabang
- Laporan perkembangan

### 🏢 **Manajemen OSS (Online Single Submission)**
- Permohonan izin operasional
- Tracking status permohonan
- Kelengkapan dokumen digital
- Timeline proses perizinan

### 💰 **Manajemen BHPNU (Biaya Hak Pengelolaan NU)**
- Pembayaran biaya pengelolaan
- Verifikasi bukti pembayaran
- Riwayat transaksi
- Status pembayaran

### 📊 **Dashboard & Reporting**
- Dashboard multi-level (Pusat, Wilayah, Cabang)
- Visualisasi data dengan charts interaktif
- Export data ke Excel/PDF
- Real-time statistics

### 🔐 **Sistem Otentikasi & Otorisasi**
- Multi-role access control
- Role-based permissions
- Session management
- Password recovery

## 🏗️ Arsitektur Sistem

### **Multi-Level Access**
```
Super Admin (PBNU)
├── Admin Pusat
├── Admin Wilayah (Provinsi)
│   ├── Admin Cabang (Kabupaten/Kota)
│   │   └── Operator Sekolah
```

### **Struktur Database**
- **Users**: Manajemen pengguna dan roles
- **Satpen**: Data satuan pendidikan
- **PDPTK**: Pendidik dan tenaga kependidikan
- **OSS**: Online Single Submission
- **BHPNU**: Biaya hak pengelolaan
- **Regional**: Provinsi, kabupaten, cabang

## 🛠️ Tech Stack

### **Backend**
- **Framework**: Laravel 10.13.2
- **PHP**: 8.1+
- **Database**: MySQL 8.0+
- **Authentication**: Session-based Authentication
- **File Storage**: Local Storage (Filesystem)

### **Frontend**
- **CSS Framework**: Bootstrap 5.x
- **Charts**: ApexCharts.js
- **Icons**: Tabler Icons
- **JavaScript**: Vanilla JS + jQuery
- **Build Tool**: Vite
- **Template Engine**: Blade

### **Packages & Libraries**
Lihat detail di [composer.json](composer.json):
- **maatwebsite/excel** (^3.1): Excel import/export
- **endroid/qr-code** (^4.8): QR Code generator
- **phpoffice/phpword** (^1.1): Word document generator
- **alkoumi/laravel-hijri-date** (^1.0): Hijri date converter
- **guzzlehttp/guzzle** (^7.2): HTTP client
- **laravel/sanctum** (^3.2): API authentication
- **symfony/dom-crawler** (^6.3): Web scraping

### **Development Tools**
- **laravel/pint** (^1.0): Code style fixer
- **phpunit/phpunit** (^10.1): Testing framework
- **spatie/laravel-ignition** (^2.0): Error page
- **fakerphp/faker** (^1.9.1): Fake data generator

## 📦 Instalasi

### **Prerequisites**
```bash
PHP >= 8.1
Composer
Node.js & NPM
MySQL >= 8.0
```

### **1. Clone Repository**
```bash
git clone https://github.com/faisolarifin/siap-lpmaarif-nupnbu.git
cd sipinter-lpmaarif-nupbnu
```

### **2. Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### **3. Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### **4. Database Configuration**
Edit `.env` file dan sesuaikan dengan konfigurasi database Anda:
```env
APP_NAME="SIPINTER LP Ma'arif NU"
APP_ENV=local
APP_KEY=base64:your_generated_key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipinter_lpmaarif_nupbnu
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Catatan untuk Laragon:**
- Default DB_USERNAME: `root`
- Default DB_PASSWORD: `` (kosong)
- Database akan dibuat otomatis atau buat manual melalui phpMyAdmin/HeidiSQL

### **5. Database Migration & Seeding**
```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### **6. Storage & Permissions**
```bash
# Create storage symlink
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### **7. Build Assets**
```bash
# Development
npm run dev

# Production
npm run build
```

### **8. Run Application**

**Opsi 1: Menggunakan PHP Built-in Server**
```bash
# Development server
php artisan serve
```
Access: `http://localhost:8000`

**Opsi 2: Menggunakan Laragon (Recommended untuk Windows)**
1. Letakkan project di folder: `C:\laragon\www\sipinter-lpmaarif-nupbnu`
2. Start Laragon (Apache & MySQL)
3. Access: `http://sipinter-lpmaarif-nupbnu.test`

**Opsi 3: Menggunakan Artisan dengan custom port**
```bash
php artisan serve --host=0.0.0.0 --port=8080
```

### **9. Default Login (Setelah Seeding)**
Jika sudah menjalankan seeder, gunakan akun default:
```
Super Admin:
Email: admin@maarifnu.or.id
Password: password123

Operator:
Email: operator@maarifnu.or.id
Password: password123
```
**Penting:** Segera ganti password default setelah login pertama!

## ⚙️ Konfigurasi Tambahan

### **Mail Configuration**
Untuk fitur email (notifikasi, reset password, dll), konfigurasi di `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@maarifnu.or.id
MAIL_FROM_NAME="SIPINTER LP Ma'arif NU"
```

**Catatan:**
- Untuk Gmail, gunakan App Password bukan password biasa
- Untuk testing lokal, bisa gunakan Mailtrap atau MailHog

### **Session Configuration**
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120  # dalam menit (2 jam)
```

### **File Upload Configuration**
Konfigurasi file upload di `php.ini` atau `.htaccess`:
```ini
upload_max_filesize=10M
post_max_size=10M
max_execution_time=300
```

File types yang diizinkan:
- Dokumen: PDF, DOC, DOCX
- Gambar: JPG, JPEG, PNG
- Maksimal ukuran: 10MB per file

### **Storage Configuration**
```env
FILESYSTEM_DISK=local  # atau 'public' untuk akses publik
```

Path storage:
- Upload NPWP: `storage/app/npyp/npwp/`
- Upload Dokumen: `storage/app/documents/`
- Temporary files: `storage/app/temp/`

## 📁 Struktur Project

```
sipinter-lpmaarif-nupbnu/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Admin area controllers
│   │   │   ├── Api/                # API controllers
│   │   │   ├── Master/             # Master data controllers
│   │   │   ├── AuthController.php
│   │   │   ├── BHPNUController.php
│   │   │   ├── CoretaxController.php
│   │   │   ├── HomeController.php
│   │   │   ├── NPYPController.php
│   │   │   ├── OSSController.php
│   │   │   └── SatpenController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/                     # Eloquent models
│   │   ├── User.php
│   │   ├── Satpen.php
│   │   ├── PTK.php
│   │   ├── NPYP.php
│   │   ├── OSS.php
│   │   ├── BHPNU.php
│   │   ├── Coretax.php
│   │   ├── Provinsi.php
│   │   ├── Kabupaten.php
│   │   ├── Jenjang.php
│   │   └── ... (30+ models)
│   ├── Helpers/                    # Helper classes
│   ├── Mail/                       # Mail classes
│   └── Exports/                    # Excel export classes
├── bootstrap/
│   └── cache/                      # Framework bootstrap cache
├── config/                         # Configuration files
├── database/
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── public/
│   ├── assets/                     # Static assets (CSS, JS, images)
│   ├── images/                     # Public images
│   └── storage/                    # Symlink to storage/app/public
├── resources/
│   ├── views/                      # Blade templates
│   │   ├── admin/                  # Admin views
│   │   │   ├── bhpnu/              # BHPNU management
│   │   │   ├── coretax/            # Coretax management
│   │   │   ├── home/               # Admin dashboard
│   │   │   ├── informasi/          # Information management
│   │   │   ├── master/             # Master data views
│   │   │   ├── npyp/               # NPYP management
│   │   │   ├── oss/                # OSS management
│   │   │   ├── profile/            # Organization profiles
│   │   │   ├── satpen/             # Satpen management
│   │   │   └── users/              # User management
│   │   ├── auth/                   # Authentication views
│   │   ├── bhpnu/                  # BHPNU operator views
│   │   ├── coretax/                # Coretax operator views
│   │   ├── component/              # Reusable components
│   │   ├── emails/                 # Email templates
│   │   ├── exception/              # Error pages
│   │   ├── home/                   # Operator dashboard
│   │   ├── landing/                # Public landing pages
│   │   ├── npyp/                   # NPYP operator views
│   │   ├── oss/                    # OSS operator views
│   │   ├── satpen/                 # Satpen operator views
│   │   └── template/               # Layout templates
│   ├── js/                         # JavaScript files
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── css/                        # Stylesheets
│       └── app.css
├── routes/
│   ├── web.php                     # Web routes
│   ├── api.php                     # API routes
│   ├── channels.php                # Broadcast channels
│   └── console.php                 # Console routes
├── storage/
│   ├── app/
│   │   ├── public/                 # Public accessible storage
│   │   └── private/                # Private storage
│   ├── framework/                  # Framework generated files
│   └── logs/                       # Application logs
├── tests/                          # Application tests
├── vendor/                         # Composer dependencies
├── .env                            # Environment configuration
├── .env.example                    # Environment template
├── composer.json                   # PHP dependencies
├── package.json                    # Node dependencies
├── artisan                         # Artisan CLI
└── vite.config.js                  # Vite configuration
```

## 🔧 API Endpoints

### **Authentication**
```http
POST /login                     # User login
POST /logout                    # User logout
POST /register                  # User registration
POST /forgot-password           # Password reset
```

### **Dashboard API**
```http
GET /api/provcount              # Province statistics
GET /api/kabcount               # Regency statistics
GET /api/pccount                # Branch statistics
GET /api/jenjangcount           # Education level stats
GET /api/ptkcount               # PTK statistics (Admin Cabang)
GET /api/pdcount                # Student statistics (Admin Cabang)
```

### **Data Management**
```http
GET /api/satpen/search          # Search institutions
GET /api/satpen/{id}            # Get institution details
GET /api/checknpsn/{npsn}       # Validate NPSN
GET /api/kabupaten/{provId}     # Get regencies by province
```

## 👤 User Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full system access, user management |
| **Admin Pusat** | National data access, reporting |
| **Admin Wilayah** | Provincial data management |
| **Admin Cabang** | Regional data management, PTK/PD charts |
| **Operator** | School data entry, document upload |

## 📊 Dashboard Features

### **Admin Cabang Dashboard**
- **Chart PTK**: Statistik Pendidik & Tenaga Kependidikan
  - Guru Laki-laki/Perempuan
  - Tendik Laki-laki/Perempuan
- **Chart Peserta Didik**: Distribusi berdasarkan jenis kelamin
- **Interactive Charts**: Bar, Pie, Line charts
- **Export Features**: PNG, PDF export

### **Multi-Level Analytics**
- Provincial distribution maps
- Regency-wise statistics
- Education level analysis
- Time-series data

## 🔒 Keamanan

- **Authentication**: Session-based authentication
- **Authorization**: Role-based access control
- **CSRF Protection**: Built-in CSRF tokens
- **Input Validation**: Server-side validation
- **File Upload Security**: Type and size restrictions
- **SQL Injection Prevention**: Eloquent ORM

## 📱 Responsive Design

- **Mobile-First**: Bootstrap 5 responsive grid
- **Cross-Browser**: Modern browser compatibility
- **Touch-Friendly**: Mobile-optimized interfaces
- **Progressive Enhancement**: Graceful degradation

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter TestName

# Generate coverage report
php artisan test --coverage
```

## 📝 Logging

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear logs
php artisan log:clear
```

## 🚀 Deployment

### **Production Setup**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set environment
APP_ENV=production
APP_DEBUG=false
```

### **Server Requirements**
- **Web Server**: Apache/Nginx
- **PHP**: 8.1+ with extensions (mbstring, openssl, PDO, tokenizer, XML)
- **Database**: MySQL 8.0+
- **Storage**: Sufficient space for file uploads

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the [MIT License](LICENSE).

## 👨‍💻 Development Team

- **Lead Developer**: [Faisal Arifin](https://github.com/faisolarifin)
- **Organization**: LP Ma'arif NU PBNU

## 📞 Support

Untuk bantuan teknis dan pertanyaan:

- **Email**: support@maarifnu.or.id
- **Website**: [https://maarifnu.or.id](https://maarifnu.or.id)
- **Documentation**: [Wiki](https://github.com/faisolarifin/siap-lpmaarif-nupnbu/wiki)

---

<p align="center">
  <strong>SIPINTER LP Ma'arif NU PBNU</strong><br>
  <em>Memajukan Pendidikan Islam Berkualitas</em>
</p>

