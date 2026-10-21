<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Builder: {{ $template->name }}</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#197fe6",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111921",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .config-section { display: none; }
        .config-section.active { display: block; }
        
        /* Device Frames */
        .device-frame { transition: all 0.3s ease; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .device-desktop { width: 100%; height: 100%; border-radius: 0; border: none; }
        .device-tablet { width: 768px; height: 1024px; max-height: 90vh; border-radius: 20px; border: 12px solid #333; }
        .device-mobile { width: 375px; height: 667px; max-height: 90vh; border-radius: 30px; border: 12px solid #333; }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 h-screen overflow-hidden flex flex-col antialiased">
    
    <div class="flex h-full w-full overflow-hidden">
        <!-- COL 1: Components (Left Panel) -->
        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 z-20 shadow-sm overflow-hidden">
            <div class="h-14 flex items-center px-4 border-b border-gray-100 bg-gray-50/50">
                <span class="material-icons text-primary mr-2 text-xl">view_quilt</span>
                <h2 class="font-bold text-gray-800 text-sm tracking-tight uppercase">Components</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                <!-- Mandatory Section: Header -->
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl relative group opacity-75">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="material-icons text-gray-400 mr-2 text-sm">lock</span>
                            <span class="text-sm font-bold text-gray-700">University Header</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 px-1.5 py-0.5 border border-gray-300 rounded uppercase">Mandatory</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-tight">Branding identity & University information.</p>
                </div>

                <!-- Mandatory Section: Event Info -->
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl relative group opacity-75">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="material-icons text-gray-400 mr-2 text-sm">lock</span>
                            <span class="text-sm font-bold text-gray-700">Event Information</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 px-1.5 py-0.5 border border-gray-300 rounded uppercase">Mandatory</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-tight">Title, Date, Time, and Venue details.</p>
                </div>

                <!-- Optional: Participant Info -->
                <div class="p-3 bg-white border border-gray-200 rounded-xl hover:border-primary/50 transition-all shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons text-primary mr-2 text-sm">person</span>
                            <span class="text-sm font-bold text-gray-700">Guest Details</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer scale-90">
                            <input type="checkbox" class="sr-only peer config-input" data-config="sections.show_participant_info" {{ ($template->config['sections']['show_participant_info'] ?? true) ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                        </label>
                    </div>
                    
                    <div class="space-y-2 border-t pt-2 border-gray-50">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-gray-600">Show Nomor Kursi</span>
                            <label class="relative inline-flex items-center cursor-pointer scale-[0.8]">
                                <input type="checkbox" class="sr-only peer config-input" data-config="sections.show_seat_number" {{ ($template->config['sections']['show_seat_number'] ?? true) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Optional: Barcode System -->
                <div class="p-3 bg-white border border-gray-200 rounded-xl hover:border-primary/50 transition-all shadow-sm space-y-3">
                    <div class="flex items-center">
                        <span class="material-icons text-primary mr-2 text-sm">qr_code_2</span>
                        <span class="text-sm font-bold text-gray-700">Presence Controls</span>
                    </div>
                    
                    <div class="space-y-2 border-t pt-2 border-gray-50">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-gray-600">Absensi Datang</span>
                            <label class="relative inline-flex items-center cursor-pointer scale-[0.8]">
                                <input type="checkbox" class="sr-only peer config-input" data-config="sections.show_barcode_masuk" {{ ($template->config['sections']['show_barcode_masuk'] ?? true) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-gray-600">Absensi Pulang</span>
                            <label class="relative inline-flex items-center cursor-pointer scale-[0.8]">
                                <input type="checkbox" class="sr-only peer config-input" data-config="sections.show_barcode_pulang" {{ ($template->config['sections']['show_barcode_pulang'] ?? false) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-fuchsia-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-gray-600">Absensi Pulang (Konsumsi)</span>
                            <label class="relative inline-flex items-center cursor-pointer scale-[0.8]">
                                <input type="checkbox" class="sr-only peer config-input" data-config="sections.show_barcode_konsumsi" {{ ($template->config['sections']['show_barcode_konsumsi'] ?? false) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-amber-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Optional: Rundown -->
                <div class="p-3 bg-white border border-gray-200 rounded-xl hover:border-primary/50 transition-all shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="material-icons text-primary mr-2 text-sm">list_alt</span>
                            <span class="text-sm font-bold text-gray-700">Event Rundown</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer scale-90">
                            <input type="checkbox" class="sr-only peer config-input" data-config="sections.show_rundown" {{ ($template->config['sections']['show_rundown'] ?? true) ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                        </label>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-tight">Details of event schedule.</p>
                </div>

                 <!-- Mandatory Section: Rules -->
                 <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl relative group opacity-75">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="material-icons text-gray-400 mr-2 text-sm">lock</span>
                            <span class="text-sm font-bold text-gray-700">Tata Tertib</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 px-1.5 py-0.5 border border-gray-300 rounded uppercase">Mandatory</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-tight">University rules & regulations.</p>
                </div>

                <!-- Mandatory Section: Footer -->
                 <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl relative group opacity-75">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="material-icons text-gray-400 mr-2 text-sm">lock</span>
                            <span class="text-sm font-bold text-gray-700">System Footer</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 px-1.5 py-0.5 border border-gray-300 rounded uppercase">Mandatory</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-tight">System credits & branding deco.</p>
                </div>
            </div>
            
            <div class="p-4 bg-gray-50 border-t border-gray-100 italic text-[10px] text-gray-400 text-center">
                Mandatory items cannot be removed.
            </div>
        </aside>

        <!-- COL 2: Canvas (Center Panel) -->
        <main class="flex-1 flex flex-col bg-[#eef0f2] relative overflow-hidden">
            <!-- Header Toolbar -->
            <div class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shadow-sm">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                         <h1 class="text-base font-bold text-gray-800">{{ $template->name }}</h1>
                         <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-wider">{{ $template->category }}</span>
                    </div>
                    <div class="h-6 w-px bg-gray-200"></div>
                    <!-- Device Toggles -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1 gap-1">
                        <button onclick="setDevice('desktop')" class="p-1.5 rounded-md hover:bg-white hover:shadow-sm text-gray-500 hover:text-primary transition-all device-btn active" data-device="desktop">
                            <span class="material-icons text-sm">desktop_windows</span>
                        </button>
                        <button onclick="setDevice('tablet')" class="p-1.5 rounded-md hover:bg-white hover:shadow-sm text-gray-500 hover:text-primary transition-all device-btn" data-device="tablet">
                            <span class="material-icons text-sm">tablet_mac</span>
                        </button>
                        <button onclick="setDevice('mobile')" class="p-1.5 rounded-md hover:bg-white hover:shadow-sm text-gray-500 hover:text-primary transition-all device-btn" data-device="mobile">
                            <span class="material-icons text-sm">smartphone</span>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button onclick="saveConfig()" id="btnSave" class="flex items-center px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-blue-600 rounded-lg shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                        <span class="material-icons text-xs mr-2">save</span>
                        SAVE CHANGES
                    </button>
                    <a href="{{ route('invitation-templates.index') }}" class="flex items-center px-3 py-2 text-xs font-bold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                        <span class="material-icons text-xs mr-2">close</span>
                        CLOSE
                    </a>
                </div>
            </div>

            <!-- Canvas Viewport -->
            <div class="flex-1 overflow-auto flex items-center justify-center p-8 custom-scrollbar">
                <div id="previewWrapper" class="device-frame device-desktop bg-white overflow-hidden relative shadow-[0_32px_64px_-12px_rgba(0,0,0,0.14)]">
                    <iframe id="previewFrame" src="{{ route('invitation-templates.preview', $template->id) }}" class="w-full h-full border-0"></iframe>
                </div>
            </div>
            
            <!-- Zoom Controls & Helpers (Floating) -->
            <div class="absolute bottom-6 right-6 flex flex-col gap-2 z-10">
                <button onclick="window.open('{{ route('invitation-templates.preview', $template->id) }}', '_blank')" class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-primary transition-all hover:-translate-y-1" title="Open Full Preview">
                    <span class="material-icons text-xl">open_in_new</span>
                </button>
            </div>
        </main>

        <!-- COL 3: Properties (Right Panel) -->
        <aside class="w-80 bg-white border-l border-gray-200 flex flex-col flex-shrink-0 z-20 shadow-sm overflow-hidden">
            <div class="h-14 flex items-center px-4 border-b border-gray-100 bg-gray-50/50">
                <span class="material-icons text-primary mr-2 text-xl">tune</span>
                <h2 class="font-bold text-gray-800 text-sm tracking-tight uppercase">Properties</h2>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <!-- Group: Look & Feel -->
                <div class="p-4 border-b border-gray-50">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Design Colors</h3>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        @foreach([
                            'primary' => ['label' => 'Primary', 'icon' => 'palette'],
                            'secondary' => ['label' => 'Secondary', 'icon' => 'format_color_fill'],
                            'bg' => ['label' => 'Page BG', 'icon' => 'wallpaper'],
                            'card_bg' => ['label' => 'Card BG', 'icon' => 'crop_din'],
                            'title_color' => ['label' => 'Title', 'icon' => 'title'],
                            'text' => ['label' => 'Body', 'icon' => 'text_fields']
                        ] as $key => $meta)
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-600 flex items-center gap-1.5">
                                <span class="material-icons text-[12px] opacity-40">{{ $meta['icon'] }}</span>
                                {{ $meta['label'] }}
                            </label>
                            <div class="flex items-center gap-1.5 p-1 bg-gray-50 rounded border border-gray-100">
                                <input type="color" class="h-6 w-6 p-0 border-0 bg-transparent cursor-pointer config-input rounded-sm" 
                                       data-config="colors.{{ $key }}" 
                                       value="{{ $template->config['colors'][$key] ?? '#000000' }}">
                                <input type="text" class="w-full bg-transparent border-0 p-0 text-[10px] text-gray-500 font-mono focus:ring-0 uppercase" 
                                       value="{{ $template->config['colors'][$key] ?? '#000000' }}" 
                                       onchange="this.previousElementSibling.value = this.value; this.previousElementSibling.dispatchEvent(new Event('change'));">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Group: Header Appearance -->
                <div class="p-4 border-b border-gray-50 bg-blue-50/20">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Header Appearance</h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-600">Header Logo URL</label>
                            <div class="relative group">
                                <input type="text" class="w-full rounded-md border-gray-200 text-xs p-2 pr-10 config-input font-mono"
                                       id="input-header-logo"
                                       data-config="layout.header_logo"
                                       value="{{ $template->config['layout']['header_logo'] ?? '' }}"
                                       placeholder="https://example.com/logo.png">
                                <button onclick="openMediaSelector('layout.header_logo', 'input-header-logo')"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-primary hover:text-primary-dark transition-colors">
                                    <span class="material-icons text-[18px]">photo_library</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-600">Background Image URL</label>
                            <div class="relative group">
                                <input type="text" class="w-full rounded-md border-gray-200 text-xs p-2 pr-10 config-input font-mono" 
                                       id="input-header-bg-image"
                                       data-config="layout.header_bg_image" 
                                       value="{{ $template->config['layout']['header_bg_image'] ?? '' }}"
                                       placeholder="https://example.com/banner.jpg">
                                <button onclick="openMediaSelector('layout.header_bg_image', 'input-header-bg-image')" 
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-primary hover:text-primary-dark transition-colors">
                                    <span class="material-icons text-[18px]">photo_library</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Overlay Color</label>
                                <div class="flex items-center gap-1.5 p-1 bg-white rounded border border-gray-100">
                                    <input type="color" class="h-6 w-6 p-0 border-0 bg-transparent cursor-pointer config-input rounded-sm" 
                                           data-config="layout.header_overlay_color" 
                                           value="{{ $template->config['layout']['header_overlay_color'] ?? '#4f46e5' }}">
                                    <input type="text" class="w-full bg-transparent border-0 p-0 text-[10px] text-gray-500 font-mono focus:ring-0 uppercase" 
                                           value="{{ $template->config['layout']['header_overlay_color'] ?? '#4f46e5' }}" 
                                           onchange="this.previousElementSibling.value = this.value; this.previousElementSibling.dispatchEvent(new Event('change'));">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[10px] font-bold text-gray-600">Overlay Opacity</label>
                                    <span class="text-[10px] font-mono text-primary font-bold" id="val-header-opacity">{{ $template->config['layout']['header_overlay_opacity'] ?? '0.8' }}</span>
                                </div>
                                <input type="range" min="0" max="1" step="0.05" 
                                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary config-input" 
                                       data-config="layout.header_overlay_opacity" 
                                       value="{{ $template->config['layout']['header_overlay_opacity'] ?? '0.8' }}"
                                       oninput="updateLabel(this, 'val-header-opacity')">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group: Typography -->
                <div class="p-4 border-b border-gray-50">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Typography</h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-600">Font Family</label>
                            <select class="w-full rounded-md border-gray-200 text-xs shadow-sm config-input focus:border-primary focus:ring-1 focus:ring-primary" data-config="typography.font">
                                @foreach(['Inter', 'Roboto', 'Poppins', 'Outfit', 'Open Sans', 'Lato', 'Montserrat', 'Lora', 'Cormorant Garamond', 'Playfair Display'] as $font)
                                    <option value="{{ $font }}" {{ ($template->config['typography']['font'] ?? '') == $font ? 'selected' : '' }}>{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="space-y-3">
                             <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-gray-600 uppercase">Title Size</label>
                                <span class="text-[10px] font-mono text-primary font-bold bg-blue-50 px-1.5 rounded" id="val-title-size">{{ $template->config['typography']['title_size'] ?? '2.5rem' }}</span>
                            </div>
                            <input type="range" min="1.5" max="4.5" step="0.1" 
                                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary config-input" 
                                   data-config="typography.title_size" 
                                   value="{{ rtrim($template->config['typography']['title_size'] ?? '2.5rem', 'rem') }}"
                                   oninput="updateLabel(this, 'val-title-size', 'rem')">
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-gray-600 uppercase">Body Size</label>
                                <span class="text-[10px] font-mono text-primary font-bold bg-blue-50 px-1.5 rounded" id="val-body-size">{{ $template->config['typography']['body_size'] ?? '1rem' }}</span>
                            </div>
                            <input type="range" min="0.7" max="1.3" step="0.05" 
                                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary config-input" 
                                   data-config="typography.body_size" 
                                   value="{{ rtrim($template->config['typography']['body_size'] ?? '1rem', 'rem') }}"
                                   oninput="updateLabel(this, 'val-body-size', 'rem')">
                        </div>

                         <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-gray-600 uppercase">Spacing</label>
                                <select class="w-full rounded-md border-gray-200 text-xs shadow-sm config-input" data-config="layout.spacing">
                                    <option value="compact" {{ ($template->config['layout']['spacing'] ?? '') == 'compact' ? 'selected' : '' }}>Compact</option>
                                    <option value="normal" {{ ($template->config['layout']['spacing'] ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="wide" {{ ($template->config['layout']['spacing'] ?? '') == 'wide' ? 'selected' : '' }}>Wide</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group: UI Style (Buttons & Advanced) -->
                <div class="p-4 space-y-6">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Button Styling</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Shape</label>
                                <select class="w-full rounded-md border-gray-200 text-xs config-input" data-config="buttons.shape">
                                    <option value="rounded" {{ ($template->config['buttons']['shape'] ?? '') == 'rounded' ? 'selected' : '' }}>Rounded</option>
                                    <option value="square" {{ ($template->config['buttons']['shape'] ?? '') == 'square' ? 'selected' : '' }}>Square</option>
                                    <option value="pill" {{ ($template->config['buttons']['shape'] ?? '') == 'pill' ? 'selected' : '' }}>Pill</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Size</label>
                                <select class="w-full rounded-md border-gray-200 text-xs config-input" data-config="buttons.size">
                                    <option value="small" {{ ($template->config['buttons']['size'] ?? '') == 'small' ? 'selected' : '' }}>Small</option>
                                    <option value="medium" {{ ($template->config['buttons']['size'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="large" {{ ($template->config['buttons']['size'] ?? '') == 'large' ? 'selected' : '' }}>Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                            <label class="text-[11px] font-bold text-gray-700">Drop Shadows</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer config-input" data-config="buttons.shadow" {{ ($template->config['buttons']['shadow'] ?? false) ? 'checked' : '' }}>
                                <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>

                    <hr class="border-gray-50">

                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Advanced Tools</h3>
                        <div class="space-y-4">
                             <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Card Radius</label>
                                <input type="text" class="w-full rounded-md border-gray-200 text-xs p-2 config-input font-mono" data-config="advanced.border_radius" value="{{ $template->config['advanced']['border_radius'] ?? '24px' }}">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Animation</label>
                                <select class="w-full rounded-md border-gray-200 text-xs config-input" data-config="advanced.animation">
                                    <option value="none" {{ ($template->config['advanced']['animation'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                                    <option value="fade" {{ ($template->config['advanced']['animation'] ?? '') == 'fade' ? 'selected' : '' }}>Fade In</option>
                                    <option value="slide" {{ ($template->config['advanced']['animation'] ?? '') == 'slide' ? 'selected' : '' }}>Slide Up</option>
                                </select>
                            </div>

                            <hr class="border-gray-50 my-2">

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-bold text-gray-600 uppercase tracking-tighter">Card Glass Blur</label>
                                    <span class="text-[10px] font-mono text-primary font-bold bg-blue-50 px-1.5 rounded" id="val-card-blur">{{ $template->config['advanced']['card_blur'] ?? '20px' }}</span>
                                </div>
                                <input type="range" min="0" max="40" step="1" 
                                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary config-input" 
                                       data-config="advanced.card_blur" 
                                       value="{{ rtrim($template->config['advanced']['card_blur'] ?? '20px', 'px') }}"
                                       oninput="updateLabel(this, 'val-card-blur', 'px')">
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-bold text-gray-600 uppercase tracking-tighter">Card Opacity</label>
                                    <span class="text-[10px] font-mono text-primary font-bold bg-blue-50 px-1.5 rounded" id="val-card-opacity">{{ $template->config['advanced']['card_opacity'] ?? '0.9' }}</span>
                                </div>
                                <input type="range" min="0.1" max="1" step="0.05" 
                                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary config-input" 
                                       data-config="advanced.card_opacity" 
                                       value="{{ $template->config['advanced']['card_opacity'] ?? '0.9' }}"
                                       oninput="updateLabel(this, 'val-card-opacity')">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Card BG Image URL</label>
                                <div class="relative group">
                                    <input type="text" class="w-full rounded-md border-gray-200 text-xs p-2 pr-10 config-input font-mono" 
                                           id="input-card-bg-image"
                                           data-config="advanced.card_bg_image" 
                                           value="{{ $template->config['advanced']['card_bg_image'] ?? '' }}"
                                           placeholder="https://example.com/image.jpg">
                                    <button onclick="openMediaSelector('advanced.card_bg_image', 'input-card-bg-image')" 
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-primary hover:text-primary-dark transition-colors">
                                        <span class="material-icons text-[18px]">photo_library</span>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-gray-600">Custom Styling (CSS)</label>
                                <textarea class="w-full rounded-md border-gray-200 text-[10px] p-3 h-32 font-mono config-input bg-gray-50 focus:bg-white transition-colors" data-config="advanced.custom_css" placeholder=".invitation-card { ... }">{{ $template->config['advanced']['custom_css'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Selector Modal -->
    <div id="mediaModal" class="hidden fixed inset-0 z-[9999] overflow-y-auto">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="toggleMediaModal(false)"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-black text-gray-800 tracking-tight">Media Library</h3>
                        <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">Select or Upload Background Assets</p>
                    </div>
                    <button onclick="toggleMediaModal(false)" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="material-icons text-gray-400">close</span>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 h-[60vh] flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex gap-2">
                            <button onclick="fetchMedia('background')" class="px-4 py-2 rounded-full text-xs font-bold bg-primary text-white shadow-lg shadow-primary/20 transition-all media-filter-btn" data-type="background">Backgrounds</button>
                            <button onclick="fetchMedia('logo')" class="px-4 py-2 rounded-full text-xs font-bold bg-white text-gray-600 border border-gray-100 hover:border-gray-300 transition-all media-filter-btn" data-type="logo">Logos</button>
                        </div>
                        
                        <!-- Mini Upload Form -->
                        <div class="flex items-center gap-2" id="miniUploadForm">
                            <input type="file" id="mediaUploadFile" class="hidden" accept="image/*" onchange="handleMediaUpload(this)">
                            <button onclick="document.getElementById('mediaUploadFile').click()" class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-green-500/20">
                                <span class="material-icons text-xs">upload</span>
                                UNGGAH BARU
                            </button>
                        </div>
                    </div>

                    <div id="mediaGrid" class="grid grid-cols-2 md:grid-cols-4 gap-4 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                        <!-- Media items will be injected here -->
                        <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-300">
                            <span class="material-icons text-6xl mb-4 animate-pulse">image</span>
                            <p class="font-bold uppercase tracking-widest text-xs">Loading Assets...</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="toggleMediaModal(false)" class="px-6 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">CLOSE</button>
                </div>
            </div>
        </div>
    </div>
            
            <div class="p-4 bg-white border-t border-gray-100 flex gap-2">
                 <!-- Export/Import mockups could go here -->
            </div>
        </aside>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let config = @json($template->config);
    
    $(document).ready(function() {
        // Bind Inputs
        $('.config-input').on('change input', function() {
            updatePreview();
        });

        // Initial preview message with full config
        const frame = document.getElementById('previewFrame');
        if (frame) {
            frame.onload = function() {
                updatePreview();
            };
        }
    });

    function updateLabel(el, targetId, suffix = '') {
        const val = el.value + suffix;
        document.getElementById(targetId).textContent = val;
        el.setAttribute('data-value', val); // Store for updatePreview
        updatePreview();
    }

    function updatePreview() {
        let newConfig = {};
        
        $('.config-input').each(function() {
            let input = $(this);
            let configKey = input.data('config');
            if (!configKey) return;

            let path = configKey.split('.');
            let value;
            
            if (input.is(':checkbox')) {
                value = input.is(':checked');
            } else if (input.is(':radio')) {
                if(input.is(':checked')) value = input.val();
                else return; 
            } else if (input.attr('type') === 'range') {
                 value = input.attr('data-value') || (input.val() + 'rem');
            } else {
                value = input.val();
            }
            
            // Re-construct nested object (Support up to 2 levels)
            if (path.length === 2) {
                if (!newConfig[path[0]]) newConfig[path[0]] = {};
                newConfig[path[0]][path[1]] = value;
            } else if (path.length === 1) {
                newConfig[path[0]] = value;
            }
        });

        // Update local object
        config = { ...config, ...newConfig };

        console.log('Updating Preview with Config:', config);

        const frame = document.getElementById('previewFrame');
        if (frame && frame.contentWindow) {
            frame.contentWindow.postMessage({
                type: 'updateConfig',
                config: config,
                category: '{{ $template->category }}'
            }, '*');
        }
    }

    function setDevice(mode) {
        const wrapper = $('#previewWrapper');
        wrapper.removeClass('device-desktop device-tablet device-mobile');
        wrapper.addClass('device-' + mode);
        
        $('.device-btn').removeClass('active text-primary bg-white shadow-sm font-bold');
        $('.device-btn').addClass('text-gray-500');
        
        $(`.device-btn[data-device="${mode}"]`).addClass('active text-primary bg-white shadow-sm font-bold').removeClass('text-gray-500');
    }

    let activeMediaField = null;
    let activeMediaInputId = null;

    function toggleMediaModal(show) {
        $('#mediaModal').toggleClass('hidden', !show);
        if (show) {
            fetchMedia('background');
        }
    }

    function openMediaSelector(configPath, inputId) {
        activeMediaField = configPath;
        activeMediaInputId = inputId;
        toggleMediaModal(true);
    }

    function fetchMedia(type) {
        $('.media-filter-btn').removeClass('bg-primary text-white shadow-lg shadow-primary/20').addClass('bg-white text-gray-600 border border-gray-100');
        $(`.media-filter-btn[data-type="${type}"]`).addClass('bg-primary text-white shadow-lg shadow-primary/20').removeClass('bg-white text-gray-600 border border-gray-100');

        const grid = $('#mediaGrid');
        grid.html('<div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-300"><span class="material-icons text-6xl mb-4 animate-spin">sync</span><p class="font-bold uppercase tracking-widest text-xs">Fetching Assets...</p></div>');

        fetch(`{{ route('certificate-media.api') }}?type=${type}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    grid.html('<div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-300"><span class="material-icons text-6xl mb-4">folder_open</span><p class="font-bold uppercase tracking-widest text-xs">No assets found</p></div>');
                    return;
                }

                grid.empty();
                data.forEach(item => {
                    grid.append(`
                        <div class="group relative aspect-square bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 hover:border-primary transition-all cursor-pointer" onclick="selectMedia('${item.url}')">
                            <img src="${item.url}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-primary/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white transition-opacity">
                                <span class="material-icons mb-1">check_circle</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Select Asset</span>
                            </div>
                            <div class="absolute bottom-2 left-2 right-2">
                                <p class="text-[8px] font-bold text-white bg-black/50 backdrop-blur-md px-2 py-1 rounded-md text-truncate">${item.name}</p>
                            </div>
                        </div>
                    `);
                });
            })
            .catch(err => {
                grid.html('<div class="col-span-full flex flex-col items-center justify-center py-20 text-red-300"><span class="material-icons text-6xl mb-4">error_outline</span><p class="font-bold uppercase tracking-widest text-xs">Failed to load media</p></div>');
            });
    }

    function selectMedia(url) {
        if (activeMediaInputId) {
            const input = document.getElementById(activeMediaInputId);
            input.value = url;
            $(input).trigger('input'); // Trigger preview update
        }
        toggleMediaModal(false);
    }

    function handleMediaUpload(input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('file', input.files[0]);
        formData.append('name', input.files[0].name.split('.')[0]);
        formData.append('type', $('.media-filter-btn.bg-primary').data('type') || 'background');

        const btn = $(input).next();
        const originalContent = btn.html();
        btn.html('<span class="material-icons text-xs animate-spin">sync</span> PROCESSING...');
        btn.prop('disabled', true);

        fetch('{{ route('certificate-media.api.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toastr.success('Media uploaded successfully!');
                fetchMedia(data.media.type);
            } else {
                toastr.error(data.message || 'Upload failed');
            }
        })
        .catch(err => {
            toastr.error('An error occurred during upload');
            console.error(err);
        })
        .finally(() => {
            btn.html(originalContent);
            btn.prop('disabled', false);
            input.value = '';
        });
    }

    function saveConfig() {
        const btn = $('#btnSave');
        const originalContent = btn.html();
        btn.html('<span class="material-icons text-xs mr-2 animate-spin">sync</span> SAVING...');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');

        fetch('{{ route('invitation-templates.updateConfig', $template->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(config)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Template configuration saved successfully!');
            } else {
                toastr.error(data.message || 'Failed to save configuration.');
            }
        })
        .catch(err => {
            toastr.error('An error occurred during save.');
            console.error(err);
        })
        .finally(() => {
             btn.html(originalContent);
             btn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed');
        });
    }
</script>
</body>
</html>
