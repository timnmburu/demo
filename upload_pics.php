<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
        
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
?>

<!DOCTYPE html>    
<html>
    <h3>Upload New Images to Website</h3>

    <form action="" method="post" enctype="multipart/form-data">
      Select image to upload:
      <input type="file" name="image" id="image" >
      <br>
      <br>
      <input type="submit" value="Upload Image" name="submitImage" style="border:2px solid grey; width:130px; height:30px;">
    </form>
    
    
</html>

<?php
    if (isset($_FILES["image"])) {
        
        $target_dir = "fileStore/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        if(isset($_POST["submitImage"])) {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check !== false) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        
        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["image"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        
        
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        $date = date('Y-m-d', strtotime('+3 hours'));
        
        $sqlStore = "INSERT INTO images (image_path, time) VALUES ('$image_path', '$date')";
        if ($conn->query($sqlStore) === TRUE) {
            //echo "New record created successfully";
        } else {
            echo "Error: " . $sqlStore . "<br>" . $conn->error;
        }
        
        $conn->close();
    }
?>