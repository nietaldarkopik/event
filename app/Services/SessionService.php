<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use Modules\Session\Models\EventSession;

/**
 * Sesi peserta (mis. Wisuda sesi 1 / 2). Mode di events.session_mode:
 *  none   = event tanpa sesi
 *  admin  = sesi ditetapkan panitia
 *  choose = peserta memilih sendiri; bebas bila sesi tanpa kuota, dibatasi bila sesi punya kuota
 */
class SessionService
{
    /** Peserta yang menempati kursi sesi (yang ditolak tidak dihitung). */
    public static function taken(int $sessionId): int
    {
        return Participant::where('session_id', $sessionId)->where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'rejected');
        })->count();
    }

    /** Daftar sesi event beserta kuota terpakai/sisa. remaining null = tanpa batas. */
    public static function overview(Event $event)
    {
        return $event->sessions()->get()->map(function (EventSession $s) {
            $taken = self::taken($s->id);
            $s->taken = $taken;
            $s->remaining = $s->max_participants ? max(0, $s->max_participants - $taken) : null;
            $s->is_full = $s->max_participants ? $taken >= $s->max_participants : false;

            return $s;
        });
    }

    public static function usesChoice(Event $event): bool
    {
        return $event->session_mode === 'choose' && $event->sessions()->exists();
    }

    /**
     * Buat peserta pada sesi yang dipilih, dengan pengecekan kuota yang aman dari balapan
     * (baris sesi dikunci selama pengecekan dan pembuatan).
     *
     * @throws \RuntimeException bila sesi tidak valid atau penuh
     */
    public static function createParticipant(Event $event, ?int $sessionId, array $attrs): Participant
    {
        return \DB::transaction(function () use ($event, $sessionId, $attrs) {
            if ($sessionId) {
                $session = EventSession::where('event_id', $event->id)->lockForUpdate()->find($sessionId);
                if (!$session) {
                    throw new \RuntimeException('Sesi yang dipilih tidak valid.');
                }
                if ($session->max_participants && self::taken($session->id) >= $session->max_participants) {
                    throw new \RuntimeException('Mohon maaf, kuota ' . $session->title . ' sudah penuh. Silakan pilih sesi lain.');
                }
            }

            return Participant::create($attrs + ['event_id' => $event->id, 'session_id' => $sessionId]);
        });
    }
}
