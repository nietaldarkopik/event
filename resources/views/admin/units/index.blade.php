@extends('adminlte::page')

@section('title', 'Master Unit')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Master Unit (Rektorat, Fakultas, Prodi)</h1>
    <a href="{{ route('units.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Unit</a>
</div>
@stop

@section('content')
<div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-tree" data-toggle="pill" href="#tab-tree-pane" role="tab"
                    aria-controls="tab-tree-pane" aria-selected="true">
                    <i class="fas fa-sitemap mr-1"></i> Tree View
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-table" data-toggle="pill" href="#tab-table-pane" role="tab"
                    aria-controls="tab-table-pane" aria-selected="false">
                    <i class="fas fa-list mr-1"></i> Table View
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-tree-pane" role="tabpanel" aria-labelledby="tab-tree">
                <p class="text-muted">
                    Tarik satu unit ke unit lain untuk mengubah parent-child. Klik panah untuk collapse/expand.
                </p>
                <div class="unit-tree-wrapper">
                    <div class="unit-tree-root-target mb-3 p-3 bg-light rounded" data-id="0" draggable="true">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-dark mr-2 text-uppercase">ROOT</span>
                            <strong>Root</strong>
                            <span class="text-muted ml-2">(Tarik unit ke sini untuk menjadikannya root)</span>
                        </div>
                    </div>
                    <ul id="unit-tree" class="unit-tree list-unstyled">
                        @foreach($treeUnits as $root)
                            @include('admin.units._tree_node', ['unit' => $root])
                        @endforeach
                    </ul>
                </div>
                <small class="text-muted d-block mt-2">Perubahan struktur akan langsung disimpan saat Anda melepas drag.</small>
            </div>
            <div class="tab-pane fade" id="tab-table-pane" role="tabpanel" aria-labelledby="tab-table">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <table class="table table-bordered table-striped" id="unitsTable">
            <thead>
                <tr>
                    <th style="width: 60px">ID</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Jenis</th>
                    <th>Parent</th>
                    <th style="width: 100px">Status</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                    <tr>
                        <td>{{ $unit->id }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->code }}</td>
                        <td>{{ ucfirst($unit->type) }}</td>
                        <td>{{ $unit->parent ? $unit->parent->name : '-' }}</td>
                        <td>
                            @if($unit->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus unit ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
        .unit-tree-wrapper {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e5e5e5;
            border-radius: .25rem;
            padding: .75rem 1rem;
            background: #fafafa;
        }
        .unit-tree-node {
            padding: .35rem .5rem;
            border-radius: .25rem;
            border: 1px solid transparent;
            cursor: move;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
            margin-bottom: 0.5rem;
        }
        .unit-tree-node:hover {
            background-color: #f8f9fa !important;
        }
        .unit-tree-item + .unit-tree-item {
            margin-top: .25rem;
        }
        .unit-tree-item.drag-over > .unit-tree-node,
        .unit-tree-root-target.drag-over > .unit-tree-node {
            border-color: #007bff;
            background: #eaf3ff;
            box-shadow: 0 0 8px rgba(0,123,255,0.3);
        }
        .unit-tree-item.dragging > .unit-tree-node {
            opacity: .6;
            border-style: dashed;
            transform: scale(0.98);
        }
        .unit-tree-root-target {
            cursor: move;
            transition: all 0.2s ease;
        }
        .unit-tree-root-target:hover {
            background-color: #e9ecef !important;
        }
        .unit-tree-children {
            margin-left: 2rem;
            margin-top: 0.5rem;
        }
    </style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#unitsTable').DataTable({
            responsive: true,
            autoWidth: false,
        });

        // Simple and reliable Drag & Drop functionality
        document.addEventListener('dragstart', function (e) {
            // Only handle drag events from unit tree items
            if (e.target.closest('.unit-tree-item') || e.target.closest('.unit-tree-root-target')) {
                const element = e.target.closest('[data-id]');
                if (element) {
                    const id = element.getAttribute('data-id');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', id);
                    element.classList.add('dragging');
                    console.log('Drag started - ID:', id);
                }
            }
        });

        document.addEventListener('dragend', function (e) {
            // Remove dragging class from all elements
            document.querySelectorAll('.dragging').forEach(el => {
                el.classList.remove('dragging');
            });
            document.querySelectorAll('.drag-over').forEach(el => {
                el.classList.remove('drag-over');
            });
        });

        document.addEventListener('dragover', function (e) {
            // Only handle dragover events from unit tree items
            if (e.target.closest('.unit-tree-item') || e.target.closest('.unit-tree-root-target')) {
                e.preventDefault();
                const element = e.target.closest('[data-id]');
                if (element) {
                    element.classList.add('drag-over');
                    e.dataTransfer.dropEffect = 'move';
                }
            }
        });

        document.addEventListener('dragleave', function (e) {
            // Remove drag-over class when leaving elements
            if (e.target.closest('.unit-tree-item') || e.target.closest('.unit-tree-root-target')) {
                const element = e.target.closest('[data-id]');
                if (element) {
                    element.classList.remove('drag-over');
                }
            }
        });

        document.addEventListener('drop', function (e) {
            // Only handle drop events from unit tree items
            if (e.target.closest('.unit-tree-item') || e.target.closest('.unit-tree-root-target')) {
                e.preventDefault();
                
                const targetElement = e.target.closest('[data-id]');
                if (!targetElement) return;
                
                targetElement.classList.remove('drag-over');
                
                // Get the dragged ID from dataTransfer and target ID from the element
                const draggedId = parseInt(e.dataTransfer.getData('text/plain'));
                const targetId = parseInt(targetElement.getAttribute('data-id'));
                
                console.log('DROP - Dragged ID:', draggedId, 'Target ID:', targetId);
                
                // Prevent self-dragging
                if (draggedId === targetId) {
                    console.log('Cannot drag to self');
                    return;
                }
                
                // Send AJAX request
                $.post('{{ route('units.reorder') }}', {
                    _token: '{{ csrf_token() }}',
                    id: draggedId,
                    parent_id: targetId === 0 ? null : targetId
                }).done(function (response) {
                    if (response.success) {
                        console.log('Success:', response.message);
                        // Update UI dynamically without reload
                        updateTreeStructure(draggedId, targetId);
                        showSuccessMessage('Struktur berhasil diubah!');
                    } else {
                        console.log('Error:', response.message);
                        alert('Gagal menyimpan perubahan: ' + response.message);
                    }
                }).fail(function (xhr) {
                    const errorMsg = xhr.responseJSON && xhr.responseJSON.message 
                        ? xhr.responseJSON.message 
                        : 'Gagal menyimpan perubahan struktur.';
                    console.log('AJAX Error:', errorMsg);
                    alert(errorMsg);
                });
            }
        });
        
        // Function to update tree structure dynamically
        function updateTreeStructure(draggedId, targetId) {
            const draggedElement = document.querySelector(`[data-id="${draggedId}"]`);
            const targetElement = document.querySelector(`[data-id="${targetId}"]`);
            
            if (!draggedElement || !targetElement) {
                console.error('Elements not found for dynamic update');
                return;
            }
            
            // If target is root (id=0), move to root level
            if (targetId === 0) {
                const unitTree = document.getElementById('unit-tree');
                if (unitTree) {
                    // Remove from current position and append to root level
                    draggedElement.remove();
                    unitTree.appendChild(draggedElement);
                    
                    // Remove children container if exists
                    const childrenContainer = draggedElement.querySelector('.unit-tree-children');
                    if (childrenContainer) {
                        childrenContainer.remove();
                    }
                    
                    // Remove toggle button if exists
                    const toggleButton = draggedElement.querySelector('.unit-toggle');
                    if (toggleButton) {
                        toggleButton.remove();
                    }
                }
            } else {
                // Move as child of target element
                let childrenContainer = targetElement.querySelector('.unit-tree-children');
                
                // Create children container if it doesn't exist
                if (!childrenContainer) {
                    childrenContainer = document.createElement('ul');
                    childrenContainer.className = 'unit-tree-children list-unstyled ml-4';
                    targetElement.appendChild(childrenContainer);
                    
                    // Add toggle button to target
                    const toggleButton = document.createElement('button');
                    toggleButton.type = 'button';
                    toggleButton.className = 'btn btn-xs btn-link text-secondary unit-toggle mr-1';
                    toggleButton.innerHTML = '<i class="fas fa-chevron-down"></i>';
                    
                    const targetNode = targetElement.querySelector('.d-flex.align-items-center');
                    if (targetNode) {
                        targetNode.prepend(toggleButton);
                    }
                }
                
                // Move dragged element to children container
                draggedElement.remove();
                childrenContainer.appendChild(draggedElement);
            }
            
            console.log('Tree structure updated dynamically');
        }
        
        // Function to show success message
        function showSuccessMessage(message) {
            // Create or update toast notification
            if (!$('#tree-success-toast').length) {
                $('body').append('<div id="tree-success-toast" class="toast" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">' +
                    '<div class="toast-header">' +
                    '<strong class="mr-auto text-success">Sukses</strong>' +
                    '<button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>' +
                    '</div>' +
                    '<div class="toast-body"></div>' +
                    '</div>');
            }
            
            $('#tree-success-toast .toast-body').text(message);
            $('#tree-success-toast').toast({delay: 3000}).toast('show');
        }
        
        // Tree toggle functionality
        $('#unit-tree').on('click', '.unit-toggle', function (e) {
            e.stopPropagation();
            const item = $(this).closest('.unit-tree-item');
            const children = item.children('ul.unit-tree-children');
            if (!children.length) {
                return;
            }
            children.toggle();
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-right');
        });

        // Initialize Bootstrap toast
        $('.toast').toast();
    });
</script>
@stop
