<?php

namespace NhatHoa\Framework;

class Test extends Base
{
    private static $_tests = array();

    public static function add($callback, $title = "Unnamed Test", $set = "General")
    {
        self::$_tests[] = array(
            "set" => $set,
            "title" => $title,
            "callback" => $callback
        );
    }
    public static function run($before = null, $after = null)
    {
        if ($before){
            $before(self::$_tests);
        }
        $passed = array();
        $failed = array();
        $exceptions = array();
        foreach (self::$_tests as $test){
            echo "running test - {$test["title"]}\n";
            try{
                $result = call_user_func($test["callback"]);
                if ($result){
                    $passed[] = array(
                        "set" => $test["set"],
                        "title" => $test["title"]
                    );
                    echo "passed \n";
                }else{
                    $failed[] = array(
                        "set" => $test["set"],
                        "title" => $test["title"]
                    );
                    echo "failed \n";
                }
            }catch (\Exception $e){
                $exceptions[] = array(
                    "set" => $test["set"],
                    "title" => $test["title"],
                    "type" => get_class($e)
                );
                echo "error";
            }
        }
        if ($after){
            $after(self::$_tests);
        }
        return array(
            "passed" => $passed,
            "failed" => $failed,
            "exceptions" => $exceptions
        );
    }
}
