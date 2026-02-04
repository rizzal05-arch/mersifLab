@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📝 Create Text Module</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.modules.store.text', $chapter) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Module Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                            <small class="form-text text-muted">
                                Gunakan editor WYSIWYG untuk formatting: bold, italic, heading, numbering, bullet points, links, images, tables, dan lainnya.
                            </small>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estimated_duration" class="form-label">Estimasi Durasi (menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                   id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration') }}" min="1" placeholder="Contoh: 30" required>
                            <small class="form-text text-muted">
                                Estimasi waktu yang dibutuhkan siswa untuk menyelesaikan module ini
                            </small>
                            @error('estimated_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Module</button>
                            <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include TinyMCE from different CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    console.log('TinyMCE script loading...');
    
    // Check if TinyMCE loaded
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE failed to load from CDN');
        // Fallback: make textarea look like editor
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('#content');
            if (textarea) {
                textarea.style.minHeight = '500px';
                textarea.style.fontSize = '14px';
                textarea.style.lineHeight = '1.6';
                textarea.style.padding = '12px';
                textarea.style.border = '1px solid #ddd';
                textarea.style.borderRadius = '4px';
                textarea.style.fontFamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';
            }
        });
    } else {
        console.log('TinyMCE loaded successfully, initializing...');
        
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#content',
                height: 500,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic underline strikethrough | ' +
                         'alignleft aligncenter alignright alignjustify | ' +
                         'bullist numlist outdent indent | ' +
                         'forecolor backcolor removeformat | ' +
                         'link image media table | ' +
                         'preview code fullscreen | help',
                toolbar_mode: 'sliding',
                block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
                content_style: 'body { font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; font-size:14px; line-height:1.6; }',
                branding: false,
                promotion: false,
                menubar: 'file edit view insert format tools table help',
                
                // Fix form submission
                setup: function(editor) {
                    console.log('TinyMCE setup called');
                    editor.on('init', function() {
                        console.log('TinyMCE initialized successfully');
                    });
                    editor.on('error', function(e) {
                        console.error('TinyMCE error:', e);
                    });
                }
            });
            
            // Handle form submission properly
            const form = document.querySelector('form[action*="store.text"]');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            console.log('Looking for form with action containing "store.text"');
            console.log('Form found:', !!form);
            console.log('Submit button found:', !!submitBtn);
            
            if (form) {
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
            }
            
            if (form && submitBtn) {
                console.log('Form and submit button found, adding event listener');
                
                form.addEventListener('submit', function(e) {
                    console.log('Form submission triggered');
                    console.log('Form action:', form.action);
                    
                    // Sync TinyMCE content to textarea
                    const editor = tinymce.get('content');
                    if (editor) {
                        console.log('Syncing TinyMCE content...');
                        editor.save();
                        console.log('Content synced to textarea');
                    }
                    
                    // Let the form submit normally
                    return true;
                });
                
                // Also handle button click as fallback
                submitBtn.addEventListener('click', function(e) {
                    console.log('Submit button clicked');
                    console.log('Form action:', form.action);
                    
                    // Sync TinyMCE content to textarea
                    const editor = tinymce.get('content');
                    if (editor) {
                        console.log('Syncing TinyMCE content from button click...');
                        editor.save();
                        console.log('Content synced to textarea');
                        
                        // Check textarea content
                        const textarea = document.querySelector('#content');
                        if (textarea) {
                            console.log('Textarea content length:', textarea.value.length);
                            console.log('Textarea content preview:', textarea.value.substring(0, 100) + '...');
                        }
                    }
                    
                    // Submit the form
                    form.submit();
                });
            } else {
                console.log('Form or submit button not found');
                console.log('All forms on page:', document.querySelectorAll('form').length);
                console.log('All submit buttons:', document.querySelectorAll('button[type="submit"]').length);
                
                // Try alternative selectors
                const altForm = document.querySelector('form');
                const altSubmit = document.querySelector('button[type="submit"]');
                console.log('Alternative form found:', !!altForm);
                console.log('Alternative submit found:', !!altSubmit);
                
                if (altForm) {
                    console.log('Alternative form action:', altForm.action);
                    console.log('Alternative form method:', altForm.method);
                    
                    // Use the alternative form
                    console.log('Using alternative form for event handling');
                    
                    altForm.addEventListener('submit', function(e) {
                        console.log('Alternative form submission triggered');
                        console.log('Form action:', altForm.action);
                        
                        // Sync TinyMCE content to textarea
                        const editor = tinymce.get('content');
                        if (editor) {
                            console.log('Syncing TinyMCE content...');
                            editor.save();
                            console.log('Content synced to textarea');
                        }
                        
                        // Let the form submit normally
                        return true;
                    });
                    
                    // Also handle button click as fallback
                    if (altSubmit) {
                        altSubmit.addEventListener('click', function(e) {
                            console.log('Alternative submit button clicked');
                            console.log('Form action:', altForm.action);
                            
                            // Sync TinyMCE content to textarea
                            const editor = tinymce.get('content');
                            if (editor) {
                                console.log('Syncing TinyMCE content from button click...');
                                editor.save();
                                console.log('Content synced to textarea');
                                
                                // Check textarea content
                                const textarea = document.querySelector('#content');
                                if (textarea) {
                                    console.log('Textarea content length:', textarea.value.length);
                                    console.log('Textarea content preview:', textarea.value.substring(0, 100) + '...');
                                }
                            }
                            
                            // Submit the form
                            altForm.submit();
                        });
                    }
                }
            }
        });
    }
</script>

<style>
    /* Enhanced textarea styling for fallback */
    #content {
        min-height: 500px;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
        font-size: 14px;
        line-height: 1.6;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        resize: vertical;
    }
    
    /* TinyMCE editor styling */
    .tox-tinymce {
        border-radius: 4px !important;
    }
    
    .tox-editor-container {
        border-radius: 4px !important;
    }
</style>
@endsection
