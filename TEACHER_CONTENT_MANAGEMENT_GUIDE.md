# 📚 Teacher Content Management System - Implementation Guide

## 📋 Overview

Implementasi lengkap untuk Teacher Content Management System yang memungkinkan teacher membuat dan manage learning content dengan struktur:

```
Class (Kursus)
 └── Chapter (Bab)
      └── Module (Konten)
          - Text (Rich Text)
          - Document (PDF)
          - Video (Upload or URL)
```

---

## 📁 File Structure

### Database Migrations
```
database/migrations/
├── 2026_01_20_150000_create_classes_table.php
├── 2026_01_20_150100_create_chapters_table.php
└── 2026_01_20_150200_create_modules_table.php
```

### Models
```
app/Models/
├── ClassModel.php (NEW)
├── Chapter.php (NEW)
├── Module.php (UPDATED)
└── User.php (UPDATED - added classes relation)
```

### Controllers
```
app/Http/Controllers/Teacher/
├── ClassController.php (NEW)
├── ChapterController.php (NEW)
└── ModuleController.php (NEW)
```

### Policies
```
app/Policies/
└── ContentPolicy.php (NEW)
```

### Views
```
resources/views/teacher/
├── classes/
│   ├── index.blade.php (list classes)
│   ├── create.blade.php (form create)
│   └── edit.blade.php (manage chapters)
├── chapters/
│   ├── index.blade.php (list chapters)
│   ├── create.blade.php (form create)
│   └── edit.blade.php (form edit)
└── modules/
    ├── create.blade.php (choose module type)
    ├── create-text.blade.php (create text module)
    ├── create-document.blade.php (create PDF module)
    ├── create-video.blade.php (create video module)
    ├── edit-text.blade.php (edit text)
    ├── edit-document.blade.php (edit document)
    └── edit-video.blade.php (edit video)
```

---

## 🗄️ Database Schema

### classes table
```sql
CREATE TABLE classes (
    id BIGINT PRIMARY KEY,
    teacher_id BIGINT NOT NULL (FK: users),
    name VARCHAR(255) NOT NULL,
    description LONGTEXT NULLABLE,
    is_published BOOLEAN DEFAULT 0,
    order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(teacher_id),
    INDEX(is_published),
    INDEX(order)
);
```

### chapters table
```sql
CREATE TABLE chapters (
    id BIGINT PRIMARY KEY,
    class_id BIGINT NOT NULL (FK: classes),
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NULLABLE,
    is_published BOOLEAN DEFAULT 0,
    order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(class_id),
    INDEX(is_published),
    INDEX(order)
);
```

### modules table
```sql
CREATE TABLE modules (
    id BIGINT PRIMARY KEY,
    chapter_id BIGINT NOT NULL (FK: chapters),
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL (text, document, video),
    
    -- Content fields
    content LONGTEXT NULLABLE (for text type),
    file_path VARCHAR(255) NULLABLE (for document/video),
    file_name VARCHAR(255) NULLABLE,
    video_url VARCHAR(255) NULLABLE (for video type),
    duration INT NULLABLE,
    
    -- Metadata
    order INT DEFAULT 0,
    is_published BOOLEAN DEFAULT 0,
    view_count INT DEFAULT 0,
    mime_type VARCHAR(255) NULLABLE,
    file_size INT NULLABLE,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(chapter_id),
    INDEX(type),
    INDEX(is_published),
    INDEX(order)
);
```

---

## 🔄 Relationships

### User → Classes (Teacher)
```php
// User.php
public function classes()
{
    return $this->hasMany(ClassModel::class, 'teacher_id');
}
```

### Class → Chapters
```php
// ClassModel.php
public function chapters(): HasMany
{
    return $this->hasMany(Chapter::class, 'class_id')->orderBy('order');
}
```

### Chapter → Modules
```php
// Chapter.php
public function modules(): HasMany
{
    return $this->hasMany(Module::class, 'chapter_id')->orderBy('order');
}
```

### Class ← Teacher (reverse)
```php
// ClassModel.php
public function teacher(): BelongsTo
{
    return $this->belongsTo(User::class, 'teacher_id');
}
```

---

## 🔐 Authorization (ContentPolicy)

### Class Policies
- **viewAny()** - Authenticated users
- **viewClass()** - Published classes OR teacher owner OR admin
- **createClass()** - Teacher or Admin
- **updateClass()** - Teacher owner or Admin
- **deleteClass()** - Teacher owner or Admin

### Chapter Policies
- **viewChapter()** - Can view parent class
- **createChapter()** - Can update parent class
- **updateChapter()** - Can update parent class
- **deleteChapter()** - Can update parent class

