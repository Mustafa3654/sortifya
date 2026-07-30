<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Writes the sample source PDFs and column templates the seeded tasks link to.
 *
 * Without these the demo data would point at files that do not exist and every
 * "Download PDF" button on a fresh install would 404. The PDF is assembled by
 * hand rather than pulled from a library: it is a handful of bytes and adding
 * a PDF dependency for placeholder data is not worth it.
 */
class PlaceholderFiles
{
    public static function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('sortifya.uploads.tasks_disk'));
    }

    /** @param  array<int, string>  $lines */
    public static function pdf(string $path, string $title, array $lines): string
    {
        if (! self::disk()->exists($path)) {
            self::disk()->put($path, self::buildPdf($title, $lines));
        }

        return $path;
    }

    /** @param  array<int, string>  $headers */
    public static function template(string $path, array $headers): string
    {
        if (! self::disk()->exists($path)) {
            // A CSV opens in Excel, Numbers and Sheets alike — the widest net
            // for workers who may not own Office.
            self::disk()->put($path, implode(',', $headers)."\r\n");
        }

        return $path;
    }

    /*
    |--------------------------------------------------------------------------
    | Minimal PDF writer
    |--------------------------------------------------------------------------
    */

    /** @param  array<int, string>  $lines */
    private static function buildPdf(string $title, array $lines): string
    {
        $content = "BT\n/F1 18 Tf\n60 780 Td\n(".self::escape($title).") Tj\nET\n";
        $y = 748;

        foreach ($lines as $line) {
            $content .= "BT\n/F1 11 Tf\n60 {$y} Td\n(".self::escape($line).") Tj\nET\n";
            $y -= 20;
        }

        $objects = [
            "<< /Type /Catalog /Pages 2 0 R >>",
            "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] "
                ."/Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>",
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
            "<< /Length ".strlen($content)." >>\nstream\n{$content}endstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        // The xref table must carry the byte offset of every object, so it is
        // written only after the body above is fully assembled.
        $xrefPosition = strlen($pdf);
        $count = count($objects) + 1;

        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefPosition}\n%%EOF";

        return $pdf;
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
