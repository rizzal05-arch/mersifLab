# 📊 Teacher Content Management - Architecture Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    TEACHER CONTENT MANAGEMENT                   │
│                        (Main Hub)                                │
│                   /teacher/manage-content                        │
└──────────────────────────┬──────────────────────────────────────┘
                           │
           ┌───────────────┼───────────────┐
           │               │               │
      [Classes]      [Chapters]       [Modules]
           │               │               │
      CRUD (4)        CRUD (4)        CRUD (5)
      Reorder         Reorder         Reorder
           │               │               │
           └───────────────┼───────────────┘
                           │
                    [View Tracking]
                    [Publishing Control]
```

---

## Hierarchical Content Structure

```
TEACHER (User)
    │
    ├─ CLASS 1 (is_published: true)
    │   ├─ CHAPTER 1 (is_published: true)
    │   │   ├─ MODULE 1 (type: text) → Database
    │   │   ├─ MODULE 2 (type: document) → File (50MB max)
    │   │   └─ MODULE 3 (type: video) → File (500MB max) OR URL
    │   │
    │   └─ CHAPTER 2 (is_published: false)
    │       └─ MODULE 4 (type: text)
    │
    └─ CLASS 2 (is_published: false)
        └─ CHAPTER 3 (is_published: false)
            └─ MODULE 5 (type: video)
```

---

## User Interface Flow

```
┌──────────────┐
│   Profile    │
│    Page      │
└──────┬───────┘
       │
       v
┌──────────────────────────────────────────┐
│  "Manage Content" Link (Teacher Only)    │
└──────┬───────────────────────────────────┘
       │
       v
┌────────────────────────────────────────────────────────────┐
│   MANAGE CONTENT DASHBOARD                                 │
│  ┌─────────────────────────────────────────────────────┐  │
│  │ Stats: 5 Classes | 12 Chapters | 38 Modules        │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                            │
│  [New Class Button]                                        │
│                                                            │
│  ┌───────────────────────────────────────────────────┐   │
│  │ CLASS 1: Web Development 101                      │   │
│  │ 3 chapters | 12 modules | [Published]            │   │
│  │ [Edit] [Delete]                                   │   │
│  │                                                    │   │
│  │ CHAPTERS:                                          │   │
│  │ • Introduction [Add]                              │   │
│  │   - 4 modules ┌──────────────────┐                │   │
│  │               │  MODULE MANAGER  │                │   │
│  │ • HTML Basics [Add]               │ MODULE 1: Intro│   │
│  │   - 5 modules │ ┌──────────────┐  │ [Edit][Delete] │   │
│  │               │ │ + Add Module │  │                │   │
│  │ • CSS        [Add]  │ • Module 1 │  │ MODULE 2: Setup│   │
│  │   - 3 modules │ │ • Module 2 │  │ [Edit][Delete] │   │
│  │               │ │ • Module 3 │  │                │   │
│  │               └──────────────┘  │ [Close] [Add]  │   │
│  │               └──────────────────┘                │   │
│  └───────────────────────────────────────────────────┘   │
│                                                            │
│  [CLASS 2 CARD] ... [CLASS 3 CARD]                        │
└────────────────────────────────────────────────────────────┘
       │
       ├─ [Edit Class] → Edit form → Save
       ├─ [Edit Chapter] → Edit form → Save
       ├─ [Manage Modules] → Modal opens
       └─ [Add Module] → Type selection ──┬─────────┬──────┐
                                           │         │      │
                                           v         v      v
                                        Text      Document  Video
                                        Form      Upload    Upload/URL
                                        │         │         │
                                        └─────────┴─────────┘
                                             │
                                             v
                                        Save Module
```

---

## Authentication & Authorization Flow

```
┌─────────────────────────┐
│   User Access Request    │
│  to /teacher/manage-...  │
└────────┬────────────────┘
         │
         v
    ┌─────────────┐
    │ Auth Check? │
    └──┬──────┬──┘
       │      │
    [NO]    [YES]
       │      │
       │      v
       │  ┌──────────────┐
       │  │ Role Check?  │
       │  │ role:teacher │
       │  └──┬───────┬──┘
       │     │       │
       │ [NO] │    [YES]
       │     │       │
       │     v       v
       │  BLOCK   ┌──────────────────┐
       │          │ Policy Check?    │
       │          │ ContentPolicy    │
       │          └──┬──────┬────┘
       │             │      │
       │         [NO]│      │[YES]
       │             │      │
       └─────────────┴──────┴──────→ ALLOW / BLOCK
            [401 Unauthorized / 403 Forbidden]
