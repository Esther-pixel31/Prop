-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2025 at 10:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `company`

-- Table structure for table `monthly_reports`
--

CREATE TABLE `monthly_reports` (
  `report_id` INT AUTO_INCREMENT PRIMARY KEY,
  `report_month` DATE NOT NULL,
  `total_payments` DECIMAL(10,2) NOT NULL,
  `total_tenants` INT NOT NULL,
  `total_invoices` INT NOT NULL,
  `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'level 0',
  `email` varchar(200) NOT NULL,
  `password` varchar(500) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `role`, `email`, `password`, `date`) VALUES
(5, 'Obed Nyakundi', 'level-0', 'obed@example.com', '$2y$12$EcXNA8UHc53gWrW0suAB2.b028O7ItuHyLqXrQN6j00ax7OHVBF3i', '2021-05-22 10:56:20'),
(8, 'Ace One', 'level-1', 'ace@nyumbani.com', '$2y$12$CLdOW9clSRIYAnLzicI97.J5VJt6KPVwnSOeR0TT8u/uZCC/mxgoq', '2021-05-24 16:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `blogid` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `name`, `comment`, `blogid`, `date`) VALUES
(1, 'Jaden', 'Awesome post guys!!', 6, '2018-07-28 00:15:59'),
(6, 'Cliff', 'I really relate to this', 5, '2018-07-28 01:00:14');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `names` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `names`, `email`, `message`, `date`) VALUES
(1, 'Ethredah', 'ethredah@gmail.com', 'Hello there Ushauri team.', '2018-07-27 16:57:59'),
(2, 'Chao', 'chao@gmail.com', 'Hi there!!', '2018-07-27 16:57:59'),
(4, 'James Mlamba', 'jaymo@gmail.com', 'I am interested in a meeting.', '2018-07-28 01:38:22');

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `houses` (
  `houseID` int(11) NOT NULL,
  `house_name` text NOT NULL,
  `number_of_rooms` int(10) NOT NULL,
  `rent_amount` double NOT NULL,
  `garbage` int(11) NOT NULL,
  `location` text NOT NULL,
  `num_of_bedrooms` int(10) NOT NULL,
  `house_status` varchar(50) NOT NULL DEFAULT 'Vacant'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `houses`
--

INSERT INTO `houses` (`houseID`, `house_name`, `number_of_rooms`, `rent_amount`, `garbage`, `location`, `num_of_bedrooms`, `house_status`) VALUES
(11, 'Tausi Apartment', 1, 50000, 300, 'Nairobi Cbd', 3, 'Vacant');

-- --------------------------------------------------------

--
-- Table structure for table `house_numbers`
--

CREATE TABLE `house_numbers` (
  `id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `house_no` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_numbers`
--

INSERT INTO `house_numbers` (`id`, `house_id`, `house_no`) VALUES
(7, 11, '13C');

-- --------------------------------------------------------

--
-- Table structure for table `house_pics`
--

CREATE TABLE `house_pics` (
  `pic_id` int(11) NOT NULL,
  `pic_name` text NOT NULL,
  `house_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoiceNumber` varchar(50) NOT NULL,
  `tenantID` int(11) NOT NULL,
  `dateOfInvoice` text NOT NULL,
  `dateDue` text NOT NULL,
  `amountDue` int(11) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'unpaid',
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `invoicesview`
-- (See below for the actual view)
--
CREATE TABLE `invoicesview` (
`invoiceNumber` varchar(50)
,`tenant_name` text
,`tenantID` int(11)
,`phone_number` varchar(13)
,`amountDue` int(11)
,`dateOfInvoice` text
,`dateDue` text
,`status` varchar(50)
,`comment` text
);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `location_name` text NOT NULL,
  `geo_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `location_name`, `geo_id`) VALUES
