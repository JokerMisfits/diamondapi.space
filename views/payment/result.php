<?php
require_once __DIR__ ."/vendor/autoload.php";
require_once __DIR__ .'/cfg/config.php';
require_once __DIR__ .'/cfg/db.php';

use cfg\db;
use cfg\config;
use Paykassa\PaykassaSCI;

$timeToSleep = 10;//Задержка перед срабатыванием, чтобы желательно сработал success
try{
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['InvId']) && isset($_POST['OutSum']) && isset($_POST['crc'])){//RoboKassa start
            $from = 'test';//Todo УДАЛИТЬ ПОСЛЕ ТЕСТА СКОЛЬКО КОММИСИИ
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa result.php: ' . json_encode($_POST) . PHP_EOL);
            fclose($file);
            sleep($timeToSleep);
            $db = new db;
            $orderId = $_POST['InvId'];
            $sql = "SELECT tg_user_id, `status`, access_days, method, shop FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = $db->query($sql, ['order_id' => $orderId]);
            if($result !== false && $result[0]['status'] == 0 && $result[0]['method'] == 'RoboKassa'){
                $days = $result[0]['access_days'];
                $userId = $result[0]['tg_user_id'];
                $shop = $result[0]['shop'];
                if($_POST['crc'] != strtoupper(md5($_POST['OutSum'] . ':' . $orderId . ':' . config::getConfig($shop)['RoboKassa'][1]))){//Валидация crc
                    echo 'OK' . $orderId . '\n';
                    $from = 'seccurity';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa result.php: Ошибка подписи ' . json_encode($_POST) . PHP_EOL);
                    fclose($file);
                }
                else{
                    $sql = "UPDATE orders SET `status` = :status_bool, resulted_time = NOW() WHERE id = :order_id;";
                    $result = $db->execute($sql, ['status_bool' => 1, 'order_id' => $orderId]);
                    if($result !== false){
                        echo 'OK' . $orderId . '\n'; 
                        $result = config::curlSendMessage(config::getResultButton($userId, $days), $shop);
                        if($result === false){
                            $from = 'curl';
                            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                            fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa result.php: ' . curl_error($ch) . PHP_EOL);
                            fclose($file);
                        }
                    }
                    else{
                        $from = 'db';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa result.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                        fclose($file);
                    }
                }
                exit(0);
            }
            else{
                echo 'OK' . $orderId . '\n';
                exit(0);
            }
        }
        elseif(isset($_POST['private_hash']) && isset($_POST['system']) && isset($_POST['currency']) && isset($_POST['order_id']) && isset($_POST['type'])){//PayKassa start
            sleep($timeToSleep);
            $db = new db;
            $orderId = $_POST['order_id'];
            $sql = "SELECT tg_user_id, `status`, method, shop, access_days FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = $db->query($sql, ['order_id' => $orderId]);
            if($result !== false && $result[0]['status'] == 0 && $result[0]['method'] == 'PayKassa'){
                $shop = $result[0]['shop'];
                $userId = $result[0]['tg_user_id'];
                $days = $result[0]['access_days'];
                $config = config::getConfig($shop)['PayKassa'];
                $paykassa = new PaykassaSCI($config["merchant_id"], $config["merchant_password"], $config["config"]["test_mode"]);
                $result = $paykassa->checkOrderIpn($_POST["private_hash"]);
                if($result['error']){
                    $from = 'result';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' От PayKassa пришел ответ на result ошибка при проверке hash: ' . $result['message'] . PHP_EOL);
                    fclose($file);
                    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                    exit(0);
                } 
                else{
                    $sql = "UPDATE orders SET `status` = :status_bool, resulted_time = NOW() WHERE id = :order_id;";
                    $result = $db->execute($sql, ['status_bool' => 1, 'order_id' => $orderId]);
                    if($result !== false){
                        echo $id . '|success';
                        $result = config::curlSendMessage(config::getResultButton($userId, $days), $shop);
                        if($result === false){
                            $from = 'curl';
                            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                            fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa result.php: ' . curl_error($ch) . PHP_EOL);
                            fclose($file);
                        }
                    }
                    else{
                        $from = 'db';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From PayKassa result.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                        fclose($file);
                    }
                    exit(0);
                }
            }
            else{
                echo $id . '|success';
                exit(0);
            }
        }//FreeKassa start
        elseif(isset($_POST['MERCHANT_ID']) && isset($_POST['AMOUNT']) && isset($_POST['intid']) && isset($_POST['MERCHANT_ORDER_ID']) && isset($_POST['SIGN']) && isset($_POST['commission'])){
            if (!in_array(getIP(), array('168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'))){
                $from = 'security';
                $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                fwrite($file, date("d.m.Y H:i:s") . ' От FreeKassa пришел ответ на result с подменой ip: ' . json_encode($_POST) . PHP_EOL);
                fclose($file);
                header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                exit(0);
            }
            $from = 'test';//Todo УДАЛИТЬ ПОСЛЕ ТЕСТА СКОЛЬКО КОММИСИИ
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa result.php: ' . json_encode($_POST) . PHP_EOL);
            fclose($file);
            sleep($timeToSleep);
            $db = new db;
            $orderId = $_POST['MERCHANT_ORDER_ID'];
            $sql = "SELECT tg_user_id, `status`, access_days, method, shop FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = $db->query($sql, ['order_id' => $orderId]);
            if($result !== false && $result[0]['status'] == 0 && $result[0]['method'] == 'FreeKassa'){
                $shop = $result[0]['shop'];
                $userId = $result[0]['tg_user_id'];
                $days = $result[0]['access_days'];
                $commission = $_POST['commission'];
                $config = config::getConfig($shop)['FreeKassa'];
                if($_POST['SIGN'] == md5($_POST['MERCHANT_ID'] . ':' . $_POST['AMOUNT'] . ':' . $config['sc2'] . ':' . $_POST['MERCHANT_ORDER_ID'])){//Проверка подписи
                    $sql = "UPDATE orders SET `status` = :status_bool, resulted_time = NOW(), commission = :commission WHERE id = :order_id;";
                    $result = $db->execute($sql, ['status_bool' => 1, 'order_id' => $orderId, 'commission' => $commission]);
                    if($result !== false){
                        echo 'YES';
                        $data = config::getResultButton($userId, $days);
                        $result = config::curlSendMessage($data, $shop);
                        if($result === false){
                            $from = 'curl';
                            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                            fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa result.php: ' . curl_error($ch) . PHP_EOL);
                            fclose($file);
                        }
                    }
                    else{
                        $from = 'db';
                        $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                        fwrite($file, date("d.m.Y H:i:s") . ' From FreeKassa result.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                        fclose($file);
                    }
                    exit(0);
                }
                else{
                    $from = 'security';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' От FreeKassa пришел ответ на result с ошибкой в подписи: ' . json_encode($_POST) . PHP_EOL);
                    fclose($file);
                    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                    exit(0);
                }
            }
            else{
                echo 'YES';
                exit(0);
            }
        }
        else{
            $from = 'test';
            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
            fwrite($file, date("d.m.Y H:i:s") . ' From Test result.php: ' . json_encode($_POST) . PHP_EOL);
            fclose($file);
            exit(0);
        }
    }
    else{// GET REQUEST
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit(0);
    }
}
catch(Exception|Throwable $e){
    $from = 'php';
    $data = 'Ошибка: ' . $e->getMessage() . PHP_EOL . 'File: ' . $e->getFile() . PHP_EOL . 'Строка: ' . $e->getLine();
    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
    fwrite($file, date("d.m.Y H:i:s") . ' ' . $data . PHP_EOL);
    fclose($file);
    exit(0);
}
function getIP() {
    if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
    return $_SERVER['REMOTE_ADDR'];
  }
?>