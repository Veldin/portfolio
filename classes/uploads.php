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
    private $fileIcons = array(
        "docx" => "<i class='fa fa-file-word-o' aria-hidden='true'></i>",
        "zip" => "<i class='fa fa-file-archive-o' aria-hidden='true'></i>",
        "gz" => "<i class='fa fa-file-archive-o' aria-hidden='true'></i>",
        "ppt" => "<i class='fa fa-file-powerpoint-o' aria-hidden='true'></i>",
        "pdf" => "<i class='fa fa-file-pdf-o' aria-hidden='true'></i>",
        "xlsx" => "<i class='fa fa-file-excel-o' aria-hidden='true'></i>",
        "gif" => "<i class='fa fa-file-image-o' aria-hidden='true'></i>",
        "ico" => "<i class='fa fa-file-image-o' aria-hidden='true'></i>",
        "png" => "<i class='fa fa-file-image-o' aria-hidden='true'></i>",
        "jpg" => "<i class='fa fa-file-image-o' aria-hidden='true'></i>",
        "mp3" => "<i class='fa fa-file-audio-o' aria-hidden='true'></i>",
        "mpeg" => "<i class='fa fa-file-audio-o' aria-hidden='true'></i>"
    );
    function getUserUploads($userid, $publicOnly = false){
        global $dbc;
        if($publicOnly){
            $stmt = $dbc->prepare("SELECT * FROM uploads WHERE userid = :userid AND public = :public ORDER BY id DESC");
            $stmt->bindParam(":public", $publicOnly);
        }else{
            $stmt = $dbc->prepare("SELECT * FROM uploads WHERE userid = :userid ORDER BY id DESC");
        }
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($results as &$result){
                $result['extension'] = pathinfo($result['url'], PATHINFO_EXTENSION);
                $result['fileicon'] = $this->fileIcons[pathinfo($result['url'], PATHINFO_EXTENSION)];
            }
            return $results;
        }else{
            return false;
        }
    }
    function getUserUploadsIds($userid){
        global $dbc;
        $stmt = $dbc->prepare("SELECT id FROM uploads WHERE userid = :userid");
        $stmt->bindParam(":userid", $userid);
        if($stmt->execute()){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($results as $result){
                $uploadIds[] = $result['id'];
            }
            return $uploadIds;
        }
    }
    function getFileLocationById($id){
        global $dbc;
        $stmt = $dbc->prepare("SELECT url FROM uploads WHERE id = :id");
        $stmt->bindParam(":id", $id);
        if($stmt->execute()){
            return $stmt->fetch(PDO::FETCH_ASSOC)['url'];
        }
    }
    function uploadFile($file, $name, $description){
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
            if(!file_exists($targetFile)){
                move_uploaded_file($file["tmp_name"], $targetFile);
                $userId = 1;
                $stmt = $dbc->prepare("INSERT INTO `uploads` VALUES (NULL, :userid, :name, :description, :target, 0)");
                $stmt->bindParam(":userid", $userId);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":target", $targetFile);
                $stmt->execute();
                return "OK";
            }else{
              return "FILE_EXISTS";
            }
        }else{
            return "FILE_NOT_ALLOWED";
        }
    }
    function updateFile($records){
        global $dbc;
        $sql = "";
        foreach($records as $key => $value){
            $sql .= "UPDATE uploads SET public = $value WHERE id = $key;";
        }
        $stmt = $dbc->prepare($sql);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    function hasRemovePermission($id){
        global $dbc;
        $userId = 1;
        $stmt = $dbc->prepare("SELECT id FROM uploads WHERE id = :id AND userid = :userid");
        $stmt->bindParam(":id", $id);
        //$stmt->bindParam(":userid", $_SESSION['userid']);
        $stmt->bindParam(":userid", $userId);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }
    function deleteFile($id){
        global $dbc;
        $stmt = $dbc->prepare("DELETE FROM uploads WHERE id = :id");
        $stmt->bindParam(":id", $id);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }
}
 ?>