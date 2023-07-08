<?php

namespace app\commands;

use Yii;
use yii\console\ExitCode;
use yii\console\Controller;

class ClearCacheController extends Controller{
    public function actionIndex(){
        Yii::$app->cache->flush();// Очистка кэша
        Yii::$app->cache->delete('yii.debug.toolbar');// Очистка дебаг-информации
        //Yii::$app->log->flushLogs();
        echo "Cache, debug info, and logs cleared successfully.\n";
        return ExitCode::OK;
    }
}