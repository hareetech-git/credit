-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2026 at 12:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `credit`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `uuid`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, '', 'admin', 'admin@gmail.com', '$2y$10$d.HjZm4Xs3kIhH7FrbPEhOj9T9ueD7lXn6UhCStB9LR8pv6UE5B5O', 'admin', '2026-01-28 06:44:17', '2026-01-28 06:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `aadhaar_number` varchar(20) DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `email`, `phone`, `password`, `aadhaar_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sanyam Srivastava', 'srivastavasanyam8052@gmail.com', '9984278970', '$2y$10$Vi3kwMBdcDqMKJMjbjklVut4qdYRkR0h8n.44nF2N..5h8GvuKPvC', '656512344321', 'active', '2026-01-29 12:27:10', '2026-01-30 10:29:03'),
(2, 'Sanyamsdsfdsa Srivastava', 'srivafdgfdgfdgstavasanyam8052@gmail.com', '8984278970', '$2y$10$73OybQocLn6ovNVxrPp7JOBxvb77VBUuZ.7JgQBs8erjW65iZFZlK', '998737892939', 'active', '2026-01-30 06:03:18', '2026-01-30 07:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `customer_profiles`
--

CREATE TABLE `customer_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `employee_type` enum('salaried','self_employed','business','professional') DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `monthly_income` decimal(12,2) DEFAULT NULL,
  `reference1_name` varchar(255) DEFAULT NULL,
  `reference1_phone` varchar(20) DEFAULT NULL,
  `reference2_name` varchar(255) DEFAULT NULL,
  `reference2_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_profiles`
--

INSERT INTO `customer_profiles` (`id`, `customer_id`, `pan_number`, `birth_date`, `state`, `city`, `pin_code`, `employee_type`, `company_name`, `monthly_income`, `reference1_name`, `reference1_phone`, `reference2_name`, `reference2_phone`, `created_at`, `updated_at`) VALUES
(10, 1, 'ABCDE1232G', '2006-09-11', 'uttar pradesh', 'lmp', '262701', 'business', 'Hareetech Development Pvt Ltd', 200000.00, 'nhnh', '8787878787', 'kjkjk', '9984278787', '2026-01-30 10:29:03', '2026-01-30 10:29:57'),
(13, 2, 'ABCDE1234F', '2026-01-21', 'Uttar Pradesh', 'Lakhimpur', '261506', 'business', 'sac', 4.00, 'xzczx', '9984278970', 'cxzc', '9984278970', '2026-01-30 10:41:31', '2026-01-30 10:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Loan Department', '2026-01-29 08:02:52', '2026-01-29 08:02:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `loan_type_id` int(11) NOT NULL,
  `loan_type_name` varchar(255) NOT NULL,
  `query_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiries`
--

INSERT INTO `enquiries` (`id`, `full_name`, `phone`, `email`, `loan_type_id`, `loan_type_name`, `query_message`, `created_at`) VALUES
(1, 'sanyam', '9984278970', 'srivastavasanyam8052@gmail.com', 3, 'Working Capital Loan', 'need help for loan', '2026-01-29 07:43:05'),
(2, 'Sanyam Srivastava', '9984278970', 'abhi100sh@gmail.com', 2, 'Business Loans', 'dfsdf', '2026-01-29 09:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `requested_amount` decimal(15,2) NOT NULL,
  `tenure_years` int(11) NOT NULL,
  `emi_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','approved','rejected','disbursed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rejection_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_applications`
--

INSERT INTO `loan_applications` (`id`, `customer_id`, `service_id`, `requested_amount`, `tenure_years`, `emi_amount`, `status`, `created_at`, `rejection_note`) VALUES
(1, 2, 5, 90000.00, 1, 0.00, 'pending', '2026-01-30 10:49:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_application_docs`
--

CREATE TABLE `loan_application_docs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_application_id` bigint(20) UNSIGNED NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `doc_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `sub_category_id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `long_description` longtext DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category_id`, `sub_category_id`, `service_name`, `title`, `slug`, `short_description`, `long_description`, `hero_image`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Loan for CA', 'Loan for Chartered Accountant', 'loan-for-ca', 'Tailored loan for Chartered Accountants.', 'Special loan products designed for Chartered Accountants with flexible repayment options.', NULL, '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(2, 1, 3, 'Loan for CS', 'Loan for Company Secretary', 'loan-for-cs', 'Business loan for Company Secretaries.', 'Customized financing solutions for Company Secretaries.', NULL, '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(3, 1, 3, 'Loan for Doctor', 'Loan for Doctor', 'loan-for-doctor', 'Medical professionals loan.', 'Loans designed specifically for doctors to support clinic setup and expansion.', NULL, '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(4, 1, 3, 'Loan for Architect dsdfd', 'Loan for Architect', 'loan-for-architect', 'Loan for architecture professionals.', 'Professional loans for architects to fund projects and office expansion.', NULL, '2026-01-29 08:03:53', '2026-01-29 10:32:53'),
(5, 1, 2, 'Secured Business Loan', 'Looking for a secured business Loan?', 'looking-for-a-secured-business-loan', 'Are you ready to take your business to greater heights? Apply for business loans online at low-interest rates through Udhar Capital. Do apply now!', 'For business owners looking to start or expand their enterprises, online business loans are critical. Udhar Capital financial services have undergone continuous innovation to satisfy the demands of entrepreneurs seeking capital. The purpose of the business loan is to businesses acquire the finance they need.The instant business loan may be utilised to cover all of your company’s needs, it’s the finest lending option for a startup. Apply business loans online for an attractive and affordable interest rate ensures you won’t have to cut back on expenses.', 'uploads/services/service_1769768621_534.jpg', '2026-01-29 08:53:13', '2026-01-30 10:23:41'),
(6, 1, 1, 'Personal Loan', 'APPLY FOR PERSONAL LOAN', 'apply-for-personal-loan', 'Collateral-free personal loan with lowest interest rate is just a few clicks away from you. Apply Personal Loan online and fulfill your dreams without worrying about money', 'The term personal loan has emerged as a boon for individuals seeking quick and hassle free access to funds. Personal loan is a financial tool that offers unparalleled flexibility and convenience to borrowers. Unlike traditional loans, a personal loan is unsecured, meaning you don’t have to provide any collateral to secure it.\r\n\r\nWith Udhar Capital Personal Loan you can say goodbye to lengthy approval processes and mountains of paperwork. Our application process ensures that you can get the funds you need when you need them, without the unnecessary delays. We are committed to providing you with the best financial solutions.', 'uploads/services/service_1769770355_584.jfif', '2026-01-29 12:09:21', '2026-01-30 10:52:35');

-- --------------------------------------------------------

--
-- Table structure for table `services_subcategories`
--

CREATE TABLE `services_subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL DEFAULT uuid(),
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `sub_category_name` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `live` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services_subcategories`
--

INSERT INTO `services_subcategories` (`id`, `uuid`, `category_id`, `sub_category_name`, `sequence`, `status`, `live`, `created_at`, `updated_at`) VALUES
(1, 'fac11733-fce8-11f0-a3c0-3863bba624fb', 1, 'Personal Loan', 1, 'active', 1, '2026-01-29 08:03:25', '2026-01-29 08:03:25'),
(2, 'fac12cd1-fce8-11f0-a3c0-3863bba624fb', 1, 'Business Loans', 2, 'active', 1, '2026-01-29 08:03:25', '2026-01-29 08:03:25'),
(3, 'fac12de9-fce8-11f0-a3c0-3863bba624fb', 1, 'Professional Loan', 3, 'active', 1, '2026-01-29 08:03:25', '2026-01-29 08:03:25'),
(4, 'fac12e7c-fce8-11f0-a3c0-3863bba624fb', 1, 'Home Loan', 4, 'active', 1, '2026-01-29 08:03:25', '2026-01-29 08:03:25'),
(5, 'fac12ee3-fce8-11f0-a3c0-3863bba624fb', 1, 'Credit Card', 5, 'active', 1, '2026-01-29 08:03:25', '2026-01-29 08:03:25');

-- --------------------------------------------------------

--
-- Table structure for table `service_banks`
--

CREATE TABLE `service_banks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `bank_key` varchar(255) NOT NULL,
  `bank_value` text NOT NULL,
  `bank_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_banks`
--

INSERT INTO `service_banks` (`id`, `service_id`, `bank_key`, `bank_value`, `bank_image`, `created_at`, `updated_at`) VALUES
(1, 5, 'Poonawalla', '18.00% p.a.', '', '2026-01-29 09:00:34', '2026-01-29 09:00:34'),
(2, 5, 'Axis Bank', '14.95% - 19.20% p.a.', '', '2026-01-29 09:00:34', '2026-01-29 09:00:34'),
(3, 5, 'HDB Financial Services Ltd.', 'Up to 36% p.a.', '', '2026-01-29 09:00:34', '2026-01-29 09:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL DEFAULT uuid(),
  `department` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `live` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `uuid`, `department`, `category_name`, `sequence`, `active`, `live`, `created_at`, `updated_at`) VALUES
(1, 'f05e5254-fce8-11f0-a3c0-3863bba624fb', 1, 'Loan', 1, 1, 1, '2026-01-29 08:03:08', '2026-01-29 08:03:08'),
(2, 'f05e6718-fce8-11f0-a3c0-3863bba624fb', 1, 'Instant Loan', 2, 1, 1, '2026-01-29 08:03:08', '2026-01-29 08:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `service_documents`
--

CREATE TABLE `service_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `disclaimer` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_documents`
--

INSERT INTO `service_documents` (`id`, `service_id`, `doc_name`, `disclaimer`, `created_at`, `updated_at`) VALUES
(1, 5, 'Aadhar Card', '', '2026-01-29 08:57:31', '2026-01-29 08:57:31'),
(2, 5, 'fsdfsd', '', '2026-01-29 08:57:31', '2026-01-29 08:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `service_eligibility_criteria`
--

CREATE TABLE `service_eligibility_criteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `criteria_key` varchar(255) NOT NULL,
  `criteria_value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_eligibility_criteria`
--

INSERT INTO `service_eligibility_criteria` (`id`, `service_id`, `criteria_key`, `criteria_value`, `created_at`, `updated_at`) VALUES
(1, 5, 'Age Requirement', 'Borrower age should be between 21 and 65 years.', '2026-01-29 08:57:24', '2026-01-29 08:57:24'),
(2, 5, 'Business Vintage', 'Business should be minimum one year old', '2026-01-29 08:57:24', '2026-01-29 08:57:24');

-- --------------------------------------------------------

--
-- Table structure for table `service_features`
--

CREATE TABLE `service_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_features`
--

INSERT INTO `service_features` (`id`, `service_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 5, 'Fulfill your Financial Needs', 'Acquire a business loan at such low interest rates and without any collateral or security.', '2026-01-29 08:54:49', '2026-01-29 08:54:49'),
(2, 5, 'Fast Disbursal of Loan', 'Obtain a business loan in few hours.', '2026-01-29 08:54:49', '2026-01-29 08:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `service_fees_charges`
--

CREATE TABLE `service_fees_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `fee_key` varchar(255) NOT NULL,
  `fee_value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_fees_charges`
--

INSERT INTO `service_fees_charges` (`id`, `service_id`, `fee_key`, `fee_value`, `created_at`, `updated_at`) VALUES
(1, 5, 'Loan Processing Fees', 'Loan processing fees should be Up to 2% of the loan amount.', '2026-01-29 08:58:03', '2026-01-29 08:58:03'),
(2, 5, 'R.O.I', 'Reducing 15% to 24%', '2026-01-29 08:58:03', '2026-01-29 08:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `service_loan_repayment`
--

CREATE TABLE `service_loan_repayment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_loan_repayment`
--

INSERT INTO `service_loan_repayment` (`id`, `service_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 5, 'EMI', 'On a specific date, the borrower makes an instalment payment to a lenders. The EMI amount remains constant throughout the loan terms.', '2026-01-29 08:58:38', '2026-01-29 08:58:38'),
(2, 5, 'A Foreclosure', 'Foreclosure means a method of paying off the entire unpaid loan amount before the due date comes around. A personal loan typically has a lock-in term after which you can opt to foreclose the remaining debt and repay the total loan amount. However, we normally suggest you to foreclose your debt when you have some extra income.', '2026-01-29 08:58:38', '2026-01-29 08:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `service_overview`
--

CREATE TABLE `service_overview` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `keys` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keys`)),
  `values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`values`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_overview`
--

INSERT INTO `service_overview` (`id`, `service_id`, `title`, `keys`, `values`, `created_at`, `updated_at`) VALUES
(1, 5, 'Obtaining a business loan from Udhar Capital is simple and straightforward with affordable interest rates and flexible repayment choices. It doesn’t matter if you’re a small business or an established institution, Udhar Capital Business Loan is accessible', '[\"Amount\",\"Loan Tenure\"]', '[\"Up to 1 Crore\",\"3 to 5 Years\"]', '2026-01-29 08:53:48', '2026-01-29 08:53:48');

-- --------------------------------------------------------

--
-- Table structure for table `service_why_choose_us`
--

CREATE TABLE `service_why_choose_us` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_why_choose_us`
--

INSERT INTO `service_why_choose_us` (`id`, `service_id`, `image`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 5, 'uploads/why_choose_us/why_1769677182_752.png', 'Competitive Interest Rates', 'To meet your financial objectives and budget, we offer low interest rates.', '2026-01-29 08:59:42', '2026-01-29 08:59:42'),
(2, 5, 'uploads/why_choose_us/why_1769677182_773.png', 'Fast Approval Process', 'Using Udhar Capital, you can count on a simple and rapid approval process that gives you access to funds exactly when you need them.', '2026-01-29 08:59:42', '2026-01-29 08:59:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_email` (`email`),
  ADD UNIQUE KEY `unique_admin_uuid` (`uuid`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_application_docs`
--
ALTER TABLE `loan_application_docs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services_subcategories`
--
ALTER TABLE `services_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_subcat_uuid` (`uuid`),
  ADD KEY `idx_category_id` (`category_id`);

--
-- Indexes for table `service_banks`
--
ALTER TABLE `service_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_category_uuid` (`uuid`),
  ADD KEY `idx_department` (`department`);

--
-- Indexes for table `service_documents`
--
ALTER TABLE `service_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_eligibility_criteria`
--
ALTER TABLE `service_eligibility_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_features`
--
ALTER TABLE `service_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_fees_charges`
--
ALTER TABLE `service_fees_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_loan_repayment`
--
ALTER TABLE `service_loan_repayment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_overview`
--
ALTER TABLE `service_overview`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_why_choose_us`
--
ALTER TABLE `service_why_choose_us`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loan_application_docs`
--
ALTER TABLE `loan_application_docs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `services_subcategories`
--
ALTER TABLE `services_subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_banks`
--
ALTER TABLE `service_banks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service_documents`
--
ALTER TABLE `service_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_eligibility_criteria`
--
ALTER TABLE `service_eligibility_criteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_features`
--
ALTER TABLE `service_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_fees_charges`
--
ALTER TABLE `service_fees_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_loan_repayment`
--
ALTER TABLE `service_loan_repayment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_overview`
--
ALTER TABLE `service_overview`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_why_choose_us`
--
ALTER TABLE `service_why_choose_us`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
