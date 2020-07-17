<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 28.07.2019
 * Time: 20:31
 */

namespace framework\helpers;

class Mail
{
    public function __construct($options = [])
    {
    }

    public static function simpleMail($to, $subject, $text)
    {
        $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=utf-8\r\n";
        $headers .= "From: robot@".getenv("HTTP_HOST")."\r\n";




        return mail($to, $subject, $text, $headers);
    }
}