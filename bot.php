<?php
$telegram_ip_ranges = [
    ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
    ['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok = false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
    if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok = true;
}
if (!$ok) die("Sik!");
//--------------------
include './config.php';
//----------START----------

if ($tc == "private") {
    if (check_channel_member("@$botChanel[0]", $chat_id) == "no") {
        $theText = "📣 - کاربر عزیز شما عضو کانال اسپانسر  ربات نیستید و امکان استفاده از ربات را ندارید .\n\n⭕️ لطفا در کانال زیر عضو شوید :\n\n🆔 @$botChanel[0]\n\nسپس به ربات برگشته و مجدد امتحان کنید ✔️";
        SendMessage($chat_id, $theText, "HTML", $message_id, $botChanelKeyboard);
    } else {
        if (str_starts_with($text, '/start _')) {
            $idFile = str_replace("/start _", "", $text);
            $File = mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `code` = '{$idFile}'");
            $download = mysqli_fetch_assoc($File);
            $dl = $download['dl'];
            $plus = $dl + 1;
            $conn->query("UPDATE `{$filesTable}` SET `dl`='{$plus}' WHERE `code` = '{$idFile}'");
            foreach ($File as $Files) {
                $dataFile = $Files['file_id'];
                $method = $Files['file'];
                bot("send$method", [
                    'chat_id' => $chat_id,
                    "$method" => $dataFile,
                    'caption' => "📥~آمار دانلود: $dl\n@$botChanel[0]",
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => "html",
                ]);
            }
            if ($user) {
                $conn->query("UPDATE `{$usersTable}` SET `step`='none' WHERE `id` = '{$from_id}' LIMIT 1");
            } else {
                // اینجا می تونید برای کاربری که برای اولین بار در ربات استارت زده یک پیام تبلیغاتی ارسال کنید.
                $theText = "پیام تبلیغاتی برای کسی که اولین بار در ربات استارت زده.\n\nدر لاین 44 و 56 فایل bot.php می تونید این پیام رو تغییر بدید.";
                SendMessage($chat_id, $theText, "HTML", $message_id);
                $conn->query("INSERT IGNORE INTO `{$usersTable}` (`id`,`step`,`start`) VALUES ('{$from_id}','none','start')");
            }
        }
        if ($text == "/start" || $text == "🔙") {
            $theText = "به ربات آپلودر خوش آمدید.";
            SendMessage($chat_id, $theText, "HTML", $message_id);
            if ($user) {
                $conn->query("UPDATE `{$usersTable}` SET `step`='none' WHERE `id` = '{$from_id}' LIMIT 1");
            } else {
                // اینجا می تونید برای کاربری که برای اولین بار در ربات استارت زده یک پیام تبلیغاتی ارسال کنید.
                $theText = "پیام تبلیغاتی برای کسی که اولین بار در ربات استارت زده.\n\nدر لاین 44 و 56 فایل bot.php می تونید این پیام رو تغییر بدید.";
                SendMessage($chat_id, $theText, "HTML", $message_id);
                $conn->query("INSERT IGNORE INTO `{$usersTable}` (`id`,`step`,`start`) VALUES ('{$from_id}','none','start')");
            }
        }
    }
    //----------ADMIN----------
    if (in_array($from_id, $admins) || in_array($fromId, $admins)) {
        if ($text == "پنل" || $text == "/panel" || $text == "↩️-بازگشت به پنل") {
            $theText = "[👤]-به پنل ادمین خوش آمدید.";
            SendMessage($chat_id, $theText, "HTML", $message_id, $adminPanel);
            $conn->query("UPDATE `{$usersTable}` SET `step`='none' WHERE `id` = '{$from_id}' LIMIT 1");
        }
        //-------------------------------------------------------------      
        if ($text == "🗑-حذف فایل" && $step == "none") {
            $theText = "🗂-لطفا شناسه فایل مورد نظر خود را جهت حذف از ربات ارسال کنید:";
            SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
            $conn->query("UPDATE `{$usersTable}` SET `step`='Delete' WHERE `id` = '{$from_id}' LIMIT 1");
        } else if ($step == "Delete" && !in_array($text, $stop)) {
            $query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `code` = '{$text}' LIMIT 1"));
            if ($query) {
                $theText = "✔️ فایل با موفقیت حذف شد ... !";
                SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                $conn->query("UPDATE `{$usersTable}` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
                $conn->query("DELETE FROM `{$filesTable}` WHERE `code` = '{$text}' LIMIT 1");
            } else {
                $theText = "▪️خطا , این فایل در دیتابیس موجود نمیباشد ... !";
                SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
            }
        }

        //-------------------------------------------------------------
        if ($text == "📤-آپلود فایل" && $step == "none") {
            $code = RandomString();
            $theText = " 🗂-لطفا فایل مورد نظر خود را جهت آپلود در ربات ارسال کنید:";
            SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
            $conn->query("UPDATE `{$usersTable}` SET `step`='uplods-$code' WHERE `id` = '{$from_id}' LIMIT 1");
        } else if (str_starts_with($step, "uplods-") !== false) {
            $rand = str_replace("uplods-", "", $step);
            if (isset($message->document)) {
                $file_id = $message->document->file_id;
                $file_size = $message->document->file_size;
                $file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `file_id` = '{$file_id}' LIMIT 1"));
                if (!$file) {
                    $size = convert($file_size);
                    $time = date('h:i:s');
                    $date = date('Y/m/d');
                    $theCaption = "📍فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n♻️ جهت ادامه فرایند آپلود(آپلود گروهی)، فایل بعدی را ارسال نمایید و در غیر این صورت بر روی گزینه «⛔️پایان آپلود» کلیک نمایید.\n▪️ شناسه فایل شما : <code>$rand</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n📥لینک: https://t.me/" . $usernamebot . "?start=_" . $rand;
                    SendDocument($chat_id, $file_id, $theCaption, $message_id, "HTML", $endUpload);
                    $conn->query("INSERT INTO `{$filesTable}` (`code`, `file_id`, `file`, `chanel`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$rand}', '{$file_id}', 'document', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
                } else {
                    $theText = "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !";
                    SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                }
            }
            //-------------------------
            else if (isset($message->video)) {
                $file_id = $message->video->file_id;
                $file_size = $message->video->file_size;
                $file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `file_id` = '{$file_id}' LIMIT 1"));
                if (!$file) {
                    global $rand;
                    $size = convert($file_size);
                    $time = date('h:i:s');
                    $date = date('Y/m/d');
                    $theCaption = "📍فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n♻️ جهت ادامه فرایند آپلود(آپلود گروهی)، فایل بعدی را ارسال نمایید و در غیر این صورت بر روی گزینه «⛔️پایان آپلود» کلیک نمایید.\n▪️ شناسه فایل شما : <code>$rand</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n📥لینک: https://t.me/" . $usernamebot . "?start=_" . $rand;
                    sendVideo($chat_id, $file_id, $theCaption, $message_id, "HTML", $endUpload);
                    $conn->query("INSERT INTO `{$filesTable}` (`code`, `file_id`, `file`, `chanel`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$rand}', '{$file_id}', 'video', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
                } else {
                    $theText = "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !";
                    SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                }
            }
            //-------------------------   
            else if (isset($message->photo)) {
                $photo = $message->photo;
                $file_id = $photo[count($photo) - 1]->file_id;
                $file_size = $photo[count($photo) - 1]->file_size;
                $file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `file_id` = '{$file_id}' LIMIT 1"));
                if (!$file) {
                    global $rand;
                    $size = convert($file_size);
                    $time = date('h:i:s');
                    $date = date('Y/m/d');
                    $theCaption = "📍فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n♻️ جهت ادامه فرایند آپلود(آپلود گروهی)، فایل بعدی را ارسال نمایید و در غیر این صورت بر روی گزینه «⛔️پایان آپلود» کلیک نمایید.\n▪️ شناسه فایل شما : <code>$rand</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n📥لینک: https://t.me/" . $usernamebot . "?start=_" . $rand;
                    bot('sendphoto', [
                        'chat_id' => $chat_id,
                        'photo' => $file_id,
                        'caption' => $theCaption,
                        'reply_to_message_id' => $message_id,
                        'parse_mode' => "HTML",
                        'reply_markup' => $endUpload
                    ]);
                    $conn->query("INSERT INTO `{$filesTable}` (`code`, `file_id`, `file`, `chanel`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$rand}', '{$file_id}', 'photo', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
                } else {
                    $theText = "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !";
                    SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                }
            }
            //------------------------- 
            else if (isset($message->voice)) {
                $file_id = $message->voice->file_id;
                $file_size = $message->voice->file_size;
                $file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `file_id` = '{$file_id}' LIMIT 1"));
                if (!$file) {
                    global $rand;
                    $size = convert($file_size);
                    $time = date('h:i:s');
                    $date = date('Y/m/d');
                    $theCaption = "📍فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n♻️ جهت ادامه فرایند آپلود(آپلود گروهی)، فایل بعدی را ارسال نمایید و در غیر این صورت بر روی گزینه «⛔️پایان آپلود» کلیک نمایید.\n▪️ شناسه فایل شما : <code>$rand</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n📥لینک: https://t.me/" . $usernamebot . "?start=_" . $rand;
                    sendVoice($chat_id, $file_id, $theCaption, $message_id, "HTML", $endUpload);
                    $conn->query("INSERT INTO `{$filesTable}` (`code`, `file_id`, `file`, `chanel`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$rand}', '{$file_id}', 'voice', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
                } else {
                    $theText = "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !";
                    SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                }
            }
            //------------------------- 
            else if (isset($message->audio)) {
                $file_id = $message->audio->file_id;
                $file_size = $message->audio->file_size;
                $file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `file_id` = '{$file_id}' LIMIT 1"));
                if (!$file) {
                    global $rand;
                    $size = convert($file_size);
                    $time = date('h:i:s');
                    $date = date('Y/m/d');
                    $theCaption = "📍فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n♻️ جهت ادامه فرایند آپلود(آپلود گروهی)، فایل بعدی را ارسال نمایید و در غیر این صورت بر روی گزینه «⛔️پایان آپلود» کلیک نمایید.\n▪️ شناسه فایل شما : <code>$rand</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n📥لینک: https://t.me/" . $usernamebot . "?start=_" . $rand;
                    sendAudio($chat_id, $file_id, $theCaption, $message_id, "HTML", $endUpload);
                    $conn->query("INSERT INTO `{$filesTable}` (`code`, `file_id`, `file`, `chanel`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$rand}', '{$file_id}', 'audio', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
                } else {
                    $theText = "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !";
                    SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                }
            } else if (isset($message->sticker)) {
                $file_id = $message->sticker->file_id;
                $file_size = $message->sticker->file_size;
                $file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `{$filesTable}` WHERE `file_id` = '{$file_id}' LIMIT 1"));
                if (!$file) {
                    global $rand;
                    $size = convert($file_size);
                    $time = date('h:i:s');
                    $date = date('Y/m/d');
                    sendSticker($chat_id, $file_id, $message_id, "HTML");
                    $theText = "📍فایل شما با موفقیت داخل دیتابیس ذخیره شده ... !\n♻️ جهت ادامه فرایند آپلود(آپلود گروهی)، فایل بعدی را ارسال نمایید و در غیر این صورت بر روی گزینه «⛔️پایان آپلود» کلیک نمایید.\n▪️ شناسه فایل شما : <code>$rand</code>\n\n➖ بقیه اطلاعات فایل شما : \n\n💾  حجم فایل : <b>$size</b>\n📥لینک: https://t.me/" . $usernamebot . "?start=_" . $rand;
                    SendMessage($chat_id, $theText, "HTML", $message_id, $endUpload);
                    $conn->query("INSERT INTO `{$filesTable}` (`code`, `file_id`, `file`, `chanel`, `file_size`, `user_id`, `date`, `time`, `dl`) VALUES ('{$rand}', '{$file_id}', 'sticker', '', '{$file_size}', '{$from_id}', '{$date}', '{$time}', '1')");
                } else {
                    $theText = "▪️ خطا , این فایل قبلا در دیتابیس اپلود شده است ... !";
                    SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                }
            } else if ($text == "⛔️پایان آپلود.") {
                $theText = "روند آپلود با موفقیت متوقف شد✔️\n\n🔙|جهت بازگشت روی دکمه زیر کلیک کنید.";
                SendMessage($chat_id, $theText, "HTML", $message_id, $adminBack);
                $conn->query("UPDATE `{$usersTable}` SET `step`='none' WHERE `id` = '{$from_id}' LIMIT 1");
            }
        }

        //--------------------------------------------------------------------
        if ($text == "[👥]-آمار کاربران") {
            $query = mysqli_query($conn, "SELECT * FROM `{$usersTable}`");
            $num = mysqli_num_rows($query);
            if ($num > 0) {
                $result = "📂لیست کاربران ربات:\n📍تعداد کاربران شما: $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
                $cnt = ($num >= 10) ? 10 : $num;
                for ($i = 1; $i <= $cnt; $i++) {
                    $fetch = mysqli_fetch_assoc($query);
                    $id = $fetch['id'];
                    $result .= "[👤]-کاربر شماره" . $i . ":" . PHP_EOL . "🔹شناسه عددی:$id" . PHP_EOL . "➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖" . PHP_EOL;
                }
                if ($num > 10) {
                    SendMessage($chat_id, $result, "HTML", $message_id, json_encode([
                        'inline_keyboard' => [
                            [['text' => "▪️ صفحه ی بعدی", 'callback_data' => "Dnext_10"]]
                        ]
                    ]));
                } else {
                    SendMessage($chat_id, $result, "HTML", $message_id);
                }
            } else {
                $theText = "▪️ربات شما هیچ کاربری ندارید... !";
                SendMessage($chat_id, $theText, "HTML", $message_id);
            }
        } elseif (str_starts_with($data, "Dnext_") !== false) {
            $last_id = str_replace('Dnext_', "", $data);
            $query = mysqli_query($conn, "SELECT * FROM `{$usersTable}`");
            $num = mysqli_num_rows($query);
            $result = "📂لیست کاربران ربات:\n📍تعداد کاربران شما: $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
            $records = [];
            while ($fetch = mysqli_fetch_assoc($query)) {
                $records[] = $fetch;
            }
            if ($last_id + 10 < $num) {
                $endponit = $last_id + 10;
            } else {
                $endponit = $num;
            }
            for ($i = $last_id; $i < $endponit; $i++) {
                $id = $records[$i]['id'];
                $result .= "[👤]-کاربر شماره" . $i . ":" . PHP_EOL . "🔹شناسه عددی:$id" . PHP_EOL . "➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖" . PHP_EOL;
            }
            if ($num > $last_id + 10) {
                EditMessageText($chatId, $messageId, $result, "HTML", json_encode([
                    'inline_keyboard' => [
                        [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_" . $endponit], ['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_" . $endponit]]
                    ]
                ]));
            } else {
                EditMessageText($chatId, $messageId, $result, "HTML", json_encode([
                    'inline_keyboard' => [
                        [['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_" . $endponit]]
                    ]
                ]));
            }
        } elseif (str_starts_with($data, "Dprev_") !== false) {
            $last_id = str_replace('Dprev_', "", $data);
            $query = mysqli_query($conn, "SELECT * FROM `{$usersTable}`");
            $num = mysqli_num_rows($query);
            $result = "📂لیست کاربران ربات:\n📍تعداد کاربران شما: $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
            $records = [];
            while ($fetch = mysqli_fetch_assoc($query)) {
                $records[] = $fetch;
            }
            if ($last_id % 10 == 0) {
                $endponit = $last_id - 10;
            } else {
                $last_id = $last_id - ($last_id % 10);
                $endponit = $last_id;
            }
            for ($i = $endponit - 10; $i <= $endponit; $i++) {
                $id = $records[$i]['id'];
                $result .= "[👤]-کاربر شماره" . $i . ":" . PHP_EOL . "🔹شناسه عددی:$id" . PHP_EOL . "➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖" . PHP_EOL;
            }
            if ($num > $last_id and $endponit - 10 > 0) {
                EditMessageText($chatId, $messageId, $result, "HTML", json_encode([
                    'inline_keyboard' => [
                        [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_" . $endponit], ['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_" . $endponit]]
                    ]
                ]));
            } else {
                EditMessageText($chatId, $messageId, $result, "HTML", json_encode([
                    'inline_keyboard' => [
                        [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_" . $endponit]]
                    ]
                ]));
            }
        }
    }
}


//-----------------------------------------------------------------------------------------------------

//The End
unlink("error_log");
