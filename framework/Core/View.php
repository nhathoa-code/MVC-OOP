<?php

namespace NhatHoa\Framework\Core;
use NhatHoa\Framework\Registry;

class View {

    protected $_viewPath;
    protected $_data = [];
    protected $_session;

    public function __construct($viewPath) {
        $this->_viewPath = $viewPath;
        $this->_session = Registry::get("session");
    }

    public function render() {
        if($this->_session->has("errors")){
            $errors = $this->_session->get("errors");
            $this->_session->remove("errors");
        }
        if($this->_session->has("old")){
            $old = $this->_session->get("old");
            $this->_session->remove("old");
        }
        if($this->_session->has("message")){
            $message = $this->_session->get("message");
            $this->_session->remove("message");
        }
   
        extract($this->_data); 

        include_once APP_PATH . '/application/Views/' . $this->_viewPath . '.php';
    }

    public function setData(array $data = array()){
        $this->_data = $data;
    }
}