<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use App\Models\InvitationTemplate;
use Illuminate\Database\Seeder;

/**
 * Template undangan bawaan: tiap kategori event punya 3 gaya (Klasik, Modern, Gelap Premium).
 * Aman dijalankan berulang: template dicocokkan dengan nama dan diperbarui, bukan digandakan.
 * Jalankan: php artisan db:seed --class=InvitationTemplateSeeder
 */
class InvitationTemplateSeeder extends Seeder
{
    /** slug kategori => [nama tampil, warna utama, warna header, aksen emas, presensi pulang?] */
    private const CATEGORIES = [
        'rapat'       => ['Rapat Internal',       '#1e3a5f', '#1e3a5f', '#b08d57', false],
        'seminar'     => ['Seminar',              '#0f766e', '#115e59', '#c9a24b', true],
        'workshop'    => ['Workshop',             '#c2410c', '#9a3412', '#b8923a', true],
        'wisuda'      => ['Wisuda',               '#166534', '#14532d', '#b8923a', false],
        'kuliah_umum' => ['Kuliah Umum',          '#1d4ed8', '#1e3a8a', '#c9a24b', true],
        'lomba'       => ['Lomba / Kompetisi',    '#b91c1c', '#991b1b', '#d4a017', false],
        'pelatihan'   => ['Pelatihan',            '#0e7490', '#155e75', '#c9a24b', true],
        'pengabdian'  => ['Pengabdian Masyarakat', '#4d7c0f', '#3f6212', '#b8923a', false],
        'semiloka'    => ['Semiloka',             '#6d28d9', '#5b21b6', '#c9a24b', true],
    ];

    public function run(): void
    {
        $base = InvitationTemplate::getDefaultConfig();
        $count = 0;

        foreach (self::CATEGORIES as $slug => [$label, $primary, $header, $gold, $hasCheckout]) {
            // Pastikan kategorinya ada & punya slug yang sama (template mencocokkan lewat slug).
            EventCategory::where('name', $label)->whereNull('slug')->update(['slug' => $slug]);

            $sections = array_merge($base['sections'], [
                'show_barcode_masuk' => true,
                'show_barcode_pulang' => $hasCheckout,
                'show_barcode_konsumsi' => $slug === 'wisuda',   // wisuda memakai "absensi pulang" jenis consumption
                'show_seat_number' => in_array($slug, ['wisuda', 'seminar', 'kuliah_umum', 'semiloka'], true),
            ]);

            $styles = [
                'Klasik' => [
                    'desc' => 'Kartu undangan resmi di atas kertas krem, huruf serif, bingkai emas tipis.',
                    'layout' => ['header_style' => 'card', 'spacing' => 'normal'],
                    'colors' => ['primary' => $primary, 'secondary' => $gold, 'bg' => '#ece6d6', 'card_bg' => '#fffdf8', 'text' => '#3a3629', 'title_color' => $header],
                    'typography' => ['font' => 'Cormorant Garamond', 'title_size' => '2rem', 'body_size' => '1rem', 'line_height' => '1.6', 'font_weight' => 'normal'],
                    'buttons' => ['shape' => 'rounded', 'size' => 'medium', 'shadow' => true],
                    'advanced' => ['border_radius' => '10px'],
                ],
                'Modern' => [
                    'desc' => 'Bersih dan terang, huruf sans-serif, sudut membulat, tombol pil.',
                    'layout' => ['header_style' => 'card', 'spacing' => 'normal'],
                    'colors' => ['primary' => $primary, 'secondary' => $gold, 'bg' => '#eef2f7', 'card_bg' => '#ffffff', 'text' => '#374151', 'title_color' => $header],
                    'typography' => ['font' => 'Poppins', 'title_size' => '2rem', 'body_size' => '0.95rem', 'line_height' => '1.6', 'font_weight' => 'normal'],
                    'buttons' => ['shape' => 'pill', 'size' => 'medium', 'shadow' => true],
                    'advanced' => ['border_radius' => '24px'],
                ],
                'Gelap Premium' => [
                    'desc' => 'Latar gelap dengan aksen emas, kesan eksklusif dan tegas.',
                    'layout' => ['header_style' => 'card', 'spacing' => 'normal'],
                    'colors' => ['primary' => '#d4af37', 'secondary' => '#d4af37', 'bg' => '#0b1020', 'card_bg' => '#141b2f', 'text' => '#cbd5e1', 'title_color' => '#f1e3b0'],
                    'typography' => ['font' => 'Inter', 'title_size' => '2rem', 'body_size' => '0.95rem', 'line_height' => '1.6', 'font_weight' => 'normal'],
                    'buttons' => ['shape' => 'rounded', 'size' => 'medium', 'shadow' => true],
                    'advanced' => ['border_radius' => '16px'],
                ],
            ];

            foreach ($styles as $style => $s) {
                $config = $base;
                $config['layout'] = array_merge($base['layout'], $s['layout'], [
                    'header_overlay_color' => $header,       // warna header mengikuti kategori
                    'header_overlay_opacity' => '1',
                ]);
                $config['colors'] = array_merge($base['colors'], $s['colors']);
                $config['typography'] = array_merge($base['typography'], $s['typography']);
                $config['buttons'] = $s['buttons'];
                $config['sections'] = $sections;
                $config['advanced'] = array_merge($base['advanced'], $s['advanced']);

                InvitationTemplate::updateOrCreate(
                    ['name' => $label . ' — ' . $style],
                    ['category' => $slug, 'is_active' => true, 'description' => $s['desc'], 'config' => $config]
                );
                $count++;
            }
        }

        $this->command && $this->command->info("$count template undangan siap.");
    }
}
