<?php

namespace NhatHoa\Framework\Abstract;
use NhatHoa\Framework\Core\Validator;

interface Rule
{
    public function validate(Validator $validator);
}