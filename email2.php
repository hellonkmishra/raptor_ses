<?php
require 'vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

$provider = CredentialProvider::instanceProfile();
$provider = CredentialProvider::memoize($provider);

$SesClient = new SesClient([
    'version' => '2010-12-01',
    'region'  => 'eu-west-1',
    'credentials' => $provider,
    'suppress_php_deprecation_warning' => true
]);
/*"
customercare@raptorsupplies.com,no-reply@raptorsupplies.com sales@raptorsupplies.com,support@raptorsupplies.com
"*/
$sender_email = 'no-reply@raptorsupplies.com';
$recipient_emails = ['nkmishra@nextgenesolutions.com'];
$subject = 'PRODUCTION COM INSTANCE - Test Email with Campaign Tag';
$body_text = "This is a test email with tagging.";
$body_html = "<h1>Test Email</h1><p>This email was sent using AWS SES with a campaign tag.</p>";

try {
    $result = $SesClient->sendEmail([
        'Source' => $sender_email,
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
        'Message' => [
            'Subject' => [
                'Data' => $subject,
                'Charset' => 'UTF-8',
            ],
            'Body' => [
                'Text' => [
                    'Data' => $body_text,
                    'Charset' => 'UTF-8',
                ],
                'Html' => [
                    'Data' => $body_html,
                    'Charset' => 'UTF-8',
                ],
            ],
        ],
        'Tags' => [
            [
                'Name' => 'campaign',
                'Value' => 'spring-sale-2025',
            ],
        ],
    ]);
    echo "✅ Email sent! Message ID: " . $result['MessageId'] . "\n";
} catch (AwsException $e) {
    echo "❌ Error sending email: " . $e->getAwsErrorMessage() . "\n";
}
