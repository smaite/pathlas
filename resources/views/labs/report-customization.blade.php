@extends('layouts.app')
@section('title', 'Report Customization')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Report Design</h1>
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Settings Form -->
        <div class="space-y-6">
            <form action="{{ route('lab.report-customization.update') }}" method="POST" enctype="multipart/form-data" id="customization-form">
                @csrf
                @method('PUT')

                <!-- Header Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">🎨</span>
                        Header Settings
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Header Color -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Header Color</label>
                            <div class="flex gap-3">
                                <input type="color" name="header_color" id="header_color" 
                                    value="{{ $lab->header_color ?? '#0066cc' }}"
                                    class="w-16 h-10 rounded border border-gray-200 cursor-pointer">
                                <input type="text" id="header_color_text" 
                                    value="{{ $lab->header_color ?? '#0066cc' }}"
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm"
                                    placeholder="#0066cc">
                            </div>
                        </div>

                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lab Logo</label>
                            <div class="flex items-center gap-4">
                                <div id="logo-preview" class="w-24 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden border">
                                    @if($lab->logo)
                                    <img src="{{ asset('storage/' . $lab->logo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                    @else
                                    <span class="text-gray-400 text-xs">No Logo</span>
                                    @endif
                                </div>
                                <input type="file" name="logo" accept="image/*" id="logo-input"
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>

                        <!-- Logo Dimensions -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Width (px)</label>
                                <input type="number" name="logo_width" id="logo_width" 
                                    value="{{ $lab->logo_width ?? 100 }}" min="30" max="200"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Height (px)</label>
                                <input type="number" name="logo_height" id="logo_height" 
                                    value="{{ $lab->logo_height ?? 60 }}" min="30" max="150"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatory 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">✍️</span>
                        Signatory 1
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input type="text" name="signature_name" id="signature_name" 
                                    value="{{ $lab->signature_name }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg"
                                    placeholder="Dr. Name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                                <input type="text" name="signature_designation" id="signature_designation" 
                                    value="{{ $lab->signature_designation }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg"
                                    placeholder="MD Pathology">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Signature Image</label>
                            <div class="flex items-center gap-4">
                                <div id="sig1-preview" class="w-24 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden border">
                                    @if($lab->signature_image)
                                    <img src="{{ asset('storage/' . $lab->signature_image) }}" alt="Sig" class="max-w-full max-h-full object-contain">
                                    @else
                                    <span class="text-gray-400 text-xs">No Sig</span>
                                    @endif
                                </div>
                                <input type="file" name="signature_image" accept="image/*" id="sig1-input"
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sig Width (px)</label>
                                <input type="number" name="signature_width" id="signature_width" 
                                    value="{{ $lab->signature_width ?? 100 }}" min="50" max="200"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sig Height (px)</label>
                                <input type="number" name="signature_height" id="signature_height" 
                                    value="{{ $lab->signature_height ?? 35 }}" min="20" max="80"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatory 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">✍️</span>
                        Signatory 2
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input type="text" name="signature_name_2" id="signature_name_2" 
                                    value="{{ $lab->signature_name_2 }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg"
                                    placeholder="Dr. Name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                                <input type="text" name="signature_designation_2" id="signature_designation_2" 
                                    value="{{ $lab->signature_designation_2 }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg"
                                    placeholder="Consultant Pathologist">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Signature Image</label>
                            <div class="flex items-center gap-4">
                                <div id="sig2-preview" class="w-24 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden border">
                                    @if($lab->signature_image_2)
                                    <img src="{{ asset('storage/' . $lab->signature_image_2) }}" alt="Sig 2" class="max-w-full max-h-full object-contain">
                                    @else
                                    <span class="text-gray-400 text-xs">No Sig</span>
                                    @endif
                                </div>
                                <input type="file" name="signature_image_2" accept="image/*" id="sig2-input"
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sig Width (px)</label>
                                <input type="number" name="signature_width_2" id="signature_width_2" 
                                    value="{{ $lab->signature_width_2 ?? 100 }}" min="50" max="200"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sig Height (px)</label>
                                <input type="number" name="signature_height_2" id="signature_height_2" 
                                    value="{{ $lab->signature_height_2 ?? 35 }}" min="20" max="80"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Notes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">📝</span>
                        Report Footer Notes
                    </h2>
                    <textarea name="report_notes" id="report_notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg"
                        placeholder="Notes to appear at bottom of reports...">{{ $lab->report_notes }}</textarea>
                </div>

                <!-- Pre-printed Paper Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600">📄</span>
                        Pre-printed Paper Settings
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Adjust margins for "Without Header" reports to match your pre-printed letterhead paper.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Top Margin (mm)</label>
                            <input type="number" name="headerless_margin_top" id="headerless_margin_top" 
                                value="{{ $lab->headerless_margin_top ?? 40 }}" min="10" max="100"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <p class="text-xs text-gray-400 mt-1">Space for pre-printed header</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bottom Margin (mm)</label>
                            <input type="number" name="headerless_margin_bottom" id="headerless_margin_bottom" 
                                value="{{ $lab->headerless_margin_bottom ?? 30 }}" min="10" max="80"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <p class="text-xs text-gray-400 mt-1">Space for pre-printed footer</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition">
                    💾 Save Customization
                </button>
            </form>
        </div>

        <!-- Right: Live Preview -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Live Preview</h2>
                <button type="button" id="refresh-preview" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    🔄 Refresh
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50" style="height: 650px;">
                <iframe id="preview-frame" src="{{ route('lab.report-preview') }}" 
                    class="w-full h-full" style="transform: scale(0.65); transform-origin: top left; width: 154%; height: 154%;"></iframe>
            </div>
            
            <p class="text-xs text-gray-500 mt-3 text-center">
                ⚡ Preview updates automatically. Save to apply changes to actual reports.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewFrame = document.getElementById('preview-frame');
    const basePreviewUrl = '{{ route("lab.report-preview") }}';
    
    // Sync color picker with text input
    const colorPicker = document.getElementById('header_color');
    const colorText = document.getElementById('header_color_text');
    
    colorPicker.addEventListener('input', function() {
        colorText.value = this.value;
        updatePreview();
    });
    
    colorText.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorPicker.value = this.value;
            updatePreview();
        }
    });

    // Image preview on file select
    document.getElementById('logo-input').addEventListener('change', function(e) {
        previewImage(e, 'logo-preview');
    });
    document.getElementById('sig1-input').addEventListener('change', function(e) {
        previewImage(e, 'sig1-preview');
    });
    document.getElementById('sig2-input').addEventListener('change', function(e) {
        previewImage(e, 'sig2-preview');
    });

    function previewImage(event, previewId) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.innerHTML = '<img src="' + e.target.result + '" class="max-w-full max-h-full object-contain">';
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Fields to watch for preview updates
    const watchFields = [
        'header_color', 'logo_width', 'logo_height',
        'signature_name', 'signature_designation', 'signature_width', 'signature_height',
        'signature_name_2', 'signature_designation_2', 'signature_width_2', 'signature_height_2',
        'report_notes'
    ];
    
    watchFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', debounce(updatePreview, 500));
        }
    });
    
    // Refresh button
    document.getElementById('refresh-preview').addEventListener('click', updatePreview);
    
    function updatePreview() {
        const params = new URLSearchParams();
        
        watchFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field.value) {
                params.set(fieldId, field.value);
            }
        });
        
        previewFrame.src = basePreviewUrl + '?' + params.toString();
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endpush
@endsection
