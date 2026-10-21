<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Participant;
use App\Models\Event;
use App\Models\CertificateCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate a certificate for a participant.
     */
    public function generate(Participant $participant, CertificateCategory $category)
    {
        return DB::transaction(function () use ($participant, $category) {
            // Check if already exists
            $existing = Certificate::where('participant_id', $participant->id)
                ->where('category_id', $category->id)
                ->first();

            if ($existing)
                return $existing;

            $event = $participant->event;
            $certificateNumber = $this->generateNumber($event, $category);
            // Secure hash: number + timestamp + secret salt
            $hash = hash('sha256', $certificateNumber . now()->timestamp . config('app.key'));

            return Certificate::create([
                'event_id' => $event->id,
                'participant_id' => $participant->id,
                'recipient_name' => $participant->name,
                'recipient_unit' => $participant->unit ?: ($participant->program_studi ?: $participant->fakultas),
                'category_id' => $category->id,
                'template_id' => $category->template_id,
                'certificate_number' => $certificateNumber,
                'unique_code' => strtoupper(Str::random(12)),
                'certificate_hash' => $hash,
                'issued_at' => now(),
                'is_active' => true,
            ]);
        });
    }

    /**
     * Terbitkan sertifikat untuk anggota panitia (data dari SK/ST di event_committees, tanpa akun peserta).
     * Idempoten per panitia + kategori.
     */
    public function generateForCommittee(\Modules\Event\Models\EventCommittee $member, CertificateCategory $category)
    {
        return DB::transaction(function () use ($member, $category) {
            $existing = Certificate::where('committee_id', $member->id)->where('category_id', $category->id)->first();
            if ($existing) {
                return $existing;
            }

            $event = Event::findOrFail($member->event_id);
            $certificateNumber = $this->generateNumber($event, $category);
            $hash = hash('sha256', $certificateNumber . now()->timestamp . config('app.key'));

            return Certificate::create([
                'event_id' => $event->id,
                'participant_id' => null,
                'committee_id' => $member->id,
                'recipient_name' => $member->display_name,
                'recipient_unit' => $member->unit,
                'category_id' => $category->id,
                'template_id' => $category->template_id,
                'certificate_number' => $certificateNumber,
                'unique_code' => strtoupper(Str::random(12)),
                'certificate_hash' => $hash,
                'issued_at' => now(),
                'is_active' => true,
            ]);
        });
    }

    /**
     * Generate a secure sequential number for the certificate.
     */
    protected function generateNumber(Event $event, CertificateCategory $category)
    {
        // Format: [PREFIX]/[EVENT_SLUG]/[YEAR]/[SEQUENCE]
        // Use DB lock to prevent race conditions during sequence generation
        return DB::transaction(function () use ($event, $category) {
            $year = date('Y');
            $prefix = $category->certificate_prefix ?: 'CERT';

            // Count existing certificates for this event and year to get the next sequence
            // Lock the table for writing
            $count = Certificate::where('event_id', $event->id)
                ->whereYear('issued_at', $year)
                ->lockForUpdate()
                ->count();

            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            return "{$prefix}/" . strtoupper(substr($event->slug, 0, 5)) . "/{$year}/{$sequence}";
        });
    }

    /**
     * Replace dynamic variables in the layout JSON.
     */
    public function replaceVariables(string $layoutJson, Certificate $certificate)
    {
        $participant = $certificate->participant;
        $committee = $certificate->committee;
        $event = $certificate->event;
        $category = $certificate->category;

        $signatures = $event->certificateSignatures()->orderBy('id')->get();
        $sig1 = $signatures->first();
        $sig2 = $signatures->skip(1)->first();

        $variables = [
            '{nama_peserta}' => $certificate->display_name,
            '{nomor_sertifikat}' => $certificate->certificate_number,
            '{judul_event}' => $event->title,
            '{tanggal_event}' => $event->start_datetime->isoFormat('D MMMM Y'),
            '{peran_event}' => $category->name,
            '{kode_unik}' => $certificate->unique_code,
            '{nim}' => optional($participant)->nim ?? '',
            '{unit}' => $certificate->display_unit ?? '',
            '{jabatan_panitia}' => optional($committee)->position ?? '',
            '{seksi}' => optional($committee)->section ?? '',
            '{nama_ttd1}' => $sig1->signer_name ?? '',
            '{jabatan_ttd1}' => $sig1->signer_position ?? '',
            '{nama_ttd2}' => $sig2->signer_name ?? '',
            '{jabatan_ttd2}' => $sig2->signer_position ?? '',
        ];

        foreach ($variables as $key => $value) {
            // nilai disisipkan ke dalam string JSON: escape dulu agar tanda kutip/garis miring pada nama tidak merusak JSON
            $safe = substr(json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1);
            $layoutJson = str_replace($key, $safe, $layoutJson);
        }

        return $layoutJson;
    }
}
