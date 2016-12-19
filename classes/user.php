<?php

class User
{
    // Enkele private variabelen waar alleen de user class toegang tot heeft.
    private $email;
    private $password;
    private $userdata;
    private $dbc;

    // Deze functie wordt aangeroepen wanneer er een instantie van de class wordt aangemaakt. Hier in worden de email, wachtwoord en de database connectie meegegeven.
    function __construct($dbc){
        $this->dbc = $dbc;
    }

    // Deze functie doet op zichzelf vrij weinig behalve de 'loggedIn' sessie naar true zetten wanneer de gebruiker succesvol is geauthenticeerd.
    public function login($email, $password){
        $this->email = htmlentities($email);
        $this->password = htmlentities($password);

        if($this->auth()){
            $this->getUserData();
            $_SESSION['loggedIn'] = true;
            return true;
        }else{
            return false;
        }
    }

    // Unset of destroy de sessie variabelen zodat de gebruiker niet meer als ingelogd wordt beschouwd.
    public function logout(){
        //session_destroy();
        unset($_SESSION['userId']);
        unset($_SESSION['loggedIn']);
    }

    // Deze function voert de query uit om de gebruiker te registreren in de database. Deze functie controleerd alleen voor bestaande email adressen!
    public function register(){
        $stmt = $this->dbc->prepare("SELECT 1 FROM `user` WHERE email = :email");
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        if($stmt->rowCount() == 0){
            $password = password_hash($this->password, CRYPT_BLOWFISH);
            $stmt = $this->dbc->prepare("INSERT INTO `user` VALUES (NULL, 1, 0, :email, :password, '', '', '', '', '', NOW())");
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $password);
            $stmt->execute();
            return true;
        }else{
            return false;
        }
    }

    // Returnd de 'credentials' array en wordt voornamelijk gebruik om gebruikerdata te verkrijgen op de volgende manier: get()['email']
    public function get(){
        if(isset($this->userdata) && !empty($this->userdata)){
          return $this->userdata;
        }else{
            $this->getUserData();
        }
    }

    // Checkt of de 'loggedIn' sessie is geset en of deze true bevat van het type boolean. Zoja, return true en anders return false;
    public function isLoggedIn(){
        return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true ?: false;
    }

    public function updateUserData($array){

    }

    // Deze functie haalt de userId en wachtwoord op aan de hand van de email die is ingevuld door de gebruiker. Wanneer er een resultaat uit de query komt worden de gehashte wachtwoorden
    // vergeleken.
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

    // Een select all query voor wanneer de gebruiker geauthenticeerd is. In deze functie worden alle gebruikekrsgegevens opgeslagen in een PHP array.
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
