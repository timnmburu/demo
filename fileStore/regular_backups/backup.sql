-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 29, 2023 at 10:29 AM
-- Server version: 10.5.20-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id21095668_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `s_no` int(11) NOT NULL,
  `custID` text NOT NULL,
  `custName` text NOT NULL,
  `custPhone` text NOT NULL,
  `subDate` text NOT NULL,
  `subAmount` text NOT NULL,
  `lastPayAmount` text NOT NULL,
  `lastPayDate` text NOT NULL,
  `nextPayDate` text NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`s_no`, `custID`, `custName`, `custPhone`, `subDate`, `subAmount`, `lastPayAmount`, `lastPayDate`, `nextPayDate`, `status`) VALUES
(1, '2301', 'Demo', '0725887269', '01-08-2023', '2000', '2000', '01-06-2023', '01-07-2023', 'limited'),
(2, '2301', 'Demo', '0725887269', '01-08-2023', '2000', '2000', '01-07-2023', '01-08-2023', 'limited'),
(3, '2301', 'Demo', '0725887269', '01-08-2023', '2000', '2000', '01-08-2023', '01-09-2023', 'limited');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `s_no` int(11) NOT NULL,
  `bookingID` text NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `services` text NOT NULL,
  `description` text DEFAULT NULL,
  `dateBooked` text NOT NULL,
  `quote` text NOT NULL,
  `dateRequested` text NOT NULL,
  `amountDue` text DEFAULT NULL,
  `depositCode` text DEFAULT NULL,
  `depositPaid` text DEFAULT '0',
  `totalPaid` text DEFAULT '0',
  `balanceDue` text DEFAULT NULL,
  `lastPaymentDate` text DEFAULT NULL,
  `confirmation` text NOT NULL DEFAULT 'Unconfirmed',
  `status` text NOT NULL DEFAULT 'Pending Payment',
  `invoiceLink` text DEFAULT NULL,
  `contractLink` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`s_no`, `bookingID`, `name`, `phone`, `email`, `services`, `description`, `dateBooked`, `quote`, `dateRequested`, `amountDue`, `depositCode`, `depositPaid`, `totalPaid`, `balanceDue`, `lastPaymentDate`, `confirmation`, `status`, `invoiceLink`, `contractLink`) VALUES
(1, '20230809121533', 'Asumpta Kimosop', '0780714***', 'vwkimti@gmail.com', 'bridal make up, pedicure and manicure', 'bridal make up, pedicure and manicure, hair styling and transport ', '2023-08-24', '14:00', '2023-08-09 12:15:33', '6800', 'KCB', '3800', '3800', '3000', '2023-08-07', 'Confirmed', 'Deposit Paid', 'fileStore/booking_invoices/INV_20230809121533.pdf', '/fileStore/booking_contracts/CONT_20230809121533.pdf'),
(2, '20230809154135', 'Hannah Wanguti', '0726905***', 'hantush@gmail.com', 'Ultimate without lashes ', 'Manicure, Pedicure, Ultimate makeup (No Lashes)', '2023-08-24', '14:00', '2023-08-09 15:41:35', '2800', '', '0', '0', '2800', '', 'Confirmed', 'Pending Payment', 'fileStore/booking_invoices/INV_20230809154135.pdf', '/fileStore/booking_contracts/CONT_20230809154135.pdf'),
(3, '20230810145536', 'Maritab  Giocho ', '0729154***', 'mgireset@gmail.com', 'Bridal', 'Manicure, Pedicure, Ultimate makeup (No Lashes)', '2023-08-24', '14:00', '2023-08-10 14:55:36', '2800', 'Other', '2800', '2800', '0', '2023-08-10', 'Confirmed', 'Payment Completed', 'fileStore/booking_invoices/INV_20230810145536.pdf', '/fileStore/booking_contracts/CONT_20230810145536.pdf'),
(4, '20230811213632', 'Wanjiku Asunda', '0723542***', 'symkid@yahoo.com', 'Bridal makeup ', 'Manicure, Pedicure, Ultimate makeup (No Lashes)', '2023-08-25', '07:45', '2023-08-11 21:36:32', '2800', '', '0', '0', '2800', '', 'Confirmed', 'Pending Payment', 'fileStore/booking_invoices/INV_20230811213632.pdf', '/fileStore/booking_contracts/CONT_20230811213632.pdf'),
(5, '20230812232528', 'Sylvester karungi', '0785777***', 'sylkaru@ymail.com', 'Bridal makeup', 'It will be done', '2023-08-24', '14:00', '2023-08-12 23:25:28', '5000', 'Other', '2000', '5000', '0', '2023-08-17', 'Confirmed', 'Payment Completed', 'fileStore/booking_invoices/INV_20230812232528.pdf', 'fileStore/booking_contracts/CONT_20230812232528.pdf'),
(12, '20230821121607', 'Temeza Butchery', '254**3747172', 'butchtemeza@hotmail.com', 'System', NULL, '2023-09-01', '2838247', '2023-08-21 12:16:07', NULL, NULL, '0', '0', NULL, NULL, 'Unconfirmed', 'Pending Payment', NULL, NULL),
(13, '20230821121734', 'Salon City', '25484***2', 'citysalon@salon.com', 'Sytem and website', NULL, '2023-09-09', '2342341', '2023-08-21 12:17:34', NULL, NULL, '0', '0', NULL, NULL, 'Unconfirmed', 'Pending Payment', 'fileStore/booking_invoices/INV_20230821121734.pdf', 'fileStore/booking_contracts/CONT_20230821121734.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `bookings1`
--

CREATE TABLE `bookings1` (
  `s_no` int(11) NOT NULL,
  `bookingID` text NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `services` text NOT NULL,
  `description` text DEFAULT NULL,
  `dateBooked` text NOT NULL,
  `time` text NOT NULL,
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
  `contractLink` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bookings1`
--

INSERT INTO `bookings1` (`s_no`, `bookingID`, `name`, `phone`, `email`, `services`, `description`, `dateBooked`, `time`, `dateRequested`, `amountDue`, `depositCode`, `depositPaid`, `totalPaid`, `balanceDue`, `lastPaymentDate`, `confirmation`, `status`, `invoiceLink`, `contractLink`) VALUES
(1, '20230809121533', 'Veronica Kimere', '0725887269', 'vwkimere@gmail.com', 'bridal make up, pedicure and manicure', 'bridal make up, pedicure and manicure, hair styling and transport ', '2023-08-24', '14:00', '2023-08-09 12:15:33', '6800', 'KCB', '3800', '0', '1500', '2023-08-09', 'Confirmed', 'Pending Confirmation', 'fileStore/booking_invoices/INV_20230809121533.pdf', '/fileStore/booking_contracts/CONT_20230809121533.pdf'),
(2, '20230809154135', 'Ann Wangari', '0733440443', 'wangtush@gmail.com', 'Ultimate without lashes ', '', '2023-08-24', '14:00', '2023-08-09 15:41:35', '2800', '', '0', '0', '2800', '', 'Confirmed', 'Pending Payment', 'fileStore/booking_invoices/INV_20230809154135.pdf', '/fileStore/booking_contracts/CONT_20230809154135.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `commission_payments`
--

CREATE TABLE `commission_payments` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `accBal` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `commission_payments`
--

INSERT INTO `commission_payments` (`s_no`, `name`, `phone`, `amount`, `accBal`, `date`) VALUES
(1, 'Annita Nyeu', '254725887***', '6830', '3200.99', '2023-06-17 20:48:27'),
(2, 'Absolom Njeri', '2547200992***', '3330', '8760.79', '2023-06-24 20:13:21'),
(3, 'Direst Dhana', '254724503***', '5250', '3410.79', '2023-06-24 20:23:57'),
(4, 'Annita Nyeu', '254722519***', '4380', '6450.79', '2023-07-01 20:04:43'),
(5, 'Direst Dhana', '0797567***', '11030', '10600.79', '2023-07-01 21:21:20'),
(6, 'Direst Dhana', '0796567***', '7880', '1150.04', '2023-07-08 19:16:53'),
(13, 'demo', '0725887269', '10', '3746.99', '2023-09-29 09:25:06');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `custID` int(11) NOT NULL,
  `custName` text NOT NULL,
  `custPhone` text NOT NULL,
  `points` int(11) DEFAULT NULL,
  `redeemed` int(11) NOT NULL DEFAULT 0,
  `lastRedeemed` timestamp NULL DEFAULT NULL,
  `pointsBal` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`custID`, `custName`, `custPhone`, `points`, `redeemed`, `lastRedeemed`, `pointsBal`) VALUES
(1, 'Hill ', '0791606***', 110, 0, '0000-00-00 00:00:00', 110),
(2, 'Bernardo', '0722512***', 40, 0, '0000-00-00 00:00:00', 40),
(3, 'Delici', '0700227***', 60, 0, '0000-00-00 00:00:00', 60),
(4, 'Lizzie', '0704895***', 90, 0, '0000-00-00 00:00:00', 90),
(5, 'Merceille', '0798166***', 410, 0, '0000-00-00 00:00:00', 410),
(6, 'Mundo', '0728113***', 50, 0, '0000-00-00 00:00:00', 50),
(7, 'Bret', '0701453***', 110, 0, '0000-00-00 00:00:00', 110),
(8, 'Hanasia', '0799047***', 310, 0, '0000-00-00 00:00:00', 310),
(84, 'Tammy', '0725887269', 30, 0, NULL, 30),
(85, 'Sylvester karungi', '00785777', 50, 0, NULL, 50);

-- --------------------------------------------------------

--
-- Table structure for table `expenseHistory`
--

CREATE TABLE `expenseHistory` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `amount` text NOT NULL,
  `date` text NOT NULL,
  `currentTotal` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `expenseHistory`
--

INSERT INTO `expenseHistory` (`s_no`, `name`, `amount`, `date`, `currentTotal`) VALUES
(1, 'Rent', '5000', '2023-07-24 19:28:21', '5000'),
(2, 'Wifi', '1000', '2023-07-24 19:28:43', '6000'),
(3, 'Electricity', '500', '2023-07-24 19:30:28', '6500'),
(4, 'Salary', '5000', '2023-07-24 19:30:41', '11500'),
(5, 'Misc Expenses', '3000', '2023-07-25 09:49:57', '14500'),
(6, 'Misc Expenses', '3000', '2023-07-27 10:04:06', '14500');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `name`, `price`, `quantity`, `date`, `paidFrom`) VALUES
(1, 'Banner ground', 5000, '1', '2023-05-10', 'KCB Paybill'),
(2, 'Teepee Pegs', 1550, '1', '2023-04-07', 'KCB Paybill'),
(3, 'Garnier Even & Matte Cream SPF 30 50ml', 10500, '1', '2023-04-07', 'KCB Paybill'),
(4, 'Imp Leather Japanese Spa Lotion 400ml ', 4500, '1', '2023-04-07', 'KCB Paybill'),
(5, 'Banner roof print', 10000, '1', '2023-04-23', 'KCB Paybill'),
(6, 'Teepee nylon rope', 860, '1', '2023-04-07', 'KCB Paybill'),
(7, 'Skytone soap box ', 1040, '1', '2023-04-07', 'KCB Paybill'),
(8, 'H&B Face towel bright red', 1150, '1', '2023-04-07', 'KCB Paybill'),
(9, 'Aluminum case LED downlighters', 18200, '7', '2023-04-24', 'KCB Paybill'),
(10, 'CCTV Bulb PTZ camera', 25000, '1', '2023-04-06', 'KCB Paybill'),
(11, 'Henna', 4000, '4', '2023-06-17', 'KCB Paybill'),
(12, 'Annita Nyea 254701519*** Commission', 6830, '1', '2023-06-17 20:48:27', 'Mpesa Online'),
(13, 'Aprons', 7800, '6', '2023-06-24', 'KCB Paybill'),
(88, 'Excel Tech 0725887269 Payment for test 05 September', 1, '1', '2023-09-05 09:37:17', 'Mpesa Online'),
(89, 'Excel Tech 0725887269 Payment for test 05 September', 1, '1', '2023-09-05 09:46:11', 'Mpesa Online'),
(90, 'Excel Tech 0725887269 Payment for Excel Tech', 1, '1', '2023-09-05 09:51:09', 'Mpesa Online'),
(91, 'Customer 0725887269 Payment for test 06 September', 5, '1', '2023-09-06 16:42:03', 'Mpesa Online'),
(92, 'Customer 0725887269 Payment for test 06 September', 5, '1', '2023-09-06 16:43:03', 'Mpesa Online'),
(93, 'Customer 0725887269 Payment for Excel Tech', 10, '1', '2023-09-06 16:47:40', 'Mpesa Online'),
(94, 'Customer 0725887269 Payment for Excel Tech', 5, '1', '2023-09-06 16:52:25', 'Mpesa Online'),
(95, 'Excel Tech 0725887269 Payment for Excel Tech', 1, '1', '2023-09-29 09:18:41', 'Mpesa Online'),
(96, 'demo 0725887269 Payment for Excel Tech', 10, '1', '2023-09-29 09:25:06', 'Mpesa Online');

-- --------------------------------------------------------

--
-- Table structure for table `expenses1`
--

CREATE TABLE `expenses1` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `comment` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`s_no`, `name`, `email`, `comment`, `date`) VALUES
(8, 'Mike Smith\r\n', 'mikeEndonormaPsymn@gmail.com', 'If you are looking to rank your local business on Google Maps in a specific area, this service is for you. \n \nGoogle Map Stacking is a highly effective technique for ranking your GMB within a specific mile radius. \n \nMore info: \nhttps://www.speed-seo.net/product/google-maps-pointers/ \n \nThanks and Regards \nMike Smith\n \n \nPS: Want a comprehensive local plan that covers everything? \nhttps://www.speed-seo.net/product/local-seo-bundle/', '2023-07-09 07:15:58'),
(9, 'StephenUrgef', 'streetwambui@gmail.com', 'Impressed with your brand and online presence. Wambui Street offers competitive loans for companies. We specialize in scaling businesses with good rates. Boost your credit for easier loan qualification. Apply for up to $25M same-day approval. Free consultation. Schedule a Zoom call: https://calendly.com/wambuistreet/meeting-with-wambui-kinuthia or fill the form: https://www.wambuistreet.com/. Looking forward to hearing from you. Wambui Kinuthia, CEO, Wambui Street', '2023-07-11 10:16:04'),
(22, 'Mike Nash\r\n', 'mikePsymn@gmail.com', 'Hi there \n \nJust checked your essentialtech.site backlink profile, I noticed a moderate percentage of toxic links pointing to your website \n \nWe will investigate each link for its toxicity and perform a professional clean up for you free of charge. \n \nStart recovering your ranks today: \nhttps://www.hilkom-digital.de/professional-linksprofile-clean-up-service/ \n \n \nRegards \nMike Nash\nHilkom Digital SEO Experts \nhttps://www.hilkom-digital.de/', '2023-08-12 14:35:05'),
(23, 'Tim', 'miltim##43@gmail.com', 'This is a very good system.', '2023-08-14');

-- --------------------------------------------------------

--
-- Table structure for table `frequentPayments`
--

CREATE TABLE `frequentPayments` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `wallet` text NOT NULL,
  `reference` text NOT NULL,
  `account` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `frequentPayments`
--

INSERT INTO `frequentPayments` (`s_no`, `name`, `wallet`, `reference`, `account`) VALUES
(1, 'Trizah Wendy', 'Mpesa', 'mpesa', '0725678***'),
(2, 'Textiles Industry', 'Paybill', '475836**', '8293949**'),
(3, 'Rent', 'Bank', 'Mayfair Bank', '28732849***'),
(4, 'Stock', 'Mpesa Buygoods', 'Mpesa', '4545***');

-- --------------------------------------------------------

--
-- Table structure for table `givings`
--

CREATE TABLE `givings` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `narration` text NOT NULL,
  `date` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Not paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `givings`
--

INSERT INTO `givings` (`s_no`, `name`, `phone`, `amount`, `narration`, `date`, `status`) VALUES
(1, 'Alexander', '254727751***', '3750', 'Tithe', '2023-08-10 23:20:01', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_path` text NOT NULL,
  `time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_path`, `time`) VALUES
('fileStore/2.jpg', '2023-04-17 11:23:23'),
('fileStore/3.jpg', '2023-04-17 11:24:36'),
('fileStore/9.jpg', '2023-04-17 11:26:50'),
('fileStore/17.jpg', '2023-04-17 11:28:32'),
('fileStore/26.jpg', '2023-04-17 11:28:32'),
('fileStore/WhatsApp Image 2023-04-10 at 13.00.06 (1).jpeg', '2023-04-17 14:15:08');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `price`, `quantity`, `date`, `paidFrom`) VALUES
(1, 'Teepee Pegs', 1550, '1', '2023-04-07', 'KCB Paybill'),
(2, 'Garnier Even & Matte Cream SPF 30 50ml', 10500, '1', '2023-04-07', 'KCB Paybill'),
(3, 'Imp Leather Japanese Spa Lotion 400ml ', 4500, '1', '2023-04-07', 'KCB Paybill'),
(4, 'Teepee nylon rope', 860, '1', '2023-04-07', 'KCB Paybill'),
(5, 'Skytone soap box ', 1040, '1', '2023-04-07', 'KCB Paybill'),
(6, 'H&B Face towel bright red', 1150, '1', '2023-04-07', 'KCB Paybill'),
(7, 'Henna', 4000, '4', '2023-06-17', 'KCB Paybill'),
(8, 'Aprons', 7800, '6', '2023-06-24', 'KCB Paybill'),
(9, 'Nail Drill', 32000, '1', '2023-06-24', 'KCB Paybill'),
(10, 'Blue sky Base and Top Coat', 19000, '2', '2023-06-24', 'KCB Paybill'),
(11, 'Nail builder brush', 4000, '2', '2023-06-24', 'KCB Paybill'),
(12, 'Magic remover', 4000, '1', '2023-06-24', 'KCB Paybill');

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_collections`
--

CREATE TABLE `mpesa_collections` (
  `s_no` int(11) NOT NULL,
  `invoice_id` text NOT NULL,
  `state` text NOT NULL,
  `provider` text NOT NULL,
  `charges` text NOT NULL,
  `net_amount` text NOT NULL,
  `value` text NOT NULL,
  `account` text NOT NULL,
  `api_ref` text NOT NULL,
  `clearing_status` text DEFAULT NULL,
  `mpesa_reference` text DEFAULT NULL,
  `failed_reason` text DEFAULT NULL,
  `failed_code` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mpesa_collections`
