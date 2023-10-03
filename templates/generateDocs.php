<?php
    require_once __DIR__ .'/../fpdf/fpdf.php';
    require_once __DIR__ .'/../FPDI/src/autoload.php';
    require_once __DIR__ .'/../vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();
    
    function generateInvoice($selectedBookingCode) {
        $date = date('d-m-Y');
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sqlBooking = "SELECT * FROM bookings WHERE bookingID = '$selectedBookingCode'";
        $result = $conn->query($sqlBooking);
    
        if ($result->num_rows > 0) {
            $bookingDetails = $result->fetch_assoc();
        
            $name = $bookingDetails['name'];
            $bookingID = $selectedBookingCode;
            $phone = $bookingDetails['phone'];
            $bookedDate = $bookingDetails['dateBooked'];
            $quote = $bookingDetails['quote'];
            $services = $bookingDetails['services'];
            $description = $bookingDetails['description'];
            $amountDue = $bookingDetails['amountDue'];
            $balanceDue = $bookingDetails['balanceDue'];
            $confirmation = $bookingDetails['confirmation'];
            $status = $bookingDetails['status'];
            
            //Check deposit payment
            $depositPaid0 = $bookingDetails['depositPaid'];
            $depositCode0 = $bookingDetails['depositCode'];
            
            if($depositPaid0 === '0'){
                $depositPaid = "Not yet paid";
                $depositCode = "Not yet paid";
            } else {
                $depositPaid = $bookingDetails['depositPaid'];
                $depositCode = $bookingDetails['depositCode'];
            }
            
        } else {
            die("Invoice not found.");
        }      
        
        // initiate FPDI
        $pdf = new \setasign\Fpdi\Fpdi();
    
        // Create a new PDF document
        $pdf->AddPage('P', [210, 297]);
        //$pdf->AddPage();
        $pdf->SetMargins(25.4, 25.4, 25.4);
        
        $watermark = 'fileStore/letterhead.pdf';
        $pdf->setSourceFile($watermark);
        $tplIdx = $pdf->importPage(1);
        
        // Get the dimensions of the watermark image
        list($watermarkWidth, $watermarkHeight) = getimagesize($watermark);
        
        // Calculate the position to place the watermark at the center of the page
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        $centerX = ($pageWidth - $watermarkWidth) /2;
        $centerY = ($pageHeight - $watermarkHeight) /2;
        
        $pdf->useTemplate($tplIdx,$watermarkWidth, $watermarkHeight , $watermarkWidth, $watermarkHeight );
        
        //Add Header & Logo
        //$pdf->Image($imgSrc , 4, 8, -200, -200, 'JPEG');
        $pdf->Cell(20);
        //$pdf->Cell(40, 10, 'Excel Tech Essentials');
        $pdf->Ln(10);
        $pdf->Cell(20);
        //$pdf->SetFont('Arial', 'BI', 12);
        //$pdf->Cell(40, 10, 'Innovation & Excellence');

        $pdf->Ln(8);
        
        // Agreement Title
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'INVOICE', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(90);
        $pdf->Cell(20, 10, 'Printed On:' . $date, 10);
        $pdf->Ln(10);
        
        // Client Details
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(45, 10, 'Incoice No: INV'.$bookingID, 0);
        $pdf->Ln(12);
        
        $pdf->Cell(45, 10, 'Client Details:', 0);
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(45, 10, 'Name:', 0);
        $pdf->Cell(60, 10, $name, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Phone No.:', 0);
        $pdf->Cell(60, 10, $phone, 0);
        $pdf->Ln(12);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(45, 10, 'Invoice Details:', 0);
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(45, 10, 'Services:', 0);
        $pdf->Cell(60, 10, $services , 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Description:', 0);
        $pdf->Cell(60, 10, $description , 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Appointment date:', 0);
        $pdf->Cell(60, 10, $bookedDate, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Quotation No.:', 0);
        $pdf->Cell(60, 10, $quote, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Total Amount Due:', 0);
        $pdf->Cell(60, 10, $amountDue , 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Deposit paid:', 0);
        $pdf->Cell(60, 10, $depositPaid , 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Payment Code:', 0);
        $pdf->Cell(60, 10, $depositCode, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Balance:', 0);
        $pdf->Cell(60, 10, $balanceDue, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Confirmation:', 0);
        $pdf->Cell(60, 10, $confirmation, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Current Status:', 0);
        $pdf->Cell(60, 10, $status, 0);
        $pdf->Ln(50);
        //$pdf->Line(10, $pdf->GetY() + 0, $pdf->GetPageWidth() - 10, $pdf->GetY() + 0);
        
        // Signatures    
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 8, 'In case of any irregularities, please contact us first before paying.', 0);
        
        //Save the file
        $path = 'fileStore/booking_invoices/';
        $filePath = __DIR__. '/../' . $path;
        $fileName = 'INV_' . $selectedBookingCode . '.pdf';
        
        // Output the PDF to the browser or save it to a file
        //$pdf->Output('I', $filePath . $fileName); //output to browser
        $pdf->Output('F', $filePath . $fileName); //save the file
        
        $invoiceLink = $path . $fileName;
    
        $sqlUpdateInvoiceLink = "UPDATE bookings SET invoiceLink = '$invoiceLink' WHERE bookingID = '$selectedBookingCode'";
        $conn->query($sqlUpdateInvoiceLink);
        
        $conn->close();
        
    }
    
    function generateContract($selectedBookingCode) {
        $date = date('d-m-Y');
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sqlBooking = "SELECT * FROM bookings WHERE bookingID = '$selectedBookingCode'";
        $result = $conn->query($sqlBooking);
    
        if ($result->num_rows > 0) {
            $bookingDetails = $result->fetch_assoc();
        
            $dateRequested = $bookingDetails['dateRequested'];
            $bookingID = $selectedBookingCode;
            $name = $bookingDetails['name'];
            $phone = $bookingDetails['phone'];
            $services = $bookingDetails['services'];
            $description = $bookingDetails['description'];
            $dateBooked = $bookingDetails['dateBooked'];
            $quote = $bookingDetails['quote'];
            $amountDue = $bookingDetails['amountDue'];
            $balanceDue = $bookingDetails['balanceDue'];
            $confirmation = $bookingDetails['confirmation'];
            $status = $bookingDetails['status'];
            
            //Check deposit payment
            $depositPaid0 = $bookingDetails['depositPaid'];
            $depositCode0 = $bookingDetails['depositCode'];
            
            if($depositPaid0 === '0'){
                $depositPaid = "Not yet paid";
                $depositCode = "Not yet paid";
            } else {
                $depositPaid = $bookingDetails['depositPaid'];
                $depositCode = $bookingDetails['depositCode'];
            }
            
        } else {
            die("Invoice not found.");
        }        
        
        // initiate FPDI
        $pdf = new \setasign\Fpdi\Fpdi();
    
        // Create a new PDF document
        //$pdf = new FPDF('P', 'mm', array(210, 297));
        $pdf->AddPage('P', [210, 297]);
        //$pdf->AddPage();
        $pdf->SetMargins(22.7, 25.4, 25.4);
        
        $watermark = 'fileStore/letterhead.pdf';
        $pdf->setSourceFile($watermark);
        $tplIdx = $pdf->importPage(1);
        //$pdf->useTemplate($tplIdx, 10, 10, 100);
        
        // Get the dimensions of the watermark image
        list($watermarkWidth, $watermarkHeight) = getimagesize($watermark);
        
        // Calculate the position to place the watermark at the center of the page
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        $centerX = ($pageWidth - $watermarkWidth) / 2;
        $centerY = ($pageHeight - $watermarkHeight) / 2;
        
        $pdf->useTemplate($tplIdx, $watermarkWidth, $watermarkHeight, $watermarkWidth,$watermarkHeight );
        
          //Add Header & Logo
        //$pdf->Image($imgSrc , 4, 8, -200, -200, 'JPEG');
        $pdf->Cell(20);
        //$pdf->Cell(40, 10, 'Excel Tech Essentials');
        $pdf->Ln(10);
        $pdf->Cell(20);
        //$pdf->SetFont('Arial', 'BI', 12);
        //$pdf->Cell(40, 10, 'Innovation & Excellence');

    
        //$pdf->Line(10, $pdf->GetY() + 12, $pdf->GetPageWidth() - 10, $pdf->GetY() + 12);
        $pdf->Ln(8);
        
        // Agreement Title
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'CONTRACTUAL AGREEMENT', 0, 0, 'C');
        $pdf->Cell(-30);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 10, 'Printed On:' . $date, 10);
        $pdf->Ln(10);
        
        // Client Details
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(45, 10, 'Client Details:', 0);
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(45, 10, 'Name:', 0);
        $pdf->Cell(45, 10, $name, 0);
        $pdf->Cell(45, 10, 'Booking ID:', 0);
        $pdf->Cell(45, 10, $bookingID, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Phone No.:', 0);
        $pdf->Cell(45, 10, $phone, 0);
        $pdf->Cell(45, 10, 'Due date:', 0);
        $pdf->Cell(40, 10, $dateBooked, 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Services:', 0);
        $pdf->Cell(45, 10, $services , 0);
        $pdf->Cell(45, 10, 'Total Amount Due:', 0);
        $pdf->Cell(40, 10, $amountDue , 0);
        $pdf->Ln(8);
        $pdf->Cell(45, 10, 'Deposit paid:', 0);
        $pdf->Cell(45, 10, $depositPaid , 0);
        $pdf->Cell(45, 10, 'Balance:', 0);
        $pdf->Cell(40, 10, $balanceDue, 0);
        $pdf->Ln(10);
        $pdf->Line(20, $pdf->GetY() + 0, $pdf->GetPageWidth() - 20, $pdf->GetY() + 0);
        
        // Terms
        $pdf->SetFont('Arial', 'B', 12);
        //$pdf->Cell(0, 10, 'Terms:', 0, 1, 'L');
        $pdf->Cell(0, 8, 'Terms:', 0);
        $pdf->Ln(8);        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, '1. Payment will be done as per the following guidelines:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(i) 50% deposit upon agreement to secure the booking', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(ii) 2nd Instalment of 30% is due on review of project and components', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(iii) Final instalment  of 20% is due on go-live', 0);
        $pdf->Cell(0);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, '2. Project content:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'The content of the project will be provided for by the Client. However, the ', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'developer may put content subject to review by the client', 0);
        $pdf->Cell(10);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, '3. Timelines:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'The timelines agreed upon will be implemented. Any changes will have to be ', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'addressed by either party. Unless the changes are due to additional development,', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'which will require appropriate additional time to be agreed upon.', 0);
        $pdf->Cell(0);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, '4. Terms of cancellations and refunds:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(i) Cancellations within 24hrs of agreement, full deposit paid will be refunded', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(ii) Cancellations done after project kickoff will forfeit 25% of deposit paid', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(iii) Cancellations done after project review will forfeit 50% of deposit paid', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, '(iv) All refunds will be paid after 48hrs of cancellation', 0);
        $pdf->Cell(0);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, '5. Service Level Agreement:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'Any issues identified by the client during or after project implementation will ', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'be raised through email, and a resolution implemented within the agreed time.', 0);
        $pdf->Cell(0);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, '6. Continuous Maintenance:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'The developer will continually work on improvements to the project components', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'to ensure high efficiency in delivery of the services. Due diligence will be ', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'employed to ensure minimal inteference during the maintenance periods.', 0);
        $pdf->Cell(0);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, '7. Amendments:', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'The terms herein are binding. Any changes can only be done upon mutual  ', 0);
        $pdf->Ln(5);
        $pdf->Cell(10);
        $pdf->Cell(0, 8, 'agreement by both parties.', 0);
        $pdf->Cell(0);
        $pdf->Ln(10);
        $pdf->Cell(0, 8, 'By making a deposit payment, you confirm to have read, understood and agreed to  ', 0);
        $pdf->Ln(8);
        $pdf->Cell(0, 8, 'the terms herein. The contract date is therefore the date of payment of the deposit.', 0);
        $pdf->Ln(7);


        // Signatures    
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 8, 'This is a system generated document. Therefore, it does not require a signature.', 0);
        
        //Save the file
        $path = 'fileStore/booking_contracts/';
        $filePath = __DIR__. '/../' . $path;
        $fileName = 'CONT_' . $selectedBookingCode . '.pdf';
        
        // Output the PDF to the browser or save it to a file
        $pdf->Output('F', $filePath . $fileName);
        //$pdf->Output('I', $filePath . $fileName);
        
        $contractLink = $path . $fileName;
    
        $sqlUpdateContractLink = "UPDATE bookings SET contractLink = '$contractLink' WHERE bookingID = '$selectedBookingCode'";
        $conn->query($sqlUpdateContractLink);
        
        $conn->close();
            
    }
    
    
    // Retrieve the payment ID from the submitted form to generate pay receipt
    if (isset($_POST['payment_id'])) {
        $paymentId = $_POST['payment_id'];
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
    
        // Fetch the payment details from the database based on the payment ID
        $sqlReceipt = "SELECT * FROM payments WHERE s_no = '$paymentId'";
        $result = $conn->query($sqlReceipt);
    
        if ($result->num_rows > 0) {
            $paymentDetails = $result->fetch_assoc();
    
                $No = $paymentDetails['s_no'];
                $name = $paymentDetails['name'];
                $phone = $paymentDetails['phone'];
                $services = $paymentDetails['services'];
                $amount = $paymentDetails['amount'];
                $paymentMode = $paymentDetails['payment_mode'];
                $staff_name = $paymentDetails['staff_name'];
                $staff_phone = $paymentDetails['staff_phone'];
                $date = $paymentDetails['date'];
    
        } else {
            die("Payment not found.");
        }
    
       // Generate the receipt using FPDF
        $pdf = new FPDF('P', 'mm', array(148, 210));
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $imgSrc = __DIR__ ."/../logos/Logo.jpg";
    
        $pdf->Image($imgSrc , 4, 8, -200, -200, 'JPEG');
        $pdf->Cell(40);
        //$pdf->Cell(40, 10, 'Excel Tech Essentials');
        $content = 'Excel Tech Essentials';
        $pdf->MultiCell(0, 10, $content);
        $pdf->Ln(10);
        $pdf->Cell(40);
        $pdf->SetFont('Arial', 'BI', 12);
        $pdf->Cell(40, 10, 'Innovation & Excellence');
    
        $pdf->Line(10, $pdf->GetY() + 12, $pdf->GetPageWidth() - 10, $pdf->GetY() + 12);
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Payment Receipt');
        
        $pdf->SetFont('Arial', '', 14);
        // Add other information to the receipt
        $pdf->Ln(15);
        $pdf->Cell(0, 10, 'Customer Name: ' . $name);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Phone Number: ' . $phone);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Services: ' . $services);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Amount: Ksh ' . $amount);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Staff Name: ' . $staff_name);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Staff Phone: ' . $staff_phone);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Payment Mode: ' . $paymentMode);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Date: ' . $date);
    
        // Output the PDF to the browser or save it to a file
        $filename = 'RCT000' . $No . '.pdf'; 
        $pdf->Output('I', $filename);
        
            exit();
            $conn->close();
    } 
  
?>