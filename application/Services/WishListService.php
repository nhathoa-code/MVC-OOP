<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\Framework\Registry;
use NhatHoa\Framework\Facades\Auth;

class WishListService extends Service
{
    protected $_connector;
    protected $_query;

    public function __construct()
    {
        $this->_connector = Registry::get("database");
        $this->_query = $this->_connector->query();
    }

    public function add($p_id)
    {
        $record = $this->_query->from("wish_list")
                        ->where("user_id",Auth::user()->id)
                        ->where("p_id",$p_id)
                        ->first();
        if(!$record){
            $this->_query->from("wish_list")->insert([
                "user_id" => Auth::user()->id,
                "p_id" => $p_id,
            ]);
        }
    }

    public function remove($p_id)
    {
        $this->_query->from("wish_list")
                    ->where("user_id",Auth::user()->id)
                    ->where("p_id",$p_id)
                    ->limit(1)
                    ->delete();
    }

}