<?php

class User
{

    private $userId;
    private $email;
    private $password;
    private $dbc;

    function __construct($email, $password, $dbc){
        $this->email = htmlentities($email);
        $this->password = htmlentities($password);
        $this->dbc = $dbc;
    }

    public function login(){
        if($this->auth()){

        }
    }

    public function register($email, $password){
        $stmt = $this->dbc->prepare("INSERT INTO user (NULL, :levelid, :slb, :email, :password, :firstname, :lastname, :phone, :zipcode, :address, :timestamp)");
        // Do some shit
    }

    private function auth(){
        $stmt = $this->dbc->prepare("SELECT id FROM user WHERE email = :email");
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $user = $stmt->fetchAll();

        }else{
            return false;
        }
    }

}

?>
