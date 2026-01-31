<?php
// core/db_master.php

if (
    $_SERVER['HTTP_HOST'] === 'localhost' ||
    $_SERVER['HTTP_HOST'] === '127.0.0.1'
) {
    // LOCAL
    $DB_HOST = "localhost";
    $DB_USER = "root";
    $DB_PASS = "";
    $DB_NAME = "credit";
    $DB_PORT = 3306;
} else {
    // PRODUCTION
    $DB_HOST = "localhost";
    $DB_USER = "u443392627_credit_loan";
    $DB_PASS = "~Q9fm9|HZi8y"; 
    $DB_NAME = "u443392627_credit_loan";
    $DB_PORT = 3306;
}
