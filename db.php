<?php
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$usersTable}` WHERE `id` = '{$from_id}' AND `start` = 'start' LIMIT 1"));
$step = $user['step'];
mysqli_query(
    $conn,
    "CREATE TABLE `{$filesTable}` (
            `code` char(250) NOT NULL ,
            `file_id` char(200) NOT NULL,
            `file` char(200) NOT NULL,
            `chanel` char(200) CHARACTER SET utf8mb4 NOT NULL,
            `file_size` bigint(20) NOT NULL,
            `user_id` bigint(20) NOT NULL,
            `date` char(200) NOT NULL,
            `time` char(200) NOT NULL,
            `dl` bigint(20) NOT NULL
            )"
);
mysqli_query(
    $conn,
    "CREATE TABLE `{$usersTable}` (
            `id` varchar(50) NOT NULL PRIMARY KEY,
            `step` TEXT NOT NULL,
            `start` varchar(50) NOT NULL
            )"
);
