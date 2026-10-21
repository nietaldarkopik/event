<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QrCodeImageService
{
    /**
     * Generate a QR code PNG image from a unique code and save it to storage.
     *
     * Returns:
     *   [
     *     'path'       => '/absolute/path/to/qrcodes/{code}.png',  (for WA local file send)
     *     'url'        => 'https://example.com/storage/qrcodes/{code}.png',
     *     'disk_path'  => 'qrcodes/{code}.png',  (relative to storage/app/public)
     *   ]
     *
     * @param string $uniqueCode  The invitation unique code (e.g. "EVT3ABC123")
     * @param string $content     What to encode in the QR. Defaults to a verification URL.
     * @return array{path: string, url: string, disk_path: string}
     */
    public static function generateAndSave(string $uniqueCode, ?string $content = null): array
    {
        // Default: encode the bare unique code, matching what the attendance
        // scanner looks up (AttendanceController::processScan). Encoding a URL
        // here previously pointed to a "/check-invitation" route that does not
        // exist, so scanning it never matched an invitation.
        if (!$content) {
            $content = $uniqueCode;
        }

        $filename  = 'qrcodes/' . $uniqueCode . '.png';
        $absPath   = storage_path('app/public/' . $filename);

        // Ensure directory exists
        $dir = dirname($absPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        // Generate QR as PNG and write to disk
        QrCode::format('png')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')   // High error correction — more robust for scanning
            ->generate($content, $absPath);

        Log::info('[QR] Generated QR code', [
            'code' => $uniqueCode,
            'path' => $absPath,
        ]);

        return [
            'path'      => $absPath,
            'url'       => Storage::disk('public')->url($filename),
            'disk_path' => $filename,
        ];
    }

    /**
     * Check if a QR code already exists for the given unique code.
     */
    public static function exists(string $uniqueCode): bool
    {
        return Storage::disk('public')->exists('qrcodes/' . $uniqueCode . '.png');
    }

    /**
     * Get the absolute path of an existing QR code (or null if not found).
     */
    public static function getPath(string $uniqueCode): ?string
    {
        $path = storage_path('app/public/qrcodes/' . $uniqueCode . '.png');
        return file_exists($path) ? $path : null;
    }

    /**
     * Get the public URL of an existing QR code (or null if not found).
     */
    public static function getUrl(string $uniqueCode): ?string
    {
        if (!self::exists($uniqueCode)) {
            return null;
        }
        return Storage::disk('public')->url('qrcodes/' . $uniqueCode . '.png');
    }
}
