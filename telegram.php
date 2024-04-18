<?php

$update = json_decode(file_get_contents('php://input'));
$channel = $update->channel_post;
$channel_message_id = $channel->message_id;
$message = $update->message;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$from_id = $message->from->id;
$text = $message->text;
$first_name = $message->from->first_name;
$last_name = $message->from->last_name;
$user_name = $message->from->username;
$tc = $update->message->chat->type;
// ------------------------------------------------------------------

if (isset($update->callback_query)) {
    $callback_query = $update->callback_query;
    $data = $callback_query->data;
    $chatId = $callback_query->message->chat->id;
    $fromId = $callback_query->from->id;
    $messageId = $callback_query->message->message_id;
    $firstName = $callback_query->from->first_name;
    $lastName = $callback_query->from->last_name;
    $username = $callback_query->from->username;
    $callback_query_id0 = $callback_query->id;
}
//----------------------------------------------------------------
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}
function answerCallbackQuery($callback_query_id, $text, $show_alert)
{
    return bot('answerCallbackQuery', [
        'callback_query_id' => $callback_query_id,
        'text' => $text,
        'show_alert' => $show_alert
    ]);
}

function EditKeyboard($chat_id, $message_id, $keyboard)
{
    bot('EditMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'reply_markup' => $keyboard
    ]);
}

function SendMessage($chat_id, $text, $mode = null, $reply = null, $keyboard = null)
{
    return bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $mode,
        'reply_to_message_id' => $reply,
        'reply_markup' => $keyboard,
        'disable_web_page_preview' => true
    ]);
}

function sendPhoto($chat_id, $photo, $caption, $mode = null, $reply = null, $keyboard = null)
{
    return bot('sendphoto', [
        'chat_id' => $chat_id,
        'photo' => $photo,
        'caption' => $caption,
        'reply_to_message_id' => $reply,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard
    ]);
}

function EditMessageText($chat_id, $message_id, $text = null, $mode = null, $keyboard = null, $disable_web_page_preview = null)
{
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard,
        'disable_web_page_preview' => $disable_web_page_preview
    ]);
}

function ForwardMessage($chat_id, $from_chat, $message_id)
{
    return bot('ForwardMessage', [
        'chat_id' => $chat_id,
        'from_chat_id' => $from_chat,
        'message_id' => $message_id
    ]);
}
function sendAnimation($chat_id, $animation, $caption)
{
    return bot('sendAnimation', [
        'chat_id' => $chat_id,
        'animation' => $animation,
        'caption' => $caption
    ]);
}

function DeleteMessage($chat_id, $msgid)
{
    bot('DeleteMessage', [
        'chat_id' => $chat_id,
        'message_id' => $msgid
    ]);
}
function SendDocument($chat_id, $document, $caption = null, $reply = null, $mode = null, $keyboard = null)
{
    bot('SendDocument', [
        'chat_id' => $chat_id,
        'document' => $document,
        'caption' => $caption,
        'reply_to_message_id' => $reply,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard
    ]);
}
function sendVideo($chat_id, $video, $caption = null, $reply = null, $mode = null, $keyboard = null)
{
    bot('sendvideo', [
        'chat_id' => $chat_id,
        'video' => $video,
        'caption' => $caption,
        'reply_to_message_id' => $reply,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard
    ]);
}
function sendVoice($chat_id, $voice, $caption = null, $reply = null, $mode = null, $keyboard = null)
{
    bot('sendvoice', [
        'chat_id' => $chat_id,
        'voice' => $voice,
        'caption' => $caption,
        'reply_to_message_id' => $reply,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard
    ]);
}

function sendAudio($chat_id, $audio, $caption = null, $reply = null, $mode = null, $keyboard = null)
{
    bot('sendaudio', [
        'chat_id' => $chat_id,
        'audio' => $audio,
        'caption' => $caption,
        'reply_to_message_id' => $reply,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard
    ]);
}
function sendSticker($chat_id, $sticker, $reply = null, $mode = null, $keyboard = null)
{
    bot('sendsticker', [
        'chat_id' => $chat_id,
        'sticker' => $sticker,
        'reply_to_message_id' => $reply,
        'parse_mode' => $mode,
        'reply_markup' => $keyboard
    ]);
}
