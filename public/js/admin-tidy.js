/* Perapian tabel admin (semua halaman daftar): berjalan sebelum DataTables diinisialisasi.
 *  - HTML yang tampil sebagai teks (mis. "<p>..</p>" dari editor teks kaya) diubah menjadi teks polos
 *  - teks panjang dipotong 2 baris, teks lengkap muncul saat kursor diarahkan
 *  - tanggal "2026-10-24 08:00:00" ditampilkan "24 Okt 2026, 08:00" tanpa merusak urutan
 *  - DataTables mendapat bahasa Indonesia, kolom Aksi tidak bisa diurutkan, tabel dibungkus agar bisa digeser
 */
(function ($) {
    'use strict';
    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    var TAG = /<\/?[a-z][a-z0-9]*(\s[^<>]*)?>/i;
    var DATE = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(:\d{2})?$/;

    function plainFromHtml(s) {
        var doc = new DOMParser().parseFromString(s.replace(/<\/(p|li|tr|div|h[1-6])>/gi, '$& ').replace(/<br\s*\/?>/gi, ' '), 'text/html');
        return (doc.body.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function tidyCells($table) {
        $table.find('tbody td').each(function () {
            var $td = $(this);
            if ($td.children().length) return;                 // sel berisi tombol/ikon/form: jangan disentuh
            var t = $td.text().trim();
            if (!t) return;

            if (TAG.test(t)) { t = plainFromHtml(t); $td.text(t); }

            var m = DATE.exec(t);
            if (m && !$td.attr('data-order')) {
                $td.attr('data-order', t);
                $td.text(parseInt(m[3], 10) + ' ' + MONTHS[parseInt(m[2], 10) - 1] + ' ' + m[1] + ', ' + m[4] + ':' + m[5]);
                $td.addClass('text-nowrap');
                return;
            }

            if (t.length > 90) { $td.attr('title', t).addClass('dt-clamp'); }
        });
    }

    function wrap($table) {
        if (!$table.closest('.table-responsive').length) {
            $table.wrap('<div class="table-responsive tidy-wrap"></div>');
        }
    }

    // Bahasa & opsi bawaan untuk semua DataTables yang dibuat halaman
    var DEFAULTS = {
        autoWidth: false,
        // Pencarian, filter, urutan, dan halaman diingat selama tab terbuka, jadi setelah hapus/ubah (halaman dimuat ulang)
        // pengguna kembali ke keadaan yang sama, bukan daftar polos.
        stateSave: true,
        stateSaveCallback: function (settings, data) {
            try { sessionStorage.setItem('dt:' + location.pathname + location.search, JSON.stringify(data)); } catch (e) { /* abaikan */ }
        },
        stateLoadCallback: function () {
            try { return JSON.parse(sessionStorage.getItem('dt:' + location.pathname + location.search)); } catch (e) { return null; }
        },
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ baris', info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', infoFiltered: '(disaring dari _MAX_ data)', zeroRecords: 'Tidak ada data yang cocok.',
            emptyTable: 'Belum ada data.', paginate: { previous: '&lsaquo;', next: '&rsaquo;' }
        }
    };

    var wrapped;
    Object.defineProperty($.fn, 'DataTable', {
        configurable: true,
        get: function () { return wrapped; },
        set: function (orig) {
            wrapped = function (opts) {
                var $t = this;
                if (!opts || typeof opts === 'object') {
                    var o = $.extend(true, {}, DEFAULTS, opts || {});
                    var last = $t.find('thead th').length - 1;
                    var head = $t.find('thead th').last().text().trim().toLowerCase();
                    if (!o.columnDefs && head === 'aksi') { o.columnDefs = [{ orderable: false, searchable: false, targets: last }]; }
                    opts = o;
                }
                return orig.call($t, opts);
            };
        }
    });

    $(function () {
        $('.content-wrapper table.table').not('.no-tidy').each(function () {
            var $t = $(this);
            tidyCells($t);
            wrap($t);
        });
    });

    // ---- Menu dropdown di dalam tabel yang bisa digulir ----
    // .table-responsive memotong apa pun yang keluar dari kotaknya, termasuk menu "⋯" pada baris terakhir.
    // Saat dibuka, menu dipindah ke <body> dengan posisi tetap (membuka ke atas bila ruang bawah kurang),
    // lalu dikembalikan ke tempat semula saat ditutup.
    $(function () {
        $('.table-responsive [data-toggle="dropdown"]').attr('data-display', 'static');
    });

    var openedAt = 0;
    $(document).on('shown.bs.dropdown', '.table-responsive .btn-group, .table-responsive .dropdown', function () {
        var $group = $(this), $btn = $group.find('[data-toggle="dropdown"]').first(), $menu = $group.find('.dropdown-menu').first();
        if (!$btn.length || !$menu.length) return;
        $group.data('floatingMenu', $menu);
        openedAt = Date.now();
        $menu.appendTo('body').css({ position: 'fixed', left: 'auto', bottom: 'auto', transform: 'none', margin: 0, zIndex: 2000, top: 0, right: 0 });
        // hitung posisi SETELAH dipindah: tinggi halaman berubah dan posisi tombol bisa bergeser beberapa piksel
        var r = $btn[0].getBoundingClientRect(), h = $menu.outerHeight(), below = window.innerHeight - r.bottom;
        var top = (below < h + 8 && r.top > h + 8) ? r.top - h - 2 : Math.min(r.bottom + 2, Math.max(8, window.innerHeight - h - 8));
        $menu.css({ top: top + 'px', right: Math.max(8, window.innerWidth - r.right) + 'px' });
    });

    $(document).on('hidden.bs.dropdown', '.table-responsive .btn-group, .table-responsive .dropdown', function () {
        var $group = $(this), $menu = $group.data('floatingMenu');
        if ($menu) {
            $menu.css({ position: '', top: '', left: '', right: '', bottom: '', transform: '', margin: '', zIndex: '' }).appendTo($group);
            $group.removeData('floatingMenu');
        }
    });

    // menu yang melayang tidak ikut bergeser saat halaman digulir atau jendela diubah: tutup lewat Bootstrap agar statusnya konsisten
    function closeFloating() {
        // memindahkan menu ke <body> dapat menggeser posisi gulir halaman beberapa piksel; abaikan sesaat setelah dibuka
        if (Date.now() - openedAt < 400) return;
        $('.table-responsive [data-toggle="dropdown"][aria-expanded="true"]').dropdown('hide');
    }
    $(window).on('scroll resize', closeFloating);
    $(document).on('scroll', '.table-responsive', closeFloating);
})(jQuery);
