<?php
//Todo Учитывать комиссию площадки и записывать в commision 10% - комиссию площадки за платеж


require_once __DIR__ .'/cfg/config.php';
require_once __DIR__ .'/cfg/db.php';

use cfg\db;
use cfg\config;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['SignatureValue']) && isset($_POST['InvId']) && isset($_POST['OutSum'])){//RoboKassa start
        $db = new db;
        $orderId = $_POST['InvId'];
        $sql = "SELECT shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
        $result = $db->query($sql, ['order_id' => $orderId]);
        if($result !== false){
            $shop = $result[0]['shop'];
            $accessDays = $result[0]['access_days'];
            $webAppQueryId = $result[0]['web_app_query_id'];
            if($_POST['SignatureValue'] != md5($_POST['OutSum'] . ':' . $orderId . ':' . config::getConfig($shop)['RoboKassa'][0])){//Валидация crc
                echo $orderId . '|Fail';
                $from = 'seccurity';
                $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa success.php: Ошибка подписи ' . json_encode($_POST) . PHP_EOL);
                fclose($file);
            }
            else{
                $sql = "UPDATE orders SET status = :status_bool, resulted_time = NOW() WHERE id = :order_id;";
                $result = $db->execute($sql, ['status_bool' => 1, 'order_id' => $orderId]);
                if($result !== false){
                    echo 'Заказ: ' . $orderId . ' Успешно оплачен' . PHP_EOL . 'Данную страницу можно закрывать.';
                    $data = [
                        'web_app_query_id' => $webAppQueryId,
                        'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                    ];
                    $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    if($result === false){
                        $from = 'curl';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa success.php: ' . curl_error($ch) . PHP_EOL);
                        fclose($file);
                    }
                }
                else{
                    echo $orderId . '|Fail';
                    $from = 'db';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa success.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                    fclose($file);
                    $data = [
                        'web_app_query_id' => $webAppQueryId,
                        'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                    ];
                    $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    if($result === false){
                        $from = 'curl';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa success.php: ' . curl_error($ch) . PHP_EOL);
                        fclose($file);
                    }
                }
            }
            exit(0);
        }
        else{
            $from = 'db';
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa success.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
            fclose($file);
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            exit(0);
        }
    }
    elseif(isset($_POST['sign']) && isset($_POST['order_id']) && isset($_POST['amount']) && isset($_POST['status'])){//PayKassa start
        $db = new db;
        $orderId = $_POST['order_id'];
        $sql = "SELECT shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
        $result = $db->query($sql, ['order_id' => $orderId]);
        if($result !== false){
            $shop = $result[0]['shop'];
            $accessDays = $result[0]['access_days'];
            $webAppQueryId = $result[0]['web_app_query_id'];
            $config = config::getConfig($shop)['PayKassa'];
            $params = array($_POST['amount'], $config['merchant_id'], $_POST['order_id'], $_POST['status'], $config['merchant_password']);
            if(md5(implode(':', $params) == $_POST['sign'])){
                $sql = "UPDATE orders SET status = :status_bool, resulted_time = NOW() WHERE id = :order_id;";
                $result = $db->execute($sql, ['status_bool' => 1, 'order_id' => $orderId]);
                if($result !== false){
                    echo 'Заказ: ' . $orderId . ' Успешно оплачен' . PHP_EOL . 'Данную страницу можно закрывать.';
                    $data = [
                        'web_app_query_id' => $webAppQueryId,
                        'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                    ];
                    $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    if($result === false){
                        $from = 'curl';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa success.php: ' . curl_error($ch) . PHP_EOL);
                        fclose($file);
                    }
                }
                else{
                    echo $orderId . '|Fail';
                    $from = 'db';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa success.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                    fclose($file);
                    $data = [
                        'web_app_query_id' => $webAppQueryId,
                        'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                    ];
                    $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    if($result === false){
                        $from = 'curl';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa success.php: ' . curl_error($ch) . PHP_EOL);
                        fclose($file);
                    }
                }
                exit(0);
            }
            else{
                $from = 'security';
                $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                fwrite($file, date("d.m.Y H:i:s") . ' От PayKassa пришел ответ на success с ошибкой в подписи: ' . json_encode($_POST) . PHP_EOL);
                fclose($file);
                exit(0);
            }
        }
        else{
            $from = 'db';
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa success.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
            fclose($file);
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            exit(0);
        }
    }//Todo Добавить валидацию crc
    elseif(isset($_POST['MERCHANT_ORDER_ID']) && isset($_POST['intid'])){//FreeKassa start
        $db = new db;
        $orderId = $_POST['MERCHANT_ORDER_ID'];
        $sql = "SELECT shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
        $result = $db->query($sql, ['order_id' => $orderId]);
        if($result !== false){
            $shop = $result[0]['shop'];
            $accessDays = $result[0]['access_days'];
            $webAppQueryId = $result[0]['web_app_query_id'];
            $config = config::getConfig($shop)['FreeKassa'];
            $sql = "UPDATE orders SET status = :status_bool, resulted_time = NOW() WHERE id = :order_id;";
            $result = $db->execute($sql, ['status_bool' => 1, 'order_id' => $orderId]);
            if($result !== false){
                echo 'Заказ: ' . $orderId . ' Успешно оплачен' . PHP_EOL . 'Данную страницу можно закрывать.';
                $data = [
                    'web_app_query_id' => $webAppQueryId,
                    'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                ];
                $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
                if($result === false){
                    $from = 'curl';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa success.php: ' . curl_error($ch) . PHP_EOL);
                    fclose($file);
                }
            }
            else{
                echo $orderId . '|Fail';
                $from = 'db';
                $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa success.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                fclose($file);
                $data = [
                    'web_app_query_id' => $webAppQueryId,
                    'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                ];
                $result = config::curlSendMessage($data, $shop, '/answerWebAppQuery');
                if($result === false){
                    $from = 'curl';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa success.php: ' . curl_error($ch) . PHP_EOL);
                    fclose($file);
                }
            }
            exit(0);
        }
        else{
            $from = 'db';
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa success.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
            fclose($file);
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            exit(0);
        }
    }
    else{
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit(0);
    }
}
else{
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    exit(0);
}
?>