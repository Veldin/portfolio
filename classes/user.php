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
    // Error codes:
      // 99 - Velden niet ingevuld
      // 100 - Email is niet correct
      // 101 - Wachtwoorden komen niet overeen
      // 102 - Password voldoet niet aan de eisen
      // 103 - Ingevoerde telefoonnummer is niet numeriek en/of 10 lang
      // 104 - Ingevoerde postcode is niet volgens het 1234AB formaat
      // 105 - Email al in gebruik
      // 106 - Kon het standaard portfolio niet aanmaken.
    public function register($email, $password, $password_repeat, $firstname, $lastname, $phone, $zipcode, $address){
        if(!empty($email) && !empty($password) && !empty($password_repeat) && !empty($firstname) && !empty($lastname) && !empty($phone) && !empty($zipcode) && !empty($address)){
            if(Validate::email($email)){
                if(Validate::passwordRepeat($password, $password_repeat)){
                    $email = stripslashes($email);
                    $password = stripslashes($password);
                    $password_repeat = stripslashes($password_repeat);
                    $firstname = stripslashes($firstname);
                    $lastname = stripslashes($lastname);
                    $phone = stripslashes($phone);
                    $zipcode = stripslashes($zipcode);
                    $address = stripslashes($address);

                    if(Validate::password($password)){
                        if(Validate::phone($phone)){
                            if(Validate::zipcode($zipcode)){
                                $stmt = $this->dbc->prepare("SELECT 1 FROM `user` WHERE email = :email");
                                $stmt->bindParam(":email", $email);
                                $stmt->execute();
                                if($stmt->rowCount() == 0){
                                    $password = password_hash($password, CRYPT_BLOWFISH);
                                    $stmt = $this->dbc->prepare("INSERT INTO `user` VALUES (NULL, 1, 0, :email, :password, :firstname, :lastname, :phone, :zipcode, :address, UNIX_TIMESTAMP())");
                                    $stmt->bindParam(":email", $email);
                                    $stmt->bindParam(":password", $password);
                                    $stmt->bindParam(":firstname", $firstname);
                                    $stmt->bindParam(":lastname", $lastname);
                                    $stmt->bindParam(":phone", $phone);
                                    $stmt->bindParam(":zipcode", $zipcode);
                                    $stmt->bindParam(":address", $address);
                                    $stmt->execute();

                                    if($this->populatePortfolio($this->dbc->lastInsertId(), $firstname, $lastname)){
                                        return true;
                                    }else{
                                        return 106;
                                    }
                                }else{
                                    return 105;
                                }
                            }else{
                                return 104;
                            }
                        }else{
                            return 103;
                        }
                    }else{
                      return 102;
                    }
                }else{
                    return 101;
                }
            }else{
                return 100;
            }
        }else{
            return 99;
        }
    }

    private function populatePortfolio($userId, $firstname, $lastname){
        $url = strtolower(substr($firstname, 0, 1) . $lastname);
        $firstname = substr($firstname, 0, 1);

        $stmt = $this->dbc->prepare("SELECT id FROM user WHERE SUBSTRING(firstname, 1, 1) = :firstname AND lastname = :lastname");
        $stmt->bindParam(":firstname", $firstname);
        $stmt->bindParam(":lastname", $lastname);
        $stmt->execute();
        if($stmt->rowCount() > 1){
            $url .= $stmt->rowCount();
        }

        $stmt = $this->dbc->prepare(
            "INSERT INTO portfolio VALUES (:userid, :url, 0, 'ffffff', '808080', 'D54247');" .
            "INSERT INTO module VALUES (null, :userid, 3, 1, 100, 'Welkom op je eigen portfolio! Dit is een header text.', UNIX_TIMESTAMP());" .
            "INSERT INTO module VALUES (null, :userid, 1, 2, 100, 'Dit is een paragraaf met text.', UNIX_TIMESTAMP())"
        );
        $stmt->bindParam(":userid", $userId);
        $stmt->bindParam(":url", $url);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Returnd de 'credentials' array en wordt voornamelijk gebruik om gebruikerdata te verkrijgen op de volgende manier: get()['email']
    public function get(){
        if(isset($this->userdata) && !empty($this->userdata)){
            return $this->userdata;
        }else{
            return $this->getUserData();
        }
    }

    // Checkt of de 'loggedIn' sessie is geset en of deze true bevat van het type boolean. Zoja, return true en anders return false;
    public function isLoggedIn(){
        return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true ?: false;
    }

    public function updateUserData($id, $array){
        $sql = "UPDATE user SET ";

        $i = 0;
        foreach($array as $set => $value){
            if(++$i !== count($array)){
                $sql .= $set . " = '" . $value . "', ";
            }else{
                $sql .= $set . " = '" . $value . "'";
            }

        }
        $sql .= " WHERE id = :id";
        //echo $sql;

        $stmt = $this->dbc->prepare($sql);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()){
            return true;
        }
        return false;
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

        $this->userdata['id'] = $results['id'];
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

class Validate
{

    public function email($email){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    public function passwordRepeat($password, $password_repeat){
        if($password === $password_repeat){
            return true;
        }
        return false;
    }

    public function password($password){
        if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $password)){
            return true;
        }
        return false;
    }

    public function phone($phone){
        if(is_numeric($phone) && (strlen($phone) === 10)){
            return true;
        }
        return false;
    }

    public function zipcode($zipcode){
        if(preg_match('/^[0-9]{4}[A-Z]{2}$/', $zipcode)){
            return true;
        }
        return false;
    }

}

?>
