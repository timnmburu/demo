DROP TABLE IF EXISTS bookings;
CREATE TABLE `bookings` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` text NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `services` text NOT NULL,
  `quote` text NOT NULL,
  `description` text DEFAULT NULL,
  `dateBooked` text NOT NULL,
  `dateRequested` text NOT NULL,
  `amountDue` text DEFAULT NULL,
  `depositCode` text DEFAULT NULL,
  `depositPaid` text DEFAULT '0',
  `totalPaid` text DEFAULT '0',
  `balanceDue` text DEFAULT NULL,
  `lastPaymentDate` text DEFAULT NULL,
  `confirmation` text NOT NULL DEFAULT 'Unconfirmed',
  `status` text NOT NULL DEFAULT 'Pending Confirmation',
  `invoiceLink` text DEFAULT NULL,
  `contractLink` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO bookings VALUES ('1','20230807135435','Timothy','0725887269','timnmburu@gmail.com','Website building','12342323','It will be done','2023-08-09','2023-08-07 13:54:35','200','','0','0','200','','Confirmed','Pending Confirmation','fileStore/booking_invoices/INV_20230807135435.pdf','/fileStore/booking_contracts/CONT_20230807135435.pdf');
DROP TABLE IF EXISTS commission_payments;
CREATE TABLE `commission_payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `accBal` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS customers;
CREATE TABLE `customers` (
  `custID` int(11) NOT NULL AUTO_INCREMENT,
  `custName` text NOT NULL,
  `custPhone` text NOT NULL,
  `points` int(11) DEFAULT NULL,
  `redeemed` int(11) NOT NULL DEFAULT 0,
  `lastRedeemed` timestamp NULL DEFAULT NULL,
  `pointsBal` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`custID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO customers VALUES ('1','Tim','0725887269','350','0','','350');
DROP TABLE IF EXISTS expenseHistory;
CREATE TABLE `expenseHistory` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `amount` text NOT NULL,
  `date` text NOT NULL,
  `currentTotal` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS expenses;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS feedback;
CREATE TABLE `feedback` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `comment` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS givings;
CREATE TABLE `givings` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `narration` text NOT NULL,
  `date` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Not paid',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS images;
CREATE TABLE `images` (
  `image_path` text NOT NULL,
  `time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS inventory;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS mpesaStkPush;
CREATE TABLE `mpesaStkPush` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `phone` text DEFAULT NULL,
  `amount` text DEFAULT NULL,
  `MerchantRequestID` text DEFAULT NULL,
  `CheckoutRequestID` text DEFAULT NULL,
  `initiateResultCode` text DEFAULT NULL,
  `ResultCode` text DEFAULT NULL,
  `ResultDesc` text DEFAULT NULL,
  `MpesaReceiptNumber` text DEFAULT NULL,
  `TransactionDate` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO mpesaStkPush VALUES ('30','254725887269','2','24719-28035647-1','ws_CO_12082023104425188725887269','0','','','','');
INSERT INTO mpesaStkPush VALUES ('31','254725887269','2','28680-58996533-1','ws_CO_12082023105240911725887269','0','1019','Transaction has expired','0','0');
INSERT INTO mpesaStkPush VALUES ('32','254725887269','3','','','','','','','');
DROP TABLE IF EXISTS mpesa_payments;
CREATE TABLE `mpesa_payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `accBal` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS offers;
CREATE TABLE `offers` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `offer_name` text NOT NULL,
  `offer_image_poster` text NOT NULL,
  `start_date` text NOT NULL,
  `end_date` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO offers VALUES ('1','Offer of July 2023','fileStore/LFH Offer thru July 2023.jpg','2023-07-01','2023-07-31','Active');
DROP TABLE IF EXISTS orders;
CREATE TABLE `orders` (
  `order_number` int(11) NOT NULL AUTO_INCREMENT,
  `orderTime` text NOT NULL,
  `custName` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `product` text NOT NULL,
  `quantity` text NOT NULL,
  `delivered` text DEFAULT 'No',
  `country` text NOT NULL,
  `postal_address` text NOT NULL,
  `postal_code` text NOT NULL,
  `location` text NOT NULL,
  PRIMARY KEY (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS payments;
CREATE TABLE `payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `services` text NOT NULL,
  `amount` int(11) NOT NULL,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `date` text NOT NULL,
  `commission_paid` text NOT NULL DEFAULT 'Not Paid',
  `payment_mode` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS performance;
CREATE TABLE `performance` (
  `cashIn` int(11) NOT NULL,
  `cashOut` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `percent` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS performanceHistory;
CREATE TABLE `performanceHistory` (
  `cashIn` int(11) NOT NULL,
  `cashOut` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `percent` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO performanceHistory VALUES ('35000','3000','32000','91.428571428571%','2023-08-03 11:45:53');
DROP TABLE IF EXISTS recruit;
CREATE TABLE `recruit` (
  `staff_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `staff_email` text NOT NULL,
  `joinDate` text NOT NULL,
  `skills` text NOT NULL,
  `cv` text DEFAULT NULL,
  PRIMARY KEY (`staff_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS recurrentExp;
CREATE TABLE `recurrentExp` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `amount` text NOT NULL,
  `date` text NOT NULL,
  `currentTotal` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS sentSMS;
CREATE TABLE `sentSMS` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` text NOT NULL,
  `message` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS staff;
CREATE TABLE `staff` (
  `staff_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `staff_email` text NOT NULL,
  `joinDate` text NOT NULL,
  `ID_front` text NOT NULL,
  `ID_back` text NOT NULL,
  `passport_pic` text NOT NULL,
  `contract` text NOT NULL,
  `status` text NOT NULL DEFAULT 'active',
  `role` text NOT NULL DEFAULT 'staff',
  `exit_comment` text NOT NULL DEFAULT '\'none\'',
  `exited_date` text NOT NULL DEFAULT 'n/a',
  PRIMARY KEY (`staff_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO staff VALUES ('1','Millicent Watetu','0720099212','mmwatetu@gmail.com','2023-04-01 8:22:23','fileStore/2.jpg','fileStore/2.jpg','fileStore/2.jpg','None','active','admin','none','n/a');
INSERT INTO staff VALUES ('2','Timothy Njoroge','0725887269','timnmburu@gmail.com','2023-06-12','fileStore/3.jpg','fileStore/3.jpg','fileStore/3.jpg','None','active','admin','none','n/a');
INSERT INTO staff VALUES ('3','Annita Njeri','0741519136','annitanwanjiru@gmail.com','2023-06-12','fileStore/staff_docs/Annita Njeri ID front.jpg','fileStore/staff_docs/Annita Njeri ID back.jpg','fileStore/5.jpg','None','exited','admin','Insubordination, missing work without communication for two days and badattitude ','2023-07-01');
INSERT INTO staff VALUES ('4','Damaris Wanjiru','0746503278','wanjiruirungu23@gmail.com','2023-06-22','fileStore/staff_docs/IMG_20211029_172608_764.jpg','fileStore/staff_docs/IMG_20211029_172608_764.jpg','fileStore/staff_docs/IMG_20211029_172608_764.jpg','fileStore/staff_docs/IMG_20211029_172608_764.jpg','active','admin','\'none\'','n/a');
DROP TABLE IF EXISTS target;
CREATE TABLE `target` (
  `monthlyTarget` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO target VALUES ('22308');
DROP TABLE IF EXISTS userlogs;
CREATE TABLE `userlogs` (
  `username` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO userlogs VALUES ('Tim','2023-08-03 10:33:33');
INSERT INTO userlogs VALUES ('Tim','2023-08-03 11:52:19');
INSERT INTO userlogs VALUES ('Tim','2023-08-07 13:55:47');
INSERT INTO userlogs VALUES ('Tim','2023-08-07 14:22:18');
INSERT INTO userlogs VALUES ('Tim','2023-08-07 18:15:25');
DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `token` text NOT NULL,
  `lastResetDate` text NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO users VALUES ('1','Millie','$2y$10$y9kSViAVvu7XekLA0XWTC.Hh7pHr6a0VmX.s0yr63M4cOcosXQ1tG','mmwatetu@gmail.com','','2023-06-22 23:14:36');
INSERT INTO users VALUES ('5','Tim','$2y$10$cgONRcJHAucDZzRjpQEXnuck7TSOUuhEZk2fZXZUBPjgMOdEzWC.u','timnmburu@gmail.com','','2023-06-22 23:10:11');
INSERT INTO users VALUES ('6','Sheerow','$2y$10$6O0/dz1rpuV1H9XZD.rereyljvAvzFUqsOUNRXzrnp3VY.a6o6pQa','wanjiruirungu23@gmail.com','','2023-06-28 15:51:04');
DROP TABLE IF EXISTS wallet;
CREATE TABLE `wallet` (
  `mpesa` text NOT NULL DEFAULT '0',
  `kcb` text NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO wallet VALUES ('4089.06','10');
