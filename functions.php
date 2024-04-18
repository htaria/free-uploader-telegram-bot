<?php
function RandomString()
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = null;
    for ($i = 0; $i < 9; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

function convert($size)
{
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . ['', 'K', 'M', 'G', 'T', 'P'][$i] . 'B';
}
function check_channel_member($channel, $chat_id)
{
    $res = bot("getChatMember", array("chat_id" => $channel, "user_id" => $chat_id));
    if ($res->result->status == "member") {
        return "yes";
    } elseif ($res->result->status == "administrator") {
        return "yes";
    } elseif ($res->result->status == "creator") {
        return "yes";
    } else {
        return "no";
    }
}
$stop = ["↩️-بازگشت به پنل","/start","🔙بازگشت به منوی اصلی"];
