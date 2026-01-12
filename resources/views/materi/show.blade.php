@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="/dashboard" class="text-blue-600 mb-4 inline-block">← Kembali</a>
    
    <h1 class="text-3xl font-bold mb-2">{{ $materi->title }}</h1>
    <p class="text-gray-600 mb-6">Kursus: {{ $materi->course->title }}</p>
    
    @if($materi->type === 'video')
        <!-- Video Player -->
        <div class="bg-black rounded mb-4 overflow-hidden" id="videoContainer" style="user-select: none;">
            <video 
                id="videoPlayer"
                controls 
                controlsList="nodownload"
                style="width: 100%; user-select: none;"
                oncontextmenu="return false;">
                <source src="{{ $fileUrl }}" type="video/mp4">
                Browser Anda tidak mendukung video player.
            </video>
        </div>
    @elseif($materi->type === 'pdf')
        <!-- PDF Viewer dengan PDF.js -->
        <div class="bg-gray-100 rounded mb-4 overflow-hidden" id="pdfContainer" style="user-select: none;">
            <div id="pdf-viewer" style="width: 100%; height: 600px; overflow: auto; position: relative;">
                <canvas id="pdfCanvas" style="display: block; margin: 0 auto; border: 1px solid #ccc;"></canvas>
            </div>
            <div style="text-align: center; padding: 10px; background: #f0f0f0;">
                <button id="prevBtn" style="padding: 5px 15px; margin-right: 10px;">← Sebelumnya</button>
                <span id="pageInfo" style="margin: 0 10px;">Page <span id="pageNum">1</span> of <span id="pageCount">0</span></span>
                <button id="nextBtn" style="padding: 5px 15px; margin-left: 10px;">Selanjutnya →</button>
            </div>
        </div>
        <p class="text-gray-600 text-sm">* Klik kanan dan copy teks tidak diizinkan untuk menjaga keamanan konten</p>
    @endif
    
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Set worker untuk pdf.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// Get PDF file path
const pdfPath = '{{ $fileUrl }}';
let pdfDoc = null;
let currentPage = 1;

// Render halaman PDF
async function renderPage(pageNum) {
    const page = await pdfDoc.getPage(pageNum);
    const canvas = document.getElementById('pdfCanvas');
    const ctx = canvas.getContext('2d');
    
    const viewport = page.getViewport({ scale: 1.5 });
    canvas.width = viewport.width;
    canvas.height = viewport.height;
    
    const renderContext = {
        canvasContext: ctx,
        viewport: viewport
    };
    
    await page.render(renderContext).promise;
    
    document.getElementById('pageNum').textContent = pageNum;
    document.getElementById('prevBtn').disabled = pageNum <= 1;
    document.getElementById('nextBtn').disabled = pageNum >= pdfDoc.numPages;
}

// Load PDF
pdfjsLib.getDocument(pdfPath).promise.then(function(doc) {
    pdfDoc = doc;
    document.getElementById('pageCount').textContent = doc.numPages;
    renderPage(currentPage);
});

// Tombol navigasi
document.getElementById('prevBtn').addEventListener('click', function() {
    if (currentPage > 1) {
        currentPage--;
        renderPage(currentPage);
    }
});

document.getElementById('nextBtn').addEventListener('click', function() {
    if (pdfDoc && currentPage < pdfDoc.numPages) {
        currentPage++;
        renderPage(currentPage);
    }
});

// Disable right-click
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    return false;
}, false);

// Disable copy/cut/paste (Ctrl+C, Ctrl+X, Ctrl+V)
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        if (e.key === 'c' || e.key === 'C' || e.key === 'x' || e.key === 'X' || e.key === 'v' || e.key === 'V') {
            e.preventDefault();
            return false;
        }
    }
});

// Disable text selection
document.body.style.userSelect = 'none';
document.body.style.webkitUserSelect = 'none';
document.body.style.msUserSelect = 'none';
document.getElementById('pdfCanvas').style.userSelect = 'none';

// Disable image drag
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
    return false;
});

// Proteksi canvas - disable context menu
document.getElementById('pdfCanvas').addEventListener('contextmenu', function(e) {
    e.preventDefault();
    return false;
});
</script>

@endsection
