<?php

// ----------------------------------------------------
// Load PHPMailer (no Composer)
// ----------------------------------------------------
require_once 'app/libraries/PHPMailer/src/PHPMailer.php';
require_once 'app/libraries/PHPMailer/src/SMTP.php';
require_once 'app/libraries/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// ----------------------------------------------------
// Email Validation Functions
// ----------------------------------------------------
function isValidSyntax($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function hasValidMx($email) {
    $domain = substr(strrchr($email, "@"), 1);
    return checkdnsrr($domain, "MX");
}

function smtpCheck($email) {
    list($user, $domain) = explode('@', $email);

    // Fetch MX records
    if (!getmxrr($domain, $mxHosts)) {
        return false;
    }

    $mx = $mxHosts[0];
    $connection = @fsockopen($mx, 25, $errno, $errstr, 10);
    if (!$connection) {
        return false;
    }

    fgets($connection);

    fputs($connection, "HELO test.com\r\n");
    fgets($connection);

    fputs($connection, "MAIL FROM:<check@test.com>\r\n");
    fgets($connection);

    fputs($connection, "RCPT TO:<$email>\r\n");
    $response = fgets($connection);

    fputs($connection, "QUIT\r\n");
    fclose($connection);

    return strpos($response, "250") !== false;
}

function emailExists($email) {
    if (!isValidSyntax($email)) {
        return "Invalid email format.";
    }

    if (!hasValidMx($email)) {
        return "No MX records found for domain.";
    }

    if (!smtpCheck($email)) {
        return "Mailbox probably does not exist.";
    }

    return true;
}


// ----------------------------------------------------
// Your Configuration
// ----------------------------------------------------
$yourGmail    = 'beastsmm98@gmail.com';
$yourAppPass  = 'miii orwi ibaq roqc';
$sendTo       = 'aliabbaszounr213@gmail.com';  // Email to send
$siteName     = 'BeastSMM';
$logoUrl      = 'https://beastsmm.pk/assets/uploads/userda4b9237bacccdf19c0760cab7aec4a8359010b0/b865ffd58a0a5a4c99fefbab7e6045b5.png';
$dashboardUrl = 'https://your-site.com/dashboard';


// ----------------------------------------------------
// Reject all NON-GMAIL addresses
// ----------------------------------------------------
if (!preg_match('/@gmail\.com$/i', $sendTo)) {
    echo "Email rejected: Only @gmail.com addresses are allowed.";
    exit;
}


// ----------------------------------------------------
// Validate Email BEFORE Sending
// ----------------------------------------------------
$validation = emailExists($sendTo);

if ($validation !== true) {
    echo "Email validation failed: " . $validation;
    exit;
}


// ----------------------------------------------------
// Email Content
// ----------------------------------------------------
$subject  = "Welcome to $siteName";
$userName = "Dear User";

$mainText = "Thanks for joining $siteName! Your account is ready. Click the button below to open your dashboard and start using the service.";


// ----------------------------------------------------
// HTML Template
// ----------------------------------------------------
$htmlBody = <<<HTML
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>{$subject}</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f4f6; font-family:Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f2f4f6; padding:20px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
        
          <tr>
            <td style="padding:20px; background:#0b63d6; color:#fff;">
              <table width="100%">
                <tr>
                  <td><img src="{$logoUrl}" width="140" style="display:block;"></td>
                  <td style="text-align:right; font-size:14px;">{$siteName}</td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:28px;">
              <h2 style="margin:0 0 12px; color:#333;">Hello {$userName},</h2>
              <p style="color:#555; line-height:1.5;">{$mainText}</p>

              <table cellpadding="0" cellspacing="0" style="margin:25px 0;">
                <tr>
                  <td align="center">
                    <a href="{$dashboardUrl}" target="_blank" 
                      style="background:#0b63d6; color:#fff; padding:14px 22px; border-radius:6px; text-decoration:none; font-weight:bold;">
                      Open Dashboard
                    </a>
                  </td>
                </tr>
              </table>

              <p style="color:#999; font-size:13px;">
                If the button doesn't work, open this link:
                <br>
                <a href="{$dashboardUrl}" style="color:#0b63d6;">{$dashboardUrl}</a>
              </p>

              <hr style="border:0; border-top:1px solid #eee; margin:30px 0;">

              <p style="color:#777; font-size:13px;">
                Need help? Reply to this email.
              </p>
            </td>
          </tr>

          <tr>
            <td style="text-align:center; padding:18px; background:#fafafa; color:#999; font-size:12px;">
              © {$siteName} — All Rights Reserved
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

$altBody = "Welcome to $siteName\n\n$mainText\n\nDashboard: $dashboardUrl";


// ----------------------------------------------------
// SEND EMAIL
// ----------------------------------------------------
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $yourGmail;
    $mail->Password   = $yourAppPass;
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom($yourGmail, $siteName . ' Mailer');
    $mail->addAddress($sendTo);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlBody;
    $mail->AltBody = $altBody;

    if ($mail->send()) {
        echo "Message sent successfully!";
    } else {
        echo "Failed: " . $mail->ErrorInfo;
    }

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}

?>