```

---

## Database Schema Relationships

```
╔════════════════╗
║     USERS      ║
╠════════════════╣
║ id (PK)        ║
║ name           ║
║ email          ║
║ role (teacher) ║
║ ...            ║
╚════════════════╝
        │
        │ (hasMany)
        │ teacher_id FK
        │
        ▼
╔════════════════╗
║    CLASSES     ║
╠════════════════╣
║ id (PK)        ║
║ teacher_id (FK)║
║ name           ║
║ description    ║
║ is_published   ║
║ order          ║
║ timestamps     ║
╚════════════════╝
        │
        │ (hasMany)
        │ class_id FK
        │
        ▼
╔════════════════╗
║    CHAPTERS    ║
╠════════════════╣
║ id (PK)        ║
║ class_id (FK)  ║
║ title          ║
║ description    ║
║ is_published   ║
║ order          ║
║ timestamps     ║
╚════════════════╝
        │
        │ (hasMany)
        │ chapter_id FK
        │
        ▼
╔════════════════════════╗
║      MODULES           ║
╠════════════════════════╣
║ id (PK)                ║
║ chapter_id (FK)        ║
║ title                  ║
║ type (text/doc/video)  ║
║ content (for text)     ║
║ file_path (for files)  ║
║ video_url              ║
║ duration               ║
║ is_published           ║
║ view_count             ║
║ mime_type              ║
║ file_size              ║
║ order                  ║
║ timestamps             ║
╚════════════════════════╝
```

---

## File Upload Architecture

```
┌─────────────────────────────────────────┐
│        Module Form (Teacher)             │
│  Title: ____________                     │
│  Content: ☐ Text ◉ PDF ○ Video          │
└────────────┬────────────────────────────┘
             │
        ┌────┴─────────────────┐
        │                      │
        v                      v
    ┌────────┐          ┌──────────┐
    │ Validation         │ Validation
    │ - MIME: pdf        │ - MIME: mp4|avi
    │ - Size: 50MB       │ - Size: 500MB
    │ - Required: Yes    │ - Required: No (URL optional)
    └────┬───────┘       └──┬───────┘
         │                  │
         v                  v
    ┌────────────────────────────────┐
    │ Storage::disk('public')        │
    │ ->storeAs(                     │
    │   'modules/documents',         │
    │   filename.pdf                 │
    │ );                             │
    └────────────┬───────────────────┘
                 │
                 v
    ┌────────────────────────────────┐
    │ storage/app/public/            │
    │ ├─ modules/                    │
    │ │  ├─ documents/               │
    │ │  │  ├─ file-name_123456.pdf  │
    │ │  │  └─ guide_654321.pdf      │
    │ │  └─ videos/                  │
    │ │     ├─ video_123456.mp4      │
    │ │     └─ intro_654321.mp4      │
    │ └─ ...                         │
    └────────────┬───────────────────┘
                 │
                 v
    ┌────────────────────────────────┐
    │ Database Module Record          │
    │ - file_path: modules/...        │
    │ - file_name: original.pdf       │
    │ - file_size: 2048576            │
    │ - mime_type: application/pdf    │
    └────────────────────────────────┘
```

---

## Request-Response Cycle

```
TEACHER CREATING TEXT MODULE
────────────────────────────

1. GET /teacher/chapters/{id}/modules/create/text
   ├─ Controller: ModuleController@createText
   ├─ View: modules/create-text.blade.php
   └─ Response: Form HTML

2. POST /teacher/chapters/{id}/modules/text
   ├─ Validation: title required, content present
   ├─ Authorization: Can update chapter via policy
   ├─ Create: Module::create([...])
   ├─ Response: Redirect to manage-content
   └─ Message: "Module created successfully"

3. GET /teacher/manage-content
   ├─ Load: Teacher's classes with relationships
   ├─ Build: Hierarchical display
   └─ Show: New module in chapter


STUDENT VIEWING TEXT MODULE
────────────────────────────

1. GET /student/classes/{id}
   ├─ Check: is_published = true
   └─ Load: Chapters

2. GET /student/chapters/{id}/modules/{id}
   ├─ Check: is_published = true AND module visible
   ├─ Action: Module::incrementViewCount()
   └─ Response: Display module content

3. Display Module
   ├─ Text type: {!! module.content !!}
   ├─ Document type: <embed src="pdf">
   └─ Video type: <video src="mp4"> OR <iframe YouTube>
```

---

## Module Type Storage Strategies

```
┌─────────────────────────────────────────────────────────────┐
│              MODULE TYPES & STORAGE                         │
└─────────────────────────────────────────────────────────────┘

