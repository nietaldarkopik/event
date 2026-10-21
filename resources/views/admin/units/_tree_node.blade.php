@php
    $badgeClass = $unit->type === 'rektorat' ? 'secondary' : ($unit->type === 'fakultas' ? 'info' : 'success');
@endphp
<li class="unit-tree-item mb-2" data-id="{{ $unit->id }}" draggable="true">
    <div class="unit-tree-node d-flex justify-content-between align-items-center p-2 rounded border">
        <div class="d-flex align-items-center">
            @if($unit->children && $unit->children->count())
                <button type="button" class="btn btn-xs btn-link text-secondary unit-toggle mr-1">
                    <i class="fas fa-chevron-down"></i>
                </button>
            @else
                <span class="mr-3"></span>
            @endif
            <span class="badge badge-{{ $badgeClass }} mr-2 text-uppercase">{{ $unit->type }}</span>
            <strong>{{ $unit->name }}</strong>
            @if($unit->code)
                <span class="text-muted ml-1">({{ $unit->code }})</span>
            @endif
        </div>
        <div class="btn-group btn-group-xs">
            <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-xs btn-warning" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Yakin ingin menghapus unit ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @if($unit->children && $unit->children->count())
        <ul class="unit-tree-children list-unstyled ml-4">
            @foreach($unit->children as $child)
                @include('admin.units._tree_node', ['unit' => $child])
            @endforeach
        </ul>
    @endif
</li>

