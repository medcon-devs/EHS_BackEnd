<?php


namespace App\Helper;


use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class _EmailHelper
{
    public function __construct()
    {
    }

    public static function sendEmail($user, $data, $view, $subject)
    {
        try {
            $mail = new PHPMailer(true);
            // SMTP configurations
            $mail->isSMTP();
            $mail->Host = 'xxxx';
            $mail->SMTPAuth = true;
            $mail->Username = 'xxxx';
            $mail->Password = 'xxxx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('xxxx.com', 'xxxx');          //This is the email your form sends From
            $mail->ContentType = "text/html;charset=UTF-8\r\n";
            $mail->Priority = 3;
            $mail->addCustomHeader("MIME-Version: 1.0\r\n");
            $mail->addCustomHeader("X-Mailer: PHP'" . phpversion() . "'\r\n");
            $mail->addAddress($user->email, $user->name);
            $mail->Subject = 'xxxx - ' . $subject;

            $mail->isHTML();
            // Email body content
            $mail->Body = view($view, $data)->render();
            // Send email
            if ($mail->send()) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }

    public static function sendEmailToInfo($email, $data, $view, $subject)
    {
        try {
            $mail = new PHPMailer();
            // SMTP configurations
            $mail->isSMTP();
            $mail->Host = 'xxxx';
            $mail->SMTPAuth = true;
            $mail->Username = 'xxxx';
            $mail->Password = 'xxxx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('xxxx', 'xxxx');          //This is the email your form sends From
            $mail->ContentType = "text/html;charset=UTF-8\r\n";
            $mail->Priority = 3;
            $mail->addCustomHeader("MIME-Version: 1.0\r\n");
            $mail->addCustomHeader("X-Mailer: PHP'" . phpversion() . "'\r\n");
            $mail->addAddress("xxxx", "xxxx");
            $mail->Subject = 'xxxx - ' . $subject;

            $mail->isHTML(true);
            // Email body content
            $mail->Body = view($view, $data)->render();
            // Send email
            if ($mail->send()) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }
}
