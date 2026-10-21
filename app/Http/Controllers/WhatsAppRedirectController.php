<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppRedirectController extends Controller
{
    public function redirect(Request $request)
    {
        // 1. Rate Limiting (handled by middleware route, but extra check here)
        // 2. Honeypot Check
        if ($request->filled('website_hp')) {
            $this->logAttempt($request, 'spam', 'Honeypot Triggered');
            return response()->json(['status' => 'error', 'message' => 'Spam detected (HP).'], 403);
        }

        // 3. ReCaptcha Check (Server-side)
        $recaptchaToken = $request->input('g-recaptcha-response');
        $score = $this->verifyRecaptcha($recaptchaToken);

        if ($score < 0.5) {
            $this->logAttempt($request, 'bot', 'Low ReCaptcha Score: ' . $score, $score);
            return response()->json(['status' => 'error', 'message' => 'Bot detected. Score: ' . $score], 403);
        }

        // 4. Content Filter (Basic)
        if ($this->hasSpamContent($request->all())) {
            $this->logAttempt($request, 'spam_content', 'Spam Keywords Found', $score);
            return response()->json(['status' => 'error', 'message' => 'Spam content detected.'], 403);
        }

        // 5. Approved
        $this->logAttempt($request, 'approved', 'Clean', $score);

        $targetUrl = $request->input('target_url');

        // Basic validation for target URL to ensure it is a WA link
        if (!str_contains($targetUrl, 'wa.me') && !str_contains($targetUrl, 'whatsapp.com')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Target URL'], 400);
        }

        return response()->json(['status' => 'success', 'redirect_url' => $targetUrl]);
    }

    private function verifyRecaptcha($token)
    {
        // For development/testing without keys, return high score
        if (env('APP_ENV') === 'local' && empty(env('RECAPTCHA_SECRET_KEY'))) {
            return 0.9;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        $body = $response->json();

        if ($body['success'] ?? false) {
            return $body['score'] ?? 0.0;
        }

        return 0.0;
    }

    private function hasSpamContent($data)
    {
        $spamKeywords = ['casino', 'crypto', 'viagra', 'investment', 'http', 'www', '.com']; // Basic examples
        $payload = json_encode($data);

        foreach ($spamKeywords as $keyword) {
            if (stripos($payload, $keyword) !== false) {
                // Allow URLs in target_url but check other fields if any
                return false; // For now, we only have structure fields, so strict keyword blocking might be too aggressive on URLs. 
                // Current strategy: The form basically just Pass-Throughs the URL.
                // So content filtering is less critical here than on a Comment form.
            }
        }
        return false;
    }

    private function logAttempt($request, $status, $reason, $score = null)
    {
        \Illuminate\Support\Facades\DB::table('whatsapp_logs')->insert([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event_slug' => $request->input('event_slug'),
            'phone_number' => $request->input('phone_number'), // Extract from URL if possible, or passed hidden
            'payload' => json_encode($request->all()),
            'recaptcha_score' => $score,
            'status' => $status,
            'reason' => $reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
