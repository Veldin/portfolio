<?php

class User
{

    private $userId;
    private $email;
    private $password;
    private $credentials;
    private $dbc;

    function __construct($email, $password, $dbc){
        $this->email = htmlentities($email);
        $this->password = htmlentities($password);
        $this->dbc = $dbc;
    }

    public function login(){
        if($this->auth()){
            $this->getCredentials();
            print_r($this->credentials);
        }
    }

    public function register(){
        if(CRYPT_BLOWFISH == 1){
            $password = crypt($this->password);
        }
        $stmt = $this->dbc->prepare("INSERT INTO `user` VALUES (NULL, 1, 0, :email, :password, '', '', '', '', '', NOW())");
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password);
        $stmt->execute();
    }

    private function auth(){
        $stmt = $this->dbc->prepare("SELECT id, password FROM user WHERE email = :email");
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            if(hash_equals($results['password'], crypt($this->password, $results['password']))){
                $this->userId = $results['id'];
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function getCredentials(){
        $stmt = $this->dbc->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindParam(":id", $this->userId);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->credentials['levelid'] = $results['levelid'];
        $this->credentials['slb'] = $results['slb'];
        $this->credentials['firstname'] = $results['firstname'];
        $this->credentials['lastname'] = $results['lastname'];
        $this->credentials['phone'] = $results['phone'];
        $this->credentials['zipcode'] = $results['zipcode'];
        $this->credentials['address'] = $results['address'];
    }

}

?>
