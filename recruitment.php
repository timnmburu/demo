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
    if(isset($_POST['submitRecruit'])){
        $name = $_POST['staff-name'];
        $phone = $_POST['staff-phone'];
        $email = $_POST['staff-email'];
        $joinDate = $_POST['joinDate'];
        $skills = $_POST['skills'];
        
    

    if (isset($_POST["cv"])) {
        
        $target_dir = "fileStore/staff_docs/";
        $target_file1 = $target_dir . basename($_FILES["cv"]["name"]);

        $uploadOk = 1;
        $imageFileType1 = strtolower(pathinfo($target_file1,PATHINFO_EXTENSION));

        
        // Check if image file is a actual image or fake image
            $check1 = getimagesize($_FILES["cv"]["tmp_name"]);
            
            if($check1 !== false ) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        
        // Check if file already exists
        if (file_exists($target_file1) ) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        
        // Check file size
        if ($_FILES["cv"]["size"] > 500000 ) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType1 != "jpg" && $imageFileType1 != "png" && $imageFileType1 != "jpeg"
        && $imageFileType1 != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file1)) {
                //echo "The files have been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your files.";
            }
        }
    
        
        $image_path_cv = $target_dir . basename($_FILES["cv"]["name"]);
    } else {
        $image_path_cv = "N/A";
    }
        
        $sqlNewStaff = "INSERT INTO recruit (staff_name, staff_phone, staff_email, joinDate, skills, cv) 
        VALUES ('$name', '$phone', '$email','$joinDate','$skills', '$image_path_cv')";

        
        if ($conn->query($sqlNewStaff) === TRUE) {
            header("Location: admins.php");
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
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            th, td {
                text-align: left;
                padding: 8px;
                border: 2px solid #ddd;
            }
            
            th {
                background-color: #f2f2f2;
            }
        </style>
    
    
    <title> New Recruits</title>
    
    <h1> Create New Recruit</h1>
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
            <input placeholder="Staff Email" type="email" name="staff-email" style="width: 95%; height: 30px;" >
            <br>
            <br>
            <title> Interview Date:</title>
            <input placeholder="Interview Date" type="date" name="joinDate" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <title> Skills Demonstrated:</title>
            <input placeholder="Skills demonstrated include:" type="text" name="skills" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <title> Staff Docs:</title>
            <br>
            Upload CV (if available): <input type="file" name="cv" id="image" > 
            <br>
            <br>
            
            <input type="submit" value="Add to Database" name="submitRecruit" style="width: 120px; height: 30px; padding: 4px;" >
        </form>
    </div>
    

    <br>
    <br>
    
    <b>RECRUITMENT TABLE</b>
    
    <table id="recruits">
        <thead>
            <tr>
                <th>Staff No.</th>
                <th>Staff Name</th>
                <th>Staff Phone</th>
                <th>Staff Email</th>
                <th>Joined Date</th>
                <th>Skills</th>
                <th>CV</th>
            </tr>
        </thead>
        <tbody>
            <?php    
                $sqlStaffTable = "SELECT staff_no, staff_name, staff_phone, staff_email, joinDate, skills, cv FROM recruit ORDER BY joinDate DESC";
                $result = $conn->query($sqlStaffTable);
        
                // Loop through the table data and generate HTML code for each row
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["staff_no"] . "</td><td>" . $row["staff_name"] . "</td><td>". $row["staff_phone"] . "</td><td>". $row["staff_email"] . "</td><td>". $row["joinDate"] . "</td><td>". $row["skills"] . "</td><td>" . "<a href='" . $row["cv"] . "' download>Download</a></td>";
                                        
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No results found.</td></tr>";
                }
                
                $conn->close();
            ?>
                        
        </tbody>
    </table>
            
            <br>
        <?php include 'templates/sessionTimeoutL.php'; ?>
</html>
