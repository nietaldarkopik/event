@extends('adminlte::page')

@section('title', 'Certificate Builder - ' . $template->name)

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="font-weight-bold text-dark"><i class="fas fa-magic text-primary mr-2"></i> Certificate Builder</h1>
        <p class="text-muted mb-0">Template: <span class="badge badge-info mr-2">{{ $template->name }}</span> | <span
                class="badge badge-secondary">{{ strtoupper($template->orientation) }}</span></p>
    </div>
    <div class="d-flex gap-2">
        <div class="btn-group shadow-sm mr-2">
            <button type="button" class="btn btn-primary px-4" onclick="saveLayout()"><i class="fas fa-save mr-2"></i>
                Simpan Layout</button>
            <a href="{{ route('certificate-templates.index') }}" class="btn btn-light border ml-1"><i
                    class="fas fa-times"></i></a>
        </div>
    </div>
</div>

<div class="alert alert-info mt-2 mb-0 py-2 small shadow-sm border-0 border-left-4 border-info">
    <i class="fas fa-info-circle mr-2"></i> <strong>PANDUAN DATA:</strong> Builder ini adalah alat <strong>Desain Tata
        Letak</strong>. Data seperti <em>Nama Peserta</em>, <em>Kategori</em>, dan <em>TTD Pejabat</em> akan diisi
    otomatis dari menu <strong>Peserta</strong> & <strong>Pengaturan Event</strong> saat cetak. Gunakan tombol
    <strong>Variabel</strong> untuk menandai posisi teks otomatis tersebut.
</div>
@stop

