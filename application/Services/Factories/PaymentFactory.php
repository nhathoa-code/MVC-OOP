<?php

namespace NhatHoa\App\Services\Factories;
use NhatHoa\App\Services\Payments\Vnpay;
use NhatHoa\App\Services\Payments\Cod;

class PaymentFactory
{
    public static function get($type)
    {
        switch($type)
        {
            case "cod":
                return new Cod();
                break;
            case "vnpay":
                return new Vnpay();
                break;   
        }
    }
}