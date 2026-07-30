<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use Throwable;

/**
 * Lifts the top of an uploaded spreadsheet into a plain array.
 *
 * Reviewers judge most submissions from the first few rows, so the preview is
 * extracted once at upload time and stored on the submission. The admin table
 * then renders it without ever touching the file again.
 *
 * A read filter caps what PhpSpreadsheet loads into memory: a worker who
 * uploads a 40,000-row sheet costs us eleven rows of RAM, not forty thousand.
 */
class SpreadsheetParser
{
    /**
     * @return array{headers: array<int, string>, rows: array<int, array<int, string>>, total_rows: int, total_columns: int, sheet: string, truncated: bool}
     */
    public function preview(string $absolutePath): array
    {
        $rowLimit = (int) config('sortifya.preview_rows');
        $columnLimit = (int) config('sortifya.preview_columns');

        $reader = IOFactory::createReaderForFile($absolutePath);
        $reader->setReadDataOnly(true);

        // Ask the file how big it is before deciding what to load.
        $info = $reader->listWorksheetInfo($absolutePath);
        $first = $info[0] ?? [];
        $totalRows = (int) ($first['totalRows'] ?? 0);
        $totalColumns = (int) ($first['totalColumns'] ?? 0);
        $sheetName = (string) ($first['worksheetName'] ?? 'Sheet1');

        if (method_exists($reader, 'setLoadSheetsOnly') && $sheetName !== '') {
            $reader->setLoadSheetsOnly($sheetName);
        }

        // +1 row for the header line itself.
        $reader->setReadFilter(new TopLeftReadFilter($rowLimit + 1, $columnLimit));

        $sheet = $reader->load($absolutePath)->getActiveSheet();

        $grid = $sheet->toArray(null, true, false, false);
        $grid = array_values(array_filter(
            $grid,
            static fn ($row) => is_array($row) && array_filter($row, static fn ($cell) => $cell !== null && $cell !== ''),
        ));

        $headerRow = array_shift($grid) ?? [];

        return [
            'headers' => $this->normaliseHeaders($headerRow, $columnLimit),
            'rows' => array_map(
                fn ($row) => $this->normaliseRow($row, $columnLimit),
                array_slice($grid, 0, $rowLimit),
            ),
            // Minus the header line, so the figure matches what a worker typed.
            'total_rows' => max(0, $totalRows - 1),
            'total_columns' => $totalColumns,
            'sheet' => $sheetName,
            'truncated' => $totalRows - 1 > $rowLimit,
        ];
    }

    /**
     * Never lets a malformed upload break the request. A submission with no
     * preview still reaches review; the admin just downloads it instead.
     *
     * @return array{headers: array<int, string>, rows: array<int, array<int, string>>, total_rows: int, total_columns: int, sheet: string, truncated: bool, error: string}|array<string, mixed>
     */
    public function previewSafely(string $absolutePath): array
    {
        try {
            return $this->preview($absolutePath);
        } catch (Throwable $e) {
            report($e);

            return [
                'headers' => [],
                'rows' => [],
                'total_rows' => 0,
                'total_columns' => 0,
                'sheet' => '',
                'truncated' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array<int, string>
     */
    private function normaliseHeaders(array $row, int $limit): array
    {
        $headers = [];

        foreach (array_slice(array_values($row), 0, $limit) as $index => $value) {
            $label = trim((string) ($value ?? ''));
            // An unlabelled column still needs a heading; fall back to A, B, C.
            $headers[] = $label !== '' ? $label : Coordinate::stringFromColumnIndex($index + 1);
        }

        return $headers;
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array<int, string>
     */
    private function normaliseRow(array $row, int $limit): array
    {
        return array_map(
            static fn ($cell) => mb_substr(trim((string) ($cell ?? '')), 0, 180),
            array_slice(array_values($row), 0, $limit),
        );
    }
}

/**
 * Loads only the top-left corner of a sheet.
 */
class TopLeftReadFilter implements IReadFilter
{
    public function __construct(
        private readonly int $maxRow,
        private readonly int $maxColumn,
    ) {}

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        if ($row > $this->maxRow) {
            return false;
        }

        return Coordinate::columnIndexFromString($columnAddress) <= $this->maxColumn;
    }
}
