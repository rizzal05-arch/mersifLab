# 🚀 Migration Instructions for Duration Fields

## Problem:
Teman kamu tidak mendapatkan field `estimated_duration` di tabel modules setelah pull dari GitHub.

## Solution:
Teman kamu harus menjalankan migration yang sudah ada:

### Step 1: Pull Latest Code
```bash
git pull origin main
```

### Step 2: Run Migration
```bash
php artisan migrate
```

### Step 3: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 4: Verify Migration Status
```bash
php artisan migrate:status
```

## 📋 Migration yang Harus Di-run:
✅ `2026_01_22_064548_add_duration_fields_to_modules_chapters_classes` - Status: Ran

## 🔍 Verification:
Setelah migration di-run, seharusnya ada field:
- `modules.estimated_duration` (INT, default 0)
- `chapters.total_duration` (INT, default 0)  
- `classes.total_duration` (INT, default 0)

## 🛠️ Jika Masih Error:
Jika migration gagal, coba:
```bash
php artisan migrate:rollback
php artisan migrate
```

Atau force migration (HATI-HATI - hanya untuk development):
```bash
php artisan migrate --force
```

## 📊 Auto-Calculation System:
Setelah migration sukses, sistem auto-calculation akan bekerja:
1. Teacher input `estimated_duration` per module (menit)
2. System otomatis hitung total per chapter
3. System otomatis hitung total per class
4. Duration tampil di courses dan course detail pages

## ✅ Testing:
Buat module baru untuk test auto-calculation:
1. Login sebagai teacher
2. Buat class baru
3. Buat chapter baru  
4. Buat module dengan `estimated_duration`
5. Check apakah duration terhitung otomatis
