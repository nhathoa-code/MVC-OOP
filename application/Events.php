<?php

use NhatHoa\Framework\Event;

Event::listen("order-email",function($order,$emailService,$subject,$template){
    $emailService->getService()
            ->to($order->email)
            ->subject($subject)
            ->template(VIEW_PATH . "/client/email_template/{$template}.php",array("name" => $order->name,"phone" => $order->phone,"payment_method"=>$order->payment_method,"order_id" => $order->id,"address" => json_decode($order->address,true)))
            ->sendQueue();
});

Event::listen("email",function($emailService,$email,$subject,$message){
    $emailService->getService()
            ->to($email)
            ->subject($subject)
            ->message($message)
            ->sendQueue();
});