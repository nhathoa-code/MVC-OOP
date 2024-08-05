<?php

use NhatHoa\Framework\Test;

Test::add(function(){
    return true;
});

Test::add(function(){
    return 1 === 1;
});

return Test::run();