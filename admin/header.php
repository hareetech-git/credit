<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include 'db/config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}




?>
<head>
	<meta charset="utf-8" />
	<title>Dashboard | Techmin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- App favicon -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">

	<!-- Daterangepicker css -->
	<link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css">

	<!-- Vector Map css -->
	<link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">

	<!-- Theme Config Js -->
	<script src="assets/js/config.js"></script>

	<!-- App css -->
	<link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

	<!-- Icons css -->
	<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
	<style>
	    .icon-btn {
			background-color: transparent;
			border: none;
			cursor: pointer;
			padding: 5px;
			color: #007bff;
			font-size: 18px;
		}

		.icon-btn:hover {
			color: #0056b3;
			background-color: #f1f1f1;
			border-radius: 5px;
		}
	</style>
</head>

<body>
	<!-- Begin page -->
	<div class="wrapper">
