{{-- Aset & perilaku bersama halaman daftar yang didesain ulang: pencarian sendiri, baris detail, tanpa kontrol bawaan yang berantakan --}}
@once
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function () {
    var table = $('#mainTable').DataTable({
        order: [[{{ $orderCol ?? 1 }}, '{{ $orderDir ?? 'asc' }}']],
        dom: "<'row'<'col-12'tr>><'row mt-3 align-items-center'<'col-md-4'l><'col-md-8'p>>",
        columnDefs: [{orderable: false, searchable: false, targets: [0, -1]}],
        drawCallback: function () {
            var i = this.api().page.info();
            $('#list-info').text(i.recordsDisplay ? ('Menampilkan ' + (i.start + 1) + '–' + i.end + ' dari ' + i.recordsDisplay + ' data') : '');
        }
    });
    // pulihkan kotak cari & filter dari status yang tersimpan (setelah hapus/ubah halaman dimuat ulang)
    var saved = table.state.loaded();
    if (saved) {
        $('#list-search').val(saved.search ? saved.search.search : '');
        var fc = saved.columns && saved.columns[{{ $filterCol ?? 1 }}];
        if (fc && fc.search && fc.search.search) {
            var fv = fc.search.search.replace(/^\^/, '').replace(/\$$/, '').replace(/\\(.)/g, '$1');
            $('#list-filter').val(fv);
        }
    }
    $('#list-search').on('input', function () { table.search(this.value).draw(); });
    $('#list-filter').on('change', function () {
        var v = this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '';
        table.column({{ $filterCol ?? 1 }}).search(v, true, false).draw();
    });
    $('#mainTable tbody').on('click', 'td.list-toggle', function () {
        var tr = $(this).closest('tr'), row = table.row(tr), d = tr.find('.list-detail').html();
        if (!d) return;
        if (row.child.isShown()) { row.child.hide(); tr.removeClass('list-open'); }
        else { row.child('<div class="list-child">' + d + '</div>').show(); tr.addClass('list-open'); }
    });
});
</script>
@endonce
