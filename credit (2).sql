-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 10:06 AM
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
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uuid` char(36) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `customer_number` varchar(100) DEFAULT NULL,
  `customer_sub_type` varchar(100) DEFAULT NULL,
  `place_of_contact` varchar(255) DEFAULT NULL,
  `currency_code` varchar(10) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `salutation` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile_phone` varchar(50) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `payment_terms_label` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `gst_treatment` varchar(100) DEFAULT NULL,
  `gstin` varchar(100) DEFAULT NULL,
  `pan_number` varchar(100) DEFAULT NULL,
  `billing_attention` varchar(255) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `billing_street2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_country` varchar(100) DEFAULT NULL,
  `billing_code` varchar(20) DEFAULT NULL,
  `billing_fax` varchar(50) DEFAULT NULL,
  `billing_phone` varchar(50) DEFAULT NULL,
  `shipping_attention` varchar(255) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_street2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `shipping_code` varchar(20) DEFAULT NULL,
  `shipping_fax` varchar(50) DEFAULT NULL,
  `shipping_phone` varchar(50) DEFAULT NULL,
  `portal_status` varchar(50) DEFAULT NULL,
  `skype_identity` varchar(100) DEFAULT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(15,2) DEFAULT NULL,
  `opening_balance_exchange_rate` decimal(10,4) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `rise_invoice` tinyint(1) NOT NULL DEFAULT 1,
  `active_dashboard` tinyint(1) NOT NULL DEFAULT 0,
  `bank_account_payment` varchar(255) DEFAULT NULL,
  `tds_name` varchar(100) DEFAULT NULL,
  `tds_section_code` varchar(20) DEFAULT NULL,
  `tds_percentage` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category_id`, `sub_category_id`, `service_name`, `title`, `slug`, `short_description`, `long_description`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Loan for CA', 'Loan for Chartered Accountant', 'loan-for-ca', 'Tailored loan for Chartered Accountants.', 'Special loan products designed for Chartered Accountants with flexible repayment options.', '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(2, 1, 3, 'Loan for CS', 'Loan for Company Secretary', 'loan-for-cs', 'Business loan for Company Secretaries.', 'Customized financing solutions for Company Secretaries.', '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(3, 1, 3, 'Loan for Doctor', 'Loan for Doctor', 'loan-for-doctor', 'Medical professionals loan.', 'Loans designed specifically for doctors to support clinic setup and expansion.', '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(4, 1, 3, 'Loan for Architect', 'Loan for Architect', 'loan-for-architect', 'Loan for architecture professionals.', 'Professional loans for architects to fund projects and office expansion.', '2026-01-29 08:03:53', '2026-01-29 08:03:53'),
(5, 1, 2, 'Secured Business Loan', 'Looking for a secured business Loan?', 'looking-for-a-secured-business-loan', 'Are you ready to take your business to greater heights? Apply for business loans online at low-interest rates through Udhar Capital. Do apply now!', 'For business owners looking to start or expand their enterprises, online business loans are critical. Udhar Capital financial services have undergone continuous innovation to satisfy the demands of entrepreneurs seeking capital. The purpose of the business loan is to businesses acquire the finance they need.The instant business loan may be utilised to cover all of your company’s needs, it’s the finest lending option for a startup. Apply business loans online for an attractive and affordable interest rate ensures you won’t have to cut back on expenses.', '2026-01-29 08:53:13', '2026-01-29 08:53:13');

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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_banks`
--

INSERT INTO `service_banks` (`id`, `service_id`, `bank_key`, `bank_value`, `created_at`, `updated_at`) VALUES
(1, 5, 'Poonawalla', '18.00% p.a.', '2026-01-29 09:00:34', '2026-01-29 09:00:34'),
(2, 5, 'Axis Bank', '14.95% - 19.20% p.a.', '2026-01-29 09:00:34', '2026-01-29 09:00:34'),
(3, 5, 'HDB Financial Services Ltd.', 'Up to 36% p.a.', '2026-01-29 09:00:34', '2026-01-29 09:00:34');

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
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `unique_email` (`email_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_service_id` (`service_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
