<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
    session_start();
    if (!isset($_SESSION['username'])) {
          header('Location: login');
          exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
        header('Location: admins');
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
    
    //Enter staff details in database
    if(isset($_POST['submitStaff'])){
        $name = $_POST['staff-name'];
        $phone = $_POST['staff-phone'];
        $email = $_POST['staff-email'];
        $joinDate = $_POST['joinDate'];
        
    

    if (!isset($_FILES["id-front"])) {
        echo "Upload Staff ID front side";
    } elseif (!isset($_FILES["id-back"])) {
        echo "Upload Staff ID back side";
    } elseif (!isset($_FILES["passport-pic"])) {
        echo "Upload Staff Passport Photo";
    } elseif (!isset($_FILES["contract"])) {
        echo "Upload Staff Passport Photo";
    }
        // Database connection
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
        $target_dir = "fileStore/staff_docs/";
        $target_file1 = $target_dir . basename($_FILES["id-front"]["name"]);
        $target_file2 = $target_dir . basename($_FILES["id-back"]["name"]);
        $target_file3 = $target_dir . basename($_FILES["passport-pic"]["name"]);
        $target_file4 = $target_dir . basename($_FILES["contract"]["name"]);
        $uploadOk = 1;
        $imageFileType1 = strtolower(pathinfo($target_file1,PATHINFO_EXTENSION));
        $imageFileType2 = strtolower(pathinfo($target_file2,PATHINFO_EXTENSION));
        $imageFileType3 = strtolower(pathinfo($target_file3,PATHINFO_EXTENSION));
        $imageFileType3 = strtolower(pathinfo($target_file4,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
            $check1 = getimagesize($_FILES["id-front"]["tmp_name"]);
            $check2 = getimagesize($_FILES["id-back"]["tmp_name"]);
            $check3 = getimagesize($_FILES["passport-pic"]["tmp_name"]);
            $check4 = getimagesize($_FILES["contract"]["tmp_name"]);
            
            if($check1 !== false && $check2 !== false && $check3 !== false && $check4 !== false ) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        
        // Check if file already exists
        if (file_exists($target_file1) && file_exists($target_file2) && file_exists($target_file3) && file_exists($target_file4)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        
        // Check file size
        if ($_FILES["id-front"]["size"] > 500000 && $_FILES["id-back"]["size"] > 500000 && $_FILES["passport-pic"]["size"] > 500000 && $_FILES["contract"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType1 != "jpg" && $imageFileType1 != "png" && $imageFileType1 != "jpeg"
        && $imageFileType1 != "gif" && $imageFileType2 != "jpg" && $imageFileType2 != "png" && $imageFileType2 != "jpeg"
        && $imageFileType2 != "gif" && $imageFileType3 != "jpg" && $imageFileType3 != "png" && $imageFileType3 != "jpeg"
        && $imageFileType3 != "gif" && $imageFileType4 != "jpg" && $imageFileType4 != "png" && $imageFileType4 != "jpeg"
        && $imageFileType4 != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["id-front"]["tmp_name"], $target_file1) && move_uploaded_file($_FILES["id-back"]["tmp_name"], $target_file2) && move_uploaded_file($_FILES["passport-pic"]["tmp_name"], $target_file3) && move_uploaded_file($_FILES["contract"]["tmp_name"], $target_file4)) {
                //echo "The files have been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your files.";
            }
        }
        
        
        $image_path_id_front = $target_dir . basename($_FILES["id-front"]["name"]);
        $image_path_id_back = $target_dir . basename($_FILES["id-back"]["name"]);
        $image_path_passport_pic = $target_dir . basename($_FILES["passport-pic"]["name"]);
        $image_path_contract = $target_dir . basename($_FILES["contract"]["name"]);
        
        $sqlNewStaff = "INSERT INTO staff (staff_name, staff_phone, staff_email, joinDate, ID_front, ID_back, passport_pic, contract) 
        VALUES ('$name', '$phone', '$email','$joinDate', '$image_path_id_front', '$image_path_id_back', '$image_path_passport_pic', '$image_path_contract' )";

        
        if ($conn->query($sqlNewStaff) === TRUE) {
            header("Location: new_staff.php");
            exit();
        } else {
            echo "Error: " . $sqlNewStaff . "<br>" . $conn->error;
            echo "Error: " . $sqlStore . "<br>" . $conn->error;
        }
        
        
        $conn->close();
    }
?>

<!DOCTYPE html>    
<html>
    <h1> Create New Staff</h1>
    <div class="newStaff" style="border:1px solid black; display:flex; padding:4px;" >
        <form style="width: 80%;" method="POST" action="" enctype="multipart/form-data">
            <title> Staff Name: </title>
            <input placeholder="Staff name" type="text" name="staff-name" style="width: 95%; height: 30px;" required>
            <br> 
            <br>
            <title> Staff Phone:</title>
            <input placeholder="Staff Phone Number" type="text" name="staff-phone" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <title> Staff Email:</title>
            <input placeholder="Staff Email" type="email" name="staff-email" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <title> Joined Date:</title>
            <input placeholder="Joined Date" type="date" name="joinDate" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <title> Staff Docs:</title>
            <br>
            Upload ID (Front): <input type="file" name="id-front" id="image" required> 
            <br>
            <br>
            Upload ID (Back): <input type="file" name="id-back" id="image" required> 
            <br>
            <br>
            Staff Passport Photo: <input type="file" name="passport-pic" id="image" required> 
            <br>
            <br>
            Staff Contract: <input type="file" name="contract" id="image" required> 
            <br>
            <br>
            <input type="submit" value="Add New Staff" name="submitStaff" style="width: 100px; height: 30px; padding: 4px;" >
        </form>
    
    <?php include 'templates/sessionTimeoutL.php'; ?>
    
</html>
