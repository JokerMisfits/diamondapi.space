<?php

if(isset($_REQUEST["InvId"])){//RoboKassa start
    $orderId = $_REQUEST["InvId"];
    echo $orderId . '|Fail';
    $db = new db;
    $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
    $result = $db->query($sql, ['order_id' => $orderId]);
    if($result !== false){
        $shop = $result[0]['shop'];
        $data = [
            'web_app_query_id' => $result[0]['web_app_query_id'],
            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
        ];
        $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
        if($result === false){
            $from = 'curl';
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa fail.php: ' . curl_error($ch) . PHP_EOL);
            fclose($file);
        }
    }
    else{
        $from = 'db';
        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
        fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa fail.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
        fclose($file);
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    }
    exit(0);
}
elseif(isset($_GET['order_id'])){//PayKassa start
    $orderId = $_GET['order_id'];
    echo $orderId . '|Fail';
    $db = new db;
    $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
    $result = $db->query($sql, ['order_id' => $orderId]);
    if($result !== false){
        $shop = $result[0]['shop'];
        $data = [
            'web_app_query_id' => $result[0]['web_app_query_id'],
            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
        ];
        $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
        if($result === false){
            $from = 'curl';
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa fail.php: ' . curl_error($ch) . PHP_EOL);
            fclose($file);
        }
    }
    else{
        $from = 'db';
        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
        fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa fail.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
        fclose($file);
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    }
    exit(0);
}
else{
    $from = 'fail';
    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
    fwrite($file, date("d.m.Y H:i:s") . ' From fail.php: ' . json_encode($_POST) . PHP_EOL);
    fclose($file);
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    exit(0);
}
?>