<?php

namespace NhatHoa\Framework\Abstract;
use NhatHoa\Framework\Core\Request;

interface Middleware
{
    public function handle(Request $request);
}