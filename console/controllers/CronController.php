<?php

namespace console\controllers;

use yii\console\Controller;

class CronController extends Controller{

    public function actionIndex(){
        echo'<pre>';print_r("hello");exit();
    }

}

?>