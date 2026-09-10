# Dokumentasi SIPINTER REST API

Selamat datang di dokumentasi RESTful API **SIPINTER LP Ma'arif NU PBNU**.
Backend ini dibangun menggunakan **Laravel 10**, **PHP 8.2+**, dan **Laravel Sanctum** untuk otentikasi berbasis token.

- **Base URL**: `http://localhost:8000/api` (atau `http://localhost/sipinter-backend/public/api`)
- **Content-Type**: `application/json`
- **Accept**: `application/json`

---

## 1. Format Respons Standar

### Sukses
```json
{
  "success": true,
  "message": "Deskripsi pesan sukses (opsional)",
  "data": { ... }
}
```

### Error / Gagal
```json
{
  "success": false,
  "message": "Deskripsi pesan error",
  "errors": { ... }
}
```

---

## 2. Autentikasi (Auth)

### A. Login
- **Endpoint**: `POST /auth/login`
- **Body**:
  ```json
  {
    "username": "nomor_registrasi_atau_username",
    "password": "password_akun"
  }
  ```
- **Response Sukses (200)**:
  ```json
  {
    "success": true,
    "message": "Login berhasil.",
    "data": {
      "token": "1|abcdef123456...",
      "token_type": "Bearer",
      "user": {
        "id_user": 1,
        "name": "Nama User",
        "username": "01020001",
        "role": "operator | super admin | admin pusat | admin wilayah | admin cabang",
        "status_active": "active"
      },
      "satpen": { ... }
    }
  }
  ```

### B. User Profile (Me)
- **Endpoint**: `GET /auth/me`
- **Header**: `Authorization: Bearer <token>`
- **Response (200)**: Mengembalikan data user yang sedang login beserta wilayah/cabang dan satpen (jika operator).

### C. Logout
- **Endpoint**: `POST /auth/logout`
- **Header**: `Authorization: Bearer <token>`
- **Response (200)**: Mencabut dan menghapus token yang sedang aktif.

### D. Cek NPSN (Validasi Sekolah)
- **Endpoint**: `POST /auth/check-npsn`
- **Body**: `{"npsn": "20101234"}`
- **Response**: Mengembalikan data sekolah dari Kemdikbud / Dapo Ma'arif / Virtual NPSN.

### E. Permohonan Virtual NPSN
- **Endpoint**: `POST /auth/virtual-npsn`
- **Body (Multipart / JSON)**:
  - `jenjang`: ID Jenjang (integer)
  - `provinsi`: ID Provinsi (integer)
  - `kabupaten`: ID Kabupaten (integer)
  - `alamat`: string
  - `nama_sekolah`: string
  - `email`: email
  - `nik_kepsek`: string

### F. Ganti Password (User Login)
- **Endpoint**: `POST /auth/change-password`
- **Header**: `Authorization: Bearer <token>`
- **Body**:
  ```json
  {
    "last_pass": "password_lama",
    "new_pass": "password_baru",
    "new_pass_confirmation": "password_baru"
  }
  ```

### G. Lupa Password
- **Request Link**: `POST /auth/forgot-password` (`{"no_registrasi": "..."}`)
- **Reset Password**: `POST /auth/reset-password` (`{"email": "...", "token": "...", "new_password": "...", "password_confirm": "..."}`)

---