--

INSERT INTO `mpesa_collections` (`s_no`, `invoice_id`, `state`, `provider`, `charges`, `net_amount`, `value`, `account`, `api_ref`, `clearing_status`, `mpesa_reference`, `failed_reason`, `failed_code`) VALUES
(1, 'KOJQV4Y', 'PENDING', 'M-PESA', '0.00', '15.00', '15.00', '254725887269', 'Lourice', NULL, NULL, NULL, NULL),
(2, 'KOJQV4Y', 'PROCESSING', 'M-PESA', '0.00', '15.00', '15.00', '254725887269', 'Lourice', NULL, NULL, NULL, NULL),
(3, 'KOJQV4Y', 'FAILED', 'M-PESA', '0.00', '15.00', '15.00', '254725887269', 'Lourice', NULL, NULL, 'Request cancelled by user', '1032'),
(4, '0VPOL20', 'PENDING', 'M-PESA', '0.00', '10.00', '10.00', '254725887269', 'Lourice', NULL, NULL, 'Request cancelled by user', '1032'),
(5, 'Y57LQVK', 'PENDING', 'M-PESA', '0.00', '100.00', '100.00', '254725887269', 'Lourice', NULL, NULL, NULL, NULL),
(21, 'KNRVVNY', 'FAILED', 'M-PESA', '0.00', '13.00', '13.00', '254725887269', 'Excel Tech', NULL, NULL, 'Request cancelled by user', '1032'),
(22, '0L3JJ9Y', 'COMPLETE', 'M-PESA', '0.00', '10.00', '10.00', '254725887269', 'Excel Tech', 'AVAILABLE', 'RI51XE4YOR', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_payments`
--

CREATE TABLE `mpesa_payments` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `accBal` text NOT NULL,
  `date` text NOT NULL,
  `notifyPhone` text DEFAULT NULL,
  `trackingID` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `notified` text NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mpesa_payments`
--

INSERT INTO `mpesa_payments` (`s_no`, `name`, `phone`, `amount`, `accBal`, `date`, `notifyPhone`, `trackingID`, `status`, `notified`) VALUES
(1, 'Annita Njeri', '254741519136', '683', '320.99', '2023-06-17 20:48:27', NULL, NULL, NULL, '0'),
(2, 'Annita Njeri', '254741519136', '333', '876.79', '2023-06-24 20:13:21', NULL, NULL, NULL, '0'),
(3, 'Damaris Wanjiru', '254746503278', '525', '341.79', '2023-06-24 20:23:57', NULL, NULL, NULL, '0'),
(4, 'Customer', '254725887269', '150', '54.79', '2023-06-29 11:42:37', NULL, NULL, NULL, '0'),
(5, 'Customer', '254720099212', '20', '24.79', '2023-06-29 13:28:04', NULL, NULL, NULL, '0'),
(6, 'Annita Njeri', '254741519136', '438', '645.79', '2023-07-01 20:04:43', NULL, NULL, NULL, '0'),
(7, 'Customer', '254741519136', '535', '100.79', '2023-07-01 20:06:09', NULL, NULL, NULL, '0'),
(8, 'Damaris Wanjiru', '254746503278', '1103', '1060.79', '2023-07-01 21:21:20', NULL, NULL, NULL, '0'),
(9, 'Customer', '254746503278', '700', '350.79', '2023-07-01 21:33:11', NULL, NULL, NULL, '0'),
(10, 'Customer', '254720099212', '548', '40.29', '2023-07-05 22:57:41', NULL, NULL, NULL, '0'),
(11, 'Customer', '254725887269', '1700', '3.24', '2023-07-05 23:26:24', NULL, NULL, NULL, '0'),
(12, 'Damaris Wanjiru', '254746503278', '788', '115.04', '2023-07-08 19:16:53', NULL, NULL, NULL, '0'),
(13, 'Customer', '254725887269', '130', '222.54', '2023-07-14 07:01:57', NULL, NULL, NULL, '0'),
(14, 'Customer', '254720099212', '210', '584.54', '2023-07-15 14:49:50', NULL, NULL, NULL, '0'),
(15, 'Customer', '254725887269', '120', '454.54', '2023-07-15 16:19:24', NULL, NULL, NULL, '0'),
(16, 'Customer', '254758860051', '100', '344.54', '2023-07-15 17:04:51', NULL, NULL, NULL, '0'),
(17, 'Customer', '254758960051', '100', '234.54', '2023-07-15 19:13:20', NULL, NULL, NULL, '0'),
(18, 'Customer', '254725887269', '11', '5.04', '2023-07-17 14:52:07', NULL, NULL, NULL, '0'),
(19, 'Customer', '0725887269', '10', '1130.86', '2023-07-19 07:58:39', NULL, NULL, NULL, '0'),
(20, 'Customer', '0725887269', '10', '1110.86', '2023-07-19 08:05:03', NULL, NULL, NULL, '0'),
(21, 'Customer', '0725887269', '10', '1090.86', '2023-07-19 08:09:44', NULL, NULL, NULL, '0'),
(22, 'Customer', '0722981937', '1000', '75.86', '2023-07-19 08:14:07', NULL, NULL, NULL, '0'),
(31, 'Customer', '0725887269', '500', '158.66', '2023-07-21 06:51:58', NULL, NULL, NULL, '0'),
(32, 'Customer', '0340161447990', '10', '158.66', '2023-07-21 10:22:35', NULL, NULL, NULL, '0'),
(33, 'Customer', '0725887269', '230', '314.66', '2023-07-21 14:07:07', NULL, NULL, NULL, '0'),
(34, 'Customer', '247247', '10', '512.66', '2023-07-22 11:30:35', NULL, NULL, NULL, '0'),
(35, 'Customer', '247247', '11', '492.66', '2023-07-22 11:33:50', NULL, NULL, NULL, '0'),
(36, 'Customer', '247247', '10', '471.66', '2023-07-22 11:37:43', NULL, NULL, NULL, '0'),
(37, 'Customer', '0725887269', '10', '431.66', '2023-07-22 11:52:00', NULL, NULL, NULL, '0'),
(38, 'Timothy Njoroge', '0725887269', '10', '411.66', '2023-07-22 13:32:48', NULL, NULL, NULL, '0'),
(39, 'Customer', '0720099212', '200', '201.66', '2023-07-22 14:50:14', NULL, NULL, NULL, '0'),
(40, 'Customer', '0340161447990', '100', '290.76', '2023-07-22 18:43:27', NULL, NULL, NULL, '0'),
(41, 'Damaris Wanjiru', '0746503278', '976', '90.76', '2023-07-22 19:46:52', NULL, NULL, NULL, '0'),
(42, 'Customer', '5378139', '120', '288.76', '2023-07-22 19:50:45', NULL, NULL, NULL, '0'),
(43, 'Customer', '5635991', '100', '158.76', '2023-07-22 19:53:58', NULL, NULL, NULL, '0'),
(44, 'Customer', '8088330', '210', '296.26', '2023-07-22 19:58:39', NULL, NULL, NULL, '0'),
(45, 'Customer', '0798905921', '75', '189.26', '2023-07-22 20:08:02', NULL, NULL, NULL, '0'),
(46, 'Timothy Njoroge', '0725887269', '10', '169.26', '2023-07-24 16:24:29', NULL, NULL, NULL, '0'),
(47, 'Customer', '0725887269', '10', '149.26', '2023-07-24 16:26:28', NULL, NULL, NULL, '0'),
(48, 'Timothy Njoroge', '0725887269', '10', '129.26', '2023-07-24 16:27:41', NULL, NULL, NULL, '0'),
(49, 'Customer', '0725887269', '10', '109.26', '2023-07-24 16:51:48', NULL, NULL, NULL, '0'),
(50, 'Customer', '0725887269', '200', '1938.66', '2023-07-29 13:30:24', NULL, NULL, NULL, '0'),
(51, 'Customer', '954105', '180', '2148.66', '2023-07-29 15:57:01', NULL, NULL, NULL, '0'),
(52, 'Customer', '0725887269', '200', '1748.66', '2023-07-29 16:00:47', NULL, NULL, NULL, '0'),
(53, 'Damaris Wanjiru', '0746503278', '1', '2232.66', '2023-07-29 19:47:22', NULL, NULL, NULL, '0'),
(54, 'Damaris Wanjiru', '0746503278', '1', '2221.66', '2023-07-29 19:57:43', NULL, NULL, NULL, '0'),
(55, 'Customer', '5378139', '320', '516.66', '2023-07-29 21:48:15', NULL, NULL, NULL, '0'),
(56, 'Customer', '247247', '60', '5059.06', '2023-08-03 18:47:20', NULL, NULL, NULL, '0'),
(57, 'Customer', '247247', '20', '4989.06', '2023-08-03 18:49:39', NULL, NULL, NULL, '0'),
(58, 'Customer', '0702561002', '150', '4799.06', '2023-08-03 19:17:49', NULL, NULL, NULL, '0'),
(59, 'Customer', '765244', '200', '4799.06', '2023-08-03 19:24:10', NULL, NULL, NULL, '0'),
(60, 'Customer', '247247', '2175', '4589.06', '2023-08-03 21:55:46', NULL, NULL, NULL, '0'),
(61, 'Customer', '0725887269', '500', '2767.06', '2023-08-04 20:18:17', NULL, NULL, NULL, '0'),
(62, 'Customer', '8581972', '200', '3737.06', '2023-08-05 17:08:07', NULL, NULL, NULL, '0'),
(63, 'Customer', '0798297323', '150', '3367.06', '2023-08-05 18:57:40', NULL, NULL, NULL, '0'),
(64, 'Customer', '9161673', '360', '3367.06', '2023-08-05 19:55:15', NULL, NULL, NULL, '0'),
(65, 'Damaris Wanjiru', '0746503278', '2', '4246.06', '2023-08-05 21:34:28', NULL, NULL, NULL, '0'),
(66, 'Customer', '222111', '1100', '4258.06', '2023-08-05 22:47:07', NULL, NULL, NULL, '0'),
(67, 'Customer', '222111', '65', '3148.06', '2023-08-05 22:56:50', NULL, NULL, NULL, '0'),
(68, 'Damaris Wanjiru', '0746503278', '2244', '796.06', '2023-08-05 23:19:38', NULL, NULL, NULL, '0'),
(69, 'Customer', '0725887269', '150', '536.06', '2023-08-07 10:34:06', NULL, NULL, NULL, '0'),
(70, 'Customer', '0720099212', '300', '662.56', '2023-08-08 14:55:55', NULL, NULL, NULL, '0'),
(71, 'Customer', '0725887269', '300', '401.06', '2023-08-08 19:46:44', NULL, NULL, NULL, '0'),
(72, 'Customer', '0720099212', '300', '91.06', '2023-08-08 20:29:17', NULL, NULL, NULL, '0'),
(73, 'Customer', '0725887269', '50', '40.76', '2023-08-09 09:15:55', NULL, NULL, NULL, '0'),
(74, 'Alex', '254727751912', '3750', '790.06', '2023-08-10 23:20:01', NULL, NULL, NULL, '0'),
(75, 'Customer', '0704028120', '100', '1116.56', '2023-08-11 13:44:11', NULL, NULL, NULL, '0'),
(76, 'Customer', '0725887269', '300', '1340.06', '2023-08-11 15:40:18', NULL, NULL, NULL, '0'),
(77, 'Customer', '0720099212', '300', '1515.06', '2023-08-11 20:24:03', NULL, NULL, NULL, '0'),
(78, 'Customer', '0725887269', '500', '1005.06', '2023-08-11 20:43:46', NULL, NULL, NULL, '0'),
(79, 'Damaris Wanjiru', '0746503278', '1400', '117.56', '2023-08-12 20:39:30', NULL, NULL, NULL, '0'),
(80, 'Customer', '0720099212', '1950', '30.06', '2023-08-13 22:15:25', NULL, NULL, NULL, '0'),
(81, 'Tim', '254725887269', '312.5', '888.56', '2023-08-15 23:30:00', NULL, NULL, NULL, '0'),
(82, 'Millie', '254720099212', '312.5', '888.56', '2023-08-15 23:30:00', NULL, NULL, NULL, '0'),
(83, 'Customer', '771837', '200', '1179.56', '2023-08-16 18:28:36', NULL, NULL, NULL, '0'),
(84, 'Customer', '0725887269', '300', '869.56', '2023-08-16 18:29:33', NULL, NULL, NULL, '0'),
(85, 'Customer', '247247', '105', '1451.56', '2023-08-16 19:41:59', NULL, NULL, NULL, '0'),
(86, 'Tim', '254725887269', '225', '866.56', '2023-08-16 23:30:00', NULL, NULL, NULL, '0'),
(87, 'Millie', '254720099212', '225', '866.56', '2023-08-16 23:30:00', NULL, NULL, NULL, '0'),
(88, 'Customer', '0725887269', '500', '380.81', '2023-08-17 16:34:12', NULL, NULL, NULL, '0'),
(89, 'Damaris Wanjiru', '0746503278', '3133', '2600.31', '2023-08-19 21:24:40', NULL, NULL, NULL, '0'),
(90, 'Customer', '0795880582', '1700', '875.31', '2023-08-19 22:01:08', NULL, NULL, NULL, '0'),
(91, 'Customer', '0722981937', '670', '195.31', '2023-08-19 22:30:20', NULL, NULL, NULL, '0'),
(92, 'Customer', '0725887269', '200', '518.81', '2023-08-22 19:51:15', NULL, NULL, NULL, '0'),
(93, 'Customer', '0798297323', '120', '1950.51', '2023-08-24 13:03:37', NULL, NULL, NULL, '0'),
(94, 'Customer', '9161673', '150', '1950.51', '2023-08-24 13:52:00', NULL, NULL, NULL, '0'),
(95, 'Customer', '0725887269', '500', '265.51', '2023-08-25 09:47:22', NULL, NULL, NULL, '0'),
(96, 'Customer', '0746503278', '100', '737.51', '2023-08-26 16:16:16', NULL, NULL, NULL, '0'),
(97, 'Customer', '0798905921', '75', '652.51', '2023-08-26 17:15:12', NULL, NULL, NULL, '0'),
(98, 'Customer', '0725887269', '300', '1167.01', '2023-08-26 19:54:54', NULL, NULL, NULL, '0'),
(99, 'Damaris Wanjiru', '0746503278', '4827', '65.25', '2023-08-27 09:20:09', NULL, NULL, NULL, '0'),
(100, 'Customer', '0795880582', '400', '3457.65', '2023-09-02 19:30:39', NULL, NULL, NULL, '0'),
(101, 'Damaris Wanjiru', '0746503278', '1435', '4814.65', '2023-09-02 20:32:40', NULL, NULL, NULL, '0'),
(102, 'Customer', '0746503278', '5000', '16.85', '2023-09-02 20:44:06', NULL, NULL, NULL, '0'),
(103, 'Customer', '0725887269', '10', '675.85', '2023-09-04 15:17:06', NULL, NULL, NULL, '0'),
(104, 'Customer', '0725887269', '10', '655.85', '2023-09-04 15:25:46', NULL, NULL, NULL, '0'),
(105, 'Customer', '07258872', '10', '645.55', '2023-09-04 16:30:35', NULL, NULL, NULL, '0'),
(106, 'Customer', '07258872', '10', '655.25', '2023-09-04 20:03:15', NULL, NULL, NULL, '0'),
(107, 'Customer', '07258872', '10', '655.25', '2023-09-04 20:15:01', NULL, NULL, NULL, '0'),
(108, 'Customer', '07258872', '10', '655.25', '2023-09-04 20:23:52', NULL, NULL, NULL, '0'),
(109, 'Customer', '0725887', '10', '655.25', '2023-09-04 20:30:25', NULL, NULL, NULL, '0'),
(110, 'Customer', '072588726', '11', '654.25', '2023-09-04 20:35:02', NULL, NULL, NULL, '0'),
(111, 'Customer', '07258872', '10', '655.25', '2023-09-04 20:37:10', NULL, NULL, NULL, '0'),
(112, 'Customer', '0725887269', '10', '655.25', '2023-09-04 20:39:10', NULL, NULL, NULL, '0'),
(113, 'Customer', '0725887269', '10', '635.25', '2023-09-04 20:41:33', NULL, NULL, NULL, '0'),
(114, 'Customer', '247247', '10', '654.65', '2023-09-04 22:58:27', NULL, NULL, NULL, '0'),
(115, 'Customer', '247247', '10', '634.65', '2023-09-05 08:17:37', NULL, NULL, NULL, '0'),
(116, 'Customer', '0430190937023', '5498', '5619.85', '2023-09-06 11:08:06', NULL, NULL, NULL, '0'),
(117, 'Customer', '072588726', '11', '0.85', '2023-09-06 11:42:36', NULL, NULL, NULL, '0'),
(118, 'Customer', '072588726', '10', '1.85', '2023-09-06 11:48:46', NULL, NULL, NULL, '0'),
(119, 'Customer', '072566', '11', '20.25', '2023-09-06 12:11:09', '254725887269', '3113e07c-4cda-43fe-b4b7-716b0b996f4b', NULL, '0'),
(120, 'Customer', '072566', '11', '20.25', '2023-09-06 15:01:46', '254725887269', '4b83bb8d-de6e-44b8-81c6-9e4b40baf720', NULL, '0'),
(121, 'Customer', '072566', '11', '311.25', '2023-09-06 15:27:51', '254725887269', 'd6bb1158-2df8-43bb-83d5-9fefdadd8c57', NULL, '0'),
(122, 'Customer', '072566', '11', '408.25', '2023-09-06 15:33:07', '254725887269', 'dedb386a-a0fd-44d4-83af-ef64aa407da6', NULL, '0'),
(123, 'Customer', '072566', '11', '387.25', '2023-09-06 15:33:52', '254725887269', '3ddaad84-6828-4bce-82e2-3608ea090645', NULL, '0'),
(124, 'Customer', '072566', '11', '387.25', '2023-09-06 15:34:26', '254725887269', 'a1862d7a-c2de-4267-9013-7f5fac55f94f', NULL, '0'),
(125, 'Customer', '072566', '11', '408.25', '2023-09-06 15:35:25', '254725887269', '47091d58-abc2-43aa-9bc4-53912c566832', NULL, '0'),
(126, 'Customer', '072566', '11', '408.25', '2023-09-06 15:36:46', '254725887269', '6cd05a04-63d7-4d5b-97b1-dc9f409c9f06', NULL, '0'),
(127, 'Customer', '0725887269', '11', '368.25', '2023-09-06 15:47:30', '254725887269', 'c1f5e4f9-baee-4f3c-b9cf-41a79109b880', NULL, '0'),
(128, 'Customer', '0725887269', '3', '355.25', '2023-09-06 16:00:11', '', '534972fb-1b2e-4c13-a5bd-6f121f740c61', 'Pending', '1'),
(129, 'Customer', '0725887269', '8', '350.25', '2023-09-06 16:01:30', '254725887269', '0d54e3b7-168a-41a9-9919-af6c5d86d00a', 'Pending', '1'),
(130, 'Customer', '0725887269', '7', '351.25', '2023-09-06 16:07:46', '254725887269', '2f0b8b57-a0e6-4ad0-813e-47122d7c3d90', 'Pending', '1'),
(131, 'Customer', '0725887269', '3', '355.25', '2023-09-06 16:10:33', '254725887269', 'da81a022-d52f-40ba-a89c-b98fe34ef965', 'Pending', '1'),
(132, 'Customer', '0725887269', '10', '348.25', '2023-09-06 16:14:48', '254725887269', '9a692fe5-3099-4bc6-a1c9-1b7944166a79', 'Pending', '1'),
(133, 'Customer', '0725887269', '1', '337.25', '2023-09-06 16:18:26', '254725887269', '0a85471b-ebe4-4e08-9b5d-b07c1601e463', 'Pending', '1'),
(134, 'Customer', '0725887269', '5', '318.25', '2023-09-06 16:42:03', '254725887269', '63939d18-907a-4dbb-a2f4-77a535d3052e', NULL, '0'),
(135, 'Customer', '0725887269', '5', '318.25', '2023-09-06 16:43:03', '254725887269', '7ec0d79b-c746-43bf-85ed-361748b8ba61', NULL, '0'),
(136, 'Customer', '0725887269', '10', '313.25', '2023-09-06 16:47:40', '254725887269', '9119c0da-7c2b-4cfa-aec2-68e9a4a0cc4f', 'Successful', '1'),
(137, 'Customer', '0725887269', '5', '298.25', '2023-09-06 16:52:25', '254725887269', '2caa7761-e502-4a3c-aa6a-ee821cc9152a', 'Unsuccessful', '1'),
(138, 'Excel Tech', '0725887269', '1', '3755.99', '2023-09-29 09:18:41', '254725887269', 'b18fed1f-6e5b-413c-9522-4aa120ffc096', '', '1'),
(139, 'demo', '0725887269', '10', '3746.99', '2023-09-29 09:25:06', '254725887269', '0c8887b0-9107-483a-b94a-5de06f900aac', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transfers`
--

CREATE TABLE `mpesa_transfers` (
  `s_no` int(11) NOT NULL,
  `file_id` text NOT NULL,
  `tracking_id` text NOT NULL,
  `status` text NOT NULL,
  `status_code` text NOT NULL,
  `transaction_id` text NOT NULL,
  `transaction_status` text NOT NULL,
  `transaction_status_code` text NOT NULL,
  `provider` text NOT NULL,
  `bank_code` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `account` text DEFAULT NULL,
  `account_type` text DEFAULT NULL,
  `account_reference` text DEFAULT NULL,
  `provider_reference` text DEFAULT NULL,
  `provider_account_name` text DEFAULT NULL,
  `amount` text NOT NULL,
  `charge` text DEFAULT NULL,
  `narrative` text DEFAULT NULL,
  `failed_amount` text DEFAULT NULL,
  `wallet_available_balance` text NOT NULL,
  `updated_at` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mpesa_transfers`
--

INSERT INTO `mpesa_transfers` (`s_no`, `file_id`, `tracking_id`, `status`, `status_code`, `transaction_id`, `transaction_status`, `transaction_status_code`, `provider`, `bank_code`, `name`, `account`, `account_type`, `account_reference`, `provider_reference`, `provider_account_name`, `amount`, `charge`, `narrative`, `failed_amount`, `wallet_available_balance`, `updated_at`) VALUES
(8, 'KZ55ER0', 'dd5cab5f-fed7-44ff-9825-a8c0ff7b49c5', 'Completed', 'BC100', 'KO6D9J0', 'Successful', 'TS100', 'MPESA-B2B', NULL, 'Customer', '247247', 'PayBill', '0340161447990', 'RI58X8JLFW', '  Equity Paybill Account', '10.00', '10.00', 'test 05 September', '0', '614.65', '2023-09-05T08:17:58.051603+03:00'),
(9, 'Y7ZZBLK', '49c343d8-dee1-47bf-8c8a-50f4ea61d992', 'Completed', 'BC100', '0WX3Q4K', 'Unsuccessful', 'TF106', 'MPESA-B2C', NULL, NULL, '254725887269', NULL, NULL, 'RI56XH74HS', NULL, '1.00', '10.00', 'Excel Tech', '1.00', '624.35', '2023-09-05T09:51:21.361643+03:00'),
(10, '0WXD94K', '9119c0da-7c2b-4cfa-aec2-68e9a4a0cc4f', 'Completed', 'BC100', 'Y6XEBD0', 'Successful', 'TS100', 'MPESA-B2C', NULL, NULL, '254725887269', NULL, NULL, 'RI652TDHW7', '  TIMOTHY NJOROGE MBURU', '10.00', '10.00', 'Excel Tech', '0', '313.25', '2023-09-06T16:47:59.019248+03:00'),
(11, 'YD3Q730', '2caa7761-e502-4a3c-aa6a-ee821cc9152a', 'Completed', 'BC100', '0LNELEY', 'Unsuccessful', 'TF106', 'MPESA-B2C', NULL, NULL, '254725887269', NULL, NULL, 'RI682TX3BU', NULL, '5.00', '10.00', 'Excel Tech', '5.00', '313.25', '2023-09-06T16:52:37.905018+03:00');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `s_no` int(11) NOT NULL,
  `offer_name` text NOT NULL,
  `offer_image_poster` text NOT NULL,
  `start_date` text NOT NULL,
  `end_date` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`s_no`, `offer_name`, `offer_image_poster`, `start_date`, `end_date`, `status`) VALUES
(1, 'Offer of July 2023', 'fileStore/Salon Offer thru July 2023.jpg', '2023-07-01', '2023-07-31', 'Stopped 2023-08-16');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_number` int(11) NOT NULL,
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
  `location` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_number`, `orderTime`, `custName`, `email`, `phone`, `product`, `quantity`, `delivered`, `country`, `postal_address`, `postal_code`, `location`) VALUES