### Module Policies
- **viewModule()** - Published modules OR teacher owner OR admin
- **createModule()** - Can update parent chapter
- **updateModule()** - Can update parent chapter
- **deleteModule()** - Can update parent chapter

---

## 🛣️ Routes

### Class Management
```
GET    /teacher/classes                    ClassController@index
GET    /teacher/classes/create             ClassController@create
POST   /teacher/classes                    ClassController@store
GET    /teacher/classes/{class}/edit       ClassController@edit
PUT    /teacher/classes/{class}            ClassController@update
DELETE /teacher/classes/{class}            ClassController@destroy
```

### Chapter Management
```
GET    /teacher/classes/{class}/chapters                   ChapterController@index
GET    /teacher/classes/{class}/chapters/create            ChapterController@create
POST   /teacher/classes/{class}/chapters                   ChapterController@store
GET    /teacher/classes/{class}/chapters/{chapter}/edit    ChapterController@edit
PUT    /teacher/classes/{class}/chapters/{chapter}         ChapterController@update
DELETE /teacher/classes/{class}/chapters/{chapter}         ChapterController@destroy
POST   /teacher/chapters/reorder                           ChapterController@reorder
```

### Module Management
```
GET    /teacher/chapters/{chapter}/modules/create              ModuleController@create
GET    /teacher/chapters/{chapter}/modules/create/text         ModuleController@createText
GET    /teacher/chapters/{chapter}/modules/create/document     ModuleController@createDocument
GET    /teacher/chapters/{chapter}/modules/create/video        ModuleController@createVideo

POST   /teacher/chapters/{chapter}/modules/text                ModuleController@storeText
POST   /teacher/chapters/{chapter}/modules/document            ModuleController@storeDocument
POST   /teacher/chapters/{chapter}/modules/video               ModuleController@storeVideo

GET    /teacher/chapters/{chapter}/modules/{module}/edit       ModuleController@edit
PUT    /teacher/chapters/{chapter}/modules/{module}            ModuleController@update
DELETE /teacher/chapters/{chapter}/modules/{module}            ModuleController@destroy

POST   /teacher/modules/reorder                                ModuleController@reorder
```

### Content Management Hub
```
GET    /teacher/manage-content            ClassController@manageContent
```

---

## 📤 File Upload Handling

### Configuration (config/filesystems.php)
```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

### Storage Strategy

**PDF Files:**
```
storage/app/public/modules/documents/
├── module-title_timestamp.pdf
└── another-module_timestamp.pdf
```

**Video Files:**
```
storage/app/public/modules/videos/
├── lesson-video_timestamp.mp4
└── tutorial-video_timestamp.mp4
```

### File Upload Best Practices

**1. Validation:**
```php
// Document (PDF)
'file' => 'required|file|mimes:pdf|max:50000' // 50MB

// Video
'file' => 'required|file|mimes:mp4,avi,mov,wmv|max:500000' // 500MB
```

**2. Storage:**
```php
$file = $request->file('file');
$fileName = Str::slug($title) . '_' . time() . '.' . $file->getClientOriginalExtension();
$path = $file->storeAs('modules/documents', $fileName, 'public');

// Store in DB
$module->file_path = $path;
$module->file_name = $file->getClientOriginalName();
$module->file_size = $file->getSize();
$module->mime_type = $file->getMimeType();
```

**3. Retrieval:**
```php
// Generate URL
$url = Storage::disk('public')->url($module->file_path);

// Download
return Storage::disk('public')->download($module->file_path);
```

**4. Deletion:**
```php
if ($module->file_path && Storage::disk('public')->exists($module->file_path)) {
    Storage::disk('public')->delete($module->file_path);
}
```

### Security Considerations

✅ **Use authenticated routes** - Only teacher can upload  
✅ **Validate file types** - Whitelist specific MIME types  
✅ **Check file size** - Implement reasonable limits  
✅ **Rename files** - Avoid using original names  
✅ **Use private storage** - For sensitive files  
✅ **Virus scan** - Consider integrating with antivirus API  
✅ **Rate limiting** - Prevent abuse

---

## 💻 Usage Examples

### Create Class
```php
$class = auth()->user()->classes()->create([
    'name' => 'Web Development 101',
    'description' => 'Learn web development from scratch',
    'order' => 1,
]);
```

### Create Chapter
```php
$chapter = $class->chapters()->create([
    'title' => 'Introduction to HTML',
    'description' => 'Learn the basics of HTML',
    'order' => 1,
]);
```

### Create Text Module
```php
$module = $chapter->modules()->create([
    'title' => 'HTML Fundamentals',
    'type' => Module::TYPE_TEXT,
    'content' => '<h2>Welcome</h2><p>This is HTML content...</p>',
    'order' => 1,
]);
```

### Create PDF Module
```php
$file = $request->file('file');
$path = $file->storeAs('modules/documents', 'guide.pdf', 'public');

