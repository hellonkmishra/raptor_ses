<?php
function update_domain_email_count($domain, $count) {
    try
    {
    $logFile = __DIR__ . '/centralized_email_count_stats.json';

    // Read existing stats
    $stats = [];
    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        if ($content) {
            $stats = json_decode($content, true);
        }
    }

    // Update count for this domain
    if (!isset($stats[$domain])) {
        $stats[$domain] = 0;
    }
    $stats[$domain] += $count;

    // Save updated stats back to JSON
    file_put_contents($logFile, json_encode($stats, JSON_PRETTY_PRINT));
}
    catch(Exception $e) 
{
 echo  $e."-----";exit; 
}
}

// Example usage
#update_domain_email_count('com', 5); // Adds 5 to 'com'
#update_domain_email_count('uk', 3);  // Adds 3 to 'uk'
#update_domain_email_count('com', 2); // Adds 2 more to 'com'
?>
