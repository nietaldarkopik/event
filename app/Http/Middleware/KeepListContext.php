<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Setelah aksi pada halaman daftar (hapus, setujui, dll.) pengguna dikembalikan ke halaman daftar YANG SAMA
 * lengkap dengan pencarian, filter, dan nomor halamannya, bukan ke daftar polos.
 *
 * Aturannya sempit: hanya bila aksi bukan GET dan controller mengarahkan ke halaman dengan path yang sama
 * dengan halaman asal (referer). Alur lain (mis. simpan dari form edit ke daftar) tidak terpengaruh.
 */
class KeepListContext
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('GET') || ! $response instanceof RedirectResponse) {
            return $response;
        }

        $referer = $request->headers->get('referer');
        if (! $referer) {
            return $response;
        }

        $from = parse_url($referer);
        $to = parse_url($response->getTargetUrl());

        $sameHost = ($from['host'] ?? null) === ($request->getHost());
        $samePath = rtrim($from['path'] ?? '', '/') === rtrim($to['path'] ?? '', '/');

        if ($sameHost && $samePath && ! empty($from['query'])) {
            $response->setTargetUrl($referer);
        }

        return $response;
    }
}
