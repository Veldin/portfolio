<?php
class Uploads
{
    private $dbc;
    private $userid;
    private $uploads;

    function __construct($dbc){
        $this->dbc = $dbc;
    }

    function getUserUploads($userid, $publicOnly = false){
        $this->userid = $userid;

        if($publicOnly){
            $stmt = $this->dbc->prepare("SELECT * FROM uploads WHERE userid = :userid AND public = 1");
        }else{
            $stmt = $this->dbc->prepare("SELECT * FROM uploads WHERE userid = :userid");
        }
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->uploads = $results;
        }

        return $this->uploads;
    }

    function getAllUploads(){
        $sql = "SELECT * FROM uploads";
        foreach($this->dbc->query($sql) as $row) {
            $this->uploads[] = $row;
        }

        return $this->uploads;
    }
}
 ?>
