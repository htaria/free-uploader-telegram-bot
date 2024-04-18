<?php

$adminPanel = json_encode([
    'keyboard' => [
        [['text' => '🗑-حذف فایل'], ['text' => '📤-آپلود فایل']],
        [['text' => '[👥]-آمار کاربران']]
    ],
    "resize_keyboard" => true,
    'one_time_keyboard' => true,
]);

$adminBack = json_encode([
    'keyboard' => [
        [['text' => '↩️-بازگشت به پنل']],
    ],
    "resize_keyboard" => true,
    'one_time_keyboard' => true,
]);


$endUpload = json_encode([
    'keyboard' => [
        [['text' => '⛔️پایان آپلود.']],
    ],
    "resize_keyboard" => true,
    'one_time_keyboard' => true,
]);

$botChanelKeyboard = json_encode([
    'inline_keyboard' => [
        [['text' => "$botChanel[1]", 'url' => "https://t.me/{$botChanel[0]}"]],
    ]
]);
