@php
    $badgeClass = $unit->type === 'rektorat' ? 'secondary' : ($unit->type === 'fakultas' ? 'info' : 'success');
@endphp
<li class="jquery-tree-item" data-id="{{ $unit->id }}">
    <div class="jquery-tree-node d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            @if($unit->children && $unit->children->count())
                <button type="button" class="btn btn-xs btn-link text-secondary jquery-tree-toggle mr-1">
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
        <ul class="jquery-tree-children list-unstyled ml-4" style="display: none;">
            @foreach($unit->children as $child)
                @include('admin.units._jquery_tree_node', ['unit' => $child])
            @endforeach
        </ul>
    @endif
</li>