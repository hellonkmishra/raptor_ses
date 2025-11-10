<?php
function log_email($domain, $from, $to = [], $cc = [], $bcc = [], $subject = '') {

    $logFile = __DIR__ . '/centralized_email_log.json';

    // Ensure arrays (fixes count() warnings)
    $to  = is_array($to)  ? $to  : ($to  ? [$to]  : []);
    $cc  = is_array($cc)  ? $cc  : ($cc  ? [$cc]  : []);
    $bcc = is_array($bcc) ? $bcc : ($bcc ? [$bcc] : []);

    // Load previous logs (if file exists)
    $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

    if (!is_array($logs)) {
        $logs = [];
    }

    // Serial number
    $sno = count($logs) + 1;

    // Total recipients
    $total_recipients = count($to) + count($cc) + count($bcc);

    // Create entry
    $entry = [
        's_no' => $sno,
        'domain' => $domain,
        'from' => $from,
        'to' => $to,
        'cc' => $cc,
        'bcc' => $bcc,
        'total_recipients' => $total_recipients,
        'subject' => $subject,
        'date_time' => date('Y-m-d H:i:s')
    ];

    $logs[] = $entry;

    // Write safely (no crash)
    if (is_writable(dirname($logFile))) {
        file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    } else {
        error_log("⚠ Cannot write to $logFile — check permissions.");
    }
}
