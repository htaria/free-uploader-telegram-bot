<?php

$myDatabase = [
    'host' => 'localhost',
    'username' => 'db_username', // یوزرنیم دیتابیس
    'password' => 'db_password', // پسورد دیتابیس
    'dbname' => 'db_name' // نام دیتابیس
];
$conn = mysqli_connect($myDatabase["host"], $myDatabase["username"], $myDatabase["password"], $myDatabase["dbname"]);
$usersTable = ""; // نام جدول  یوزر های ربات
$filesTable = ""; // نام جدول فایل های ربات

define('API_KEY', 'TOKEN'); // توکن رو جای عبارت TOKEN بذارید.
$usernamebot = "BOTUSERNAME"; // یوزرنیم ربات رو بدون @ جای BOTUSERNAME بذارید.
$admins = [5551507638, 1311631827]; // آیدی عددی ادمین های ربات به صورت آرایه.
$botChanel = ["URL", "NAME"]; // ایندکس 0 آرایه یوزرنیم کانال بدون@ و ایندکس 1 آرایه نام کانال جوین اجباری ربات هست. ربات باید ادمین این کانال باشه.
include './telegram.php';
include './db.php';
include './functions.php';
include './keyboards.php';
