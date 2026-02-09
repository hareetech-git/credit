-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 08:18 AM
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
(1, '', 'admin', 'admin@gmail.com', '$2y$10$d.HjZm4Xs3kIhH7FrbPEhOj9T9ueD7lXn6UhCStB9LR8pv6UE5B5O', 'admin', '2026-01-28 06:44:17', '2026-02-07 05:53:41');

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
(2, 'Sanyam Srivastava', 'sanyam.fullstackdev@gmail.com', '9984278970', '$2y$10$fYMqxeGHcz5uS2QCzZ7yIueQ7KQMsa1vhyzzW.ePsDqhfHiDIUwJ.', NULL, 'active', '2026-02-07 06:06:11', '2026-02-07 06:08:38'),
(3, 'Sanyam Srivastava', 'srivastavasanyadafsm8052@gmail.com', '9984278971', '$2y$10$TK6FLY30IiOgP0x1uJFxIOpJFrMmriONPsPzLR67El/vPf2ZOIe8y', NULL, 'active', '2026-02-07 09:09:41', '2026-02-07 09:09:41');

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
(2, 2, 'ABCDE1234F', '2026-02-26', 'Uttar Pradesh', 'Lakhimpur', '261506', 'business', 'Random Pvt Ltd', 89998.00, 'Sanyam Srivastava', '9984278970', 'Sanyam Srivastava', '9984278970', '2026-02-07 06:06:11', '2026-02-07 06:06:11'),
(3, 3, 'ABCDE1234F', '2008-01-31', 'Uttar Pradesh', 'Lakhimpur', '261506', 'business', 'Hareetech Development Pvt Ltd', 8000.00, 'Sanyam Srivastava', '9984278970', 'Sanyam Srivastavaa', '9984278971', '2026-02-07 09:09:41', '2026-02-07 09:09:41');

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
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `loan_type_id` int(11) NOT NULL,
  `loan_type_name` varchar(255) NOT NULL,
  `query_message` text NOT NULL,
  `status` enum('new','assigned','conversation','converted','closed') NOT NULL DEFAULT 'new',
  `assigned_staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `converted_by_role` enum('admin','staff','customer') DEFAULT NULL,
  `converted_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `closed_by_role` enum('admin','staff','customer') DEFAULT NULL,
  `closed_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiries`
--

