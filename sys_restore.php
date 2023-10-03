<?php

    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Retrieve the list of tables in the database
    $tables = array('signup', 'sentSMS');
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    // Generate the SQL content for each table
    $sql_data = '';
    
    foreach ($tables as $table) {
        if ($table === 'signup' || $table === 'sentSMS') {
            $sql_data = ''; // Initialize SQL data for the specific table
    
            $result = $conn->query("SELECT * FROM $table");
            $sql_data .= "DROP TABLE IF EXISTS $table;\n";
            $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
            $sql_data .= $row2[1] . ";\n";
    
            while ($row = $result->fetch_assoc()) {
                $row = array_map('addslashes', $row);
                $sql_data .= "INSERT INTO $table VALUES ('" . implode("','", $row) . "');\n";
            }
    
            $temp_file_path = 'fileStore/regular_backups/' . $table .'.sql'; // Removed unique identifier
            file_put_contents($temp_file_path, $sql_data);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $backupFilePath = 'fileStore/regular_backups/backup.sql';
    $signupBackup = 'fileStore/regular_backups/signup.sql';
    $sentSMSBackup = 'fileStore/regular_backups/sentSMS.sql';
    
    
    if (file_exists($backupFilePath)) {
        // Drop all existing tables
        $dropAllTablesQuery = "SHOW TABLES";
        $existingTables = $conn->query($dropAllTablesQuery);
    
        if ($existingTables) {
            while ($row = $existingTables->fetch_row()) {
                $tableName = $row[0];
                $dropTableQuery = "DROP TABLE IF EXISTS $tableName";
                $conn->query($dropTableQuery);
            }
        } else {
            echo "Error fetching existing tables: " . $conn->error;
        }
    
        // Read and execute the SQL backup file
        $sqlContent = file_get_contents($backupFilePath);
        $queries = explode(';', $sqlContent);
    
        foreach ($queries as $query) {
            if (trim($query) !== '') {
                $result = $conn->query($query);
    
                if (!$result) {
                    echo "Error executing query: " . $conn->error;
                }
            }
        }
        
        // Read and execute the SQL backup file for the Signup Table
        $sqlContent = file_get_contents($signupBackup);
        $queries = explode(';', $sqlContent);
    
        foreach ($queries as $query) {
            if (trim($query) !== '') {
                $result = $conn->query($query);
    
                if (!$result) {
                    echo "Error executing query: " . $conn->error;
                }
            }
        }
        
        // Read and execute the SQL backup file for the sentSMS Table
        $sqlContent = file_get_contents($sentSMSBackup);
        $queries = explode(';', $sqlContent);
    
        foreach ($queries as $query) {
            if (trim($query) !== '') {
                $result = $conn->query($query);
    
                if (!$result) {
                    echo "Error executing query: " . $conn->error;
                }
            }
        }
    
        echo "Database restored successfully!";
    } else {
        echo "Backup file not found.";
    }

?>