## 3. Master Data (Publik)

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/master/provinsi` | Daftar seluruh provinsi |
| `GET` | `/master/provinsi/{id}` | Detail provinsi beserta kabupaten & cabang |
| `GET` | `/master/kabupaten?id_prov={id}` | Daftar kabupaten (opsional filter provinsi) |
| `GET` | `/master/kabupaten/{id}` | Detail kabupaten |
| `GET` | `/master/cabang?id_prov={id}` | Daftar pengurus cabang (opsional filter) |
| `GET` | `/master/cabang/{id}` | Detail pengurus cabang |
| `GET` | `/master/jenjang` | Daftar jenjang pendidikan |
| `GET` | `/master/kategori` | Daftar kategori Satpen (A, B, C, D) |
| `GET` | `/master/tapel` | Daftar tahun pelajaran |
| `GET` | `/informasi` | Berita / pengumuman terbit |
| `GET` | `/informasi/{slugOrId}` | Detail informasi |

---

## 4. Satpen (Satuan Pendidikan)

### A. Pencarian & Detail Publik
- `GET /satpen/search?keyword=...&jenjang=...&prov=...&kab=...`
- `GET /satpen/public/{satpenId}`

### B. Registrasi Satpen Baru
- **Endpoint**: `POST /auth/register-satpen`
- **Content-Type**: `multipart/form-data`
- **Fields**:
  - `propinsi`, `kabupaten`, `cabang`, `jenjang`
  - `nm_satpen`, `npsn`, `yayasan`, `kepsek`, `telp`, `email`, `thn_berdiri`
  - `alamat`, `kelurahan`, `kecamatan`, `aset_tanah`, `nm_pemilik`, `password`
  - `no_srt_permohonan`, `tgl_srt_permohonan`, `file_permohonan` (file PDF/Image)
  - `nm_rekom_pc`, `cabang_rekom_pc`, `no_srt_rekom_pc`, `tgl_srt_rekom_pc`, `file_rekom_pc` (file PDF/Image)
  - `nm_rekom_pw`, `wilayah_rekom_pw`, `no_srt_rekom_pw`, `tgl_srt_rekom_pw`, `file_rekom_pw` (file PDF/Image)

---

## 5. Portal Operator (`/operator/*`)
*Memerlukan header `Authorization: Bearer <token>` (Role: `operator`)*

- `GET /operator/satpen`: Data profil Satpen milik operator saat ini.
- `GET /operator/pdptk`: Data PDPTK Satpen.
- `PUT /operator/pdptk`: Simpan perubahan data PDPTK.
- `GET /operator/pdptk/dapo/{npsn}`: Sinkronisasi data Dapodik untuk PDPTK.
- `GET /operator/other`: Data informasi lainnya.
- `PUT /operator/other`: Simpan perubahan data lainnya.

### Manajemen PTK Operator
- `GET /operator/ptk/data`: Daftar guru & tenaga kependidikan.
- `GET /operator/ptk/status-counts`: Statistik status PTK.
- `POST /operator/ptk`: Tambah PTK baru.
- `GET /operator/ptk/{id}`: Detail PTK.
- `PUT /operator/ptk/{id}`: Update PTK.
- `POST /operator/ptk/{id}/revisi`: Ajukan revisi dokumen PTK.
- `DELETE /operator/ptk/{id}`: Hapus PTK.

---

## 6. Portal Admin (`/admin/*`)
*Memerlukan header `Authorization: Bearer <token>` (Role: Admin / Super Admin)*

### Manajemen Satpen
- `GET /admin/satpen?page=1&per_page=15&keyword=...&status=...`: Rekap & filter satpen (otomatis terfilter berdasarkan wilayah admin cabang/wilayah).
- `GET /admin/satpen/{id}`: Detail lengkap Satpen.
- `PUT /admin/satpen/{id}/status`: Update status satpen (`setujui`, `revisi`, `tolak`, `proses dokumen`, `expired`).
- `DELETE /admin/satpen/{satpen}`: Hapus satpen (Super Admin only).

### Dashboard & Statistik
- `GET /admin/dashboard/provcount`: Jumlah satpen per provinsi.
- `GET /admin/dashboard/kabcount/{provId?}`: Jumlah satpen per kabupaten.
- `GET /admin/dashboard/pccount`: Jumlah satpen per pengurus cabang.
- `GET /admin/dashboard/jenjangcount`: Jumlah satpen per jenjang.
- `GET /admin/dashboard/ptkcount`: Total hitungan PTK.
- `GET /admin/dashboard/pdcount`: Total hitungan PD.

### Verifikasi PTK (Admin)
- `GET /admin/ptk/data`: Data pengajuan PTK untuk verifikasi.
- `GET /admin/ptk/statistics`: Statistik verifikasi PTK.
- `GET /admin/ptk/{id}/detail`: Detail verifikasi PTK.
- `POST /admin/ptk/action`: Aksi persetujuan/penolakan PTK.

### Virtual NPSN
- `GET /admin/vnpsn`: Daftar permohonan Virtual NPSN.
- `PUT /admin/vnpsn/{virtualNPSN}/accept`: Terbitkan nomor Virtual NPSN.
- `DELETE /admin/vnpsn/{virtualNPSN}/reject`: Tolak permohonan.
- `DELETE /admin/vnpsn/{virtualNPSN}`: Hapus Virtual NPSN.

### Export Excel
- `GET /admin/export/satpen`: Download Excel rekap Satpen.
- `GET /admin/export/pdptk`: Download Excel PDPTK.
- `GET /admin/export/other`: Download Excel data lainnya.
- `GET /admin/export/wilayah`: Download Excel rekap wilayah.
- `GET /admin/export/cabang`: Download Excel rekap cabang.

---

## 7. Cara Menjalankan Backend

1. Buka terminal di folder:
   ```bash
   cd C:\xampp\htdocs\sipinter-backend
   ```
2. Pastikan database MySQL di XAMPP telah berjalan dan file `.env` telah disesuaikan (nama DB, username, password).
3. Jalankan server Laravel API:
   ```bash
   C:\xampp\php\php.exe artisan serve
   ```
   API akan berjalan di `http://127.0.0.1:8000`.
4. Anda dapat langsung menguji endpoint menggunakan Postman, Thunder Client, Insomnia, atau cURL.
