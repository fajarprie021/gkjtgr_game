<?php
/**
 * Generate CSV — UTF-8 BOM, semicolon separator (Excel ID-friendly).
 * All 30 audit rows, 12 placeholder rows, 15 summary rows concatenated.
 */

$d = require __DIR__ . '/_audit_data.php';

$outDir = __DIR__ . '/../docs/audit';
if (!is_dir($outDir)) { mkdir($outDir, 0775, true); }
$out = $outDir . '/bible-adventure-audit.csv';

function csvRow(array $cols): string {
    $out = [];
    foreach ($cols as $c) {
        $c = (string)$c;
        if (strpos($c, '"') !== false || strpos($c, ';') !== false || strpos($c, "\n") !== false) {
            $c = '"' . str_replace('"', '""', $c) . '"';
        }
        $out[] = $c;
    }
    return implode(';', $out);
}

$rows = $d['audit'];
$total = count($rows);
$lulus = count(array_filter($rows, fn($r) => $r[5] === 'Lulus'));
$partial = count(array_filter($rows, fn($r) => $r[5] === 'Partial'));
$gagal = count(array_filter($rows, fn($r) => $r[5] === 'Gagal'));
$belum = count(array_filter($rows, fn($r) => $r[5] === 'Belum Dicek'));
$na = count(array_filter($rows, fn($r) => $r[5] === 'N/A'));

$now = date('Y-m-d H:i:s');

$csv = "\xEF\xBB\xBF"; // UTF-8 BOM

// Header info
$csv .= csvRow(['Bible Adventure v1 — Audit Hasil']) . "\n";
$csv .= csvRow(['Tanggal', $now]) . "\n";
$csv .= csvRow(['Mesin', '127.0.0.1:8000 (PHP built-in)']) . "\n";
$csv .= "\n";

// Stats
$csv .= csvRow(['Metrik', 'Jumlah']) . "\n";
foreach ([
    ['Total item diaudit', $total],
    ['Lulus', $lulus],
    ['Partial', $partial],
    ['Gagal', $gagal],
    ['Belum Dicek', $belum],
    ['N/A', $na],
] as $s) {
    $csv .= csvRow([$s[0], (string)$s[1]]) . "\n";
}
$csv .= "\n";

// Audit 30 Item
$csv .= '=== AUDIT 30 ITEM ===' . "\n";
$csv .= csvRow($d['headers']) . "\n";
foreach ($rows as $r) {
    $csv .= csvRow($r) . "\n";
}
$csv .= "\n";

// Placeholder Pages
$csv .= '=== PLACEHOLDER PAGES ===' . "\n";
$csv .= csvRow($d['placeholder_headers']) . "\n";
foreach ($d['placeholders'] as $p) {
    $csv .= csvRow($p) . "\n";
}
$csv .= "\n";

// Ringkasan A-030
$csv .= '=== RINGKASAN A-030 ===' . "\n";
$csv .= csvRow(['Modul', 'Status', 'Catatan']) . "\n";
foreach ($d['summaries'] as $s) {
    $csv .= csvRow($s) . "\n";
}

file_put_contents($out, $csv);
echo "CSV written: {$out} (" . number_format(filesize($out)) . " bytes)\n";
