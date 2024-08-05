<?php

namespace NhatHoa\Framework\Abstract;

abstract class Migration
{
    abstract public function up();

    abstract public function down();
}