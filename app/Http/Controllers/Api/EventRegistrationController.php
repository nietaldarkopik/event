<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\Participant;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail; // Untuk pengiriman email

class EventRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'event_id' => 'required|exists:events,id',
        ]);

        $email = $request->input('email');
        $event_id = $request->input('event_id');

        $event = Event::find($event_id);

        // Cek apakah peserta sudah terdaftar untuk event ini
        $existingParticipant = Participant::where('event_id', $event_id)
                                        ->where('email', $email)
                                        ->first();

        if ($existingParticipant) {
            if ($existingParticipant->status === 'pending') {
                return response()->json([
                    'message' => 'Pendaftaran Anda untuk event ini sedang menunggu verifikasi admin.',
                    'status' => 'pending'
                ], 202); // Accepted for processing
            } else {
                // Peserta sudah terdaftar dan disetujui
                // Kirim ulang undangan jika diperlukan
                $invitation = Invitation::where('participant_id', $existingParticipant->id)->first();
                if ($invitation) {
                    // Kirim ulang email undangan
                    Mail::to($email)->send(new EventInvitationMail($invitation));
                    return response()->json([
                        'message' => 'Anda sudah terdaftar untuk event ini. Undangan telah dikirim ulang.',
                        'status' => 'registered',
                        'invitation_code' => $invitation->unique_code
                    ]);
                } else {
                    // Ini seharusnya tidak terjadi jika alur normal diikuti
                    return response()->json([
                        'message' => 'Anda sudah terdaftar untuk event ini, tetapi undangan tidak ditemukan. Silakan hubungi admin.',
                        'status' => 'error'
                    ], 500);
                }
            }
        }

        // Jika peserta belum terdaftar, cek status email
        $user = User::where('email', $email)->first();

        if ($user) {
            // Email terdaftar sebagai user, langsung set status approved
            $participant = Participant::create([
                'event_id' => $event_id,
                'email' => $email,
                'name' => $user->name, // Ambil nama dari user jika ada
                'status' => 'approved',
                // TODO: Tambahkan data participant lainnya jika diperlukan dari request
            ]);

            $uniqueCode = Str::random(10); // Generate kode unik
            $invitation = Invitation::create([
                'event_id' => $event_id,
                'participant_id' => $participant->id,
                'unique_code' => $uniqueCode,
                'is_active' => true,
                'expired_at' => now()->addDays(7), // Undangan berlaku 7 hari
                'sent_at' => now(),
            ]);

            // Kirim email undangan
            Mail::to($email)->send(new EventInvitationMail($invitation));

            return response()->json([
                'message' => 'Pendaftaran berhasil! Undangan telah dikirim ke email Anda.',
                'status' => 'approved',
                'invitation_code' => $uniqueCode
            ], 201); // Created
        } else {
            // Email belum terdaftar, set status pending untuk verifikasi admin
            $participant = Participant::create([
                'event_id' => $event_id,
                'email' => $email,
                'name' => $request->input('name', 'Guest'), // Nama bisa dari request atau default
                'status' => 'pending',
                // TODO: Tambahkan data participant lainnya jika diperlukan dari request
            ]);

            return response()->json([
                'message' => 'Pendaftaran Anda berhasil dan sedang menunggu verifikasi admin.',
                'status' => 'pending'
            ], 202); // Accepted for processing
        }
    }
}