@section('content')
<div class="row">
    <!-- LEFT PANEL: Toolkit -->
    <div class="col-md-2 pr-0">
        <div class="card card-dark-panel shadow-sm h-100 mb-0 rounded-0">
            <div class="card-header border-0 py-2">
                <h3 class="card-title font-weight-bold small"><i class="fas fa-tools mr-2"></i> Design Elements</h3>
            </div>
            <div class="card-body p-2 overflow-auto" style="height: 80vh;">
                <div class="toolkit-grid">
                    <!-- Standard Layouts -->
                    <button class="tool-btn" onclick="applyStandardLayout('center')" title="Standard Layout (Center)">
                        <i class="fas fa-align-center text-warning"></i> <span>Layout Center</span>
                    </button>
                    <button class="tool-btn" onclick="applyStandardLayout('left')" title="Standard Layout (Left)">
                        <i class="fas fa-align-left text-warning"></i> <span>Layout Left</span>
                    </button>

                    <div class="border-top my-1 border-secondary"></div>

                    <!-- Basic Tools -->
                    <button class="tool-btn" onclick="addText('Teks Baru')">
                        <i class="fas fa-font text-primary"></i> <span>Teks</span>
                    </button>

                    <button class="tool-btn" onclick="$('#var-dropdown').toggle()">
                        <i class="fas fa-code text-success"></i> <span>Var</span>
                    </button>
                    <div id="var-dropdown" class="p-2 rounded mb-2"
                        style="display:none; font-size:0.85rem; background: var(--btn-bg); border: 1px solid #dee2e6; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                        <span class="d-block text-muted mb-2 small font-weight-bold text-uppercase"
                            style="letter-spacing: 0.5px; border-bottom: 1px solid #dee2e6; padding-bottom: 4px;">Data
                            Peserta</span>
                        <a href="#" class="d-block text-dark mb-1 ml-2 var-link"
                            onclick="addDynamicText('{nama_peserta}')"><i
                                class="fas fa-caret-right mr-1 text-muted"></i> Nama</a>
                        <a href="#" class="d-block text-dark mb-1 ml-2 var-link"
                            onclick="addDynamicText('{nomor_sertifikat}')"><i
                                class="fas fa-caret-right mr-1 text-muted"></i> No. Sertifikat</a>
                        <a href="#" class="d-block text-dark mb-2 ml-2 var-link"
                            onclick="addDynamicText('{peran_event}')"><i class="fas fa-caret-right mr-1 text-muted"></i>
                            Peran</a>

                        <span class="d-block text-muted mb-2 mt-2 small font-weight-bold text-uppercase"
                            style="letter-spacing: 0.5px; border-bottom: 1px solid #dee2e6; padding-bottom: 4px;">Event
                            & TTD</span>
                        <a href="#" class="d-block text-dark mb-1 ml-2 var-link"
                            onclick="addDynamicText('{judul_event}')"><i class="fas fa-caret-right mr-1 text-muted"></i>
                            Judul Event</a>
                        <a href="#" class="d-block text-dark mb-1 ml-2 var-link"
                            onclick="addDynamicText('{tanggal_event}')"><i
                                class="fas fa-caret-right mr-1 text-muted"></i> Tanggal</a>
                        <a href="#" class="d-block text-dark mb-1 ml-2 var-link"
                            onclick="addDynamicText('{nama_ttd1}')"><i class="fas fa-caret-right mr-1 text-muted"></i>
                            TTD 1</a>
                        <a href="#" class="d-block text-dark mb-1 ml-2 var-link"
                            onclick="addDynamicText('{nama_ttd2}')"><i class="fas fa-caret-right mr-1 text-muted"></i>
                            TTD 2</a>
                    </div>

                    <button class="tool-btn" onclick="addLine()">
                        <i class="fas fa-minus text-secondary"></i> <span>Garis</span>
                    </button>

                    <button class="tool-btn" onclick="addQR()">
                        <i class="fas fa-qrcode text-danger"></i> <span>QR</span>
                    </button>

                    <div class="border-top my-1 border-secondary"></div>

                    <!-- Gallery Assets -->
                    <button class="tool-btn" onclick="openGallery('logo')">
                        <i class="fas fa-image text-info"></i> <span>Logo</span>
                    </button>

                    <button class="tool-btn" onclick="openGallery('signature')">
                        <i class="fas fa-signature text-primary"></i> <span>TTD</span>
                    </button>

                    <button class="tool-btn" onclick="openGallery('stamp')">
                        <i class="fas fa-stamp text-warning"></i> <span>Cap</span>
                    </button>

                    <button class="tool-btn" onclick="openGallery('background')">
                        <i class="fas fa-image text-muted"></i> <span>BG</span>
                    </button>

                    <!-- Hidden Inputs -->
                    <input type="file" id="bg-input" accept="image/*" style="display: none"
                        onchange="uploadBackground(this)">
                    <input type="file" id="img-input" accept="image/*" style="display: none"
                        onchange="uploadImage(this, 'custom_image')">
                    <input type="file" id="ttd-input" accept="image/*" style="display: none"
                        onchange="uploadImage(this, 'signature')">
                    <input type="file" id="cap-input" accept="image/*" style="display: none"
                        onchange="uploadImage(this, 'stamp')">
                </div>
            </div>
        </div>
    </div>

    <!-- CENTER PANEL: Canvas -->
    <div class="col-md-10 px-0" id="center-panel" style="transition: all 0.3s ease;">
        <div class="card bg-editor shadow-sm border-0 mb-0 h-100 rounded-0" style="position: relative;">
            <div id="canvas-wrapper" style="position: relative;">
                <canvas id="c"></canvas>
            </div>

            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button class="btn btn-sm" onclick="undo()" title="Undo (Ctrl+Z)"><i class="fas fa-undo"></i></button>
                <button class="btn btn-sm" onclick="redo()" title="Redo (Ctrl+Y)"><i class="fas fa-redo"></i></button>
                <div class="border-left mx-1" style="height: 20px; align-self: center;"></div>
                <button class="btn btn-sm" onclick="zoomIn()" title="Zoom In"><i class="fas fa-plus"></i></button>
                <button class="btn btn-sm" onclick="fitToContainer()" title="Fit to Screen"><i
                        class="fas fa-compress"></i></button>
                <button class="btn btn-sm" onclick="zoomOut()" title="Zoom Out"><i class="fas fa-minus"></i></button>
                <button class="btn btn-sm" id="btn-pan" onclick="togglePanMode()" title="Mode Geser (Pan)"><i
                        class="fas fa-hand-paper"></i></button>
            </div>
        </div>
    </div>

    <!-- TOGGLE BUTTON (For Popup) -->
    <button id="params-toggle-btn" class="btn btn-primary" onclick="togglePropertiesPopup()"
        style="position: fixed; right: 20px; bottom: 20px; z-index: 1050; border-radius: 50px; padding: 10px 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <i class="fas fa-sliders-h mr-2"></i> Properties
    </button>

    <!-- PROPERTIES POPUP -->
    <div id="properties-popup" class="card shadow-lg"
        style="position: fixed; right: 20px; top: 80px; width: 300px; z-index: 1060; max-height: 85vh; border-radius: 8px; display: none;">
        <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center"
            style="cursor: move;">
            <h6 class="mb-0 small font-weight-bold"><i class="fas fa-sliders-h mr-2"></i> Properties & Layers</h6>
            <button class="btn btn-sm btn-link text-white p-0" onclick="togglePropertiesPopup()"><i
                    class="fas fa-times"></i></button>
        </div>

        <div class="card-body p-0" style="overflow-y: auto; max-height: calc(85vh - 40px);">
            <!-- TABS -->
            <ul class="nav nav-tabs nav-fill" id="propTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active py-2 small" id="props-tab" data-toggle="tab" href="#tab-props"
                        role="tab">Properties</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 small" id="layers-tab" data-toggle="tab" href="#tab-layers"
                        role="tab">Layers</a>
                </li>
            </ul>

            <div class="tab-content border-0">
                <!-- PROPERTIES CONTENT -->
                <div class="tab-pane fade show active p-3" id="tab-props" role="tabpanel">
                    <div id="inspector">
                        <!-- Font Family -->
                        <div class="form-group mb-2">
                            <label>Font Family</label>
                            <select id="prop-fontFamily" class="form-control form-control-sm"
                                onchange="updateSelectedProp('fontFamily', this.value)">
                                <option value="Arial">Arial</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Courier New">Courier New</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label>Font Size</label>
                                    <input type="number" id="prop-fontSize" class="form-control form-control-sm"
                                        onchange="updateSelectedProp('fontSize', this.value)">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label>Color</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" id="prop-fill" class="form-control form-control-sm h-100"
                                            onchange="updateSelectedProp('fill', this.value)">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-dark border-secondary text-light ml-1"
                                                id="hex-view">#000000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label>Alignment</label>
                            <div class="btn-group w-100 design-controls">
                                <button class="btn btn-sm"
                                    onclick="updateSelectedProp('textAlign', 'left')">Left</button>
                                <button class="btn btn-sm"
                                    onclick="updateSelectedProp('textAlign', 'center')">Center</button>
                                <button class="btn btn-sm"
                                    onclick="updateSelectedProp('textAlign', 'right')">Right</button>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label>Style</label>
                            <div class="design-controls d-flex justify-content-between">
                                <button class="btn btn-sm flex-fill mr-1"
                                    onclick="updateSelectedProp('fontWeight', 'bold')">Bold</button>
                                <button class="btn btn-sm flex-fill mr-1"
                                    onclick="updateSelectedProp('fontStyle', 'italic')">Italic</button>
                                <button class="btn btn-sm flex-fill"
                                    onclick="updateSelectedProp('underline', true)">Underline</button>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label>Transform</label>
                            <div class="row no-gutters">
                                <div class="col-3 pr-1"><input type="number" id="prop-x"
                                        class="form-control form-control-sm mb-1" placeholder="X"
                                        onchange="updateTransform('left', this.value)"></div>
                                <div class="col-3 pr-1"><input type="number" id="prop-y"
                                        class="form-control form-control-sm mb-1" placeholder="Y"
                                        onchange="updateTransform('top', this.value)"></div>
                                <div class="col-3 pr-1"><input type="number" id="prop-w"
                                        class="form-control form-control-sm mb-1" placeholder="W"
                                        onchange="updateTransform('width', this.value)"></div>
                                <div class="col-3"><input type="number" id="prop-h"
                                        class="form-control form-control-sm mb-1" placeholder="H"
                                        onchange="updateTransform('height', this.value)"></div>
                            </div>
                        </div>
                    </div>

                    <div id="inspector-none"
                        class="text-center text-muted d-flex flex-column justify-content-center py-5">
                        <p class="small mb-0">Select an element to edit</p>
                    </div>
                </div>

                <!-- LAYERS CONTENT -->
                <div class="tab-pane fade" id="tab-layers" role="tabpanel">
                    <div class="p-0" id="layer-list" style="max-height: 400px; overflow-y: auto;">
                        <!-- Dynamic Layers will go here -->
                        <div class="text-center text-muted py-3 small">No layers</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    /* Light Theme Properties (Reverted from Dark) */
    :root {
        --panel-bg: #ffffff;
        --panel-text: #212529;
        --accent-color: #007bff;
        --accent-hover: #0056b3;
        --border-color: #dee2e6;
        --btn-bg: #f8f9fa;
        --btn-hover: #e9ecef;
    }

    body {
        background-color: #e9ecef;
        /* Slightly darker bg for contrast */
    }

    .card-dark-panel {
        background-color: var(--panel-bg);
        color: var(--panel-text);
        border: 1px solid var(--border-color);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-dark-panel .card-header {
        border-bottom: 1px solid var(--border-color);
        background-color: #f8f9fa;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .card-header h3 {
        color: #495057;
    }

    /* Column Layout */
    .toolkit-grid {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 5px;
    }

    /* Vertical Tool Buttons */
    .tool-btn {
        width: 100%;
        background: var(--btn-bg);
        border: 1px solid #dee2e6;
        color: #495057;
        text-align: left;
        padding: 8px 10px;
        /* Smaller padding */
        border-radius: 6px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        transition: all 0.2s ease-in-out;
        margin-bottom: 0;
        position: relative;
        overflow: hidden;
    }

    .tool-btn:hover {
        background: var(--btn-hover);
        color: var(--accent-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        border-color: #ced4da;
    }

    .tool-btn i {
        width: 28px;
        margin-right: 12px;
        font-size: 1.15em;
        text-align: center;
        margin-bottom: 0;
        transition: transform 0.2s;
        color: #6c757d;
    }

    .tool-btn:hover i {
        transform: scale(1.1);
        color: var(--accent-color);
    }

    .tool-btn span {
        font-size: 0.85rem;
        /* Smaller font */
        font-weight: 500;
        color: inherit;
        letter-spacing: 0.3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Properties Panel */
    .property-panel input,
    .property-panel select {
        background-color: #fff;
        border: 1px solid #ced4da;
        color: #495057;
        font-size: 0.85rem;
        height: calc(1.5em + .75rem + 2px);
    }

    .property-panel input:focus,
    .property-panel select:focus {
        background-color: #fff;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .property-panel label {
        color: #6c757d;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .design-controls button {
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #ced4da;
        padding: 6px 10px;
        transition: all 0.2s;
    }

    .design-controls button:hover {
        background: #e2e6ea;
        color: #212529;
    }

    .design-controls button.active {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Layers List */
    .layer-item {
        background: #fff;
        border-bottom: 1px solid #f1f3f5;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background-color 0.2s;
        border-radius: 4px;
        margin-bottom: 2px;
    }

    .layer-item:hover {
        background: #f8f9fa;
    }

    .layer-item.active {
        background: rgba(0, 123, 255, 0.1);
        border-left: 4px solid var(--accent-color);
        padding-left: 11px;
        /* Compensate for border */
    }

    .layer-controls i {
        cursor: pointer;
        opacity: 0.4;
        margin-left: 10px;
        transition: opacity 0.2s, color 0.2s;
        font-size: 0.9em;
        color: #adb5bd;
    }

    .layer-controls i:hover {
        opacity: 1;
        color: #495057;
    }

    .layer-controls i.fa-trash:hover {
        color: #dc3545 !important;
    }

    .bg-editor {
        background-color: #ffffff !important;
        /* Grid Pattern */
        background-image:
            linear-gradient(#e9ecef 1px, transparent 1px),
            linear-gradient(90deg, #e9ecef 1px, transparent 1px);
        background-size: 20px 20px;
        /* Initial size, will be updated by JS */
        background-position: center center;

        height: 82vh;
        /* maximize height */
        border: 1px solid #dee2e6;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
        overflow: auto;
        /* Enable scrolling */
        display: flex;
        align-items: center;
        /* Center vertically */
        justify-content: center;
        /* Center horizontally */
        position: relative;
        transition: background-size 0.2s;
        /* Smooth grid scaling */
    }

    #canvas-wrapper {
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.25);
        /* Stronger shadow */
        border: 1px solid #ced4da;
        /* Distinct border */
        margin: auto;
        /* Ensure centering in scrollable area */
        background-color: white;
        /* Ensure canvas background is white */
    }

    /* Popup/Modal Styles */
    #properties-popup {
        transition: opacity 0.2s;
    }

    #right-panel-toggle {
        position: fixed;
        right: 0;
        top: 50%;
        width: 24px;
        height: 48px;
        background: #343a40;
        color: white;
        border-radius: 8px 0 0 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1050;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        transition: right 0.3s;
    }

    #right-panel-toggle:hover {
        width: 32px;
        background: var(--accent-color);
    }

    /* Zoom Controls Overlay */
    .zoom-controls {
        background: rgba(255, 255, 255, 0.9);
        padding: 8px;
        border-radius: 50px;
        backdrop-filter: blur(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 8px;
    }

    .zoom-controls button {
        background: transparent;
        color: #495057;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .zoom-controls button:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #007bff;
    }

    /* Gallery Styling Override */
    .gallery-item-card {
        border-color: #dee2e6 !important;
    }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    let canvas;
    const templateOrientation = '{{ $template->orientation }}'; // landscape or portrait
    const backgroundUrl = '{{ asset('storage/' . $template->background_file) }}';
    let currentGalleryType = 'logo';

    // Scale factor to fit canvas in reasonable view
    const A4_WIDTH = templateOrientation === 'landscape' ? 1123 : 794;
    const A4_HEIGHT = templateOrientation === 'landscape' ? 794 : 1123;

    $(document).ready(function () {
        initCanvas();
        $(window).resize(fitToContainer);

        // Draggable Logic for Popup
        const popup = document.getElementById("properties-popup");
        const header = popup.querySelector(".card-header");
        let isDraggingPopup = false;
        let startX, startY, initialLeft, initialTop;

        if (header && popup) {
            header.addEventListener("mousedown", (e) => {
                isDraggingPopup = true;
                startX = e.clientX;
                startY = e.clientY;
                const rect = popup.getBoundingClientRect();
                initialLeft = rect.left;
                initialTop = rect.top;
                header.style.cursor = "grabbing";
            });

            document.addEventListener("mousemove", (e) => {
                if (!isDraggingPopup) return;
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                popup.style.left = `${initialLeft + dx}px`;
                popup.style.top = `${initialTop + dy}px`;
                popup.style.right = 'auto'; // Clear right when dragging
            });

            document.addEventListener("mouseup", () => {
                isDraggingPopup = false;
                if (header) header.style.cursor = "move";
            });
        }

        // --- URL Parameter Handling (Auto-Add) ---
        const urlParams = new URLSearchParams(window.location.search);
        const fileParam = urlParams.get('file');
        const nameParam = urlParams.get('name');
        const typeParam = urlParams.get('type'); // 'signature', 'stamp', 'logo'

        if (fileParam && typeParam) {
            // Assume file is accessible via storage or direct URL if http
            let cleanFile = fileParam;
            // If it's just a filename, assume standard storage path (adjust as needed based on app logic)
            // For now, if it doesn't start with http, we might need to prepend asset path
            // But usually this feature expects a full URL or a relative path from accessible storage.
            // Let's try to add it directly.

            // Check if we need to decode
            // const decodedFile = decodeURIComponent(fileParam);

            // NOTE: Logic to construct full path if needed. 
            // For this specific environment, we'll try using it as is or prepending '/storage/' if it looks like a filename.
            let imageUrl = fileParam;
            if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
                imageUrl = '/storage/' + imageUrl;
            }

            fabric.Image.fromURL(imageUrl, function (img) {
                if (!img) {
                    console.error('Failed to load image from URL:', imageUrl);
                    toastr.error('Gagal memuat gambar dari URL.');
                    return;
                }

                // Scale if too big
                if (img.width > 300) img.scaleToWidth(300);

                img.set({
                    left: 100,
                    top: 100,
                    data: { type: typeParam, name: nameParam || typeParam }
                });

                canvas.add(img);
                canvas.setActiveObject(img);
                canvas.renderAll();
                toastr.success('Gambar ditambahkan dari URL: ' + (nameParam || typeParam));

                // Remove params to prevent re-adding on refresh (optional, but good UX)
                // window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });

    function initCanvas() {
        // Create canvas at full A4 size
        canvas = new fabric.Canvas('c', {
            width: A4_WIDTH,
            height: A4_HEIGHT,
            backgroundColor: '#fff'
        });

        // Apply zoom for display (full size)
        canvas.setZoom(1.0);
        canvas.setWidth(A4_WIDTH);
        canvas.setHeight(A4_HEIGHT);

        // Load Background
        fabric.Image.fromURL(backgroundUrl, function (img) {
            img.set({
                originX: 'left',
                originY: 'top',
                selectable: false,
                evented: false,
                scaleX: A4_WIDTH / img.width,
                scaleY: A4_HEIGHT / img.height
            });
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
        });

        // Load existing layout if exists
        let existingLayout = @json($template->layout_json);
        if (existingLayout) {
            canvas.loadFromJSON(existingLayout, function () {
                canvas.renderAll();
                updateLayersList();
                updateGrid();
            });
        }

        // Object selection event
        canvas.on('selection:created', onObjectSelected);
        canvas.on('selection:updated', onObjectSelected);
        canvas.on('selection:cleared', function () {
            $('#inspector').hide();
            $('#inspector-none').show();
        });

        // Hook history to canvas events
        canvas.on('object:added', function (e) { if (!isHistoryProcessing) saveHistory(); });
        canvas.on('object:modified', function (e) { if (!isHistoryProcessing) saveHistory(); });
        canvas.on('object:removed', function (e) { if (!isHistoryProcessing) saveHistory(); });

        // Initial Grid Update
        updateGrid();

        // Init Layer Sortable
        initLayerSortable();
    }

    function initLayerSortable() {
        const el = document.getElementById('layer-list');
        Sortable.create(el, {
            animation: 150,
            onEnd: function (evt) {
                // Get new order of indices from DOM (Top to Bottom)
                const newOrder = [];
                $('#layer-list .layer-item').each(function () {
                    newOrder.push($(this).data('fabric-index'));
                });

                // Reconstruct canvas._objects
                // The DOM list is Top-to-Bottom. Fabric objects are Bottom-to-Top.
                // So we iterate the DOM list in reverse to build the new Fabric array.
                const currentObjects = canvas.getObjects();
                const newObjects = [];

                for (let i = newOrder.length - 1; i >= 0; i--) {
                    const originalIndex = newOrder[i];
                    if (currentObjects[originalIndex]) {
                        newObjects.push(currentObjects[originalIndex]);
                    }
                }

                canvas._objects = newObjects;
                canvas.renderAll();
                updateLayersList(); // Refresh list to update indices
                saveHistory();
            }
        });
    }

    function applyStandardLayout(type) {
        if (!confirm('This will clear the current canvas. Continue?')) return;

        canvas.clear();
        canvas.setBackgroundColor('#fff', canvas.renderAll.bind(canvas));

        const isLeft = type === 'left';
        const startX = isLeft ? 100 : A4_WIDTH / 2;
        const alignX = isLeft ? 'left' : 'center';

        // 1. Title
        const title = new fabric.IText('CERTIFICATE', {
            left: startX,
            top: 100,
            fontSize: 60,
            fontFamily: 'serif',
            fontWeight: 'bold',
            originX: alignX,
            fill: '#daa520' // Golden color
        });

        const subtitle = new fabric.IText('OF APPRECIATION', {
            left: startX,
            top: 170,
            fontSize: 24,
            fontFamily: 'sans-serif',
            originX: alignX,
            spacing: 5,
            fill: '#333'
        });

        // 2. Presentation Text
        const presentedTo = new fabric.IText('This certificate is proudly presented to:', {
            left: startX,
            top: 250,
            fontSize: 18,
            fontFamily: 'sans-serif',
            originX: alignX,
            fill: '#666'
        });

        // 3. Recipient Name (Dynamic)
        const recipientName = new fabric.IText('{nama_peserta}', {
            left: startX,
            top: 320,
            fontSize: 50,
            fontFamily: 'cursive',
            originX: alignX,
            fill: '#000',
            data: { type: 'dynamic', variable: '{nama_peserta}' }
        });

        // 4. Body Text
        const bodyText = new fabric.Textbox('For outstanding performance and dedication to the event. Your contribution has been invaluable.', {
            left: startX,
            top: 420,
            width: A4_WIDTH * 0.7,
            fontSize: 16,
            fontFamily: 'sans-serif',
            textAlign: alignX,
            originX: alignX,
            fill: '#444'
        });

        // 5. Signatures
        const sig1 = new fabric.IText('{nama_ttd1}', {
            left: 200,
            top: A4_HEIGHT - 150,
            fontSize: 18,
            fontFamily: 'sans-serif',
            originX: 'center',
            fill: '#333'
        });
        const sigLine1 = new fabric.Line([0, 0, 200, 0], {
            left: 100,
            top: A4_HEIGHT - 160,
            stroke: '#333',
            strokeWidth: 2
        });

        const sig2 = new fabric.IText('{nama_ttd2}', {
            left: A4_WIDTH - 200,
            top: A4_HEIGHT - 150,
            fontSize: 18,
            fontFamily: 'sans-serif',
            originX: 'center',
            fill: '#333'
        });
        const sigLine2 = new fabric.Line([0, 0, 200, 0], {
            left: A4_WIDTH - 300,
            top: A4_HEIGHT - 160,
            stroke: '#333',
            strokeWidth: 2
        });

        canvas.add(title, subtitle, presentedTo, recipientName, bodyText, sig1, sigLine1, sig2, sigLine2);

        // Ensure changes are saved
        updateLayersList();
        saveHistory();
    }

    function applyStandardLayout(type) {
        if (!confirm('This will clear the current canvas. Continue?')) return;
        // Re-apply background if exists
        fabric.Image.fromURL(backgroundUrl, function (img) {
            img.set({
                originX: 'left', originY: 'top',
                selectable: false, evented: false,
                scaleX: A4_WIDTH / img.width, scaleY: A4_HEIGHT / img.height
            });
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
        });

        const isLeft = type === 'left';
        // Center: A4_WIDTH/2, Left: 100
        const startX = isLeft ? 100 : A4_WIDTH / 2;
        const alignX = isLeft ? 'left' : 'center';
        const textAlign = isLeft ? 'left' : 'center';

        // 1. Logo UNWIM (Placeholder)
        // Adjust URL to actual logo if available, for now using a placeholder or generic icon
        fabric.Image.fromURL('https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/Logo_Unwim.png/240px-Logo_Unwim.png', function (img) {
            // Fallback if load fails handled by fabric usually by just not rendering
            if (!img) return;
            img.set({
                left: startX,
                top: 50,
                originX: alignX,
                scaleX: 0.5,
                scaleY: 0.5,
                data: { type: 'logo' }
            });
            canvas.add(img);
        });

        // 2. Header: UNIVERSITAS WINAYA MUKTI
        const header = new fabric.IText('UNIVERSITAS WINAYA MUKTI', {
            left: startX,
            top: 140,
            fontSize: 28,
            fontFamily: 'serif',
            fontWeight: 'bold',
            originX: alignX,
            fill: '#01411C' // Dark Green
        });

        // 3. Certificate Number
        const certNo = new fabric.IText('No. Sertifikat: {nomor_sertifikat}', {
            left: startX,
            top: 190,
            fontSize: 16,
            fontFamily: 'sans-serif',
            originX: alignX,
            fill: '#333',
            data: { type: 'dynamic', variable: '{nomor_sertifikat}' }
        });

        const header2 = new fabric.IText('SERTIFIKAT PENGHARGAAN', {
            left: startX,
            top: 230,
            fontSize: 36,
            fontFamily: 'serif',
            fontWeight: 'bold',
            originX: alignX,
            fill: '#d4af37' // Gold
        });

        // 4. Given To
        const givenTo = new fabric.IText('Sertifikat ini diberikan kepada:', {
            left: startX,
            top: 300,
            fontSize: 18,
            fontFamily: 'sans-serif',
            originX: alignX,
            fill: '#555'
        });

        // 5. Participant Name
        const name = new fabric.IText('{nama_peserta}', {
            left: startX,
            top: 350,
            fontSize: 48,
            fontFamily: 'cursive', // or 'Times New Roman'
            fontWeight: 'bold',
            originX: alignX,
            fill: '#000',
            data: { type: 'dynamic', variable: '{nama_peserta}' }
        });

        // Line under name
        const line = new fabric.Line([0, 0, 400, 0], {
            left: startX,
            top: 410,
            stroke: '#d4af37',
            strokeWidth: 2,
            originX: alignX,
            data: { type: 'line' }
        });
        if (isLeft) line.set({ left: startX, originX: 'left' }); // Correct for left alignment behavior

        // 6. Event Details
        const eventText = new fabric.Textbox('Sebagai Peserta dalam acara: {judul_event}', {
            left: startX,
            top: 440,
            width: A4_WIDTH * 0.7,
            fontSize: 20,
            fontFamily: 'sans-serif',
            textAlign: textAlign,
            originX: alignX,
            fill: '#333',
            data: { type: 'dynamic', variable: '{judul_event}' }
        });

        // 7. Place and Date
        const dateText = new fabric.IText('Bandung, {tanggal_event}', {
            left: startX,
            top: 520,
            fontSize: 16,
            fontFamily: 'sans-serif',
            originX: alignX,
            fill: '#555',
            data: { type: 'dynamic', variable: '{tanggal_event}' }
        });

        // 8. QR Code (Verification)
        fabric.Image.fromURL('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=VERIFY', function (img) {
            img.set({
                left: isLeft ? 100 : A4_WIDTH / 2 - 50, // Center if center layout
                top: 580,
                scaleX: 0.6,
                scaleY: 0.6,
                originX: 'center',
                data: { type: 'qr' }
            });
            if (isLeft) img.set({ left: 100, originX: 'left' });
            canvas.add(img);
        });

        // 9. Signatures (2 TTD)
        // Position them at bottom
        const sigY = A4_HEIGHT - 200;
        
        // TTD 1 (Left side usually, or distributed)
        const ttd1X = isLeft ? 150 : 200;
        const ttd2X = A4_WIDTH - 200;

        // Line (Above name)
        const sigLine1 = new fabric.Line([0, 0, 200, 0], {
            left: ttd1X,
            top: sigY,
            stroke: '#333',
            strokeWidth: 1.5,
            originX: 'center',
            data: { type: 'line' }
        });

        const sig1Name = new fabric.IText('{nama_ttd1}', {
            left: ttd1X,
            top: sigY + 10,
            fontSize: 18,
            fontFamily: 'sans-serif',
            fontWeight: 'bold',
            originX: 'center',
            fill: '#000',
            data: { type: 'dynamic', variable: '{nama_ttd1}' }
        });
        const sig1Jabatan = new fabric.IText('Ketua Pelaksana', { // Placeholder or variable
            left: ttd1X,
            top: sigY + 35,
            fontSize: 14,
            fontFamily: 'sans-serif',
            originX: 'center',
            fill: '#555'
        });

        // TTD 2
        // Line (Above name)
        const sigLine2 = new fabric.Line([0, 0, 200, 0], {
            left: ttd2X,
            top: sigY,
            stroke: '#333',
            strokeWidth: 1.5,
            originX: 'center',
            data: { type: 'line' }
        });

        const sig2Name = new fabric.IText('{nama_ttd2}', {
            left: ttd2X,
            top: sigY + 10,
            fontSize: 18,
            fontFamily: 'sans-serif',
            fontWeight: 'bold',
            originX: 'center',
            fill: '#000',
            data: { type: 'dynamic', variable: '{nama_ttd2}' }
        });
        const sig2Jabatan = new fabric.IText('Rektor', { // Placeholder
            left: ttd2X,
            top: sigY + 35,
            fontSize: 14,
            fontFamily: 'sans-serif',
            originX: 'center',
            fill: '#555'
        });

        canvas.add(header, certNo, header2, givenTo, name, line, eventText, dateText, sigLine1, sig1Name, sig1Jabatan, sigLine2, sig2Name, sig2Jabatan);

        // Ensure changes are saved
        updateLayersList();
        saveHistory();
        toastr.success('Layout ' + type + ' (Custom UNWIM) applied.');
    }

    // --- Popup Logic ---
    function togglePropertiesPopup() {
        $('#properties-popup').fadeToggle(200);
    }

    function updateGrid() {
        const zoom = canvas.getZoom();
        const gridSize = 20 * zoom;
        // Darker grid lines for better visibility (#ced4da)
        $('#grid-overlay').css({
            'background-image': `linear-gradient(#ced4da 1px, transparent 1px), linear-gradient(90deg, #ced4da 1px, transparent 1px)`,
            'background-size': `${gridSize}px ${gridSize}px`
        });

        // Remove grid from bg-editor to avoid duplication
        $('.bg-editor').css('background-image', 'none');
    }

    function fitToContainer() {
        const container = $('.bg-editor');
        const containerHeight = container.height();
        const containerWidth = container.width();

        if (!containerWidth || !containerHeight) return;

        // Calculate available space minus padding
        const padding = 40;
        const availableW = containerWidth - padding;
        const availableH = containerHeight - padding;

        // Calculate scale to fit
        const scaleX = availableW / A4_WIDTH;
        const scaleY = availableH / A4_HEIGHT;

        // Use the smaller scale to ensure it fits both dimensions
        let scale = Math.min(scaleX, scaleY);

        // Clamp scale nicely
        scale = Math.min(Math.max(scale, 0.1), 2.0);

        canvas.setZoom(scale);
        canvas.setWidth(A4_WIDTH * scale);
        canvas.setHeight(A4_HEIGHT * scale);

        canvas.renderAll();
        updateGrid();

        // Center the canvas in the scrollable container
        setTimeout(() => {
            const scrollLeft = (container[0].scrollWidth - container.width()) / 2;
            const scrollTop = (container[0].scrollHeight - container.height()) / 2;
            container.scrollLeft(scrollLeft);
            container.scrollTop(scrollTop);
        }, 10);
    }

    function zoomIn() {
        let zoom = canvas.getZoom();
        zoom = Math.min(zoom + 0.1, 3.0); // Max zoom 3x
        canvas.setZoom(zoom);
        canvas.setWidth(A4_WIDTH * zoom);
        canvas.setHeight(A4_HEIGHT * zoom);
        canvas.renderAll();
        updateGrid();
    }

    function zoomOut() {
        let zoom = canvas.getZoom();
        zoom = Math.max(zoom - 0.1, 0.2); // Min zoom 0.2x
        canvas.setZoom(zoom);
        canvas.setWidth(A4_WIDTH * zoom);
        canvas.setHeight(A4_HEIGHT * zoom);
        canvas.renderAll();
        updateGrid();
    }

    // --- Panning Logic ---
    let isPanningMode = false;
    let isDragging = false;
    let startX, startY, scrollLeft, scrollTop;

    function togglePanMode() {
        isPanningMode = !isPanningMode;
        const btn = $('#btn-pan');
        const container = $('.bg-editor');

        if (isPanningMode) {
            btn.addClass('active btn-primary').removeClass('btn-light');
            container.addClass('panning');

            // Disable canvas interaction
            canvas.selection = false;
            canvas.forEachObject(o => { o.selectable = false; o.evented = false; });
            canvas.defaultCursor = 'grab';
            canvas.hoverCursor = 'grab';
        } else {
            btn.removeClass('active btn-primary').addClass('btn-light');
            container.removeClass('panning');

            // Enable canvas interaction
            canvas.selection = true;
            canvas.forEachObject(o => { o.selectable = true; o.evented = true; });
            canvas.defaultCursor = 'default';
            canvas.hoverCursor = 'move';
        }
        canvas.renderAll();
    }

    // Drag Scrolling Logic
    const editorContainer = document.querySelector('.bg-editor');

    editorContainer.addEventListener('mousedown', (e) => {
        if (!isPanningMode) return;
        isDragging = true;
        editorContainer.classList.add('active'); // Optional: for grabbing cursor style override
        startX = e.pageX - editorContainer.offsetLeft;
        startY = e.pageY - editorContainer.offsetTop;
        scrollLeft = editorContainer.scrollLeft;
        scrollTop = editorContainer.scrollTop;
    });

    editorContainer.addEventListener('mouseleave', () => {
        isDragging = false;
    });

    editorContainer.addEventListener('mouseup', () => {
        isDragging = false;
    });

    editorContainer.addEventListener('mousemove', (e) => {
        if (!isDragging || !isPanningMode) return;
        e.preventDefault();
        const x = e.pageX - editorContainer.offsetLeft;
        const y = e.pageY - editorContainer.offsetTop;
        const walkX = (x - startX); // Scroll speed 1:1
        const walkY = (y - startY);
        editorContainer.scrollLeft = scrollLeft - walkX;
        editorContainer.scrollTop = scrollTop - walkY;
    });

    // --- Copy / Paste / Duplicate Logic ---
    let _clipboard = null;

    function copy() {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            activeObject.clone(function (cloned) {
                _clipboard = cloned;
            }, ['data']); // Include custom 'data' property
        }
    }

    function paste() {
        if (!_clipboard) return;
        _clipboard.clone(function (clonedObj) {
            canvas.discardActiveObject();
            clonedObj.set({
                left: clonedObj.left + 20, // Offset to make it visible
                top: clonedObj.top + 20,
                evented: true,
            });
            if (clonedObj.type === 'activeSelection') {
                // Active selection needs special handling
                clonedObj.canvas = canvas;
                clonedObj.forEachObject(function (obj) {
                    canvas.add(obj);
                });
                clonedObj.setCoords();
            } else {
                canvas.add(clonedObj);
            }
            _clipboard.top += 20; // Shift clipboard for next paste
            _clipboard.left += 20;
            canvas.setActiveObject(clonedObj);
            canvas.requestRenderAll();
            saveHistory();
        }, ['data']);
    }

    function duplicate() {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            activeObject.clone(function (clonedObj) {
                canvas.discardActiveObject();
                clonedObj.set({
                    left: activeObject.left + 20,
                    top: activeObject.top + 20,
                    evented: true,
                });
                canvas.add(clonedObj);
                canvas.setActiveObject(clonedObj);
                canvas.requestRenderAll();
                saveHistory();
            }, ['data']);
        }
    }

    // --- Undo/Redo History ---
    let history = [];
    let historyStep = -1;
    let isHistoryProcessing = false;

    // ... (rest of history logic) ...
    function saveHistory() {
        if (isHistoryProcessing) return;
        // If we are in middle of stack, remove future steps
        if (historyStep < history.length - 1) {
            history = history.slice(0, historyStep + 1);
        }
        history.push(JSON.stringify(canvas.toJSON(['data'])));
        historyStep++;
    }

    // Canvas events hooked in initCanvas() 

    function undo() {
        if (historyStep > 0) {
            isHistoryProcessing = true;
            historyStep--;
            canvas.loadFromJSON(history[historyStep], function () {
                canvas.renderAll();
                isHistoryProcessing = false;
            });
        }
    }

    function redo() {
        if (historyStep < history.length - 1) {
            isHistoryProcessing = true;
            historyStep++;
            canvas.loadFromJSON(history[historyStep], function () {
                canvas.renderAll();
                isHistoryProcessing = false;
            });
        }
    }

    // --- Keyboard Shortcuts ---
    document.addEventListener('keydown', function (e) {
        // Prevent if typing in inputs
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        // Copy (Ctrl+C)
        if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
            e.preventDefault();
            copy();
        }

        // Paste (Ctrl+V)
        if ((e.ctrlKey || e.metaKey) && e.key === 'v') {
            e.preventDefault();
            paste();
        }

        // Duplicate (Alt+V)
        if (e.altKey && e.key === 'v') {
            e.preventDefault();
            duplicate();
        }

        // Delete Object (Delete or Backspace)
        if (e.key === 'Delete' || e.key === 'Backspace') {
            e.preventDefault(); // Prevent browser back navigation
            const activeObjects = canvas.getActiveObjects();
            if (activeObjects.length) {
                canvas.discardActiveObject();
                activeObjects.forEach(function (obj) {
                    canvas.remove(obj);
                });
                saveHistory(); // Save state after delete
            }
        }

        // Undo (Ctrl+Z)
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
            e.preventDefault();
            undo();
        }

        // Redo (Ctrl+Y or Ctrl+Shift+Z)
        if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.shiftKey && e.key === 'z'))) {
            e.preventDefault();
            redo();
        }
    });

    // Keyboard shortcuts above...

    function onObjectSelected(e) {
        const obj = canvas.getActiveObject();
        if (!obj) return;

        $('#inspector-none').hide();
        $('#inspector').show();

        // Populate inspector
        // Handle Size: Text uses 'fontSize', Line uses 'strokeWidth'
        const sizeVal = obj.type === 'line' ? obj.strokeWidth : obj.fontSize;
        $('#prop-fontSize').val(sizeVal || '');

        // Handle color: Text uses 'fill', Line uses 'stroke'
        const colorVal = obj.type === 'line' ? obj.stroke : obj.fill;
        $('#prop-fill').val(colorVal || '#000000');

        $('#prop-fontFamily').val(obj.fontFamily || 'Arial');
    }

    function addText(text) {
        const txt = new fabric.IText(text, {
            left: 100,
            top: 100,
            fontSize: 24,
            fontFamily: 'Arial',
            fill: '#000000'
        });
        canvas.add(txt);
        canvas.setActiveObject(txt);
    }

    function addDynamicText(variable) {
        const txt = new fabric.IText(variable, {
            left: 100,
            top: 150,
            fontSize: 30,
            fontFamily: 'Times New Roman',
            fontWeight: 'bold',
            fill: '#000000',
            data: { type: 'dynamic', variable: variable }
        });
        canvas.add(txt);
        canvas.setActiveObject(txt);
    }

    function addQR() {
        // Dummy QR placeholder
        fabric.Image.fromURL('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=PREVIEW', function (img) {
            img.set({
                left: A4_WIDTH - 200,
                top: A4_HEIGHT - 200,
                scaleX: 0.8,
                scaleY: 0.8,
                data: { type: 'qr' }
            });
            canvas.add(img);
            canvas.setActiveObject(img);
        });
    }

    function addSignature() {
        // Tanda Tangan placeholder
        const group = new fabric.Group([
            new fabric.Rect({
                width: 200,
                height: 100,
                fill: 'rgba(0,0,0,0.05)',
                stroke: '#ccc',
                strokeDashArray: [5, 5]
            }),
            new fabric.IText('Tanda Tangan', {
                fontSize: 14,
                originX: 'center',
                originY: 'center',
                left: 100,
                top: 50
            })
        ], {
            left: 100,
            top: A4_HEIGHT - 250,
            data: { type: 'signature' }
        });
        canvas.add(group);
        canvas.setActiveObject(group);
    }

    function updateSelectedProp(prop, value) {
        const obj = canvas.getActiveObject();
        if (!obj) return;

        if (prop === 'fontWeight') {
            const nextValue = obj.fontWeight === 'bold' ? 'normal' : 'bold';
            obj.set('fontWeight', nextValue);
        } else if (prop === 'fontStyle') {
            const nextValue = obj.fontStyle === 'italic' ? 'normal' : 'italic';
            obj.set('fontStyle', nextValue);
        } else if (prop === 'fill' && obj.type === 'line') {
            // Lines use 'stroke' instead of 'fill'
            obj.set('stroke', value);
        } else if (prop === 'fontSize' && obj.type === 'line') {
            // Lines use 'strokeWidth' instead of 'fontSize'
            obj.set('strokeWidth', parseInt(value));
        } else {
            obj.set(prop, value);
        }

        canvas.renderAll();
    }

    function deleteSelected() {
        const obj = canvas.getActiveObject();
        if (obj) {
            canvas.remove(obj);
            canvas.discardActiveObject().renderAll();
        }
    }

    function bringToFront() {
        const obj = canvas.getActiveObject();
        if (obj) {
            obj.bringToFront();
            canvas.renderAll();
        }
    }

    function bringForward() {
        const obj = canvas.getActiveObject();
        if (obj) {
            obj.bringForward();
            canvas.renderAll();
        }
    }

    function sendToBack() {
        const obj = canvas.getActiveObject();
        if (obj) {
            obj.sendToBack();
            // Ensure background image stays below everything
            const bg = canvas.backgroundImage;
            if (bg) bg.sendToBack();
            canvas.renderAll();
        }
    }

    function sendBackwards() {
        const obj = canvas.getActiveObject();
        if (obj) {
            obj.sendBackwards();
            // Ensure background image stays below
            const bg = canvas.backgroundImage;
            if (bg) bg.sendToBack();
            canvas.renderAll();
        }
    }

    function alignObject(alignment) {
        const obj = canvas.getActiveObject();
        if (!obj) return;

        if (alignment === 'center') {
            obj.set({ originX: 'center' });
            if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                obj.set({ textAlign: 'center' });
            }
            obj.centerH();
        } else if (alignment === 'left') {
            obj.set({ originX: 'left' });
            if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                obj.set({ textAlign: 'left' });
            }
            obj.set({ left: 100 }); // Standard left margin
        }

        obj.setCoords();
        canvas.renderAll();
        saveHistory();
    }



    function changeBackgroundTrigger() {
        $('#bg-input').click();
    }

    function addImageTrigger() {
        $('#img-input').click();
    }

    function addSignatureTrigger() {
        $('#ttd-input').click();
    }

    function addStampTrigger() {
        $('#cap-input').click();
    }

    function uploadBackground(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                fabric.Image.fromURL(e.target.result, function (img) {
                    img.set({
                        originX: 'left', originY: 'top',
                        selectable: false, evented: false,
                        scaleX: A4_WIDTH / img.width, scaleY: A4_HEIGHT / img.height
                    });
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                });
            };
            reader.readAsDataURL(input.files[0]);

            // Mark that background needs to be uploaded to server on save
            window.newBackgroundFile = input.files[0];
        }
    }

    function uploadImage(input, type = 'custom_image') {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                fabric.Image.fromURL(e.target.result, function (img) {
                    img.scaleToWidth(200);
                    img.set({ left: 100, top: 100, data: { type: type } });
                    canvas.add(img);
                    canvas.setActiveObject(img);
                });
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addLine() {
        const line = new fabric.Line([50, 50, 350, 50], {
            stroke: '#000', strokeWidth: 2, data: { type: 'line' }
        });
        canvas.add(line);
        canvas.setActiveObject(line);
    }

    function saveLayout() {
        // 1. Save current responsive state
        const currentZoom = canvas.getZoom();
        const currentWidth = canvas.getWidth();
        const currentHeight = canvas.getHeight();

        // 2. Reset to 1:1 Scale (A4 Standard) for consistent saving
        // This ensures the saved JSON is device-independent
        canvas.setZoom(1);
        canvas.setWidth(A4_WIDTH);
        canvas.setHeight(A4_HEIGHT);

        // 3. Export JSON from the normalized canvas
        const layoutJson = JSON.stringify(canvas.toJSON(['data']));

        // 4. Restore the responsive view immediately
        canvas.setZoom(currentZoom);
        canvas.setWidth(currentWidth);
        canvas.setHeight(currentHeight);
        canvas.renderAll();

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('layout_json', layoutJson);

        if (window.newBackgroundFile) {
            formData.append('background_file', window.newBackgroundFile);
        } else if (window.galleryBackgroundUrl) {
            formData.append('gallery_background_url', window.galleryBackgroundUrl);
        }

        $.ajax({
            url: '{{ route('certificate-templates.updateLayout', $template->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    window.newBackgroundFile = null;
                    window.galleryBackgroundUrl = null;
                }
            },
            error: function () {
                toastr.error('Terjadi kesalahan saat menyimpan.');
            }
        });
    }



    function openGallery(type = 'logo') {
        currentGalleryType = type;
        const modal = $('#galleryModal');
        modal.modal('show');

        // Reset tabs UI
        $('#galleryTabs .nav-link').removeClass('active');
        $(`#tab-${type}`).addClass('active');

        switchGalleryTab(type);
    }

    function switchGalleryTab(type) {
        currentGalleryType = type;
        const content = $('#gallery-content');
        const loading = $('#gallery-loading');
        const empty = $('#gallery-empty');
        const label = $('#gallery-type-label');

        // Update labels
        const labels = {
            'logo': 'Koleksi Logo',
            'signature': 'Koleksi Tanda Tangan',
            'stamp': 'Koleksi Cap/Stempel',
            'background': 'Koleksi Background'
        };
        label.text(labels[type] || 'Koleksi Media');
        $('#upload-type').val(type);
        $('#gallery-upload-form').hide();

        content.empty().hide();
        empty.hide();
        loading.show();

        $.get("{{ route('certificate-media.api') }}", { type: type }, function (data) {
            loading.hide();
            if (data.length === 0) {
                empty.show();
            } else {
                data.forEach(item => {
                    const col = $(`
                        <div class="col-md-3 col-sm-4 mb-3 px-2">
                            <div class="card h-100 shadow-sm border gallery-item-card" onclick="addFromGallery('${item.url}', '${item.type}')" style="cursor:pointer; transition: transform 0.2s;">
                                <div class="bg-light d-flex align-items-center justify-content-center p-2" style="height: 120px;">
                                    <img src="${item.url}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="card-footer p-2 text-center bg-white border-0">
                                    <small class="text-truncate d-block font-weight-bold" title="${item.name}">${item.name}</small>
                                </div>
                            </div>
                        </div>
                    `);
                    content.append(col);
                });
                content.show();
            }
        });
    }

    // --- Layer Management & Advanced Properties ---

    function addFromGallery(url, type) {
        if (type === 'background') {
            fabric.Image.fromURL(url, function (img) {
                img.set({
                    originX: 'left',
                    originY: 'top',
                    selectable: false,
                    evented: false,
                    scaleX: A4_WIDTH / img.width,
                    scaleY: A4_HEIGHT / img.height
                });
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                window.galleryBackgroundUrl = url;
                $('#galleryModal').modal('hide');
                saveHistory();
            });
        } else {
            fabric.Image.fromURL(url, function (img) {
                if (img.width > 300) img.scaleToWidth(300);

                img.set({
                    left: 100,
                    top: 100,
                    data: { type: type }
                });
                canvas.add(img);
                canvas.setActiveObject(img);
                $('#galleryModal').modal('hide');
                saveHistory();
            });
        }
    }

    function updateLayersList() {
        const list = $('#layer-list');
        list.empty();

        const objects = canvas.getObjects().slice().reverse(); // Show top layer first

        if (objects.length === 0) {
            list.html('<div class="text-center text-muted py-3 small">No layers</div>');
            return;
        }

        objects.forEach((obj, index) => {
            // Determine content type/name
            let name = 'Unknown Layer';
            let icon = 'fa-layer-group';

            if (obj.data && obj.data.type) {
                const t = obj.data.type;
                if (t === 'logo') { name = 'Logo Universitas'; icon = 'fa-image text-info'; }
                else if (t === 'signature') { name = 'Tanda Tangan'; icon = 'fa-signature text-primary'; }
                else if (t === 'stamp') { name = 'Cap / Stempel'; icon = 'fa-stamp text-warning'; }
                else if (t === 'qr') { name = 'QR Code'; icon = 'fa-qrcode text-danger'; }
                else if (t === 'dynamic') { name = obj.data.variable || 'Dynamic Text'; icon = 'fa-code text-success'; }
                else if (t === 'line') { name = 'Garis'; icon = 'fa-minus'; }
                else if (t === 'custom_image') { name = 'Gambar'; icon = 'fa-image'; }
            } else if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                name = obj.text ? (obj.text.substring(0, 15) + (obj.text.length > 15 ? '...' : '')) : 'Teks';
                icon = 'fa-font';
            } else if (obj.type === 'line') {
                name = 'Garis';
                icon = 'fa-minus';
            } else if (obj.type === 'image') {
                name = 'Gambar';
                icon = 'fa-image';
            } else if (obj.type === 'rect') {
                name = 'Kotak';
                icon = 'fa-vector-square';
            }

            const isActive = canvas.getActiveObject() === obj;
            const fabricIndex = objects.length - 1 - index; // Original index in canvas._objects

            const item = $(`
                <div class="layer-item ${isActive ? 'active' : ''}" data-fabric-index="${fabricIndex}" onclick="selectLayer(${fabricIndex})">
                    <div class="d-flex align-items-center text-truncate">
                        <i class="fas ${icon} mr-2" style="width:16px;"></i>
                        <span class="text-truncate" style="max-width: 120px;">${name}</span>
                    </div>
                    <div class="layer-controls">
                        <i class="fas ${obj.visible ? 'fa-eye' : 'fa-eye-slash'}" onclick="toggleVisibility(event, ${fabricIndex})"></i>
                        <i class="fas ${obj.lockMovementX ? 'fa-lock' : 'fa-lock-open'}" onclick="toggleLock(event, ${fabricIndex})"></i>
                         <i class="fas fa-trash text-danger ml-1" onclick="deleteLayer(event, ${fabricIndex})"></i>
                    </div>
                </div>
             `);
            list.append(item);
        });
    }

    function selectLayer(index) {
        const objects = canvas.getObjects();
        if (objects[index]) {
            canvas.setActiveObject(objects[index]);
            canvas.renderAll();
        }
    }

    function toggleVisibility(e, index) {
        e.stopPropagation();
        const obj = canvas.item(index);
        if (obj) {
            obj.visible = !obj.visible;
            if (!obj.visible) canvas.discardActiveObject();
            canvas.renderAll();
            saveHistory();
        }
    }

    function toggleLock(e, index) {
        e.stopPropagation();
        const obj = canvas.item(index);
        if (obj) {
            const isLocked = !obj.lockMovementX;
            obj.set({
                lockMovementX: isLocked,
                lockMovementY: isLocked,
                lockRotation: isLocked,
                lockScalingX: isLocked,
                lockScalingY: isLocked
            });
            canvas.renderAll();
            updateLayersList(); // Refresh icon
        }
    }

    function deleteLayer(e, index) {
        e.stopPropagation();
        if (confirm('Hapus layer ini?')) {
            const obj = canvas.item(index);
            canvas.remove(obj);
            canvas.renderAll();
            saveHistory();
        }
    }

    function updateTransform(prop, value) {
        const obj = canvas.getActiveObject();
        if (!obj) return;

        value = parseFloat(value);
        if (isNaN(value)) return;

        if (prop === 'left' || prop === 'top') {
            obj.set(prop, value);
        } else if (prop === 'width') {
            obj.scaleToWidth(value);
        } else if (prop === 'height') {
            obj.scaleToHeight(value);
        }

        obj.setCoords();
        canvas.renderAll();
        saveHistory();
    }

    function updateInspectorInputs() {
        const obj = canvas.getActiveObject();
        if (!obj) return;

        // Update Transform Inputs
        $('#prop-x').val(Math.round(obj.left));
        $('#prop-y').val(Math.round(obj.top));
        $('#prop-w').val(Math.round(obj.getScaledWidth()));
        $('#prop-h').val(Math.round(obj.getScaledHeight()));

        // Update Hex Color Label
        const color = $('#prop-fill').val();
        $('#hex-view').text(color);
    }

    // Hook to events
    canvas.on('selection:created', () => { updateLayersList(); updateInspectorInputs(); });
    canvas.on('selection:updated', () => { updateLayersList(); updateInspectorInputs(); });
    canvas.on('selection:cleared', () => { updateLayersList(); });
    canvas.on('object:added', () => { updateLayersList(); });
    canvas.on('object:removed', () => { updateLayersList(); });
    canvas.on('object:modified', () => { updateLayersList(); updateInspectorInputs(); });
    canvas.on('object:moving', () => { updateInspectorInputs(); });
    canvas.on('object:scaling', () => { updateInspectorInputs(); });

    function toggleUploadForm() {
        $('#gallery-upload-form').slideToggle();
    }

    $(document).ready(function () {
        $('#ajax-upload-form').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

            $.ajax({
                url: "{{ route('certificate-media.api.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    toastr.success('Media berhasil diunggah');
                    $('#ajax-upload-form')[0].reset();
                    $('#gallery-upload-form').hide();
                    switchGalleryTab(currentGalleryType);
                },
                error: function (err) {
                    toastr.error('Gagal mengunggah media. Pastikan file adalah gambar & max 2MB.');
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });
    });

    function addFromGallery(url, type) {
        fabric.Image.fromURL(url, function (img) {
            if (type === 'background') {
                img.set({
                    originX: 'left', originY: 'top',
                    selectable: false, evented: false,
                    scaleX: A4_WIDTH / img.width, scaleY: A4_HEIGHT / img.height
                });
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                window.newBackgroundFile = null;
                window.galleryBackgroundUrl = url;
            } else {
                img.scaleToWidth(200);
                img.set({
                    left: 100,
                    top: 100,
                    data: { type: type, url: url } // Store URL for persistent reference
                });
                canvas.add(img);
                canvas.setActiveObject(img);
            }
            $('#galleryModal').modal('hide');
        });
    }

    // Initialize Responsive Canvas
    fitToContainer();
    window.addEventListener('resize', fitToContainer);

    // Initialize History
    setTimeout(function () {
        fitToContainer(); // Ensure fit after full load
        saveHistory();
        checkUrlParams(); // Check for auto-add params
    }, 1000);

    function checkUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const file = urlParams.get('file');
        const name = urlParams.get('name');
        const type = urlParams.get('type') || 'signature';

        if (file) {
            // First check if it matches a gallery item via API
            $.get("{{ route('certificate-media.api') }}", { type: type }, function (data) {
                const match = data.find(item => item.name === name || item.name === file);

                if (match) {
                    // Start adding from gallery match
                    addFromGallery(match.url, type);
                    toastr.success('Menambahkan ' + (name || 'item') + ' dari koleksi.');
                } else {
                    // Assume it is a direct file path or filename in storage
                    // Construct a likely path. If it's just a filename, assume standard storage path
                    let itemsUrl = file;
                    if (!file.startsWith('http')) {
                        // Default path assumption based on type
                        if (type === 'signature') {
                            itemsUrl = '{{ asset("storage/certificates/signatures") }}/' + file;
                        } else if (type === 'logo') {
                            itemsUrl = '{{ asset("storage/certificates/logos") }}/' + file;
                        } else {
                            // Fallback to media folder if unknown
                            itemsUrl = '{{ asset("storage/certificates/media") }}/' + file;
                        }
                    }

                    // Try to load
                    fabric.Image.fromURL(itemsUrl, function (img) {
                        if (!img) {
                            toastr.warning('Gagal memuat gambar otomatis. File mungkin tidak ditemukan.');
                            return;
                        }

                        img.scaleToWidth(200);

                        // Default position for signature center bottom
                        let topPos = 100;
                        let leftPos = 100;

                        if (type === 'signature') {
                            topPos = A4_HEIGHT - 250;
                            leftPos = 150;
                        }

                        img.set({
                            left: leftPos,
                            top: topPos,
                            data: { type: type, url: itemsUrl, autoAdded: true }
                        });
                        canvas.add(img);
                        canvas.setActiveObject(img);
                        toastr.success('Item otomatis ditambahkan: ' + (name || file));
                        saveHistory();
                    }, { crossOrigin: 'anonymous' });
                }
            });

            // Clean URL
            const url = new URL(window.location.href);
            url.searchParams.delete('file');
            url.searchParams.delete('name');
            url.searchParams.delete('type');
            window.history.replaceState({}, document.title, url);
        }
    }
</script>
@stop