$module = $chapter->modules()->create([
    'title' => 'HTML Style Guide',
    'type' => Module::TYPE_DOCUMENT,
    'file_path' => $path,
    'file_name' => $file->getClientOriginalName(),
    'file_size' => $file->getSize(),
]);
```

### Create Video Module
```php
// From URL
$module = $chapter->modules()->create([
    'title' => 'Introduction Video',
    'type' => Module::TYPE_VIDEO,
    'video_url' => 'https://youtube.com/watch?v=...',
    'duration' => 3600,
]);

// From Upload
$file = $request->file('file');
$path = $file->storeAs('modules/videos', 'intro.mp4', 'public');

$module = $chapter->modules()->create([
    'title' => 'Introduction Video',
    'type' => Module::TYPE_VIDEO,
    'file_path' => $path,
    'file_name' => $file->getClientOriginalName(),
    'file_size' => $file->getSize(),
]);
```

### Query Content
```php
// Get all classes from teacher
$classes = auth()->user()->classes()->with('chapters.modules')->get();

// Get published modules
$publishedModules = Module::published()->get();

// Get modules by type
$textModules = Module::ofType(Module::TYPE_TEXT)->get();

// Get total modules in class
$totalModules = $class->total_modules; // Uses custom accessor
```

---

## 🔄 Authorization in Blade

### Checking Authorization
```blade
@can('createClass', auth()->user())
    <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
        Create New Class
    </a>
@endcan

@can('updateModule', $module)
    <a href="{{ route('teacher.modules.edit', [$chapter, $module]) }}" class="btn btn-sm btn-warning">
        Edit
    </a>
@endcan

@cannot('deleteModule', $module)
    <button class="btn btn-sm btn-danger" disabled>Can't Delete</button>
@endcannot
```

---

## 📱 Module Display for Students

### Student View (read-only)
```php
// StudentController.php
public function viewModule(Module $module)
{
    $this->authorize('viewModule', $module);
    
    $module->incrementViewCount();
    
    return view('student.module-view', compact('module'));
}
```

### Rendering Module Content
```blade
@if($module->type === 'text')
    <div class="module-content">
        {!! $module->content !!}
    </div>
@elseif($module->type === 'document')
    <embed src="{{ Storage::disk('public')->url($module->file_path) }}" type="application/pdf" width="100%" height="600px">
@elseif($module->type === 'video')
    @if($module->file_path)
        <video width="100%" controls>
            <source src="{{ Storage::disk('public')->url($module->file_path) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
        <iframe width="100%" height="600" src="{{ $module->video_url }}" frameborder="0" allowfullscreen></iframe>
    @endif
@endif
```

---

## 🚀 Implementation Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Register ContentPolicy in AppServiceProvider
- [ ] Publish storage: `php artisan storage:link`
- [ ] Test class creation
- [ ] Test chapter creation
- [ ] Test module creation (text)
- [ ] Test module creation (PDF upload)
- [ ] Test module creation (video upload)
- [ ] Test module creation (video URL)
- [ ] Test authorization (student cannot create)
- [ ] Test file upload validation
- [ ] Test file deletion
- [ ] Test student view access
- [ ] Setup TinyMCE for rich text editing
- [ ] Configure file size limits

---

## 🔧 Configuration

### TinyMCE Setup (Rich Text Editor)
```html
<!-- In create-text.blade.php -->
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 400,
        plugins: ['link', 'image', 'code', 'lists', 'table'],
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code'
    });
</script>
```

### Storage Link
```bash
# Create symbolic link for public file access
php artisan storage:link
```

---

## 🐛 Troubleshooting

### Files not uploading
- Check storage directory permissions: `chmod -R 775 storage`
- Ensure storage link exists: `php artisan storage:link`
- Check max file size in php.ini

### Images not showing in text
- Use absolute URLs: `{{ Storage::disk('public')->url('...') }}`
- Check file permissions
- Verify storage disk configuration

### Authorization errors
- Register policy in AppServiceProvider
- Clear config cache: `php artisan config:clear`
- Verify middleware is applied to routes

---

**Status:** ✅ Complete Implementation  
**Version:** 1.0  
**Last Updated:** 20 Januari 2026

