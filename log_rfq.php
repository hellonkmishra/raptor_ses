<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// require '/var/www/html/mage/com/brand-pages/vendor/PHPMailer/src/Exception.php';
// require '/var/www/html/mage/com/brand-pages/vendor/PHPMailer/src/PHPMailer.php';
// require '/var/www/html/mage/com/brand-pages/vendor/PHPMailer/src/SMTP.php';

require $_SERVER['DOCUMENT_ROOT'].'/brand-pages/vendor/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/brand-pages/vendor/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/brand-pages/vendor/PHPMailer/src/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'].'/global/global_smtp_con.php';

require '/var/www/html/mage/raptor_ses/centralize_email.php';
require '/var/www/html/mage/raptor_ses/tracks-overall-email-stats.php';

$log_filename=$_SERVER['DOCUMENT_ROOT']."/log";
// $log_filename="/var/www/html/mage/com/log";
//echo $log_filename;exit;
if(!file_exists($log_filename))
{
 mkdir($log_filename,0777,true); 
}
$blockEmail=array("sunil.mandal@nextgenesolutions.com","santoshkumar@nextgenesolutions.com");
$log_file_data=$log_filename.'/log_'.date('d-M-Y').'.log';
$log_email_count =$log_filename.'/log_email_count_'.date('d-M-Y').'.log';

if(!file_exists($log_file_data))
{
 $log_msg="Let us log all the email of Europe (Frankfurt) eu-central-1 ";
 chmod($log_file_data,0777);
 file_put_contents($log_file_data,$log_msg."\n",FILE_APPEND);
}
if(!file_exists($log_email_count))
{
 $log_msg="Let us log all the email of Europe (Frankfurt) eu-central-1 ";
 chmod($log_email_count,0777);
 file_put_contents($log_email_count,$log_msg."\n",FILE_APPEND);
}
$mailSendStatus=0;$mailLogWrite="";$getIp="";$countMailWrite='';
foreach(array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR','HTTP_FORWARDED') as $key)
{
 if(array_key_exists($key,$_SERVER)===true)
 {
  foreach(explode(',',$_SERVER[$key]) as $ip)
  {
   if(filter_var($ip,FILTER_VALIDATE_IP)!==false)
   $getIp=$ip;
  }
 }
}
function curl_get_contents1($url)
{
 $ch=curl_init();
 curl_setopt($ch,CURLOPT_URL,$url);
 curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
 curl_setopt($ch,CURLOPT_HEADER,false);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1000);
 $data=curl_exec($ch);
 curl_close($ch);
 return $data;
}
$ipgeo_url="https://raptoripgeo.raptorsupplies.com/"; 
$mailCountry=json_decode(curl_get_contents1("$ipgeo_url?addr=$getIp&api='ngBm.F2E3ihqc'"),true);
$mailCountryName="";
#$mailCountryName=$mailCountry['country_name'];
######
/*$senderEmail="info@raptorsupplies.de";$email="nirbhay@raptorsupplies.com";$mailSubject="Testing";$mailBody="Verifying";*/
######
$blockedReceipient="";
$mail=new PHPMailer(true);
try 
{
 //$mail->SMTPDebug=1;
 $mail->SMTPDebug=0;
 $mail->isSMTP();
 /*$mail->Host='email-smtp.eu-west-1.amazonaws.com';
 $mail->Username='AKIAROILJ6GG54W7FIXI';
 $mail->Password='BF+8A1a9j4gO59s1hY1e5xXCfvgw2rqOoMcMoCEj3J7S';*/
 $mail->Host=$mail_Host;
 $mail->SMTPAuth=true;
 $mail->Username=$mail_Username;
 $mail->Password=$mail_Password;
 $mail->SMTPSecure='tls';
 $mail->Port=587;
 $mail->setFrom($senderEmail,'Raptor Supplies');
 $mail->addAddress($senderEmail,'Raptor Supplies');
 $mail->addReplyTo($email);
 //$mail->addReplyTo($senderEmail, 'noreply');
 if($allowAttachment==0)
 {
  for($i=0; $i < count($_FILES['document_upload']['name']); $i++) 
  { 
   $mail->addAttachment($_FILES['document_upload']['tmp_name'][$i],$_FILES['document_upload']['name'][$i]);
   $blockedReceipient.=$_FILES['document_upload']['name'][$i];
  }
 } 
 $mail->addBCC('raptorsuppliesemailcatch@gmail.com');
 /*$mail->addBCC("nkmishra@nextgenesolutions.com"); 
 $mail->addBCC("shikhac@nextgenesolutions.com"); 
 $mail->addBCC("sanjayarya@nextgenesolutions.com");
 */
 $mail->Subject=$mailSubject;
 $mail->CharSet="UTF-8";
 $mail->isHTML(true);
 $mail->Body=$mailBody;
 if(in_array($email,$blockEmail))
 {
  $blockedReceipient=" Blocked ";
 }
 else
 {
  $mail->send();
 }
 $mailSendStatus=1;

 $domain="com";
 $from = $senderEmail;
 $to = [$email];
 $cc = [];
 $bcc = ["raptorsuppliesemailcatch@gmail.com"];
 $subject = $mailSubject;
 // Log the email
 log_email($domain, $from, $to, $cc, $bcc, $subject);
 // Update overall email recipient count per domain
 update_domain_email_count($domain, count($to) + count($cc) + count($bcc));


 $mailLogWrite="senderEmail = $senderEmail, recipientEmail = $email ,subject = $mailSubject ,body = $mailBody,recipientCountry = $mailCountryName, recipientIP = $getIp , exception=$blockedReceipient ";    
 file_put_contents($log_file_data,$mailLogWrite."\n",FILE_APPEND);
   $countMailWrite="senderEmail = $senderEmail, recipientEmail = $email ,subject = $mailSubject ,recipientCountry = $mailCountryName, recipientIP = $getIp , exception=$blockedReceipient ";
     
$lines = file($log_email_count, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (!empty($lines) && preg_match('/^Total Emails:/', end($lines))) {
    array_pop($lines);
}

$lines[] = $countMailWrite;

$totalEmails = count($lines) - 1;

$lines[] = "Total Emails: $totalEmails";

file_put_contents($log_email_count, implode("\n", $lines)."\n");
} 
catch(Exception $e) 
{
 $mailSendStatus=0;
 $exceptionLog='Mailer Error: '.$mail->ErrorInfo;
 $mailLogWrite="senderEmail = $senderEmail, recipientEmail = $email ,subject = $mailSubject ,body = $mailBody,recipientCountry = $mailCountryName, recipientIP = $getIp, exception=$exceptionLog ";
 file_put_contents($log_file_data,$mailLogWrite."\n",FILE_APPEND); 
}
?>