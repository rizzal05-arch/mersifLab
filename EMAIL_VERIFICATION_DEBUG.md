# 📧 Email Verification System - Debug Guide

## ⚠️ Status Sistem

Sistem verifikasi email sudah di-setup dengan fitur-fitur berikut:
- ✅ Database migration untuk `email_verification_token` dan `email_verification_sent_at`
- ✅ Email notification system diset ke SMTP Gmail
- ✅ CLI command untuk manual verification
- ✅ Debug endpoint untuk development

---

## 🧪 Cara Test Email Verification

### Option 1: Verifikasi Manual via Web (Development Only)
Akses: **`http://localhost:8000/debug/verify-email`**

Halaman ini memungkinkan Anda untuk langsung verify email tanpa menunggu email masuk.

### Option 2: Verifikasi via CLI Command
```bash
php artisan user:verify namauser@email.com
```

### Option 3: Tunggu Email Asli
1. Register dengan email yang valid
2. Cek inbox email (dan folder spam)
3. Klik link verifikasi dari email
4. Email akan terverifikasi otomatis
5. Login dengan akun Anda

---

## 🔍 Debugging

### Melihat Logs
Semua aktivitas tercatat di: `storage/logs/laravel.log`

Cari log yang relevan:
```
[INFO] Sending email verification
[INFO] Email verification sent successfully
[ERROR] Failed to send email verification
[INFO] Email verification attempt
[INFO] Email verified successfully
```

### Mengecek User di Database
```bash
php artisan tinker
> User::where('email', 'namauser@email.com')->first()
```

---

## 📝 User Flow

### New User Registration:
```
1. User fill register form
2. System create user + generate token
3. System send email notification
4. User redirect ke halaman "Periksa Email Anda"
5. (Email dikirim ke inbox)
6. User buka email dan klik link
7. Link verify di `/verify-email?token=xxx&email=xxx`
8. Email marked as verified
9. User dapat login
```

### Login Check:
```
1. User input email & password
2. Credentials dicheck
3. Email verified? 
   - YES: Login success, redirect ke home
   - NO: Login failed, minta verify email
```

---

## 🚨 Troubleshooting

### Error: "Link verifikasi tidak valid atau telah kadaluarsa"
**Penyebab:** 
- Email belum diterima (notification gagal)
- Token tidak tersimpan di database
- Token expired (lebih dari 24 jam)

**Solusi:**
1. Cek email di spam folder
2. Gunakan `/debug/verify-email` untuk verify manual
3. Cek logs di `storage/logs/laravel.log`
4. Pastikan SMTP config benar di `.env`

### Error: "Silakan verifikasi email Anda terlebih dahulu"
**Penyebab:** 
- User coba login tapi email belum diverifikasi

**Solusi:**
1. User harus terlebih dahulu verify email
2. Atau gunakan `/debug/verify-email` untuk auto-verify (dev only)

---

## 🔐 Security Notes

- ✅ Token di-generate secara random (60 karakter)
- ✅ Token unique per user
- ✅ Token expired setelah 24 jam
- ✅ Token dihapus setelah verification berhasil
- ✅ Email format di-validasi
- ✅ Email unique di database

---

## 📧 Email Configuration

Konfigurasi SMTP di `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=riz4lalfarizzi@gmail.com
MAIL_PASSWORD=helfnoqrbisnkwkr (App Password, bukan password Gmail)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=riz4lalfarizzi@gmail.com
MAIL_FROM_NAME="MersifLab"
```

⚠️ **Penting**: Gunakan **App Password** Gmail, bukan password biasa!

---

## 📦 Files Modified

1. **Database:**
   - ✅ Migration: `2026_02_12_000000_add_email_verification_columns_to_users_table.php`

2. **Controllers:**
   - ✅ `AuthController.php` - Register, Login, Verifikasi, Resend
   - ✅ `DebugController.php` - Debug endpoints

3. **Models:**
   - ✅ `User.php` - Add verification columns & casts

4. **Notifications:**
   - ✅ `VerifyEmailNotification.php` - Email template

5. **Views:**
   - ✅ `auth/email-verification-pending.blade.php`
   - ✅ `debug/verify-email.blade.php`

6. **Routes:**
   - ✅ `routes/web.php` - Add email verification routes

7. **Commands:**
   - ✅ `app/Console/Commands/VerifyUserEmail.php`

---

## 🎯 Next Steps

1. ✅ Test registration dengan email valid
2. ✅ Verify email menggunakan `/debug/verify-email` atau CLI
3. ✅ Coba login dengan verified email
4. ✅ Cek SMTP config apakah benar-benar kirim email
5. ✅ Monitor logs untuk identify issues

---

## 💡 Tips

- Untuk development, gunakan email dummy atau Gmail sandbox
- Jangan gunakan password Gmail real, gunakan App Password
- Check spam folder jika email tidak masuk ke inbox
- Logs akan membantu identify masalah dengan email gateway
