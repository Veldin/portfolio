<?php

class Uploads
{

    private $uploadDir = "uploads/";
    private $mimes = [
        "application/postscript",
        "application/msword",
        "application/x-compressed",
        "application/x-zip-compressed",
        "application/x-gzip",
        "application/zip",
        "multipart/x-gzip",
        "application/mspowerpoint",
        "application/mspowerpoint",
        "application/pdf",
        "image/jpeg",
        "image/pjpeg",
        "image/x-jps",
        "image/vasa",
        "image/bmp",
        "image/x-icon",
        "image/x-windows-bmp",
        "image/pict",
        "image/png",
        "image/gif",
        "image/x-xpixmap",
        "image/x-quicktime",
        "audio/mpeg",
        "audio/x-mpeg",
        "audio/mpeg3",
        "audio/x-mpeg-3"
    ];

    function getUserUploads($userid, $publicOnly = false){
        global $dbc;

        $stmt = $dbc->prepare("SELECT * FROM uploads WHERE userid = :userid AND public = :public");
        $stmt->bindParam(":userid", $userid);
        $stmt->bindParam(":public", $publicOnly);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($results);
        }
    }

    function uploadFile($file){
        $targetFile = $this->uploadDir . basename($file["name"]);
        $fileName = basename($file["name"]);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        echo finfo_file();
    }

}

if(isset($_POST["upload"])){
    $uploads = new Uploads;
    $uploads->uploadFile($_FILES["fileToUpload"]);
}


 ?>
