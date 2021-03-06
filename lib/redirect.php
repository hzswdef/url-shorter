<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/lib/DB/MySQL.class.php";

class db_requests extends MySQL
{
    public function __construct()
    {
        parent::__construct();
    }
    
    protected function get_url(string $url)
    {
        return mysqli_fetch_array($this->conn->query("SELECT * FROM `shorten urls` WHERE `url`='$url'"));
    }
}

class redirect extends db_requests
{
    private $point;
    
    public function __construct(string $point)
    {
        parent::__construct();
        $this->point = $point;
    }
    
    private function get_data()
    {
        $data = $this->get_url($this->point);
        //var_dump($data);
        self::close();
        
        return $data;
    }
    
    // redirect to url
    public function forward()
    {
        $data = $this->get_data();
        
        if ($data == null) {
            die(include_once($_SERVER['DOCUMENT_ROOT'] . "/404.php"));
        }
        
        //
        // MUST ADD OTHER PROTOCOLS OR ANOTHER SOLUTION
        //
        if (strpos($data["point"], "https://") !== false) {
            return header("Location: " . $data["point"]);
        }
        else if (strpos($data["point"], "http://") !== false) {
            return header("Location: " . $data["point"]);
        }
        return header("Location: https://" . $data["point"]);
    }
}

// Fetch unique code from requested URL
$point = str_replace("/", "", $_SERVER['REQUEST_URI']);

$redirect = new redirect($point);
$redirect->forward();

?>