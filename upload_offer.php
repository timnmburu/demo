<?php    
    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    session_start();
    if (!isset($_SESSION['username'])) {
          header('Location: login');
          exit;
    }
    

    
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
    <h3>Upload New Offer to Website</h3>

    <form action="" method="post" enctype="multipart/form-data">
        Offer Name:
        <input type="text" name="offer-name" >
        <br>
        <br>
        
        Select Offer Poster (Images only):
        <input type="file" name="image" id="image" >
        <br>
        <br>
        Offer Start Date:
        <input type="date" name="offer-start-date" >
        <br>
        <br>
        Offer End Date:
        <input type="date" name="offer-end-date" >
        <br>
        <br>
        <input type="submit" value="Upload Offer" name="submitOffer" style="border:2px solid grey; width:130px; height:30px;">
    </form>
    
    
</html>

<?php
    if (isset($_FILES["image"])) {
        

        $target_dir = "fileStore/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        if(isset($_POST["submitOffer"])) {
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
        
        $offerName = $_POST['offer-name'];
        $startDate = $_POST['offer-start-date'];
        $endDate = $_POST['offer-end-date'];
        
        $sqlStore = "INSERT INTO offers (offer_name, offer_image_poster, start_date, end_date) VALUES ('$offerName', '$image_path', '$startDate', '$endDate')";
        if ($conn->query($sqlStore) === TRUE) {
            //echo "New record created successfully";
        } else {
            echo "Error: " . $sqlStore . "<br>" . $conn->error;
        }
        
        $conn->close();
    }
?>