<?php

error_log("===== callback start. =====");

// アカウント情報設定
$channel_id     = "1463870346";
$channel_secret = "30c70c5e83a326afb105b7f048c00cb2";
$mid            = "uf3cac4e17beca49cbe74611aeb364e6a";

// メッセージ受信
$line_datas = getLineMessage();

// ユーザ情報取得
$user = getApiUserProfileRequest($line_datas['from']);

// 対話データ取得
$taiwa = taiwa($line_datas['text']);
$taiwa = json_decode($taiwa,true);

// テキストで返信
$response_format_text = [
    'contentType' => 1,
    'toType' => 1,
    'text' => $taiwa['utt']
];
$post_data = [
    'to' => [$line_datas['from']],
    'toChannel' => "1383378250",
    'eventType' => "138311608800106203",
    'content' => $response_format_text
];
$ret = postApiRequest('/v1/events', json_encode($post_data));

error_log("===== callback end. =====");

function getLineMessage(){
    $line_datas = [];
    $json_object = json_decode(file_get_contents('php://input'));
    $content = $json_object->result{0}->content;

    $line_datas = [
        'to_type'       =>  $content->toType,
        'time'          =>  $content->createdTime,
        'text'          =>  $content->text,
        'from'          =>  $content->from,
        'message_id'    =>  $content->id,
        'content_type'  =>  $content->contentType,
        'location'      =>  $content->location
    ];

    return $line_datas;
}

function postApiRequest($path, $post) {
    $url = "https://trialbot-api.line.me{$path}";
    $headers = array(
        "Content-Type: application/json",
        "X-Line-ChannelID: {$GLOBALS['channel_id']}",
        "X-Line-ChannelSecret: {$GLOBALS['channel_secret']}",
        "X-Line-Trusted-User-With-ACL: {$GLOBALS['mid']}"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);

    return $output;
}

function getApiUserProfileRequest($mid) {
    $url = "https://trialbot-api.line.me/v1/profiles?mids={$mid}";
    $headers = array(
        "X-Line-ChannelID: {$GLOBALS['channel_id']}",
        "X-Line-ChannelSecret: {$GLOBALS['channel_secret']}",
        "X-Line-Trusted-User-With-ACL: {$GLOBALS['mid']}"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);

    return $output;
}

function taiwa($text){
/* 新対話データ(repl api)
    // ユーザID取得
    $url = "https://api.repl-ai.jp/v1/registration";
    $headers = [
        "Content-Type: application/json",
        "x-api-key: fUDSMEZQAS8bBQPsYibTI18FuV3648eb5TlIvEht"
    ];
    $post = [
        'botId' =>  'LineBot'
    ];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $res = json_decode(curl_exec($curl),true);

    // 対話データ取得
    $url = "https://api.repl-ai.jp/v1/dialogue";
    $headers = [
        "Content-Type: application/json",
        "x-api-key: fUDSMEZQAS8bBQPsYibTI18FuV3648eb5TlIvEht"
    ];
    $post = [
        'appUserId'         =>  $res['appUserId'],
        'botId'             =>  'LineBot',
        'voiceText'         =>  'init',
        'initTalkingFlag'   =>  true,
        'appRecvTime'       =>  date("Y-m-d H:i:s"),
        'appSendTime'       =>  date("Y-m-d H:i:s")
    ];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = json_decode(curl_exec($curl),true);
*/

    $apikey = "6f6767726b5a37706f3957376d4531507a6d4f626d46474256764953762f66324c2e78624b703443635338";
    $url = "https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=$apikey";
    $headers = array(
        "Content-Type: application/json"
    );

    $post = [
        'utt' => $text,
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);

    return $output;
}
