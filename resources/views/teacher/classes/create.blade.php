@extends('layouts.app')

@section('title', 'Create New Class')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Create New Class
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('teacher.classes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" placeholder="e.g., Web Development 101"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5"
                                      placeholder="Describe your class..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Class Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*" required>
                            <small class="text-muted">Upload gambar/thumbnail untuk class ini (JPG, PNG, GIF, WEBP, maks 5MB)</small>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" min="0" max="99999999.99" step="0.01" 
                                       placeholder="0.00" value="{{ old('price', 0) }}" required>
                            </div>
                            <small class="text-muted">Harga class dalam Rupiah (Maksimal: Rp 99.999.999,99)</small>
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_discount" name="has_discount" value="1" {{ old('has_discount') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_discount">Tawarkan diskon untuk kelas ini</label>
                                <small class="d-block text-muted mt-1">Centang untuk menambahkan nominal diskon.</small>
                            </div>
                        </div>

                        <div class="mb-3" id="discountBlock" style="display: none;">
                            <label for="discount" class="form-label">Nominal Diskon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" min="0" max="99999999.99" step="0.01" placeholder="0.00" value="{{ old('discount') }}">
                            </div>
                            <small class="text-muted">Masukkan nominal diskon (dalam Rupiah). Jika ingin memberikan potongan, pastikan nilai ini tidak melebihi harga.</small>
                            @error('discount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="row mt-3">
                                <div class="col-md-6 mb-2">
                                    <label for="discount_starts_at" class="form-label">Mulai Diskon</label>
                                    <input type="date" class="form-control @error('discount_starts_at') is-invalid @enderror" id="discount_starts_at" name="discount_starts_at" value="{{ old('discount_starts_at') }}">
                                    @error('discount_starts_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="discount_ends_at" class="form-label">Berakhir Diskon</label>
                                    <input type="date" class="form-control @error('discount_ends_at') is-invalid @enderror" id="discount_ends_at" name="discount_ends_at" value="{{ old('discount_ends_at') }}">
                                    @error('discount_ends_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="what_youll_learn" class="form-label">What You'll Learn <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('what_youll_learn') is-invalid @enderror" 
                                      id="what_youll_learn" name="what_youll_learn" rows="6"
                                      placeholder="Masukkan poin-poin yang akan dipelajari, pisahkan dengan baris baru..." required>{{ old('what_youll_learn') }}</textarea>
                            <small class="text-muted">Tuliskan apa yang akan dipelajari siswa dalam class ini (satu poin per baris)</small>
                            @error('what_youll_learn')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Course Includes <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-3">Pilih fitur yang tersedia untuk kelas ini. Ini akan membantu siswa memahami apa yang mereka dapatkan.</small>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_video" name="includes[]" value="video" {{ old('includes') && in_array('video', old('includes')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_video">
                                            <i class="fas fa-video text-primary me-2"></i>
                                            Video pembelajaran on-demand
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_lifetime" name="includes[]" value="lifetime" {{ old('includes') && in_array('lifetime', old('includes')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_lifetime">
                                            <i class="fas fa-infinity text-success me-2"></i>
                                            Akses seumur hidup
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_certificate" name="includes[]" value="certificate" {{ old('includes') && in_array('certificate', old('includes')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_certificate">
                                            <i class="fas fa-certificate text-warning me-2"></i>
                                            Sertifikat penyelesaian
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_ai" name="includes[]" value="ai" {{ old('includes') && in_array('ai', old('includes')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_ai">
                                            <i class="fas fa-robot text-primary me-2"></i>
                                            Tanya AI Assistant
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_lifetime" name="includes[]" value="lifetime" {{ old('includes') && in_array('lifetime', old('includes')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_lifetime">
                                            <i class="fas fa-infinity text-success me-2"></i>
                                            Akses seumur hidup
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_mobile" name="includes[]" value="mobile" {{ old('includes') && in_array('mobile', old('includes')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_mobile">
                                            <i class="fas fa-mobile-alt text-success me-2"></i>
                                            Akses mobile & tablet
                                        </label>
                                    </div>
                            </div>
                            
                            @error('includes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirement" class="form-label">Requirements <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('requirement') is-invalid @enderror" 
                                      id="requirement" name="requirement" rows="4"
                                      placeholder="Masukkan persyaratan untuk mengikuti class, pisahkan dengan baris baru..." required>{{ old('requirement') }}</textarea>
                            <small class="text-muted">Tuliskan persyaratan yang diperlukan untuk mengikuti class ini (satu poin per baris)</small>
                            @error('requirement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" {{ old('category') === $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Fallback to constant categories if database is empty --}}
                                    @foreach(\App\Models\ClassModel::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Choose the category that best describes your class</small>
                            @error('category')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" 
                                       name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Publish this class immediately
                                </label>
                                <small class="d-block text-muted mt-1">Students can only see published classes</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Class
                            </button>
                            <a href="{{ route('teacher.manage.content') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Creating a Class
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li>Give your class a clear, descriptive name</li>
                        <li>Write a detailed description to help students understand the content</li>
                        <li>Start with Draft status, then publish when you're ready</li>
                        <li>You can always edit or delete your class later</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Image preview
    document.getElementById('image')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    // Discount toggle
    const hasDiscountCheckbox = document.getElementById('has_discount');
    const discountBlock = document.getElementById('discountBlock');
    const discountInput = document.getElementById('discount');

    function toggleDiscountBlock() {
        if (!hasDiscountCheckbox) return;
        if (hasDiscountCheckbox.checked) {
            discountBlock.style.display = 'block';
            if (discountInput) discountInput.required = true;
        } else {
            discountBlock.style.display = 'none';
            if (discountInput) {
                discountInput.required = false;
                discountInput.value = '';
            }
        }
    }

    hasDiscountCheckbox?.addEventListener('change', toggleDiscountBlock);
    // Initialize on load (in case of validation errors)
    if (hasDiscountCheckbox && hasDiscountCheckbox.checked) {
        toggleDiscountBlock();
    }
</script>
@endsection