(1, '2023-06-03 07:01:00', 'Jasmine Gibb', 'jasgibb1@gmail.com', '9492930072', 'pd', '2', 'Yes', '', '', '', ''),
(2, '2023-06-03 07:01:30', 'Jasmine Gibb', 'jasgibb1@gmail.com', '9492930072', 'pd', '2', 'No', '', '', '', ''),
(3, '2023-08-31 22:49:57', 'Katy Kitkat', 'info@essentialtech.site', '25478546**', 'System', '1', 'No', 'Kenya', '9384', '9000', 'Nairobi');

-- --------------------------------------------------------

--
-- Table structure for table `otpQ`
--

CREATE TABLE `otpQ` (
  `s_no` int(11) NOT NULL,
  `phone` text NOT NULL,
  `otpHash` text NOT NULL,
  `dateInitiated` text NOT NULL,
  `status` text DEFAULT NULL,
  `dateDelivered` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `otpQ`
--

INSERT INTO `otpQ` (`s_no`, `phone`, `otpHash`, `dateInitiated`, `status`, `dateDelivered`) VALUES
(20, '254725887269', '5yPE32yjB0Lp5eGLg4EwVAo6F0aVqeO9xw4tCgIssuYjPrM7D3NUQVSfdJBteaGKAMhYY9cL7AhJn1KPwqm2NVlLhaw3h5DWEYsUowqswVrKwazsmBSSrUo2hLM7aZBF', '2023-09-28 09-35-24', NULL, NULL),
(21, '254725887269', 'qxe15K7Z8skeeLsGiO1kq/uLWdVHoOjNHVN91D7ozGKP4c7vXy2T1awi8QuELEl1UGJoNQq71ZQUtkJWGqWvFwGsDqTI2PQipPhhw7yING1x2It/+/lMLs+NDjlBjABI', '2023-09-28 10-36-11', NULL, NULL),
(22, '254725887269', 'c90gJHIeszAeQr46VR8thymEMQIAsoA6pclUniLDPtV2JygJ0qpRJG7L7mBCThony28JbgEJOQhzM269AwDBuxKKJc4hPcliuwOUwS7qQaQ7rqPM00P6Uia2IfoHJg9q', '2023-09-28 10-47-16', NULL, NULL),
(23, '254725887269', '63rVJf2B8R9WRH+STUM5IIhx9irKBue2pZ8dEdDQjhEKlYj/GVCj1rB2T/Di5gr5H2zU2KgQBRk+pFlYZBM9+4g8LSUMIaGHW3G5LpSWiZVwMi9GBXTGVNq1vz+5s9SB', '2023-09-28 10-49-52', NULL, NULL),
(28, '254725887269', 'ASjpTP2zVHbJ26ex4lK8hPkcu3ATqoQxHHniWDWFWnV8qE6Y4K2KJC/R0UJOYFNmEBiWymOEJJj5ZSA4InkYITVY9bTUUlBuHYlrV1xe7lUGXYeraDO+c88jI51d27yv', '2023-09-28 11-08-12', NULL, NULL),
(37, '254725887269', '7BhFqi7f6VUZ1ObweUv39aIvl2pSDuhcfZoC0VH2nsGWzpuOj1jgHiAaNTEXgZZugetEy4jXILADF24EMau87kX1zcXNlsklgBnHWr6BRmJRgtD1FErbq45d4MzxSnoR', '2023-09-28 15:30:31', NULL, NULL),
(38, '254725887269', 'Bjvphi7p/s05xrCXDegVS+o0BnOGoD6668HD6Lnol1+OA2EIDVPH50HZ/NcpRlZFnMpdDSO28rOzbpVVBbRk2kFw/Kj8QbBQ7Twg3TscHWquYPpiC7gMRtXh+I2rdd4x', '2023-09-29 08:37:55', NULL, NULL),
(39, '254725887269', 'J8l4WPbXBayk6EJioFgNzZSYQ9pUE3RvwSZtfnTY6V73aslm9LmnhGHpBhjJiZYGJeoTiUq38AHONtpi5ohdfBCxcgh1tLbAANbGdNQYO1p4yXk9BLxyavwSM2jdBtX/', '2023-09-29 08:40:37', NULL, NULL),
(40, '254725887269', 'VaQOUHMaLN5/3LR0QqmcGz0iheHAPDPdMkybabbenEdzbNCHSdfZJ/nRNxnm6gTa6XDcaoGSJ7bYePrusfhviRfGnjpr0fR+GibGLXs8tSWLHCJOn2/wflzTR/NoiIge', '2023-09-29 08:42:33', NULL, NULL),
(41, '254725887269', 'c/ricElNDSKNE8rNzB78kedweO2XuGOi+Ttgu5zvmWUN/7em8xuyNaX6Wl2a2LaYtG7yONPUvben9XnpyrD1Ldd6po8QuEm08wKcjP51McsK4KR625x/biT8GvJ11fOZ', '2023-09-29 09:13:51', NULL, NULL),
(42, '254725887269', '9Q0wLRdmskEvncAXGYYhxrMKjNRtgCzessezQ3hcDBUg5OpegnHi5zJAGljPCTCed0Ugs0D5clnYOxnNvkk6D4aQMXiYQzWMWzT38SUThkSTbg5MpPetI42kijzTh2ft', '2023-09-29 09:16:41', NULL, NULL),
(43, '254725887269', 'NkplKh78jHziDoOJ3gzqZk99TWYZZA2bTmtYQcRu8lVtneBsdm6DAx9ViO9W+1SzB6BUT0hPi21SmJ+O3UNyN9F4aKiBXbNPp9O4F+zi/x9o9lMXWgFNA/J6ByMuWKnM', '2023-09-29 09:18:19', NULL, NULL),
(44, '254725887269', 'WRgHAC0PCwRJy9092LBliZIBw9LwSaxnIsIuu3TRg+XK/1/2oa1sOkFOZ27lpk1q3oGaihkUILdgoJQCzWzvAFZ+nhcSMCGXY9lmVStQgbvZ+4JfRGd0wMTDVVyulULh', '2023-09-29 09:24:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `services` text NOT NULL,
  `amount` int(11) NOT NULL,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `date` text NOT NULL,
  `commission_paid` text NOT NULL DEFAULT 'Not Paid',
  `payment_mode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`s_no`, `name`, `phone`, `services`, `amount`, `staff_name`, `staff_phone`, `date`, `commission_paid`, `payment_mode`) VALUES
(1, 'Hasedi nyambura', '0700088***', 'Tips', 7500, 'Dante Koros', '0700166***', '2023-08-10 17:24:28', 'Paid', 'Mpesa Online'),
(2, 'Melita vumba', '0700166***', 'Overlay', 2000, 'Dante Koros', '0700166***', '2023-08-10 18:15:34', 'Paid', 'KCB Paybill'),
(3, 'Patrice', '0798672***', 'Overlay and gel', 4500, 'Dante Koros', '0700166***', '2023-08-15 12:28:33', 'Paid', 'KCB Paybill'),
(4, 'Jennifer nyagate', '0725230***', 'Overlay and gel', 5500, 'Dante Koros', '0700166***', '2023-08-09 18:56:46', 'Paid', 'Mpesa Online'),
(5, 'Dorite', '0798611***', 'Stickons ', 5000, 'Dante Koros', '0700166***', '2023-08-01 18:58:51', 'Paid', 'Mpesa Online'),
(6, 'Brigit', '0763571***', 'French tips', 5500, 'Dante Koros', '0700166***', '2023-08-10 20:33:45', 'Not Paid', 'Mpesa Online'),
(7, 'Lizzie', '0724770***', 'Pedi Gel', 3000, 'Dante Koros', '0700166***', '2023-07-12 20:43:27', 'Not Paid', 'KCB Paybill'),
(8, 'Eunice ndugu', '0767268***', 'Pedi gel', 3000, 'Dante Koros', '0700166***', '2023-07-13 20:04:26', 'Not Paid', 'Mpesa Online'),
(9, 'Martha ngulio', '0716904***', ' Pedi gel', 3000, 'Dante Koros', '0700166***', '2023-07-13 20:05:58', 'Not Paid', 'Mpesa Online'),
(10, 'Raiyu njaramba', '0712339***', 'Eyebrows and overlay', 6000, 'Dante Koros', '0700166***', '2023-07-23 20:08:19', 'Not Paid', 'Mpesa Online'),
(11, 'Wazeri', '0790542***', 'Eyebrows shaping and overlay', 7500, 'Dante Koros', '0700166***', '2023-07-03 20:10:33', 'Not Paid', 'Mpesa Online'),
(12, 'Tammy', '0725887269', 'gel', 3000, 'demo', '0725887269', '2023-07-10 16:43:07', 'Paid', 'KCB Paybill'),
(13, 'Sylvester karungi', '00785777', 'Booking for Bridal makeup', 2500, 'LFH Booking', '0787654***', '2023-08-17 09:33:19', 'Not Paid', 'Mpesa Online');

-- --------------------------------------------------------

--
-- Table structure for table `payments2`
--

CREATE TABLE `payments2` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `services` text NOT NULL,
  `amount` int(11) NOT NULL,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `date` text NOT NULL,
  `commission_paid` text NOT NULL DEFAULT 'Not Paid',
  `payment_mode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `cashIn` int(11) NOT NULL,
  `cashOut` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `percent` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `performance`
--

INSERT INTO `performance` (`cashIn`, `cashOut`, `income`, `percent`, `date`) VALUES
(55030, 96469, -41439, '-75.302562238779%', '2023-09-29 12:00:55');

-- --------------------------------------------------------

--
-- Table structure for table `performanceHistory`
--

CREATE TABLE `performanceHistory` (
  `cashIn` int(11) NOT NULL,
  `cashOut` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `percent` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `performanceHistory`
--

INSERT INTO `performanceHistory` (`cashIn`, `cashOut`, `income`, `percent`, `date`) VALUES
(24800, 1000, 23800, '95.967741935484%', '2023-05-27 17:49:27'),
(24800, 1000, 23800, '95.967741935484%', '2023-05-27 17:55:29'),
(24800, 1000, 23800, '95.967741935484%', '2023-05-27 17:56:02'),
(24800, 3460, 21340, '86.048387096774%', '2023-05-27 18:06:17'),
(24800, 7780, 17020, '68.629032258065%', '2023-05-27 18:20:14'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-04 20:03:58'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-04 21:18:39'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:08:35'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:09:45'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:09:49'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:10:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:10:05'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:10:30'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:11:27'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:12:58'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:24:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:24:49'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:24:53'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:24:59'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:25:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:25:38'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:25:45'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:25:48'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:37:21'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:38:30'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:38:39'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:44:18'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:44:26'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:45:50'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:01'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:02'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:02'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:04'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:04'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:46:04'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:50:48'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:50:53'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:50:56'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:51:24'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:51:36'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:51:45'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:51:48'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:55:33'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 11:58:43'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:09:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:11:21'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:13:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:14:09'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:16:46'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:17:53'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:21:37'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:21:56'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:21:58'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:21:59'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:22:10'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:22:16'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:41:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:43:02'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:43:20'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:50:43'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:55:04'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:56:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:58:33'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 13:58:50'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:01:34'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:02:32'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:06:49'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:07:09'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:09:08'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:09:47'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:10:09'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:10:12'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:13:16'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:15:30'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:15:50'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:16:12'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:17:33'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:18:36'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:20:32'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:21:00'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:21:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:27:58'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:30:50'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:31:25'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:31:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:33:02'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:33:02'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:33:37'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:33:58'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:34:26'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:35:19'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:36:24'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:37:10'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:37:25'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:37:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:38:21'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:47:02'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:47:59'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:48:26'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:49:18'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:53:28'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 14:53:54'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 16:36:33'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 18:50:01'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 19:16:11'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 19:45:19'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 20:19:45'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-05 21:59:21'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-07 16:56:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-09 15:30:49'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-09 15:31:32'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-12 12:00:46'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-12 16:46:50'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-12 19:03:20'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:28:01'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:28:44'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:29:26'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:30:54'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:31:34'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:33:00'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:33:40'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 16:34:03'),
(24800, 7780, 17020, '68.629032258065%', '2023-06-14 18:11:35'),
(25550, 7780, 17770, '69.549902152642%', '2023-06-15 00:57:27'),
(25550, 7780, 17770, '69.549902152642%', '2023-06-15 00:57:28'),
(25550, 7780, 17770, '69.549902152642%', '2023-06-15 00:59:03'),
(25550, 7780, 17770, '69.549902152642%', '2023-06-15 00:59:14'),
(25550, 7780, 17770, '69.549902152642%', '2023-06-15 00:59:46'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 09:43:58'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 09:45:29'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 09:46:21'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 09:46:41'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 10:27:02'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 10:27:15'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 10:29:44'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 15:05:03'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 15:07:13'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 15:07:19'),
(25850, 7780, 18070, '69.903288201161%', '2023-06-16 15:09:19'),
(26050, 7780, 18270, '70.134357005758%', '2023-06-16 21:40:27'),
(26050, 7780, 18270, '70.134357005758%', '2023-06-16 22:01:46'),
(25910, 7801, 18109, '69.891933616364%', '2023-06-16 22:35:55'),
(26952, 8945, 18007, '66.811368358563%', '2023-06-17 22:41:23'),
(26750, 8945, 17805, '66.560747663551%', '2023-06-20 13:16:26'),
(27750, 9069, 18681, '67.318918918919%', '2023-06-22 22:43:37'),
(28050, 16323, 11727, '41.807486631016%', '2023-06-24 15:59:35'),
(31600, 17351, 14249, '45.091772151899%', '2023-06-29 15:22:37'),
(33400, 17351, 16049, '48.050898203593%', '2023-07-01 19:03:24'),
(33400, 17351, 16049, '48.050898203593%', '2023-07-01 19:05:28'),
(33400, 17401, 15999, '47.90119760479%', '2023-07-01 19:30:30'),
(33400, 17401, 15999, '47.90119760479%', '2023-07-01 19:31:21'),
(33400, 17401, 15999, '47.90119760479%', '2023-07-01 19:32:27'),
(34650, 20177, 14473, '41.76911976912%', '2023-07-04 20:36:41'),
(35250, 22425, 12825, '36.382978723404%', '2023-07-06 15:57:37'),
(35500, 22425, 13075, '36.830985915493%', '2023-07-07 08:51:02'),
(35500, 22425, 13075, '36.830985915493%', '2023-07-07 08:52:28'),
(35500, 22425, 13075, '36.830985915493%', '2023-07-07 08:55:01'),
(36400, 20185, 16215, '44.546703296703%', '2023-07-08 20:13:23'),
(36400, 20185, 16215, '44.546703296703%', '2023-07-09 12:23:52'),
(36400, 20185, 16215, '44.546703296703%', '2023-07-09 12:26:15'),
(36400, 20185, 16215, '44.546703296703%', '2023-07-09 12:28:40'),
(36400, 20185, 16215, '44.546703296703%', '2023-07-09 12:31:10'),
(36400, 20185, 16215, '44.546703296703%', '2023-07-11 21:32:48'),
(37750, 20185, 17565, '46.529801324503%', '2023-07-13 13:39:54'),
(38500, 20315, 18185, '47.233766233766%', '2023-07-15 10:14:22'),
(40300, 20845, 19455, '48.275434243176%', '2023-07-15 22:23:46'),
(40300, 20845, 19455, '48.275434243176%', '2023-07-15 22:32:21'),
(40300, 20845, 19455, '48.275434243176%', '2023-07-16 00:10:11'),
(43650, 20856, 22794, '52.219931271478%', '2023-07-18 00:45:31'),
(43650, 20856, 22794, '52.219931271478%', '2023-07-18 00:50:04'),
(43650, 20856, 22794, '52.219931271478%', '2023-07-18 11:11:13'),
(43950, 21886, 22064, '50.202502844141%', '2023-07-19 16:57:21'),
(45500, 22626, 22874, '50.272527472527%', '2023-07-22 10:32:06'),
(45500, 22626, 22874, '50.272527472527%', '2023-07-22 10:44:18'),
(45620, 22877, 22743, '49.853134590092%', '2023-07-22 18:01:23'),
(45620, 24458, 21162, '46.387549320473%', '2023-07-24 13:23:13'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 13:23:48'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 13:51:22'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 13:58:19'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:00:05'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:01:59'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:03:08'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:03:27'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:04:44'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:06:18'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:07:42'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:08:04'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:08:36'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:10:16'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:12:08'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:12:40'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:16:14'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:21:03'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:23:03'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:25:51'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:26:59'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:33:02'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:39:59'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:40:22'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:41:32'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:49:01'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:49:33'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:49:35'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:49:44'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:52:14'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:57:39'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:58:45'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 14:58:54'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 15:05:41'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 15:05:46'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 15:06:08'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 15:06:40'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 15:07:49'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 15:59:48'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 16:06:40'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 16:06:59'),
(45590, 24458, 21132, '46.352270234701%', '2023-07-24 16:10:26'),
(45590, 24488, 21102, '46.286466330336%', '2023-07-24 16:33:04'),
(45590, 24366, 21224, '46.554068874753%', '2023-07-24 17:50:11'),
(45590, 24366, 21224, '46.554068874753%', '2023-07-24 22:29:25'),
(46590, 24366, 22224, '47.701223438506%', '2023-07-27 09:45:16'),
(46590, 24366, 22224, '47.701223438506%', '2023-07-27 09:46:00'),
(46590, 24366, 22224, '47.701223438506%', '2023-07-27 10:04:48'),
(46590, 24366, 22224, '47.701223438506%', '2023-07-27 10:05:40'),
(46590, 24366, 22224, '47.701223438506%', '2023-07-27 11:08:49'),
(47290, 24366, 22924, '48.475364770565%', '2023-07-28 11:51:21'),
(48590, 24366, 24224, '49.853879399053%', '2023-07-28 17:44:25'),
(49390, 24366, 25024, '50.666126746305%', '2023-07-28 21:48:39'),
(49390, 24366, 25024, '50.666126746305%', '2023-07-29 12:11:42'),
(50390, 24946, 25444, '50.494145663822%', '2023-07-29 16:18:05'),
(50390, 24946, 25444, '50.494145663822%', '2023-07-29 16:20:40'),
(50390, 24946, 25444, '50.494145663822%', '2023-07-29 18:14:48'),
(50890, 24946, 25944, '50.980546276282%', '2023-07-29 19:44:36'),
(50890, 24948, 25942, '50.976616231087%', '2023-07-29 20:55:30'),
(50890, 24948, 25942, '50.976616231087%', '2023-07-29 21:01:20'),
(51140, 25268, 25872, '50.590535784122%', '2023-07-30 15:18:19'),
(51140, 25268, 25872, '50.590535784122%', '2023-07-30 19:09:43'),
(51140, 25268, 25872, '50.590535784122%', '2023-07-31 17:44:58'),
(51990, 25268, 26722, '51.398345835738%', '2023-08-01 14:05:35'),
(52340, 25268, 27072, '51.723347344287%', '2023-08-01 17:26:07'),
(52340, 25268, 27072, '51.723347344287%', '2023-08-01 17:38:37'),
(52340, 25268, 27072, '51.723347344287%', '2023-08-02 00:27:47'),
(54240, 28373, 25867, '47.689896755162%', '2023-08-04 23:58:47'),
(56000, 28573, 27427, '48.976785714286%', '2023-08-05 18:39:03'),
(56000, 29083, 26917, '48.066071428571%', '2023-08-05 20:01:46'),
(57300, 32494, 24806, '43.291448516579%', '2023-08-05 23:21:42'),
(61550, 32644, 28906, '46.963444354184%', '2023-08-08 13:05:40'),
(61550, 32644, 28906, '46.963444354184%', '2023-08-08 14:50:46'),
(63450, 33594, 29856, '47.054373522459%', '2023-08-09 17:06:48'),
(61950, 33594, 28356, '45.772397094431%', '2023-08-09 17:07:57'),
(61950, 33594, 28356, '45.772397094431%', '2023-08-09 17:09:25'),
(62950, 33594, 29356, '46.633836378078%', '2023-08-09 22:28:59'),
(65750, 33594, 32156, '48.906463878327%', '2023-08-10 16:34:14'),
(66500, 33594, 32906, '49.482706766917%', '2023-08-10 18:13:20'),
(68200, 38544, 29656, '43.483870967742%', '2023-08-12 05:14:33'),
(68200, 38544, 29656, '43.483870967742%', '2023-08-12 05:22:14'),
(69050, 39944, 29106, '42.152063721941%', '2023-08-12 20:43:51'),
(5250, 9643, -4393, '-83.67619047619%', '2023-08-14 13:22:32'),
(5250, 9643, -4393, '-83.67619047619%', '2023-08-14 13:25:17'),
(5250, 9643, -4393, '-83.67619047619%', '2023-08-14 13:26:54'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-14 22:09:19'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 08:16:47'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 08:16:53'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 08:18:46'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 09:57:29'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:25:13'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:27:25'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:28:45'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:29:13'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:29:32'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:31:23'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:32:25'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:33:16'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:33:19'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:33:22'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:33:50'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:34:59'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:35:03'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:35:09'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:35:12'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:36:12'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:36:22'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:36:44'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:38:03'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:38:19'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:40:49'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:41:14'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:41:17'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:41:35'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 10:55:54'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 14:15:10'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 14:27:29'),
(5550, 9643, -4093, '-73.747747747748%', '2023-08-16 15:04:27'),
(5550, 96430, -90880, '-1637.4774774775%', '2023-08-16 18:57:10'),
(5550, 96430, -90880, '-1637.4774774775%', '2023-08-16 20:32:20'),
(55500, 96430, -40930, '-73.747747747748%', '2023-08-16 20:35:48'),
(55500, 96430, -40930, '-73.747747747748%', '2023-08-16 20:37:53'),
(55500, 96430, -40930, '-73.747747747748%', '2023-08-16 20:38:58'),
(55500, 96430, -40930, '-73.747747747748%', '2023-08-16 20:40:59'),
(55500, 96430, -40930, '-73.747747747748%', '2023-08-16 20:42:19'),
(60500, 96430, -35930, '-59.388429752066%', '2023-08-17 17:12:36'),
(58000, 96458, -38458, '-66.306896551724%', '2023-09-20 21:58:06'),
(58000, 96458, -38458, '-66.306896551724%', '2023-09-20 21:58:50'),
(58000, 96458, -38458, '-66.306896551724%', '2023-09-20 21:58:57'),
(58000, 96458, -38458, '-66.306896551724%', '2023-09-20 21:59:03'),
(58000, 96458, -38458, '-66.306896551724%', '2023-09-20 21:59:16'),
(55030, 96469, -41439, '-75.302562238779%', '2023-09-29 12:00:55');

-- --------------------------------------------------------

--
-- Table structure for table `recruit`
--

CREATE TABLE `recruit` (
  `staff_no` int(11) NOT NULL,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `staff_email` text NOT NULL,
  `joinDate` text NOT NULL,
  `skills` text NOT NULL,
  `cv` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurrentExp`
--

CREATE TABLE `recurrentExp` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `amount` text NOT NULL,
  `date` text NOT NULL,
  `currentTotal` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `recurrentExp`
--

INSERT INTO `recurrentExp` (`s_no`, `name`, `amount`, `date`, `currentTotal`) VALUES
(1, 'Rent', '5000', '2023-07-24 19:28:21', '5000'),
(2, 'Wifi', '1000', '2023-07-24 19:28:43', '6000'),
(3, 'Electricity', '500', '2023-07-24 19:30:28', '6500'),
(4, 'Salary', '5000', '2023-07-24 19:30:41', '11500'),
(5, 'Misc Expenses', '3000', '2023-07-27 10:04:06', '14500');

-- --------------------------------------------------------

--
-- Table structure for table `sentSMS`
--

CREATE TABLE `sentSMS` (
  `s_no` int(11) NOT NULL,
  `recipient` text NOT NULL,
  `message` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sentSMS`
--

INSERT INTO `sentSMS` (`s_no`, `recipient`, `message`, `date`) VALUES
(1, '+254725887269', 'Hello Lourice, thank you for your interest in our services. Use code: a3ebec for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 13:47:04'),
(2, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: abe7e5 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 13:50:14'),
(3, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: aebd2e for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 13:53:51'),
(4, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 041386 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 13:56:35'),
(5, '+254725882769', 'Dear Tim, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.30000. Loyalty Points now at 300. See you again. www.essentialtech.site', '2023-08-22 17:27:34'),
(6, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 608472 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 14:33:31'),
(7, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 683914 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 14:38:10'),
(8, '254725887269', 'sds\nLourice Beauty Parlour. www.lfhcompany.site', '2023-08-22 15:00:42'),
(9, '+254725882769', 'Dear Tim, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.30000. Loyalty Points now at 300. See you again. www.essentialtech.site', '2023-08-22 18:32:48'),
(10, '+254718509240', 'Hello Sweet savour perfumes , thank you for your interest in our services. Use code: 574892 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-22 16:34:02'),
(11, '+254254', 'Hello Cute, thank you for your interest in our services. Use code: 637289 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-24 13:08:27'),
(12, '+254710422071', 'Hello Oneclin , thank you for your interest in our services. Use code: 213480 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-24 16:20:05'),
(13, '+254733440443', 'Hello Fashion, thank you for your interest in our services. Use code: 173650 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-26 11:33:43'),
(14, '+254725887269', 'Hello Smart, thank you for your interest in our services. Use code: 435710 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-08-28 16:53:42'),
(15, '', '', '2023-08-29 12:30:39'),
(16, '', '', '2023-08-29 12:32:03'),
(17, '', '', '2023-08-29 12:35:56'),
(18, '', '', '2023-08-29 12:37:40'),
(19, '', '', '2023-08-29 12:37:45'),
(20, '', '', '2023-08-29 12:37:48'),
(21, '', '', '2023-08-29 12:37:49'),
(22, '', '', '2023-08-29 12:37:49'),
(23, '', '', '2023-08-29 12:37:50'),
(24, '', '', '2023-08-29 12:37:51'),
(25, '', '', '2023-08-29 12:37:52'),
(26, '', '', '2023-08-29 12:37:52'),
(27, '', '', '2023-08-29 12:37:53'),
(28, '', '', '2023-08-29 12:37:54'),
(29, '', '', '2023-08-29 12:37:55'),
(30, '', '', '2023-08-29 12:37:55'),
(31, '', '', '2023-08-29 12:37:56'),
(32, '', '', '2023-08-29 12:37:57'),
(33, '', '', '2023-08-29 12:37:58'),
(34, '', '', '2023-08-29 12:47:54'),
(35, '254725887269', 'Testing', '2023-08-30 11:30:36'),
(36, '254725887269', 'Testing', '2023-08-30 11:58:07'),
(37, '254725887269', 'Testing1', '2023-08-30 12:01:15'),
(38, '254725887269', 'Testing', '2023-08-30 12:17:12'),
(39, '254725887269', 'LFH', '2023-08-30 12:52:36'),
(40, '254725887269', 'message', '2023-08-30 12:53:15'),
(41, '254725887269', 'LFH test', '2023-08-30 12:54:01'),
(42, '254725887269', 'LFH test', '2023-08-30 12:55:55'),
(43, '254725887269', 'Postman', '2023-08-30 12:59:36'),
(44, '254725887269', 'LFH testing with Auth', '2023-08-30 14:27:08'),
(45, '254725887269', 'LFH testing with Auth', '2023-08-30 14:33:04'),
(46, '254725887269', 'LFH testing with Auth', '2023-08-30 14:35:42'),
(47, '254725887269', 'LFH testing with Auth and user', '2023-08-30 14:35:57'),
(48, '254725887269', 'LFH testing with Auth & user', '2023-08-30 14:37:27'),
(49, '254725887269', 'LFH with Auth & user', '2023-08-30 14:38:58'),
(50, '254725887269', 'LFH with Auth', '2023-08-30 14:39:59'),
(51, '254725887269', 'LFH with Auth', '2023-08-30 14:48:09'),
(52, '254725887269', 'LFH with Auth', '2023-08-30 15:20:35'),
(53, '254725887269', 'LFH with Auth', '2023-08-30 22:15:36'),
(54, '254725887269', 'LFH with Auth', '2023-08-30 22:15:39'),
(55, '254725887269', 'LFH with Auth', '2023-08-30 22:24:05'),
(56, '254725887269', 'LFH with Auth', '2023-08-30 22:25:35'),
(57, '254725887269', 'LFH with Auth', '2023-08-30 22:26:57'),
(58, '254725887269', 'LFH with Auth', '2023-08-30 22:30:25'),
(59, '254725887269', 'LFH with Auth ttt', '2023-08-30 22:30:42'),
(60, '254725887269', 'LFH with Auth ttt', '2023-08-30 22:30:59'),
(61, '254725887269', 'LFH with Auth ttt', '2023-08-30 22:31:46'),
(62, '254725887269', 'LFH with Auth ttt', '2023-08-30 22:32:35'),
(63, '254725887269', 'LFH with Auth ttt', '2023-08-30 22:36:52'),
(64, '254725887269', 'LFH with Auth ttt', '2023-08-30 22:37:54'),
(65, '+254725887269', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-08-30 23:50:56'),
(66, '+254733440443', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-08-31 17:55:11'),
(67, '254725887269', 'LFH with Auth ttt', '2023-08-31 21:59:40'),
(68, '+254725887269', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-01 09:28:47'),
(69, '+254733440443', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-01 09:30:51'),
(70, '+2540098475', 'Dear Tim, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.20000. Loyalty Points now at 250. See you again. www.essentialtech.site', '2023-09-01 20:20:52'),
(71, '254725887269', 'Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 06-09-2023. Transaction reference number RI652TDHW7. Thank you.', '2023-09-06 16:48:03'),
(72, '254725887269', 'Hi, your payment of Kshs.5.00 to 254725887269 is Unsuccessful on 06-09-2023. Transaction reference number RI682TX3BU. Thank you.', '2023-09-06 16:53:02'),
(73, '+254798297323', 'Hello Cute girl , thank you for your interest in our services. Use code: 247193 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-06 22:46:54'),
(74, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 06-09-2023. Transaction reference number RI6444AGR8. Thank you.', '2023-09-06 23:18:04'),
(75, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 06-09-2023. Transaction reference number RI6444BOTM. Thank you.', '2023-09-06 23:19:04'),
(76, '+254733440443', 'Hello Lourice, thank you for your interest in our services. Use code: 137829 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-07 16:55:00'),
(77, '254725887269', 'OTP: 123456', '2023-09-08 16:37:42'),
(78, '254725887269', 'OTP: 123456', '2023-09-08 16:38:14'),
(79, '254725887269', 'OTP: 123456', '2023-09-08 16:39:16'),
(80, '254725887269', 'OTP: 123456', '2023-09-08 16:43:32'),
(81, '254725887269', 'OTP: 123456', '2023-09-08 16:50:35'),
(82, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 82Ro6erB5fqmT3arNh8XkqCS6L7nBLOb6TR5cjZZqRLjE+COExt2CAS9+YVSw0wDzMd+tP2SVqh2SBPd7cl+NpTxpwuBKcB/I98brV2DgpGRD9MldOpFIqGccGqAmmpQ for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 08:56:04'),
(83, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 632451 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 08:58:44'),
(84, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 458302 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 08:59:48'),
(85, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 702491 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:04:41'),
(86, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 097618 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:10:09'),
(87, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 103762 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:52:59'),
(88, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 093716 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:55:07'),
(89, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 809625 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:55:21'),
(90, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 965873 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:57:38'),
(91, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 714528 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 09:58:32'),
(92, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 402376 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:01:09'),
(93, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 247638 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:02:52'),
(94, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 196745 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:04:12'),
(95, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 047159 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:05:27'),
(96, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 720894 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:06:17'),
(97, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 926140 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:11:06'),
(98, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 650784 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:11:58'),
(99, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 604958 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:13:23'),
(100, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 135426 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:17:53'),
(101, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 023815 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:34:28'),
(102, '+254725887269', 'Hello Lourice, thank you for your interest in our services. Use code: 679184 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:36:47'),
(103, '+254725887269', 'Hello Lourice, thank you for your interest in our services. Use code: 491567 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:39:31'),
(104, '+254725887269', 'Hello Lourice, thank you for your interest in our services. Use code: 598672 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:39:56'),
(105, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 103726 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:41:55'),
(106, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 845173 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:44:18'),
(107, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 482960 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:44:50'),
(108, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 415296 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-09 10:50:01'),
(109, '+254725887269', 'Hello Excel Tech, thank you for your interest in our services. Use code: 019384 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-10 13:13:15'),
(110, '+254540099212', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-10 18:46:30'),
(111, '+254540099212', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-10 18:55:18'),
(112, '+254540099212', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-10 19:02:02'),
(113, '+254540099212', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-10 19:19:54'),
(114, '+254710422071', 'Dear Peter wano, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.600. Loyalty Points now at 6. See you again. www.essentialtech.site', '2023-09-11 02:34:14'),
(115, '254710422071', 'Hello \nExcel Tech Essentials. www.essentialtech.site', '2023-09-10 23:46:15'),
(116, '+254545887269', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', '2023-09-11 01:42:59'),
(117, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 719256 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-11 12:10:00'),
(118, '+254725887269', 'Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 350764 for each Login to the Demo. Login here www.essentialtech.site/demo', '2023-09-11 12:15:31'),
(119, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS0Z1GFH6. Thank you.', '2023-09-28 17:10:05'),
(120, '', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS1Z246VN. Thank you.', '2023-09-28 17:15:03'),
(121, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS2Z2OA4C. Thank you.', '2023-09-28 17:19:06'),
(122, '254725887269', 'Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS9Z9045L. Thank you.', '2023-09-28 18:05:02'),
(123, '254725887269', 'Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS7Z9L4T9. Thank you.', '2023-09-28 18:09:02'),
(124, '254725887269', 'Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS7ZA5JML. Thank you.', '2023-09-28 18:12:04'),
(125, '254725887269', 'Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS3ZBP04N. Thank you.', '2023-09-28 18:22:03'),
(126, '254725887269', 'Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS8ZC70H2. Thank you.', '2023-09-28 18:26:07'),
(127, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS5ZCHZ1F. Thank you.', '2023-09-28 18:27:05'),
(128, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS1ZDJPOP. Thank you.', '2023-09-28 18:34:04'),
(129, '254725887269', 'Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS2ZEDVCM. Thank you.', '2023-09-28 18:39:02'),
(130, '254725887269', 'Hi, your payment of Kshs. to  is  on 28-09-2023. Transaction reference number . Thank you.', '2023-09-28 18:41:25'),
(131, '254725887269', 'Hi, your payment of Kshs. to  is  on 28-09-2023. Transaction reference number . Thank you.', '2023-09-28 22:19:18'),
(132, '254725887269', 'Hi, your payment of Kshs. to  is  on 29-09-2023. Transaction reference number . Thank you.', '2023-09-29 09:18:53');

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `s_no` int(11) NOT NULL,
  `bizname` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `code` text NOT NULL,
  `username` text NOT NULL DEFAULT 'demo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`s_no`, `bizname`, `email`, `phone`, `code`, `username`) VALUES
(1, 'Excel Tech Essentials', 'info@essentialtech.site', '254725887269', '1TZgp9u32Ecf/NMgVWNnpqt7u7d2rTZwyKITAq7XS70IriSHJO0/w34w5YZHCGKkOGqu+liGjVQGLfUjHOlm6qfCg2EuCUWEyhscKvA16igPH1b/E8VAkqdHSRoL4jCZ', 'demo'),
(2, 'Sweet savour perfumes ', 'njorogeoffice@gmail.com', '254718509240', 'bI8wGQBQhDkFQTkavH86mCojqP639dUfgM5863arKnXStRhdBt7Gb3oxqAP/698IJ177qPFEOy/HsG56A4Nhf9aC2kRqsilteqmYvnQ1cbRGdbNPyNdLX3E1QGYPUjHi', 'demo'),
(3, 'Oneclin ', 'peterwano@gmail.com', '254710422071', 'jVWmjvNaVdu9oH0aySSqvEbOKHDHXjXbZUaXGEb8HZRr3DI9VR5stIzu1LDOMVJmvzAZ1zGO3pNXN9uvSyaegrp+lGkSbdSZsiI0WKsFIxoNuuPDJSoq37JP9G0lYx4Q', 'demo'),
(4, 'Fashion', 'timnmburu1@gmail.com', '254733440443', 'O3q7ahhg6mS4Yl6LfMJkwyqpIYUA0+5IsLi9Zwpj3cabTeZqZLbo+mhnwIGt0zT3VF30ZCs+WBuOc0+2jsXrc24MUhqQjBqsJ+r6K1bhaN8vKuUt1sqgWs98LeUsulkB', 'demo'),
(5, 'Cute girl ', 'ceciliandindah@gmail.com', '254798297323', 'CZvgYJgkS0IuRTm5RvCLyjIgGJp2cSnHnOypjlOLr+KTcvHsWLmp7bxhIFaqEirgvBpPXIxPMSFKrjU4QxG8CjqP2kr0GO614ITQofcC54O0fxaPbtpPSZoOUbY23VNU', 'demo');

-- --------------------------------------------------------

--
-- Table structure for table `smsQ`
--

CREATE TABLE `smsQ` (
  `s_no` int(11) NOT NULL,
  `recipient` text NOT NULL,
  `message` text NOT NULL,
  `sender1` text NOT NULL,
  `sender2` text NOT NULL,
  `dateInitiated` text NOT NULL,
  `dateDelivered` text DEFAULT NULL,
  `status` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `smsQ`
--

INSERT INTO `smsQ` (`s_no`, `recipient`, `message`, `sender1`, `sender2`, `dateInitiated`, `dateDelivered`, `status`) VALUES
(40, '+254725887269', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', 'https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=%2B254725887269&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&msgtype=5&dlr=0', 'https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&shortcode=JuaMobile&mobile=%2B254725887269', '2023-08-30 23:43:15', NULL, NULL),
(46, '+254733440443', 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site', 'https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=%2B254733440443&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&msgtype=5&dlr=0', 'https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&shortcode=JuaMobile&mobile=%2B254733440443', '2023-08-31 17:55:11', NULL, NULL),
(47, '254725887269', 'LFH with Auth ttt', 'https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=LFH+with+Auth+ttt&msgtype=5&dlr=0', 'https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=LFH+with+Auth+ttt&shortcode=JuaMobile&mobile=254725887269', '2023-08-31 21:59:40', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_no` int(11) NOT NULL,
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
  `exited_date` text NOT NULL DEFAULT 'n/a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_no`, `staff_name`, `staff_phone`, `staff_email`, `joinDate`, `ID_front`, `ID_back`, `passport_pic`, `contract`, `status`, `role`, `exit_comment`, `exited_date`) VALUES
(1, 'Kendrick Kamil', '0706921***', 'sedrickkml@gmail.com', '2023-04-01 8:22:23', 'fileStore/2.jpg', 'fileStore/2.jpg', 'fileStore/2.jpg', 'None', 'active', 'admin', 'none', 'n/a'),
(2, 'Desaw Britania', '0727887***', 'dabri234@gmail.com', '2023-06-12', 'fileStore/3.jpg', 'fileStore/3.jpg', 'fileStore/3.jpg', 'None', 'active', 'admin', 'none', 'n/a'),
(3, 'Alice Nyeu', '0740019***', 'alinyeu837@gmail.com', '2023-06-12', 'fileStore/3.jpg', 'fileStore/3.jpg', 'fileStore/5.jpg', 'None', 'exited', 'admin', 'Insubordination, missing work without communication for two days and badattitude ', '2023-07-01'),
(4, 'Dante Koros', '0733440443', 'timnmburu1@gmail.com', '2023-06-22', 'fileStore/3.jpg', 'fileStore/3.jpg', 'fileStore/3.jpg', 'fileStore/3.jpg', 'active', 'admin', '\'none\'', 'n/a'),
(5, 'demo', '0725887269', 'info@essentialtech.site', '2023-08-14', 'fileStore/staff_docs/9.jpg', 'fileStore/staff_docs/9.jpg', 'fileStore/staff_docs/9.jpg', 'fileStore/staff_docs/9.jpg', 'active', 'admin', '\'none\'', 'n/a');

-- --------------------------------------------------------

--
-- Table structure for table `target`
--

CREATE TABLE `target` (
  `monthlyTarget` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `target`
--

INSERT INTO `target` (`monthlyTarget`) VALUES
(50000);

-- --------------------------------------------------------

--
-- Table structure for table `userlogs`
--

CREATE TABLE `userlogs` (
  `username` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlogs`
--

INSERT INTO `userlogs` (`username`, `date`) VALUES
('demo', '2023-08-21 11:51:00'),
('demo', '2023-08-21 12:06:40'),
('demo', '2023-08-29 16:46:03'),
('demo', '2023-08-30 23:53:51'),
('demo', '2023-08-31 00:46:00'),
('demo', '2023-08-31 09:11:38'),
('demo', '2023-08-31 09:54:26'),
('demo', '2023-08-31 10:19:33'),
('demo', '2023-08-31 21:31:17'),
('demo', '2023-08-31 22:12:38'),
('demo', '2023-08-31 22:49:04'),
('demo', '2023-09-05 09:29:00'),
('demo', '2023-09-05 09:36:49'),
('demo', '2023-09-05 09:45:31'),
('demo', '2023-09-06 11:27:39'),
('demo', '2023-09-06 22:47:37'),
('demo', '2023-09-06 22:53:02'),
('demo', '2023-09-09 01:37:44'),
('demo', '2023-09-18 11:09:10'),
('demo', '2023-09-18 11:09:42'),
('demo', '2023-09-18 11:09:53'),
('demo', '2023-09-18 11:20:48'),
('demo', '2023-09-18 11:21:39'),
('demo', '2023-09-18 11:38:51'),
('demo', '2023-09-18 11:41:54'),
('demo', '2023-09-18 11:44:42'),
('demo', '2023-09-18 11:45:16'),
('demo', '2023-09-18 11:45:35'),
('demo', '2023-09-18 11:54:32'),
('demo', '2023-09-18 11:58:12'),
('demo', '2023-09-18 12:06:39'),
('demo', '2023-09-18 12:14:49'),
('demo', '2023-09-18 12:16:25'),
('demo', '2023-09-18 12:16:40'),
('demo', '2023-09-18 12:20:18'),
('demo', '2023-09-19 16:47:10'),
('demo', '2023-09-19 16:47:16'),
('demo', '2023-09-20 08:51:11'),
('demo', '2023-09-20 09:48:02'),
('demo', '2023-09-20 09:57:03'),
('demo', '2023-09-20 09:58:04'),
('demo', '2023-09-20 10:39:50'),
('demo', '2023-09-20 10:41:04'),
('demo', '2023-09-20 10:53:04'),
('demo', '2023-09-20 15:41:25'),
('demo', '2023-09-20 15:47:10'),
('demo', '2023-09-20 21:01:46'),
('demo', '2023-09-20 21:12:13'),
('demo', '2023-09-20 21:36:31'),
('demo', '2023-09-20 22:20:07'),
('demo', '2023-09-20 22:20:30'),
('demo', '2023-09-20 22:23:15'),
('demo', '2023-09-28 08:09:07'),
('demo', '2023-09-28 10:42:16'),
('demo', '2023-09-28 10:44:01'),
('demo', '2023-09-28 11:08:08'),
('demo', '2023-09-28 11:27:57'),
('demo', '2023-09-28 11:31:34'),
('demo', '2023-09-28 11:32:09'),
('demo', '2023-09-28 11:32:42'),
('demo', '2023-09-28 11:36:31'),
('demo', '2023-09-29 08:37:41'),
('demo', '2023-09-29 09:23:41'),
('demo', '2023-09-29 09:54:41'),
('demo', '2023-09-29 11:06:06'),
('daante', '2023-09-29 12:00:36'),
('demo', '2023-09-29 12:06:06'),
('demo', '2023-09-29 12:37:42'),
('daante', '2023-09-29 12:46:57'),
('daante', '2023-09-29 12:47:47'),
('demo', '2023-09-29 12:52:59'),
('daa', '2023-09-29 12:55:53'),
('demo', '2023-09-29 12:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `phone` text DEFAULT NULL,
  `token` text NOT NULL,
  `lastResetDate` text NOT NULL DEFAULT current_timestamp(),
  `api_key` text DEFAULT NULL,
  `role` text DEFAULT NULL,
  `custID` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `token`, `lastResetDate`, `api_key`, `role`, `custID`) VALUES
(1, 'Millie', '$2y$10$y9kSViAVvu7XekLA0XWTC.Hh7pHr6a0VmX.s0yr63M4cOcosXQ1tG', 'mmwatetu@gmail.com', NULL, '', '2023-06-22 23:14:36', NULL, NULL, '2301'),
(5, 'Tim', '$2y$10$cgONRcJHAucDZzRjpQEXnuck7TSOUuhEZk2fZXZUBPjgMOdEzWC.u', 'timnmburu@gmail.com', NULL, '', '2023-06-22 23:10:11', '123456', NULL, '2301'),
(7, 'demo', 'cnUSsmLUL9Eic7e1n7IzllNZb4mMTD09rERpd6vMSiMHu41yUu6cEKQQpmB/Iw1ARb+lOUXba257OyfjWRCQHxFb7GOHylMC4LP4EZjo+ZepaOxvlXasTp6v7bRkdXkE', 'info@essentialtech.site', '254725887269', '', '2023-09-29 12:05:31', '12gdfgg.3478', 'admin', '2301');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `mpesa` text NOT NULL,
  `kcb` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`mpesa`, `kcb`) VALUES
('3746.99', '15604');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `bookings1`
--
ALTER TABLE `bookings1`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `commission_payments`
--
ALTER TABLE `commission_payments`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`custID`);

--
-- Indexes for table `expenseHistory`
--
ALTER TABLE `expenseHistory`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses1`
--
ALTER TABLE `expenses1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `frequentPayments`
--
ALTER TABLE `frequentPayments`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `givings`
--
ALTER TABLE `givings`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mpesa_collections`
--
ALTER TABLE `mpesa_collections`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `mpesa_transfers`
--
ALTER TABLE `mpesa_transfers`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_number`);

--
-- Indexes for table `otpQ`
--
ALTER TABLE `otpQ`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `payments2`
--
ALTER TABLE `payments2`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `recruit`
--
ALTER TABLE `recruit`
  ADD PRIMARY KEY (`staff_no`);

--
-- Indexes for table `recurrentExp`
--
ALTER TABLE `recurrentExp`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `sentSMS`
--
ALTER TABLE `sentSMS`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `smsQ`
--
ALTER TABLE `smsQ`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_no`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bookings1`
--
ALTER TABLE `bookings1`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `commission_payments`
--
ALTER TABLE `commission_payments`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `custID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `expenseHistory`
--
ALTER TABLE `expenseHistory`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `expenses1`
--
ALTER TABLE `expenses1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `frequentPayments`
--
ALTER TABLE `frequentPayments`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `givings`
--
ALTER TABLE `givings`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `mpesa_collections`
--
ALTER TABLE `mpesa_collections`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `mpesa_transfers`
--
ALTER TABLE `mpesa_transfers`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `otpQ`
--
ALTER TABLE `otpQ`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments2`
--
ALTER TABLE `payments2`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruit`
--
ALTER TABLE `recruit`
  MODIFY `staff_no` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sentSMS`
--
ALTER TABLE `sentSMS`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `smsQ`
--
ALTER TABLE `smsQ`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
