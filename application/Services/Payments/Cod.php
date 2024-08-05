<?php

namespace NhatHoa\App\Services\Payments;

use NhatHoa\App\Services\Interfaces\Payment;

class Cod implements Payment
{
    public function process(string $order_id,float $amount)
    {
        // do nothing...
    }
}