<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Models
use Modules\Authentication\Models\User;
use Modules\Organization\Models\Organization;
use Modules\Category\Models\EventCategory;
use Modules\Event\Models\Event;
use Modules\Session\Models\EventSession;
use Modules\Speaker\Models\Speaker;
use Modules\Ticket\Models\TicketType;
use Modules\Sponsor\Models\Sponsor;
use Modules\Gallery\Models\Gallery;
use Modules\Event\Models\Announcement;
use Modules\Event\Models\EventCommittee;
use Modules\Participant\Models\Participant;
use Modules\Registration\Models\Registration;
use Modules\Payment\Models\Payment;
use Modules\Attendance\Models\AttendanceLog;
use Modules\Registration\Models\QrCode;
use Modules\Certificate\Models\Certificate;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Clear existing data to avoid constraint issues (except roles)
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Certificate::truncate();
        QrCode::truncate();
        AttendanceLog::truncate();
        Payment::truncate();
        Registration::truncate();
        Participant::truncate();
        EventCommittee::truncate();
        Announcement::truncate();
        Gallery::truncate();
        Sponsor::truncate();
        TicketType::truncate();
        DB::table('session_speakers')->truncate();
        EventSession::truncate();
        Speaker::truncate();
        Organization::truncate();
        \App\Models\Unit::truncate();
        // Clear all users except super_admin
        User::where('email', '!=', 'admin@unwim.ac.id')->delete();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Seed Units (Faculties & Study Programs)
        $ft = \App\Models\Unit::create(['name' => 'Fakultas Teknik', 'type' => 'fakultas', 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Teknik Sipil', 'type' => 'prodi', 'parent_id' => $ft->id, 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Teknik Geodesi', 'type' => 'prodi', 'parent_id' => $ft->id, 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Perencanaan Wilayah & Kota', 'type' => 'prodi', 'parent_id' => $ft->id, 'is_active' => true]);

        $fp = \App\Models\Unit::create(['name' => 'Fakultas Pertanian', 'type' => 'fakultas', 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Agroteknologi', 'type' => 'prodi', 'parent_id' => $fp->id, 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Agribisnis', 'type' => 'prodi', 'parent_id' => $fp->id, 'is_active' => true]);

        $fh = \App\Models\Unit::create(['name' => 'Fakultas Kehutanan', 'type' => 'fakultas', 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Kehutanan', 'type' => 'prodi', 'parent_id' => $fh->id, 'is_active' => true]);

        $feb = \App\Models\Unit::create(['name' => 'Fakultas Ekonomi & Bisnis', 'type' => 'fakultas', 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Manajemen', 'type' => 'prodi', 'parent_id' => $feb->id, 'is_active' => true]);
        \App\Models\Unit::create(['name' => 'Prodi Akuntansi', 'type' => 'prodi', 'parent_id' => $feb->id, 'is_active' => true]);

        $superAdmin = User::where('email', 'admin@unwim.ac.id')->first();

        // 2. Create Organizations
        $org1 = Organization::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Badan Eksekutif Mahasiswa (BEM) FEB UNWIM',
            'logo' => 'https://picsum.photos/200/200?random=1',
            'email' => 'bem.feb@unwim.ac.id',
            'phone' => '08123456789',
            'website' => 'https://feb.unwim.ac.id/bem',
            'address' => 'Kampus UNWIM Bandung, Jl. Turangga No.25'
        ]);

        $org2 = Organization::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Himpunan Mahasiswa Teknik (HMT) UNWIM',
            'logo' => 'https://picsum.photos/200/200?random=2',
            'email' => 'hmt@unwim.ac.id',
            'phone' => '08234567890',
            'website' => 'https://ft.unwim.ac.id/hmt',
            'address' => 'Kampus UNWIM Tanjungsari, Jl. Raya Bandung-Sumedang'
        ]);

        // 3. Categories (fetch from DatabaseSeeder or firstOrCreate)
        $catSeminar = EventCategory::firstOrCreate(['name' => 'Seminar']);
        $catWorkshop = EventCategory::firstOrCreate(['name' => 'Workshop']);
        $catWisuda = EventCategory::firstOrCreate(['name' => 'Wisuda']);

        // 4. Create Organizer Users
        $orgRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin_event', 'guard_name' => 'web']);
        $orgUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Ketua Pelaksana BEM',
            'email' => 'panitia.bem@unwim.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $orgRole->id,
            'status' => 'ACTIVE'
        ]);
        $orgUser->assignRole($orgRole);

        // 5. Create Events
        $event1 = Event::create([
            'uuid' => (string) Str::uuid(),
            'organization_id' => $org1->id,
            'category_id' => $catSeminar->id,
            'created_by' => $superAdmin->id,
            'title' => 'Seminar IT Nasional 2026: Transformasi Digital Menuju Society 5.0',
            'slug' => 'seminar-it-nasional-2026',
            'short_description' => 'Seminar nasional membahas masa depan teknologi informasi dan dampaknya terhadap masyarakat modern.',
            'description' => 'Seminar IT Nasional 2026 adalah forum diskusi ilmiah yang membahas akselerasi teknologi AI, IoT, Cloud Computing, dan Blockchain dalam mendukung transisi Indonesia menuju Society 5.0. Dihadiri oleh pembicara ahli dari Google DeepMind dan akademisi terkemuka.',
            'banner' => 'https://picsum.photos/800/400?random=10',
            'venue_name' => 'Aula Rektorat Utama UNWIM',
            'address' => 'Jl. Raya Bandung-Sumedang KM 29, Tanjungsari, Sumedang',
            'city' => 'Sumedang',
            'province' => 'Jawa Barat',
            'registration_start' => Carbon::now()->subDays(10),
            'registration_end' => Carbon::now()->addDays(5),
            'start_date' => Carbon::now()->addDays(6)->setTime(9, 0, 0),
            'end_date' => Carbon::now()->addDays(6)->setTime(16, 0, 0),
            'max_participants' => 300,
            'certificate_enabled' => true,
            'attendance_required' => true,
            'visibility' => 'PUBLIC',
            'status' => 'PUBLISHED'
        ]);

        $event2 = Event::create([
            'uuid' => (string) Str::uuid(),
            'organization_id' => $org2->id,
            'category_id' => $catWorkshop->id,
            'created_by' => $orgUser->id,
            'title' => 'Workshop Practical Laravel 10 & Clean Architecture',
            'slug' => 'workshop-laravel-clean-architecture',
            'short_description' => 'Pelatihan intensif coding best-practices dan pemisahan logika bisnis menggunakan repository pattern di Laravel.',
            'description' => 'Bagi developer yang ingin meningkatkan standar coding ke tingkat profesional, workshop praktis ini akan mengajari Anda teknik structuring modular, decoupling database, repository and service pattern, serta unit-testing di Laravel.',
            'banner' => 'https://picsum.photos/800/400?random=11',
            'venue_name' => 'Laboratorium Rekayasa Perangkat Lunak Gedung Teknik',
            'address' => 'Jl. Turangga No. 25, Lengkong, Bandung',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'registration_start' => Carbon::now()->subDays(5),
            'registration_end' => Carbon::now()->addDays(2),
            'start_date' => Carbon::now()->addDays(3)->setTime(8, 0, 0),
            'end_date' => Carbon::now()->addDays(4)->setTime(17, 0, 0),
            'max_participants' => 50,
            'certificate_enabled' => true,
            'attendance_required' => true,
            'visibility' => 'PUBLIC',
            'status' => 'PUBLISHED'
        ]);

        // 6. Create Event Sessions
        $session1 = EventSession::create([
            'event_id' => $event1->id,
            'title' => 'Sesi 1: Generative AI & Deep Learning untuk Masa Depan Akademis',
            'description' => 'Pembahasan mengenai revolusi ChatGPT, Gemini, dan pemanfaatannya dalam bidang riset.',
            'room' => 'Aula Utama Lt. 1',
            'start_datetime' => Carbon::now()->addDays(6)->setTime(9, 30, 0),
            'end_datetime' => Carbon::now()->addDays(6)->setTime(12, 0, 0),
            'max_participants' => 300
        ]);

        $session2 = EventSession::create([
            'event_id' => $event1->id,
            'title' => 'Sesi 2: Peluang Karir IT & Startup di Era Globalisasi',
            'description' => 'Sesi diskusi panel dengan praktisi industri.',
            'room' => 'Aula Utama Lt. 1',
            'start_datetime' => Carbon::now()->addDays(6)->setTime(13, 30, 0),
            'end_datetime' => Carbon::now()->addDays(6)->setTime(15, 30, 0),
            'max_participants' => 300
        ]);

        $sessionWorkshop = EventSession::create([
            'event_id' => $event2->id,
            'title' => 'Hari 1: Fundamental SOLID & Clean Code',
            'description' => 'Teori dan live-coding pemisahan model, controller, dan repository.',
            'room' => 'Lab Komputer RPL',
            'start_datetime' => Carbon::now()->addDays(3)->setTime(8, 30, 0),
            'end_datetime' => Carbon::now()->addDays(3)->setTime(16, 0, 0),
            'max_participants' => 50
        ]);

        // 7. Create Speakers
        $spk1 = Speaker::create([
            'name' => 'Prof. Dr. Ir. H. M. Budi, M.T.',
            'company' => 'Google DeepMind Team',
            'title' => 'Senior AI Researcher',
            'photo' => 'https://picsum.photos/300/300?random=20',
            'bio' => 'Telah berkecimpung di dunia riset machine learning selama 15 tahun dan memiliki puluhan publikasi ilmiah internasional terindeks Scopus Q1.',
            'linkedin' => 'https://linkedin.com/in/prof-budi',
            'instagram' => 'https://instagram.com/prof.budi',
            'email' => 'prof.budi@google.com',
            'phone' => '08999999999'
        ]);

        $spk2 = Speaker::create([
            'name' => 'Eko Kurniawan, S.Kom.',
            'company' => 'Programmer Zaman Now',
            'title' => 'Principal Software Engineer',
            'photo' => 'https://picsum.photos/300/300?random=21',
            'bio' => 'Kreator konten pembelajaran programming paling populer di Indonesia dengan latar belakang arsitektur sistem skala besar.',
            'linkedin' => 'https://linkedin.com/in/eko-pzn',
            'instagram' => 'https://instagram.com/eko_pzn',
            'email' => 'eko@programmerzamannow.com',
            'phone' => '08777777777'
        ]);

        // 8. Link Speakers to Sessions (Pivot Table)
        DB::table('session_speakers')->insert([
            ['session_id' => $session1->id, 'speaker_id' => $spk1->id],
            ['session_id' => $session2->id, 'speaker_id' => $spk1->id],
            ['session_id' => $sessionWorkshop->id, 'speaker_id' => $spk2->id]
        ]);

        // 9. Ticket Types
        $ticket1_free = TicketType::create([
            'event_id' => $event1->id,
            'name' => 'Reguler (Gratis)',
            'price' => 0.00,
            'quota' => 250,
            'sale_start' => Carbon::now()->subDays(10),
            'sale_end' => Carbon::now()->addDays(5),
            'description' => 'Mendapatkan akses masuk ke semua sesi utama seminar, konsumsi snack, dan E-Sertifikat.'
        ]);

        $ticket1_vip = TicketType::create([
            'event_id' => $event1->id,
            'name' => 'VIP (Berbayar)',
            'price' => 75000.00,
            'quota' => 50,
            'sale_start' => Carbon::now()->subDays(10),
            'sale_end' => Carbon::now()->addDays(5),
            'description' => 'Mendapatkan akses kursi depan (front-row), seminar kit fisik, sertifikat cetak, lunch buffet bersama pembicara.'
        ]);

        $ticket2 = TicketType::create([
            'event_id' => $event2->id,
            'name' => 'Tiket Akses Penuh RPL',
            'price' => 150000.00,
            'quota' => 50,
            'sale_start' => Carbon::now()->subDays(5),
            'sale_end' => Carbon::now()->addDays(2),
            'description' => 'Akses penuh 2 hari workshop praktis, repositori source code github premium, dan review code personal.'
        ]);

        // 10. Sponsors
        Sponsor::create([
            'event_id' => $event1->id,
            'name' => 'Google Cloud Indonesia',
            'logo' => 'https://picsum.photos/150/50?random=30',
            'website' => 'https://cloud.google.com',
            'level' => 'GOLD'
        ]);

        Sponsor::create([
            'event_id' => $event1->id,
            'name' => 'PT. Telkom Indonesia',
            'logo' => 'https://picsum.photos/150/50?random=31',
            'website' => 'https://telkom.co.id',
            'level' => 'SILVER'
        ]);

        // 11. Galleries
        Gallery::create([
            'event_id' => $event1->id,
            'title' => 'Suasana Aula Registrasi Seminar IT 2025',
            'image' => 'https://picsum.photos/600/400?random=40',
            'sort_order' => 1
        ]);

        // 12. Announcements
        Announcement::create([
            'event_id' => $event1->id,
            'title' => 'Informasi Penukaran Seminar Kit & Pakaian Peserta',
            'content' => 'Kepada seluruh peserta seminar IT Nasional, diwajibkan menggunakan pakaian kemeja rapi / jaket almamater UNWIM. Penukaran seminar kit fisik dapat dilakukan di loket Aula Lt.1 mulai pukul 07.30 WIB dengan menunjukan QR Code tiket.',
            'published_at' => Carbon::now()
        ]);

        // 13. Committees
        EventCommittee::create([
            'event_id' => $event1->id,
            'user_id' => $orgUser->id,
            'position' => 'Koordinator Perlengkapan & Audio Visual'
        ]);

        // 14. Create Participant Users & Profiles
        $names = [
            'Rizky Pratama', 'Aulia Rahmawati', 'Daffa Ramadhan', 'Indah Permata', 
            'Budi Santoso', 'Siti Aminah', 'Fajar Nugraha', 'Dewi Lestari', 
            'Aditya Wijaya', 'Nabila Putri'
        ];

        foreach ($names as $idx => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@unwim.ac.id';
            
            // Create User Account
            $userAcc = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role_id' => null, // Regular participant
                'status' => 'ACTIVE'
            ]);

            // Create Participant Profile
            $participant = Participant::create([
                'user_id' => $userAcc->id,
                'full_name' => $name,
                'gender' => $idx % 2 == 0 ? 'M' : 'F',
                'birth_date' => Carbon::parse('2003-05-12')->subYears($idx)->format('Y-m-d'),
                'institution' => 'Universitas Winaya Mukti',
                'profession' => 'Mahasiswa',
                'phone' => '08781234560' . $idx,
                'address' => 'Kec. Tanjungsari, Sumedang, Jawa Barat',
                'photo' => 'https://picsum.photos/200/200?random=' . ($idx + 50)
            ]);

            // 15. Create Registrations
            // Map 6 participants to Seminar Event (Reguler)
            if ($idx < 6) {
                $reg = Registration::create([
                    'event_id' => $event1->id,
                    'participant_id' => $participant->id,
                    'ticket_type_id' => $ticket1_free->id,
                    'registration_number' => 'REG-' . $event1->id . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                    'status' => 'APPROVED',
                    'registered_at' => Carbon::now()->subDays(3),
                    'checked_in_at' => null,
                    'notes' => 'Pendaftaran reguler gratis'
                ]);

                // QR Code
                QrCode::create([
                    'registration_id' => $reg->id,
                    'code' => 'QR-' . $reg->registration_number,
                    'expired_at' => $event1->start_date->addHours(8),
                    'is_used' => false
                ]);

                // Payments (Free ticket, but create payment record as PAID with 0 amount)
                Payment::create([
                    'registration_id' => $reg->id,
                    'invoice_number' => 'INV-' . $reg->registration_number,
                    'payment_method' => 'SYSTEM_FREE',
                    'amount' => 0.00,
                    'status' => 'PAID',
                    'paid_at' => Carbon::now()->subDays(3)
                ]);
            } 
            // Map 2 participants to Seminar Event (VIP - Paid)
            elseif ($idx < 8) {
                $reg = Registration::create([
                    'event_id' => $event1->id,
                    'participant_id' => $participant->id,
                    'ticket_type_id' => $ticket1_vip->id,
                    'registration_number' => 'REG-' . $event1->id . '-VIP' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                    'status' => 'APPROVED',
                    'registered_at' => Carbon::now()->subDays(2),
                    'checked_in_at' => Carbon::now()->addDays(6)->setTime(8, 45, 0), // Already Checked-in!
                    'notes' => 'Membeli tiket VIP'
                ]);

                // QR Code (Used!)
                QrCode::create([
                    'registration_id' => $reg->id,
                    'code' => 'QR-' . $reg->registration_number,
                    'expired_at' => $event1->start_date->addHours(8),
                    'is_used' => true
                ]);

                // Payment
                Payment::create([
                    'registration_id' => $reg->id,
                    'invoice_number' => 'INV-' . $reg->registration_number,
                    'payment_method' => 'BANK_TRANSFER_MANDIRI',
                    'payment_gateway' => 'MIDTRANS',
                    'transaction_id' => 'TX-' . Str::upper(Str::random(10)),
                    'amount' => 75000.00,
                    'status' => 'PAID',
                    'paid_at' => Carbon::now()->subDays(2)
                ]);

                // Attendance Log (checked in!)
                AttendanceLog::create([
                    'registration_id' => $reg->id,
                    'session_id' => $session1->id,
                    'scanner_user_id' => $superAdmin->id,
                    'device' => 'Android App Scanner V1',
                    'scan_time' => Carbon::now()->addDays(6)->setTime(8, 45, 0),
                    'status' => 'SUCCESS'
                ]);

                // Certificate
                Certificate::create([
                    'event_id' => $event1->id,
                    'participant_id' => $participant->id,
                    'registration_id' => $reg->id,
                    'certificate_number' => 'CERT-IT-2026-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                    'unique_code' => 'CERT-' . Str::upper(Str::random(16)),
                    'certificate_hash' => hash('sha256', $reg->registration_number . $reg->id . now()),
                    'pdf_path' => 'certificates/cert_' . $reg->registration_number . '.pdf',
                    'issued_at' => Carbon::now()->addDays(6)->setTime(17, 0, 0)
                ]);
            }
            // Map remaining 2 participants to Workshop (VIP - Paid)
            else {
                $reg = Registration::create([
                    'event_id' => $event2->id,
                    'participant_id' => $participant->id,
                    'ticket_type_id' => $ticket2->id,
                    'registration_number' => 'REG-' . $event2->id . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                    'status' => 'PENDING', // Awaiting payment
                    'registered_at' => Carbon::now()->subDays(1),
                    'checked_in_at' => null,
                    'notes' => 'Menunggu verifikasi pembayaran'
                ]);

                // QR Code
                QrCode::create([
                    'registration_id' => $reg->id,
                    'code' => 'QR-' . $reg->registration_number,
                    'expired_at' => $event2->start_date->addHours(8),
                    'is_used' => false
                ]);

                // Payment (UNPAID)
                Payment::create([
                    'registration_id' => $reg->id,
                    'invoice_number' => 'INV-' . $reg->registration_number,
                    'payment_method' => 'BANK_TRANSFER_BCA',
                    'amount' => 150000.00,
                    'status' => 'UNPAID',
                    'paid_at' => null
                ]);
            }
        }
    }
}
