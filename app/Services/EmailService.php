<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    protected $app_name;
    protected $host;
    protected $port;
    protected $username;
    protected $password;




    function __construct()
    {
        $this->app_name = config('app.name');
        $this->host = config('app.mail_host');
        $this->port = config('app.mail_port');
        $this->username = config('app.mail_username');
        $this->password = config('app.mail_password');
    }

    public function sendEmail($subject, $emailUser, $nameUser, $isHtml, $activation_code, $activation_token)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        $mail->SMTPAuth = true;
        $mail->Subject = $subject;
        $mail->setFrom($this->app_name, $this->app_name);
        $mail->addReplyTo($this->app_name, $this->app_name);
        $mail->addAddress($emailUser, $nameUser);
        $mail->isHTML($isHtml);
        $mail->Body = $this->viewSendEmail($nameUser, $activation_code, $activation_token);
        $mail->send();

    }

    public function resetPassword($subject, $emailUser, $nameUser, $isHtml, $activation_token)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        $mail->SMTPAuth = true;
        $mail->Subject = $subject;
        $mail->setFrom($this->app_name, $this->app_name);
        $mail->addReplyTo($this->app_name, $this->app_name);
        $mail->addAddress($emailUser, $nameUser);
        $mail->isHTML($isHtml);
        $mail->Body = $this->viewResetpassword($nameUser, $activation_token);
        $mail->send();
    }

    public function viewSendEmail($name, $activation_code, $activation_token)
    {
        return view('mail.configuration_email')
                                ->with([
                                    'name' => $name,
                                    'activation_code' => $activation_code,
                                    'activation_token' => $activation_token
                                ]);
    }

    public function viewResetpassword($name, $activation_token)
    {
        return view('mail.reset_password')
                                ->with([
                                    'name' => $name,
                                    'activation_token' => $activation_token
                                ]);
    }
}
