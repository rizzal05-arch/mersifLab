# 🚀 Quick Start Guide - Teacher Content Management

## In 5 Minutes

### 1. Verify Everything Works
```bash
cd d:\laragon\www\mersiflab
php artisan serve
```
Then visit: http://127.0.0.1:8000

---

## Teacher's Quick Start

### Step 1: Login as Teacher
1. Register with email
2. Select "Teacher" role during signup/registration
3. Login with teacher credentials

### Step 2: Go to Content Management
- Click **Profile** in top navigation
- Click **"Manage Content"** in sidebar
- OR direct URL: `http://yoursite/teacher/manage-content`

### Step 3: Create a Class (Course)
1. Click blue **"New Class"** button
2. Enter:
   - Class name (required)
   - Description (optional)
   - Order (default: 0)
3. Check "Publish immediately" if ready
4. Click **"Create Class"**

### Step 4: Add a Chapter
1. On class card, scroll to chapters section
2. Click **"Add"** button next to "Chapters"
3. Enter:
   - Chapter title (required)
   - Description (optional)
   - Order (default: last)
4. Check "Publish" if ready
5. Click **"Create Chapter"**

### Step 5: Add a Module
1. Click **folder icon** on any chapter (opens modal)
2. Click **"Add New Module"** button
3. Choose module type:
   - **📝 Text** - Rich text content
   - **📄 Document** - Upload PDF (max 50MB)
   - **🎥 Video** - Upload or embed YouTube
4. Fill in details and save

---

## Common Tasks

### Edit Module
1. Go to Manage Content
2. Click folder icon on chapter
3. Click **pencil icon** on module
4. Update content and save

### Delete Module
1. Go to Manage Content
2. Click folder icon on chapter
3. Click **trash icon** on module
4. Confirm deletion

### Publish Content
- Check **"Publish"** box in create/edit forms
- Students only see published content
- Can unpublish anytime by editing

### View Student Perspective
- Go to student account
- Browse your published classes
- View modules (read-only)

---

## File Upload Limits

| Type | Format | Max Size | Storage |
|------|--------|----------|---------|
| **Text** | HTML | N/A | Database |
| **Document** | PDF | 50 MB | storage/public |
| **Video** | MP4, MOV, AVI | 500 MB | storage/public |

---

## URL Reference

| Task | URL | Type |
|------|-----|------|
| Manage Content | `/teacher/manage-content` | GET |
| Create Class | `/teacher/classes/create` | GET |
| Create Chapter | `/teacher/classes/{id}/chapters/create` | GET |
| Create Module | `/teacher/chapters/{id}/modules/create` | GET |
| Edit Module | `/teacher/chapters/{id}/modules/{id}/edit` | GET |

---

## Troubleshooting

### Can't see "Manage Content"?
✓ Make sure you're logged in as teacher  
✓ Go to Profile page first  
✓ Check browser address bar shows `/profile`  

### File won't upload?
✓ Check file size (50MB PDF, 500MB video)  
✓ Verify file format (PDF for docs, MP4 for videos)  
✓ Check storage permissions: `chmod -R 775 storage`  

### Content not showing to students?
✓ Verify "Publish" is checked  
✓ Check parent chapter/class is also published  
✓ Wait a moment for page cache to clear  

### Can't delete?
✓ You might not be the owner  
✓ Ask admin or use another teacher account  
✓ Confirm deletion in the popup  

---

## Database Check

### Verify Data
```bash
# SSH into database
php artisan tinker

# Check classes
App\Models\ClassModel::count()

# Check chapters
App\Models\Chapter::count()

# Check modules
App\Models\Module::count()
```

---

## File Organization

### Where Files Are Stored
```
storage/app/public/
├── modules/
│   ├── documents/     (PDFs)
│   └── videos/        (MP4s, etc.)
```

### How to Access Uploaded Files
```
/storage/modules/documents/file_name.pdf
/storage/modules/videos/video_name.mp4
```

---

## Authorization Quick Guide

| Operation | Teacher | Student | Admin |
|-----------|---------|---------|-------|
| Create Class | ✅ Own | ❌ | ✅ |
| Edit Class | ✅ Own | ❌ | ✅ |
| Delete Class | ✅ Own | ❌ | ✅ |
| View Published | ✅ | ✅ | ✅ |
| View Unpublished | ✅ Own | ❌ | ✅ |
| Edit Module | ✅ Own | ❌ | ✅ |

---

## Feature Highlights

✨ **Three-Level Hierarchy** - Class → Chapter → Module  
✨ **Multiple Content Types** - Text, PDF, Video  
✨ **Publication Control** - Publish/unpublish anytime  
✨ **File Uploads** - Secure with validation  
✨ **View Tracking** - See module engagement  
✨ **Organization** - Reorder content logically  
✨ **Role-Based** - Teachers only access management  
✨ **Responsive** - Works on mobile/tablet/desktop  

---

## Tips & Best Practices

💡 **Naming:** Use clear, descriptive names for classes and chapters  
💡 **Organization:** Group related content in chapters  
💡 **Publishing:** Create in Draft, publish when ready  
💡 **Content Mix:** Combine text, PDFs, and videos for engagement  
💡 **File Size:** Optimize videos before uploading  
💡 **Backup:** Download important PDFs as backup  

---

## Need More Help?

See detailed documentation:
- `TEACHER_CONTENT_MANAGEMENT_GUIDE.md` - Full technical guide
- `TEACHER_CONTENT_FEATURES.md` - All features explained
- `IMPLEMENTATION_CHECKLIST.md` - Complete checklist

---

**Version:** 1.0  
**Last Updated:** January 20, 2026  
**Status:** Production Ready ✅
