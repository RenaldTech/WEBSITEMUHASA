<?php
declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$scanDirs = [
    $projectRoot . '/src',
    $projectRoot . '/admin',
    $projectRoot . '/includes',
];
$logDir = $projectRoot . '/logs';
$logFile = $logDir . '/sql_audit.log';

if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
    fwrite(STDERR, "Unable to create log directory: {$logDir}\n");
    exit(1);
}

$patterns = [
    'HIGH' => '/\bescapeString\s*\(/i',
    'MEDIUM' => '/->query\s*\(|fetch_assoc\s*\(/i',
    'LOW' => '/\b(SELECT|INSERT|UPDATE|DELETE|REPLACE)\b/i',
];

$reportLines = [];

foreach ($scanDirs as $directory) {
    if (!is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getRealPath();
        if ($path === false) {
            continue;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $index => $line) {
            $priority = null;
            foreach ($patterns as $level => $pattern) {
                if (preg_match($pattern, $line)) {
                    $priority = $level;
                    break;
                }
            }

            if ($priority !== null) {
                $reportLines[] = sprintf(
                    "%s:%d: [%s] %s",
                    $path,
                    $index + 1,
                    $priority,
                    trim($line)
                );
            }
        }
    }
}

$report = "SQL audit report generated on " . date('Y-m-d H:i:s') . "\n";
$report .= "Scan directories: " . implode(', ', $scanDirs) . "\n";
$report .= str_repeat('=', 80) . "\n";
$report .= implode("\n", $reportLines) . "\n";

file_put_contents($logFile, $report);
fwrite(STDOUT, "Audit complete. Report written to: {$logFile}\n");
