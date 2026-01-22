# Logika Sistem Notifikasi

## Alur Notifikasi Setelah Admin Melakukan Aksi

### 1. **Course/Chapter Dinonaktifkan (Toggle Status)**

**Alur:**
1. Admin mengklik tombol "Suspend" pada course/chapter
2. Sistem mengubah `is_published = false` di database
3. Sistem membuat record notifikasi di tabel `notifications`:
   - `type`: 'course_suspended' atau 'chapter_suspended'
   - `title`: 'Course Dinonaktifkan' atau 'Chapter Dinonaktifkan'
   - `message`: Pesan detail tentang course/chapter yang dinonaktifkan
   - `user_id`: ID teacher yang memiliki course/chapter tersebut
   - `is_read`: false (belum dibaca)
4. Course/chapter langsung tidak terlihat di:
   - Student view (karena filter `where('is_published', true)`)
   - Teacher view untuk course yang bukan miliknya
   - Public course listing
5. Teacher yang memiliki course/chapter tersebut:
   - Masih bisa melihat course/chapter di dashboard mereka (karena filter `byTeacher`)
   - Akan melihat efek visual nonaktif (opacity/grayscale)
   - Menerima notifikasi di dashboard/profile mereka

**Efek Visual untuk Course/Chapter Nonaktif:**
- Thumbnail: `opacity: 0.5; filter: grayscale(100%);`
- Nama course/chapter: `opacity: 0.6;`
- Badge status: Menampilkan "Draft" atau "Suspended"

### 2. **Course/Chapter Dihapus (Remove)**

**Alur:**
1. Admin mengklik tombol "Delete" pada course/chapter
2. Sistem membuat notifikasi SEBELUM menghapus:
   - `type`: 'course_deleted' atau 'chapter_deleted'
   - `title`: 'Course Dihapus' atau 'Chapter Dihapus'
   - `message`: Pesan bahwa course/chapter telah dihapus
3. Sistem menghapus course/chapter dari database (cascade delete untuk chapters/modules)
4. Course/chapter langsung hilang dari semua view:
   - Student view
   - Teacher view
   - Admin view
   - Public listing
5. Teacher yang memiliki course/chapter tersebut:
   - Tidak bisa lagi melihat course/chapter
   - Menerima notifikasi bahwa course/chapter telah dihapus
   - Bisa melihat notifikasi di dashboard/profile mereka

### 3. **Apa yang Bisa Dilakukan Teacher Setelah Menerima Notifikasi?**

**Untuk Course/Chapter yang Dinonaktifkan:**
1. **Lihat Notifikasi**: Teacher login dan melihat notifikasi di dashboard/profile
2. **Baca Detail**: Klik notifikasi untuk melihat detail mengapa course/chapter dinonaktifkan
3. **Edit Course/Chapter**: 
   - Teacher bisa mengedit course/chapter untuk memperbaiki masalah
   - Setelah diperbaiki, teacher bisa request approval ke admin
   - Atau admin bisa langsung mengaktifkan kembali
4. **Hubungi Admin**: Teacher bisa menghubungi admin untuk klarifikasi
5. **Mark as Read**: Teacher bisa mark notifikasi sebagai sudah dibaca

**Untuk Course/Chapter yang Dihapus:**
1. **Lihat Notifikasi**: Teacher melihat notifikasi bahwa course/chapter telah dihapus
2. **Klarifikasi**: Teacher bisa menghubungi admin untuk klarifikasi mengapa dihapus
3. **Buat Ulang**: Jika perlu, teacher bisa membuat course/chapter baru
4. **Mark as Read**: Teacher bisa mark notifikasi sebagai sudah dibaca

### 4. **Best Practices untuk Implementasi Notifikasi**

**Di Sidebar/Dashboard Teacher:**
```php
// Tampilkan badge dengan jumlah notifikasi belum dibaca
$unreadCount = auth()->user()->unreadNotificationsCount();
```

**Di Halaman Notifikasi:**
- List semua notifikasi (read dan unread)
- Highlight notifikasi yang belum dibaca
- Tombol "Mark as Read" untuk setiap notifikasi
- Tombol "Mark All as Read"
- Filter berdasarkan type (course_suspended, course_deleted, dll)

**Real-time Updates (Opsional):**
- Bisa menggunakan Laravel Broadcasting untuk real-time notification
- Atau polling dengan AJAX untuk update notifikasi tanpa refresh

### 5. **Contoh Implementasi View Notifikasi**

```blade
@foreach(auth()->user()->notifications()->latest()->get() as $notification)
    <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
        <h5>{{ $notification->title }}</h5>
        <p>{{ $notification->message }}</p>
        <small>{{ $notification->created_at->diffForHumans() }}</small>
        @if(!$notification->is_read)
            <button onclick="markAsRead({{ $notification->id }})">Mark as Read</button>
        @endif
    </div>
@endforeach
```

### 6. **Kesimpulan**

**Logika setelah notifikasi dikirim:**
1. ✅ Notifikasi tersimpan di database
2. ✅ Teacher bisa melihat notifikasi di dashboard/profile
3. ✅ Teacher bisa melakukan action (edit, hubungi admin, dll)
4. ✅ Course/chapter yang dinonaktifkan tidak terlihat di student view
5. ✅ Course/chapter yang dihapus hilang dari semua view
6. ✅ Teacher masih bisa melihat course/chapter mereka yang dinonaktifkan (dengan efek visual)
7. ✅ Teacher tidak bisa melihat course/chapter yang sudah dihapus

**Next Steps untuk Implementasi Lengkap:**
1. Buat halaman notifikasi untuk teacher
2. Tambahkan badge notifikasi di sidebar/navbar
3. Implementasi mark as read functionality
4. (Opsional) Real-time notification dengan Laravel Broadcasting
