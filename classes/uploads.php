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
        "application/vnd.ms-office",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-powerpoint",
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
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            var_dump($results);
        }
    }

    function uploadFile($file){
        global $dbc;

        if($file['name'] == ''){
            return false;
        }

        $targetFile = $this->uploadDir . basename($file["name"]);
        $fileName = basename($file["name"]);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        if(in_array($mimeType, $this->mimes)){
            move_uploaded_file($file["tmp_name"], $targetFile);

            $userId = 1;
            $stmt = $dbc->prepare("INSERT INTO `uploads` VALUES (NULL, :userid, 'name', 'description', :target, 0)");
            $stmt->bindParam(":userid", $userId);
            $stmt->bindParam(":target", $targetFile);
            $stmt->execute();

            return true;
        }else{
            return false;
        }
    }

}


 ?>