(1, 'Shika Adabu', ''),
(2, 'Mtongwe', ''),
(3, 'Mvita', ''),
(4, 'Nyali', ''),
(5, 'Nairobi Cbd', 'undefined'),
(6, 'Tanga', 'undefined');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `paymentID` int(11) NOT NULL,
  `tenantID` int(11) NOT NULL,
  `invoiceNumber` varchar(50) NOT NULL,
  `expectedAmount` int(11) NOT NULL,
  `amountPaid` int(11) NOT NULL,
  `balance` int(11) NOT NULL,
  `mpesaCode` varchar(30) NOT NULL DEFAULT 'None',
  `dateofPayment` text NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `paymentsview`
-- (See below for the actual view)
--
CREATE TABLE `paymentsview` (
`paymentID` int(11)
,`tenantID` int(11)
,`tenant_name` text
,`house_name` text
,`invoiceNumber` varchar(50)
,`expectedAmount` int(11)
,`amountPaid` int(11)
,`balance` int(11)
,`mpesaCode` varchar(30)
,`dateofPayment` text
,`comment` text
);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `author` varchar(200) NOT NULL,
  `title` varchar(400) NOT NULL,
  `content` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `author`, `title`, `content`, `date`) VALUES
(5, 'Ethredah', 'MENTAL HEALTH IS REAL', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,csem.Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. enean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar,\r\n\r\n', '2018-07-27 15:28:31'),
(6, 'Derick', 'Ushauri Lending a hand', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. enean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar,\r\n\r\n', '2018-07-27 15:50:04');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `date`) VALUES
(3, 'ethredah@gmail.com', '2018-07-27 18:21:30'),
(4, 'james@hack3.io', '2018-07-27 18:21:30'),
(6, 'admin@pikash.sales', '2018-07-28 01:49:21');

-- --------------------------------------------------------

--
-- Stand-in structure for view `tenanthouseview`
-- (See below for the actual view)
--
CREATE TABLE `tenanthouseview` (
`tenantID` int(11)
,`tenant_name` text
,`house_name` text
,`house_no` varchar(255)
,`rent` double
,`garbage` int(11)
,`current_reading` decimal(10,2)
,`previous_reading` decimal(10,2)
,`water_rate` decimal(10,2)
,`total_units` decimal(10,2)
,`total_consumption` decimal(10,2)
,`outstanding_balance` int(11)
,`total_amount` int(11)
,`invoice_number` varchar(50)
,`date_of_invoice` text
,`date_due` text
,`Amount Due` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `tenantID` int(11) NOT NULL,
  `houseNumber` int(10) NOT NULL,
  `tenant_name` text NOT NULL,
  `email` text NOT NULL,
  `ID_number` int(10) NOT NULL,
  `profession` text NOT NULL,
  `phone_number` varchar(13) NOT NULL,
  `agreement_file` text DEFAULT NULL,
  `dateAdmitted` text DEFAULT NULL,
  `account` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `tenantsdetailsview`
-- (See below for the actual view)
--
CREATE TABLE `tenantsdetailsview` (
`tenantID` int(11)
,`tenant_name` text
,`house_name` text
,`house_no` varchar(255)
,`rent` double
,`garbage` int(11)
,`current_reading` decimal(10,2)
,`previous_reading` decimal(10,2)
,`water_rate` decimal(10,2)
,`total_units` decimal(10,2)
,`total_consumption` decimal(10,2)
,`outstanding_balance` int(11)
,`invoice_number` varchar(50)
,`date_of_invoice` text
,`date_due` text
,`total_amount` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tenantsview`
-- (See below for the actual view)
--
CREATE TABLE `tenantsview` (
`tenantID` int(11)
,`houseNumber` int(10)
,`tenant_name` text
,`email` text
,`ID_number` int(10)
,`profession` text
,`phone_number` varchar(13)
,`dateAdmitted` text
,`agreement_file` text
,`house_name` text
,`rent_amount` double
,`house_no` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tenants_houses_view`
-- (See below for the actual view)
--
CREATE TABLE `tenants_houses_view` (
`tenantID` int(11)
,`tenant_name` text
,`house_name` text
,`house_no` varchar(255)
,`rent` double
,`garbage` int(11)
,`current_reading` decimal(10,2)
,`previous_reading` decimal(10,2)
,`water_rate` decimal(10,2)
,`total_units` decimal(10,2)
,`total_consumption` decimal(10,2)
,`outstanding_balance` int(11)
,`total_amount` int(11)
,`invoice_number` varchar(50)
,`date_of_invoice` text
,`date_due` text
);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `actor` text DEFAULT NULL,
  `time` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `seen` varchar(10) NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `actor`, `time`, `description`, `seen`) VALUES
(21, 'Admin (obed)', '2023-10-19 : 13:18:23', 'obed added a new house (A Blue House) with 4 rentable units, and 2 bedrooms per unit located in Nairobi Cbd', 'YES'),
(22, 'Admin (obed)', '2023-10-19 : 13:20:16', 'obed admitted a new tenant (Obed Paul) at 2023-10-19 : 13:20:16', 'YES'),
(23, 'Admin (obed)', '2023-10-19 : 13:23:49', 'obed added a new rental invoice (INV20231019132349) for tenant (Obed Paul) at 2023-10-19 : 13:23:49.', 'YES'),
(24, 'Admin (obed)', '2023-10-19 : 13:25:50', 'obed added payment of 7000 for Obed Paul, under invoice ID: INV20231019132349', 'YES'),
(25, 'Admin (obed)', '2023-10-19 : 13:27:25', 'obed added payment of 7500 for Obed Paul, under invoice ID: INV20231019132349', 'YES'),
(26, 'Admin (obed)', '2023-10-19 : 13:31:52', 'obed admitted a new tenant (Ann Tenant) at 2023-10-19 : 13:31:52', 'YES'),
(27, 'Admin (obed)', '2023-10-19 : 13:34:01', 'obed added a new rental invoice (INV20231019133401) for tenant (Ann Tenant) at 2023-10-19 : 13:34:01.', 'YES'),
(28, 'Admin (obed)', '2023-10-19 : 13:35:21', 'obed added payment of 3000 for Ann Tenant, under invoice ID: INV20231019133401', 'YES'),
(29, 'Admin (obed)', '2023-10-19 : 13:37:33', 'obed added a new house (The Palatial House) with 1 rentable units, and 5 bedrooms per unit located in Nairobi Cbd', 'YES'),
(30, 'Admin (obed)', '2023-10-19 : 13:39:10', 'obed admitted a new tenant (Someone Watching) at 2023-10-19 : 13:39:10', 'YES'),
(31, 'Admin (obed)', '2023-10-19 : 13:41:19', 'obed added a new rental invoice (INV20231019134119) for tenant (Someone Watching) at 2023-10-19 : 13:41:19.', 'YES'),
(32, 'Admin (obed)', '2023-10-19 : 13:42:34', 'obed added payment of 70000 for Someone Watching, under invoice ID: INV20231019134119', 'YES'),
(33, 'Admin (obed)', '2024-01-25 : 10:17:45', 'obed added a new house (Telposta House) with 12 rentable units, and 2 bedrooms per unit located in Mvita', 'YES'),
(35, 'Admin (obed)', '', 'obed added a new property (Tausi Apartment) with  number_of_rooms, 3 bedrooms per unit, located in Nairobi Cbd, with a rent of 30000 and garbage fee of 300', 'YES'),
(39, 'Admin (obed)', '2025-01-31 : 23:06:26', 'obed admitted a new tenant (Esther Mutua) at 2025-01-31 : 23:06:26', 'YES'),
(40, 'Admin (obed)', '2025-01-31 : 23:06:42', 'obed admitted a new tenant (Raphael Mutua) at 2025-01-31 : 23:06:42', 'YES'),
(41, 'Admin (obed)', '2025-01-31 : 23:07:24', 'obed admitted a new tenant (Margaret) at 2025-01-31 : 23:07:24', 'YES'),
(42, 'Admin (obed)', '2025-02-01 : 18:08:48', 'obed admitted a new tenant (Esther Mutua) at 2025-02-01 : 18:08:48', 'YES'),
(43, 'Admin (obed)', '2025-02-01 : 23:42:54', 'obed added payment of 30000 for Esther Mutua, under invoice ID: INV-1', 'YES'),
(50, 'Admin (obed)', '2025-02-05 : 15:18:58', 'obed admitted a new tenant (Raphael Mutua) at 2025-02-05 : 15:18:58', 'YES'),
(51, 'Admin (obed)', '2025-02-05 : 15:25:13', 'obed admitted a new tenant (Raphael Mutua) at 2025-02-05 : 15:25:13', 'YES'),
(52, 'Admin (obed)', '2025-02-05 : 15:26:08', 'obed admitted a new tenant (Margaret) at 2025-02-05 : 15:26:08', 'YES'),
(53, 'Admin (obed)', '2025-02-05 : 15:38:23', 'obed admitted a new tenant (Esther Mutua) at 2025-02-05 : 15:38:23', 'YES'),
(54, 'Admin (obed)', '2025-02-05 : 15:45:15', 'obed admitted a new tenant (Esther Mutua) at 2025-02-05 : 15:45:15', 'YES'),
(55, 'Admin (obed)', '2025-02-05 : 15:45:45', 'obed admitted a new tenant (Esther Mutua) at 2025-02-05 : 15:45:45', 'YES'),
(56, 'Admin (obed)', '2025-02-05 : 15:59:40', 'obed admitted a new tenant (Esther Mutua) at 2025-02-05 : 15:59:40', 'YES'),
(57, 'Admin (obed)', '2025-02-05 : 17:09:34', 'obed admitted a new tenant (Raphael Mutua) at 2025-02-05 : 17:09:34', 'YES'),
(58, 'Admin (obed)', '2025-02-05 : 17:15:24', 'obed admitted a new tenant (Margaret) at 2025-02-05 : 17:15:24', 'YES'),
(59, 'Admin (obed)', '2025-02-05 : 20:45:01', 'obed added payment of 29000 for Esther Mutua, under invoice ID: INV202502050001', 'YES'),
(61, 'Admin (obed)', '2025-02-05 : 21:27:42', 'obed added payment of 32000 for Raphael Mutua, under invoice ID: INV202502050002', 'YES'),
(62, 'Admin (obed)', '2025-02-05 : 23:06:39', 'obed added payment of 37000 for Margaret, under invoice ID: INV202502050003', 'YES'),
(63, 'Admin (obed)', '2025-02-05 : 23:29:36', 'obed added payment of 37000 for Margaret, under invoice ID: INV202502050002', 'YES'),
(64, 'Admin (obed)', '2025-02-05 : 23:39:07', 'obed added payment of 31000 for Esther Mutua, under invoice ID: INV202502050001', 'YES'),
(65, 'Admin (obed)', '2025-02-05 : 23:39:37', 'obed added payment of 25000 for Raphael Mutua, under invoice ID: INV202502050002', 'YES'),
(66, 'Admin (obed)', '', 'obed added a new property (Tausi Apartment) with 1 number of rooms, 3 bedrooms per unit, located in Nairobi Cbd, with a rent of 50000 and garbage fee of 500', 'YES'),
(67, 'Admin (obed)', '', 'obed added a new property (Tausi Apartment) with 1 number of rooms, 3 bedrooms per unit, located in Nairobi Cbd, with a rent of 30000 and garbage fee of 300', 'YES');

-- --------------------------------------------------------

--
-- Table structure for table `water_readings`
--

CREATE TABLE `water_readings` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `previous_reading` decimal(10,2) NOT NULL,
  `current_reading` decimal(10,2) NOT NULL,
  `total_units` decimal(10,2) NOT NULL,
  `water_rate` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `reading_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `water_reading_view`
-- (See below for the actual view)
--
CREATE TABLE `water_reading_view` (
`id` int(11)
,`tenant_name` text
,`previous_reading` decimal(10,2)
,`current_reading` decimal(10,2)
,`total_units` decimal(10,2)
,`water_rate` decimal(10,2)
,`total_amount` decimal(10,2)
,`reading_date` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `invoicesview`
--
DROP TABLE IF EXISTS `invoicesview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `invoicesview`  AS SELECT `invoices`.`invoiceNumber` AS `invoiceNumber`, `tenants`.`tenant_name` AS `tenant_name`, `invoices`.`tenantID` AS `tenantID`, `tenants`.`phone_number` AS `phone_number`, `invoices`.`amountDue` AS `amountDue`, `invoices`.`dateOfInvoice` AS `dateOfInvoice`, `invoices`.`dateDue` AS `dateDue`, `invoices`.`status` AS `status`, `invoices`.`comment` AS `comment` FROM (`invoices` left join `tenants` on(`invoices`.`tenantID` = `tenants`.`tenantID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `paymentsview`
--
DROP TABLE IF EXISTS `paymentsview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `paymentsview`  AS SELECT `payments`.`paymentID` AS `paymentID`, `payments`.`tenantID` AS `tenantID`, `tenantsview`.`tenant_name` AS `tenant_name`, `tenantsview`.`house_name` AS `house_name`, `payments`.`invoiceNumber` AS `invoiceNumber`, `payments`.`expectedAmount` AS `expectedAmount`, `payments`.`amountPaid` AS `amountPaid`, `payments`.`balance` AS `balance`, `payments`.`mpesaCode` AS `mpesaCode`, `payments`.`dateofPayment` AS `dateofPayment`, `payments`.`comment` AS `comment` FROM (`payments` left join `tenantsview` on(`payments`.`tenantID` = `tenantsview`.`tenantID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `tenanthouseview`
--
DROP TABLE IF EXISTS `tenanthouseview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tenanthouseview`  AS SELECT `tenants`.`tenantID` AS `tenantID`, `tenants`.`tenant_name` AS `tenant_name`, `houses`.`house_name` AS `house_name`, `house_numbers`.`house_no` AS `house_no`, `houses`.`rent_amount` AS `rent`, `houses`.`garbage` AS `garbage`, `water_readings`.`current_reading` AS `current_reading`, `water_readings`.`previous_reading` AS `previous_reading`, `water_readings`.`water_rate` AS `water_rate`, `water_readings`.`total_units` AS `total_units`, `water_readings`.`total_amount` AS `total_consumption`, `payments`.`balance` AS `outstanding_balance`, `payments`.`expectedAmount` AS `total_amount`, `invoices`.`invoiceNumber` AS `invoice_number`, `invoices`.`dateOfInvoice` AS `date_of_invoice`, `invoices`.`dateDue` AS `date_due`, `invoices`.`amountDue` AS `Amount Due` FROM (((((`tenants` left join `house_numbers` on(`tenants`.`houseNumber` = `house_numbers`.`id`)) left join `houses` on(`house_numbers`.`house_id` = `houses`.`houseID`)) left join `water_readings` on(`tenants`.`tenantID` = `water_readings`.`tenant_id`)) left join `payments` on(`tenants`.`tenantID` = `payments`.`tenantID`)) left join `invoices` on(`tenants`.`tenantID` = `invoices`.`tenantID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `tenantsdetailsview`
--
DROP TABLE IF EXISTS `tenantsdetailsview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tenantsdetailsview`  AS SELECT `tenants`.`tenantID` AS `tenantID`, `tenants`.`tenant_name` AS `tenant_name`, `houses`.`house_name` AS `house_name`, `house_numbers`.`house_no` AS `house_no`, `houses`.`rent_amount` AS `rent`, `houses`.`garbage` AS `garbage`, `water_readings`.`current_reading` AS `current_reading`, `water_readings`.`previous_reading` AS `previous_reading`, `water_readings`.`water_rate` AS `water_rate`, `water_readings`.`total_units` AS `total_units`, `water_readings`.`total_amount` AS `total_consumption`, `payments`.`balance` AS `outstanding_balance`, `invoices`.`invoiceNumber` AS `invoice_number`, `invoices`.`dateOfInvoice` AS `date_of_invoice`, `invoices`.`dateDue` AS `date_due`, `invoices`.`totalAmount` AS `total_amount` FROM (((((`tenants` left join `house_numbers` on(`tenants`.`houseNumber` = `house_numbers`.`id`)) left join `houses` on(`house_numbers`.`house_id` = `houses`.`houseID`)) left join `water_readings` on(`tenants`.`tenantID` = `water_readings`.`tenant_id`)) left join `payments` on(`tenants`.`tenantID` = `payments`.`tenantID`)) left join `invoices` on(`tenants`.`tenantID` = `invoices`.`tenantID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `tenantsview`
--
DROP TABLE IF EXISTS `tenantsview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tenantsview`  AS SELECT `tenants`.`tenantID` AS `tenantID`, `tenants`.`houseNumber` AS `houseNumber`, `tenants`.`tenant_name` AS `tenant_name`, `tenants`.`email` AS `email`, `tenants`.`ID_number` AS `ID_number`, `tenants`.`profession` AS `profession`, `tenants`.`phone_number` AS `phone_number`, `tenants`.`dateAdmitted` AS `dateAdmitted`, `tenants`.`agreement_file` AS `agreement_file`, `houses`.`house_name` AS `house_name`, `houses`.`rent_amount` AS `rent_amount`, `house_numbers`.`house_no` AS `house_no` FROM ((`tenants` left join `house_numbers` on(`tenants`.`houseNumber` = `house_numbers`.`id`)) left join `houses` on(`house_numbers`.`house_id` = `houses`.`houseID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `tenants_houses_view`
--
DROP TABLE IF EXISTS `tenants_houses_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tenants_houses_view`  AS SELECT `tenants`.`tenantID` AS `tenantID`, `tenants`.`tenant_name` AS `tenant_name`, `houses`.`house_name` AS `house_name`, `house_numbers`.`house_no` AS `house_no`, `houses`.`rent_amount` AS `rent`, `houses`.`garbage` AS `garbage`, `water_readings`.`current_reading` AS `current_reading`, `water_readings`.`previous_reading` AS `previous_reading`, `water_readings`.`water_rate` AS `water_rate`, `water_readings`.`total_units` AS `total_units`, `water_readings`.`total_amount` AS `total_consumption`, `payments`.`balance` AS `outstanding_balance`, `payments`.`expectedAmount` AS `total_amount`, `invoices`.`invoiceNumber` AS `invoice_number`, `invoices`.`dateOfInvoice` AS `date_of_invoice`, `invoices`.`dateDue` AS `date_due` FROM (((((`tenants` left join `house_numbers` on(`tenants`.`houseNumber` = `house_numbers`.`id`)) left join `houses` on(`house_numbers`.`house_id` = `houses`.`houseID`)) left join `water_readings` on(`tenants`.`tenantID` = `water_readings`.`tenant_id`)) left join `payments` on(`tenants`.`tenantID` = `payments`.`tenantID`)) left join `invoices` on(`tenants`.`tenantID` = `invoices`.`tenantID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `water_reading_view`
--
DROP TABLE IF EXISTS `water_reading_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `water_reading_view`  AS SELECT `wr`.`id` AS `id`, `t`.`tenant_name` AS `tenant_name`, `wr`.`previous_reading` AS `previous_reading`, `wr`.`current_reading` AS `current_reading`, `wr`.`total_units` AS `total_units`, `wr`.`water_rate` AS `water_rate`, `wr`.`total_amount` AS `total_amount`, `wr`.`reading_date` AS `reading_date` FROM (`water_readings` `wr` join `tenants` `t` on(`wr`.`tenant_id` = `t`.`tenantID`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blogid` (`blogid`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `houses`
--
ALTER TABLE `houses`
  ADD PRIMARY KEY (`houseID`);

--
-- Indexes for table `house_numbers`
--
ALTER TABLE `house_numbers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `house_id` (`house_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoiceNumber`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`tenantID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `water_readings`
--
ALTER TABLE `water_readings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_water_tenant` (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `houses`
--
ALTER TABLE `houses`
  MODIFY `houseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `house_numbers`
--
ALTER TABLE `house_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `water_readings`
--
ALTER TABLE `water_readings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `house_numbers`
--
ALTER TABLE `house_numbers`
  ADD CONSTRAINT `house_numbers_ibfk_1` FOREIGN KEY (`house_id`) REFERENCES `houses` (`houseID`);

--
-- Constraints for table `water_readings`
--
ALTER TABLE `water_readings`
  ADD CONSTRAINT `fk_water_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenantID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
