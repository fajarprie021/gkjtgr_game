<?php
/**
 * Master build — generate all 3 audit export formats (XML, XLS, CSV).
 * Single source of truth: _audit_data.php
 */

require __DIR__ . '/_audit_data.php';

$here = __DIR__;
$builds = [
    $here . '/_build_audit_xml.php',
    $here . '/_build_audit_xls.php',
    $here . '/_build_audit_csv.php',
];

foreach ($builds as $b) {
    echo "--- running " . basename($b) . " ---\n";
    passthru(PHP_BINARY . ' ' . escapeshellarg($b));
}
echo "\n=== Done ===\n";