INSERT INTO `enquiries` (`id`, `customer_id`, `full_name`, `phone`, `email`, `loan_type_id`, `loan_type_name`, `query_message`, `status`, `assigned_staff_id`, `assigned_by`, `assigned_at`, `converted_by_role`, `converted_by_id`, `converted_at`, `closed_by_role`, `closed_by_id`, `closed_at`, `created_at`) VALUES
(3, 2, 'Sanyam Customer', '9984278970', 'sanyam.fullstackdev@gmail.com', 2, 'Business Loans', 'fujyuj', 'converted', 1, 1, '2026-02-05 10:21:44', 'staff', 1, '2026-02-05 10:22:58', NULL, NULL, NULL, '2026-02-05 10:17:09'),
(4, NULL, 'Sanyam Customer', '9948728807', 'fgadmin@gmail.com', 1, 'Personal Loan', 'gfd', 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-05 12:00:59'),
(5, NULL, 'new test', '7878787878', 'custohgjgjkgjhgymer@gmail.com', 1, 'Personal Loan', 'fddrtd', 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:27:44');

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_conversations`
--

CREATE TABLE `enquiry_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enquiry_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiry_conversations`
--

INSERT INTO `enquiry_conversations` (`id`, `enquiry_id`, `created_at`) VALUES
(1, 3, '2026-02-05 10:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_messages`
--

CREATE TABLE `enquiry_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_role` enum('admin','staff','customer') NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiry_messages`
--

INSERT INTO `enquiry_messages` (`id`, `conversation_id`, `sender_role`, `sender_id`, `message`, `created_at`) VALUES
(1, 1, 'admin', 1, 'jhj', '2026-02-05 10:21:53'),
(2, 1, 'customer', 2, 'hfghfg', '2026-02-05 10:22:06'),
(3, 1, 'customer', 2, 'hfghfgh', '2026-02-05 10:22:16'),
(4, 1, 'staff', 1, 'nrtr', '2026-02-05 10:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_notes`
--

CREATE TABLE `enquiry_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enquiry_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_by_role` enum('admin','staff') NOT NULL,
  `created_by_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiry_notes`
--

INSERT INTO `enquiry_notes` (`id`, `enquiry_id`, `note`, `created_by_role`, `created_by_id`, `created_at`) VALUES
(1, 3, 'jkhj', 'staff', 1, '2026-02-05 10:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_role` enum('admin','staff') NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `created_by`, `created_role`, `status`, `created_at`) VALUES
(1, 'Who can apply for a loan on this website?', 'Any Indian citizen aged 18 years or above with a valid identity proof, active mobile number, and bank account can apply for a loan through our platform.', 1, 'admin', 1, '2026-02-06 07:29:20'),
(2, 'How long does it take to get loan approval?', 'Loan approval usually takes a few minutes after submitting the application. In some cases, it may take up to 24 hours depending on document verification.', 1, 'admin', 1, '2026-02-06 07:29:20'),
(3, 'What documents are required to apply for a loan?', 'You need a valid identity proof, address proof, and basic income details. No physical paperwork is required as the process is completely online.', 2, 'staff', 1, '2026-02-06 07:29:20'),
(4, 'How will I receive the loan amount?', 'Once your loan is approved, the amount will be directly credited to your registered bank account via secure electronic transfer.', 2, 'staff', 1, '2026-02-06 07:29:20'),
(5, 'Is my personal and financial information safe?', 'Yes. We use industry-standard security measures and encryption to protect your personal and financial information at all times.', 1, 'admin', 1, '2026-02-06 07:29:20');

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `interest_type` enum('year','month') NOT NULL DEFAULT 'year',
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

INSERT INTO `loan_applications` (`id`, `customer_id`, `service_id`, `assigned_staff_id`, `assigned_by`, `assigned_at`, `interest_rate`, `interest_type`, `requested_amount`, `tenure_years`, `emi_amount`, `status`, `created_at`, `rejection_note`) VALUES
(2, 2, 1, 1, 1, '2026-02-07 08:37:09', 1.00, 'year', 100000.00, 12, 8378.54, 'approved', '2026-02-07 06:06:11', ''),
(3, 3, 5, NULL, NULL, NULL, 0.00, 'year', 90000.00, 0, 0.00, 'pending', '2026-02-07 09:09:41', NULL);

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

--
-- Dumping data for table `loan_application_docs`
--

INSERT INTO `loan_application_docs` (`id`, `loan_application_id`, `doc_name`, `doc_path`, `created_at`, `status`, `rejection_reason`) VALUES
(1, 3, 'Identity Proof', 'uploads/loans/loan_3_1770455381_Identity_Proof.png', '2026-02-07 09:09:41', 'pending', NULL),
(2, 3, 'fsdfsd', 'uploads/loans/loan_3_1770455381_fsdfsd.png', '2026-02-07 09:09:41', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `perm_key` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `perm_key`, `description`) VALUES
(1, 'cust_create', 'Add New Customers'),
(2, 'cust_read', 'View Customer Details'),
(3, 'cust_update', 'Edit Customer Info'),
(4, 'cust_delete', 'Delete Customers'),
(5, 'loan_view', 'View Loan Apps'),
(6, 'loan_process', 'Approve/Reject Loans'),
(7, 'loan_delete', 'Delete Loan Applications'),
(8, 'enquiry_view_assigned', 'View Assigned Enquiries'),
(9, 'enquiry_view_all', 'View All Enquiries'),
(10, 'enquiry_delete', 'Delete Enquiries'),
(11, 'enquiry_status_change', 'Change Enquiry Status'),
(12, 'faq_create', 'Create FAQs'),
(13, 'faq_delete', 'Delete FAQs');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'All Staff');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12);

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
  `card_img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category_id`, `sub_category_id`, `service_name`, `title`, `slug`, `short_description`, `long_description`, `hero_image`, `card_img`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Loan for CA', 'Loan for Chartered Accountant', 'loan-for-ca', 'Tailored loan for Chartered Accountants.', 'Special loan products designed for Chartered Accountants with flexible repayment options.', '', 'uploads/services/service_card_1770446416_913.avif', '2026-01-29 08:03:53', '2026-02-07 06:40:16'),
(2, 1, 3, 'Loan for CS', 'Loan for Company Secretary', 'loan-for-cs', 'Business loan for Company Secretaries.', 'Customized financing solutions for Company Secretaries.', '', 'uploads/services/service_card_1770446506_245.avif', '2026-01-29 08:03:53', '2026-02-07 06:41:46'),
(3, 1, 3, 'Loan for Doctor', 'Loan for Doctor', 'loan-for-doctor', 'Medical professionals loan.', 'Loans designed specifically for doctors to support clinic setup and expansion.', '', 'uploads/services/service_card_1770446634_104.avif', '2026-01-29 08:03:53', '2026-02-07 06:43:54'),
(4, 1, 3, 'Loan for Architect dsdfd', 'Loan for Architect', 'loan-for-architect', 'Loan for architecture professionals.', 'Professional loans for architects to fund projects and office expansion.', '', 'uploads/services/service_card_1770446710_324.avif', '2026-01-29 08:03:53', '2026-02-07 06:45:10'),
(5, 1, 2, 'Secured Business Loan', 'Looking for a secured business Loan?', 'looking-for-a-secured-business-loan', 'Are you ready to take your business to greater heights? Apply for business loans online at low-interest rates through Udhar Capital. Do apply now!', 'For business owners looking to start or expand their enterprises, online business loans are critical. Udhar Capital financial services have undergone continuous innovation to satisfy the demands of entrepreneurs seeking capital. The purpose of the business loan is to businesses acquire the finance they need.The instant business loan may be utilised to cover all of your company’s needs, it’s the finest lending option for a startup. Apply business loans online for an attractive and affordable interest rate ensures you won’t have to cut back on expenses.', 'uploads/services/service_1769768621_534.jpg', 'uploads/services/service_card_1770446733_289.avif', '2026-01-29 08:53:13', '2026-02-07 06:45:33'),
(6, 1, 1, 'Personal Loan', 'APPLY FOR PERSONAL LOAN', 'apply-for-personal-loan', 'Collateral-free personal loan with lowest interest rate is just a few clicks away from you. Apply Personal Loan online and fulfill your dreams without worrying about money', 'The term personal loan has emerged as a boon for individuals seeking quick and hassle free access to funds. Personal loan is a financial tool that offers unparalleled flexibility and convenience to borrowers. Unlike traditional loans, a personal loan is unsecured, meaning you don’t have to provide any collateral to secure it.\r\n\r\nWith Udhar Capital Personal Loan you can say goodbye to lengthy approval processes and mountains of paperwork. Our application process ensures that you can get the funds you need when you need them, without the unnecessary delays. We are committed to providing you with the best financial solutions.', 'uploads/services/service_1769770355_584.jfif', 'uploads/services/service_card_1770446778_715.avif', '2026-01-29 12:09:21', '2026-02-07 06:46:18'),
(7, 1, 5, 'CSWIP 3.1 Preparatory Course', 'ndjkc', 'ndjkc', 'sadfcasdf', 'dasfdas', '', NULL, '2026-02-04 06:45:13', '2026-02-04 06:45:13'),
(8, 1, 2, 'Eddy Current Testing (ET)', 'fcsdf', 'fcsdfsdfsd', 'dsfsdf', 'dsfdsaf', '', NULL, '2026-02-04 07:07:05', '2026-02-04 07:07:05');

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
(5, 5, 'Identity Proof', '', '2026-02-04 07:20:45', '2026-02-04 07:20:45'),
(6, 5, 'fsdfsd', '', '2026-02-04 07:20:45', '2026-02-04 07:20:45'),
(7, 6, 'Other', '', '2026-02-04 07:21:22', '2026-02-04 07:21:22'),
(8, 6, 'new', '', '2026-02-04 07:21:22', '2026-02-04 07:21:22');

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

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `password`, `department_id`, `created_by`, `status`, `created_at`, `role_id`) VALUES
(1, 'Sanyam Staff', 'rahul9369917075@gmail.com', '$2y$10$xescOMJIKTr4o33NG2LVXuckSu5mroEUOsH.Uu4j7zpMZbnNyf5hC', 1, 1, 'active', '2026-02-05 09:40:42', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff_permissions`
--

CREATE TABLE `staff_permissions` (
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_permissions`
--

INSERT INTO `staff_permissions` (`staff_id`, `permission_id`) VALUES
(1, 13);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_enquiry_customer` (`customer_id`),
  ADD KEY `idx_enquiry_status` (`status`),
  ADD KEY `idx_enquiry_assigned_staff` (`assigned_staff_id`),
  ADD KEY `idx_enquiry_assigned_by` (`assigned_by`);

--
-- Indexes for table `enquiry_conversations`
--
ALTER TABLE `enquiry_conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_enquiry_conversation` (`enquiry_id`);

--
-- Indexes for table `enquiry_messages`
--
ALTER TABLE `enquiry_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation_id` (`conversation_id`),
  ADD KEY `idx_sender` (`sender_role`,`sender_id`);

--
-- Indexes for table `enquiry_notes`
--
ALTER TABLE `enquiry_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_enquiry_notes_enquiry` (`enquiry_id`),
  ADD KEY `idx_enquiry_notes_creator` (`created_by_role`,`created_by_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assigned_staff` (`assigned_staff_id`),
  ADD KEY `idx_assigned_by` (`assigned_by`);

--
-- Indexes for table `loan_application_docs`
--
ALTER TABLE `loan_application_docs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perm_key` (`perm_key`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`);

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
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `staff_permissions`
--
ALTER TABLE `staff_permissions`
  ADD PRIMARY KEY (`staff_id`,`permission_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enquiry_conversations`
--
ALTER TABLE `enquiry_conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enquiry_messages`
--
ALTER TABLE `enquiry_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enquiry_notes`
--
ALTER TABLE `enquiry_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_application_docs`
--
ALTER TABLE `loan_application_docs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
