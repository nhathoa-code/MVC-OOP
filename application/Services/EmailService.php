<?php

namespace NhatHoa\App\Services;
use NhatHoa\Framework\Abstract\Service;
use NhatHoa\Framework\Factories\MailFactory;

class EmailService extends Service
{
    protected $_service;

    public function __construct()
    {
        $mailFactory = new MailFactory();
        $this->_service = $mailFactory->initialize();
    }

    public function getService()
    {
        return $this->_service;
    }
}