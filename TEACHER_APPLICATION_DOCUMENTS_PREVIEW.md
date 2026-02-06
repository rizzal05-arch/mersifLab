# Teacher Application - Documents Preview Feature

## Overview
Fitur preview dokumen yang diperlukan telah ditambahkan pada form aplikasi guru. User dapat melihat status upload setiap dokumen yang diperlukan dan melakukan preview dokumen sebelum submit aplikasi.

## Features

### 1. **Documents Checklist Preview** (Step 3)
Ditampilkan di bagian atas form upload dokumen dengan informasi lengkap:
- **KTP/ID Card** - Identity verification document
- **Teaching Certificate** - Professional teaching certificate  
- **Institution ID Card** - Educational institution identification
- **Portfolio** - Your work portfolio or teaching materials

### 2. **Upload Status Indicators**
Setiap dokumen menampilkan badge status:
- 🟡 **Not Uploaded** (Yellow) - Dokumen belum diupload
- 🟢 **Uploaded** (Green) - Dokumen sudah diupload

Status badge dapat diklik untuk preview dokumen.

### 3. **File Preview Modal**
Ketika user mengklik status badge "Uploaded", modal preview akan muncul dengan:
- **Image files** (JPG, PNG, PNG): Tampil preview langsung
- **PDF files**: Tampil info file PDF dengan tombol download
- **ZIP files**: Tampil info file ZIP dengan tombol download
- **Word documents** (DOC, DOCX): Tampil info file Word dengan tombol download
- **Other files**: Tampil info file dengan tombol download

### 4. **File Information Preview**
Setiap file preview menampilkan:
- Nama file lengkap
- Ukuran file dalam KB
- Opsi download/view file

### 5. **Enhanced Upload Experience**
- **Remove File Button**: User dapat menghapus file yang sudah diupload dengan klik tombol X
- **File Name Display**: Nama file ditampilkan dengan format yang user-friendly
- **Visual Feedback**: Checklist item berubah style saat file diupload
- **Color Coding**: Icon dan background berubah warna sesuai status

### 6. **Validation Before Submission**
- Form akan memvalidasi bahwa SEMUA dokumen sudah diupload sebelum submit
- Jika ada dokumen yang belum diupload, form akan:
  - Highlight missing files dengan border merah
  - Tampilkan error alert
  - Prevent form submission

### 7. **Info Alert**
Ditampilkan pesan informatif:
> "Before submitting: Make sure all documents are uploaded and clearly visible. You can preview your documents by clicking on the status."

## User Interface

### Documents Checklist Section
```
╔════════════════════════════════════════════════════╗
║  Upload Status                                     ║
║                                                    ║
║  🆔 KTP/ID Card                     [Not Uploaded] ║
║     Identity verification document                 ║
║                                                    ║
║  📜 Teaching Certificate            [Not Uploaded] ║
║     Professional teaching certificate             ║
║                                                    ║
║  🏫 Institution ID Card             [Not Uploaded] ║
║     Educational institution identification        ║
║                                                    ║
║  💼 Portfolio                        [Not Uploaded] ║
║     Your work portfolio or teaching materials     ║
╚════════════════════════════════════════════════════╝
```

### After File Upload
```
╔════════════════════════════════════════════════════╗
║  Upload Status                                     ║
║                                                    ║
║  🆔 KTP/ID Card                     ✅ [Uploaded] ║
║     Identity verification document                 ║
║                                                    ║
║  📜 Teaching Certificate            [Not Uploaded] ║
║     Professional teaching certificate             ║
╚════════════════════════════════════════════════════╝
```

## JavaScript Functions Added

### `updateChecklistStatus(inputId, isUploaded, file)`
Update status badge dan checklist item saat file diupload/dihapus.

### `previewFile(file, inputId)`
Membuka modal preview untuk file yang dipilih dengan format yang sesuai.

### `validateDocuments()`
Validasi semua dokumen yang diperlukan sebelum form submission.

## CSS Classes Added

```css
.documents-checklist          /* Container checklist */
.checklist-title             /* Judul checklist */
.checklist-items             /* Container items */
.checklist-item              /* Setiap item checklist */
.checklist-item.uploaded     /* Item saat sudah upload */
.checklist-icon              /* Icon dokumen */
.checklist-content           /* Content/deskripsi */
.checklist-status            /* Status section */
.status-badge                /* Badge status */
.status-pending              /* Status belum upload */
.status-uploaded             /* Status sudah upload */
.file-preview-content        /* Preview content container */
.file-preview-container      /* Modal preview container */
.file-upload-box.missing-file /* Missing file highlight */
.btn-remove-file             /* Tombol remove file */
```

## Browser Compatibility
- ✅ Chrome/Edge (Latest)
- ✅ Firefox (Latest)
- ✅ Safari (Latest)
- ✅ Mobile browsers

## Accessibility Features
- 🔊 Proper icon FontAwesome untuk visual indicators
- ♿ Color-coded status untuk clear differentiation
- 💬 Descriptive text untuk setiap dokumen
- 🎯 Clickable status badges untuk preview

## How It Works

### User Flow
1. User mengisi form aplikasi guru step 1 & 2
2. Masuk ke Step 3: Required Documents
3. **Lihat checklist dokumen yang diperlukan** dengan status "Not Uploaded"
4. Upload setiap dokumen melalui file input
5. **Status checklist otomatis update** menjadi "Uploaded" dengan green badge
6. **Klik status badge** untuk preview dokumen sebelum submit
7. **Remove tombol** tersedia jika ingin mengganti file
8. Semua dokumen harus uploaded sebelum bisa submit
9. Submit aplikasi

### Preview Interaction
1. File di-upload → Status badge muncul dengan "Uploaded"
2. User klik status badge → Modal preview terbuka
3. Modal menampilkan preview sesuai file type
4. User bisa download atau close preview
5. User bisa remove file atau continue dengan dokumen lain

## Testing Checklist
- [ ] Upload KTP image (JPG/PNG) → Preview tampil dengan gambar
- [ ] Upload Teaching Certificate (PDF) → Preview tampil file info
- [ ] Upload Portfolio (ZIP/DOC) → Preview tampil file info
- [ ] Remove file → Status kembali "Not Uploaded"
- [ ] Click status badge → Preview modal muncul
- [ ] Try submit tanpa upload semua files → Error alert tampil
- [ ] Upload semua files → Can submit successfully

## Future Enhancements
- Drag & drop file upload support
- Multiple file upload
- Progress bar untuk large files
- File validation (size/format) sebelum upload
- Thumbnail preview untuk multiple image files
- Offline mode untuk melihat uploaded files
