<?php

require_once __DIR__ . '/vendor/autoload.php';

define("APP_PATH", dirname(__FILE__));

$config = new NhatHoa\Framework\Factories\ConfigFactory(array(
    "type" =>"ini"
));

NhatHoa\Framework\Registry::set("config", $config->initialize());

$database = new NhatHoa\Framework\Factories\DatabaseFactory();

NhatHoa\Framework\Registry::set("database", $database->initialize());

use NhatHoa\Framework\Facades\DB;
use NhatHoa\App\Services\EmailService;

$emailService = new EmailService();
$emails = DB::table("email_queue")->whereNull("sent_at")->get();

foreach($emails as $e){
    $success = $emailService->getService()
        ->to($e->to_email)
        ->subject($e->subject)
        ->message($e->message)
        ->send();
    if($success){
        DB::table("email_queue")->where("id",$e->id)->limit(1)->delete();
    }
}