TEXT MODULE
───────────────────────────────────────
Type: text
Stored In: MySQL (content column)
Example: <h2>Chapter 1</h2><p>Content...</p>
Editor: TinyMCE (rich text)
Max Size: Unlimited (DB limit ~16MB)
Access: Direct from database
Advantage: Fast retrieval, no file storage
View: {!! $module->content !!}


DOCUMENT MODULE (PDF)
───────────────────────────────────────
Type: document
Stored In: File system (storage/public/modules/documents/)
Example: guide_1705701234.pdf
Editor: File upload form
Max Size: 50MB
Access: Via file path
Advantage: Keep course materials organized
View: <embed src="/storage/modules/documents/file.pdf">
Download: Available to students


VIDEO MODULE
───────────────────────────────────────
Type: video
Storage Option 1: File system (storage/public/modules/videos/)
  Example: lecture_1705701234.mp4
  Max Size: 500MB
  View: <video src="/storage/modules/videos/file.mp4">

Storage Option 2: External URL
  Example: https://youtube.com/watch?v=abc123
  View: <iframe src="youtube-url"></iframe>

Metadata: Duration field (optional)
Advantage: Flexibility for large videos
```

---

## Security Layers

```
┌──────────────────────────────────────────────────────────┐
│         SECURITY ARCHITECTURE                             │
└──────────────────────────────────────────────────────────┘

Layer 1: AUTHENTICATION
──────────────────────
├─ auth middleware
├─ Session-based
└─ User::authenticated()


Layer 2: ROLE-BASED ACCESS
──────────────────────────
├─ role:teacher middleware
├─ role:student middleware
└─ User::isTeacher() / isStudent()


Layer 3: AUTHORIZATION POLICIES
────────────────────────────────
├─ ContentPolicy::updateClass()
│  └─ Only teacher owner OR admin
├─ ContentPolicy::updateModule()
│  └─ Only chapter owner OR admin
└─ ContentPolicy::viewModule()
   └─ Published OR owner OR admin


Layer 4: REQUEST VALIDATION
────────────────────────────
├─ Form validation
├─ File type checking (MIME)
├─ File size limits
└─ Required field checks


Layer 5: CSRF PROTECTION
────────────────────────
├─ @csrf in forms
└─ VerifyCsrfToken middleware


Layer 6: DATA PROTECTION
────────────────────────
├─ Foreign keys (referential integrity)
├─ Cascade deletes (data consistency)
├─ Encrypted fields (if needed)
└─ Soft deletes (if needed)
```

---

## Controller Action Flow

```
ClassController
├─ index()           → List teacher's classes
├─ create()          → Show class form
├─ store()           → Save new class
├─ edit()            → Show edit form
├─ update()          → Update class
├─ destroy()         → Delete class
└─ manageContent()   → Dashboard (ALL CONTENT)

ChapterController
├─ index()           → List chapters of class
├─ create()          → Show chapter form
├─ store()           → Save new chapter
├─ edit()            → Show edit form
├─ update()          → Update chapter
├─ destroy()         → Delete chapter
└─ reorder()         → Reorder chapters

ModuleController
├─ create()          → Type selector
├─ createText()      → Text form
├─ createDocument()  → Document form
├─ createVideo()     → Video form
├─ storeText()       → Save text module
├─ storeDocument()   → Save PDF module
├─ storeVideo()      → Save video module
├─ edit()            → Edit form (type-aware)
├─ update()          → Update module
├─ destroy()         → Delete module
└─ reorder()         → Reorder modules
```

---

## Deployment Architecture

```
┌────────────────────────────────────────────────┐
│         PRODUCTION DEPLOYMENT                   │
└────────────────────────────────────────────────┘

1. CODE
   ├─ Git clone / pull
   ├─ composer install
   └─ npm install & build

2. DATABASE
   ├─ php artisan migrate
   └─ Verify: 3 new tables

3. STORAGE
   ├─ chmod -R 775 storage
   ├─ php artisan storage:link
   └─ Verify: /storage → storage/app/public

4. CACHE
   ├─ php artisan config:clear
   ├─ php artisan cache:clear
   └─ php artisan view:clear

5. VERIFICATION
   ├─ Login as teacher
   ├─ Create class/chapter/module
   ├─ Upload file
   ├─ Verify in storage/
   └─ Login as student & view

6. MONITORING
   ├─ Check logs: storage/logs
   ├─ Monitor uploads: storage/app/public
   └─ Track errors: Laravel error handling
```

---

**Diagram Version:** 1.0  
**Updated:** January 20, 2026  
**Status:** Complete ✅
