<?php
declare(strict_types=1);

namespace App\Helper;

use App\Config\Env;

class Mailer {

    /**
     * Throws exceptions --. try/catch
     */
    public function mail(
        string $to , 
        string $subject, 
        string $message, 
        $additional_headers='',
        string $additional_parameters=''
    ) {
        /*
        if (! mail($to, $subject, $message, $additional_headers, $additional_parameters)) {
            throw new \Exeception('Email refused for delivery.');
        }
        */
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("cloudprj@cloud.com", "cloudprj");
        $email->setSubject($subject);
        $email->addTo($to, "Example User");
  //      $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent("text/html", $message);
        $sendgrid = new \SendGrid(Env::get('AZURE_SENDGRID_KEY'));
   //     try {
            $response = $sendgrid->send($email);
            print $response->statusCode();
            print_r($response->headers());
            print $response->body();
            exit;
/*
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
*/
    }
}