@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Class: {{ $class->name ?? 'Untitled' }}
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

                    <form action="{{ route('teacher.classes.update', $class->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" placeholder="e.g., Web Development 101"
                                   value="{{ old('name', $class->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5"
                                      placeholder="Describe your class...">{{ old('description', $class->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Class Image</label>
                            @if($class->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $class->image) }}" alt="Current image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    <p class="small text-muted mt-1">Current image</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Upload gambar baru untuk mengganti gambar saat ini (JPG, PNG, maks 2MB)</small>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <p class="small text-muted mt-1">New image preview</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" min="0" max="99999999.99" step="0.01" 
                                       placeholder="0.00" value="{{ old('price', $class->price ?? 0) }}" required>
                            </div>
                            <small class="text-muted">Harga class dalam Rupiah (Maksimal: Rp 99.999.999,99)</small>
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_discount" name="has_discount" value="1" {{ old('has_discount', $class->has_discount ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_discount">Tawarkan diskon untuk kelas ini</label>
                                <small class="d-block text-muted mt-1">Centang untuk menambahkan nominal diskon.</small>
                            </div>
                        </div>

                        <div class="mb-3" id="discountBlock" style="display: none;">
                            <label for="discount" class="form-label">Nominal Diskon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" min="0" max="99999999.99" step="0.01" placeholder="0.00" value="{{ old('discount', $class->discount ?? '') }}">
                            </div>
                            <small class="text-muted">Masukkan nominal diskon (dalam Rupiah). Jika ingin memberikan potongan, pastikan nilai ini tidak melebihi harga.</small>
                            @error('discount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="row mt-3">
                                <div class="col-md-6 mb-2">
                                    <label for="discount_starts_at" class="form-label">Mulai Diskon</label>
                                    <input type="date" class="form-control @error('discount_starts_at') is-invalid @enderror" id="discount_starts_at" name="discount_starts_at" value="{{ old('discount_starts_at', optional($class->discount_starts_at)->format('Y-m-d')) }}">
                                    @error('discount_starts_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="discount_ends_at" class="form-label">Berakhir Diskon</label>
                                    <input type="date" class="form-control @error('discount_ends_at') is-invalid @enderror" id="discount_ends_at" name="discount_ends_at" value="{{ old('discount_ends_at', optional($class->discount_ends_at)->format('Y-m-d')) }}">
                                    @error('discount_ends_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="what_youll_learn" class="form-label">What You'll Learn</label>
                            <textarea class="form-control @error('what_youll_learn') is-invalid @enderror" 
                                      id="what_youll_learn" name="what_youll_learn" rows="6"
                                      placeholder="Masukkan poin-poin yang akan dipelajari, pisahkan dengan baris baru...">{{ old('what_youll_learn', $class->what_youll_learn ?? '') }}</textarea>
                            <small class="text-muted">Tuliskan apa yang akan dipelajari siswa dalam class ini (satu poin per baris)</small>
                            @error('what_youll_learn')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirement" class="form-label">Requirements</label>
                            <textarea class="form-control @error('requirement') is-invalid @enderror" 
                                      id="requirement" name="requirement" rows="4"
                                      placeholder="Masukkan persyaratan untuk mengikuti class, pisahkan dengan baris baru...">{{ old('requirement', $class->requirement ?? '') }}</textarea>
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
                                        <option value="{{ $category->slug }}" {{ old('category', $class->category ?? '') === $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Fallback to constant categories if database is empty --}}
                                    @foreach(\App\Models\ClassModel::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $class->category ?? '') === $key ? 'selected' : '' }}>
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
                                       name="is_published" value="1" {{ old('is_published', $class->is_published ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Publish this class
                                </label>
                                <small class="d-block text-muted mt-1">Students can only see published classes</small>
                            </div>
                        </div>

                        <!-- Chapters Section -->
                        @if($chapters && count($chapters) > 0)
                        <div class="mb-4 p-3 bg-light rounded">
                            <h6 class="mb-3">Chapters in this class:</h6>
                            <ul class="list-unstyled">
                                @foreach($chapters as $chapter)
                                <li class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded border">
                                        <div>
                                            <strong>{{ $chapter->title ?? 'Untitled Chapter' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $chapter->modules->count() }} modules</small>
                                        </div>
                                        <div>
                                            <a href="{{ route('teacher.chapters.edit', [$class->id, $chapter->id]) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('teacher.chapters.index', $class->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('teacher.manage.content') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="button" class="btn btn-outline-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Delete Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-2"></i>Delete Class
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this class? This action cannot be undone.</p>
                            <p><strong>{{ $class->name ?? 'Untitled' }}</strong> will be permanently deleted along with all its chapters and modules.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('teacher.classes.destroy', $class->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Delete Class
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Editing a Class
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li>Update class details as needed</li>
                        <li>Add chapters to structure your content</li>
                        <li>Publish when ready for students to see</li>
                        <li>Changes are saved immediately</li>
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
                // Do not clear value on edit hide to avoid accidental deletion; optional
            }
        }
    }

    hasDiscountCheckbox?.addEventListener('change', toggleDiscountBlock);
    // Initialize on load (in case of validation errors or existing data)
    if (hasDiscountCheckbox && hasDiscountCheckbox.checked) {
        toggleDiscountBlock();
    }
</script>
@endsection
