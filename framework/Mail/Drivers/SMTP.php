<?php

namespace NhatHoa\Framework\Mail\Drivers;
use NhatHoa\Framework\Mail\Driver;
use NhatHoa\Framework\Registry;
use PHPMailer\PHPMailer\PHPMailer;

require_once APP_PATH . "/vendor/PHPMailer/PHPMailer/src/Exception.php";
require_once APP_PATH . "/vendor/PHPMailer/PHPMailer/src/PHPMailer.php";
require_once APP_PATH . "/vendor/PHPMailer/PHPMailer/src/SMTP.php";

class SMTP extends Driver{

    /**
    * @readwrite
    */
    protected $_host;
    /**
    * @readwrite
    */
    protected $_smtp;
    /**
    * @readwrite
    */
    protected $_username;
    /**
    * @readwrite
    */
    protected $_password;
    /**
    * @readwrite
    */
    protected $_fromEmail;
    /**
    * @readwrite
    */
    protected $_fromName;
    protected $_from;
    protected $_to;
    protected $_subject;
    protected $_message;
    protected $_mail_obj;
    protected $_template_data = array();

    protected $_query;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->_query = Registry::get("database")->query();
    }

    public function getMailObj()
    {
        if(!$this->_mail_obj){
            $this->_mail_obj = new PHPMailer(true);
        }
        return $this->_mail_obj;
    }

    public function send()
    {
        $mail = $this->getMailObj();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        try {
            $mail->isSMTP(); 
            $mail->Host = $this->_host; 
            $mail->SMTPAuth = true; 
            $mail->Username = $this->_username; 
            $mail->Password = $this->_password;
            $mail->SMTPSecure = $this->_smtp; 
            $mail->Port = 587; 
            $mail->setFrom($this->_fromEmail, $this->_fromName);
            $mail->addAddress($this->_to); 
            $mail->isHTML(true); 
            $mail->Subject = $this->_subject;
            $mail->Body = $this->_message;
            $mail->send();
            return true;
        } catch (\Exception $e) {
            echo 'Error sending email: ', $e->getMessage();
            return false;
        }
    }

    public function sendQueue()
    {
        $this->_query->from("email_queue")->insert([
            "to_email" => $this->_to,
            "subject" => $this->_subject,
            "message" => $this->_message
        ]);
    }

    public function to($to)
    {
        $this->_to = $to;
        return $this;
    }

    public function from($from)
    {
        $this->_from = $from;
        return $this;
    }

    public function subject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    public function message($message)
    {
        $this->_message = $message;
        return $this;
    }

    public function template(string $path,array $data)
    {
        extract($data);
        ob_start();
        include $path;
        $templateContent = ob_get_clean();
        $this->_message = $templateContent;
        return $this;
    }

    public function getMessage()
    {
        return $this->_message;
    }
}