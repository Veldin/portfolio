<?php

class User
{
    private $email;
    private $password;
    private $userdata;
    private $dbc;

    function __construct($email, $password, $dbc){
        $this->email = htmlentities($email);
        $this->password = htmlentities($password);
        $this->dbc = $dbc;
    }

    public function login(){
        if($this->auth()){
            $this->getUserData();
        }
    }

    public function register(){
        $password = password_hash($this->password, CRYPT_BLOWFISH);
        $stmt = $this->dbc->prepare("INSERT INTO `user` VALUES (NULL, 1, 0, :email, :password, '', '', '', '', '', NOW())");
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password);
        $stmt->execute();
    }

    public function get(){
        return $this->userdata;
    }

    private function auth(){
        $stmt = $this->dbc->prepare("SELECT id, password FROM user WHERE email = :email");
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $results['password'])){
                $_SESSION['userId'] = $results['id'];
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function getUserData(){
        $stmt = $this->dbc->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindParam(":id", $_SESSION['userId']);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->userdata['levelid'] = $results['levelid'];
        $this->userdata['slb'] = $results['slb'];
        $this->userdata['email'] = $this->email;
        $this->userdata['firstname'] = $results['firstname'];
        $this->userdata['lastname'] = $results['lastname'];
        $this->userdata['phone'] = $results['phone'];
        $this->userdata['zipcode'] = $results['zipcode'];
        $this->userdata['address'] = $results['address'];
    }

}

?>
