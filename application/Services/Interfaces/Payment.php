<?php

namespace NhatHoa\App\Services\Interfaces;

interface Payment
{
    public function process(string $order_id,float $amount);
}