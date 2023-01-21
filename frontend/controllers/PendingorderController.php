<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\CustomModel;
use common\models\Orders;
use common\models\OrdersSearch;
use common\models\PendingOrders;
use common\models\PendingOrdersSearch;
use common\models\User;
use Exception;
use Yii;
use yii\base\Model;
use yii\bootstrap5\Modal;
use yii\helpers\Url;
use yii\web\Controller;

class PendingorderController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if (CommonHelpers::CheckCreateAccessforSuperAdmin(Yii::$app->controller->action->id)) {
                Yii::$app->session->setFlash('error', 'Permission Denied');
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/index'));
                return false;
            }
            $this->enableCsrfValidation = false;
            return true;
        }
        return false;
    }

   

    public function actionIndex()
    {
        $searchmodel = new PendingOrdersSearch();
        $searchdata = $searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchmodel' => $searchmodel,
            'searchdata' => $searchdata,
        ]);
    }

    // public function actionDelete($order_no)
    // {
    //     $models = Orders::find()->where(['order_no' => $order_no])->all();
    //     foreach ($models as $key => $model) {
    //         $model->status = Orders::STATUS_DELETED;
    //         $model->save(false);
    //     }
    //     Yii::$app->session->setFlash('success', 'your order has been deleted successfully');
    //     return $this->redirect($_SERVER['HTTP_REFERER']);
    // }

    // public function actionView($order_no)
    // {
    //     $orders = Orders::find();
    //     if (in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::DEALER])) {
    //         $orders->joinWith(['dealer', 'dealer.distributor']);
    //     }
    //     $result = $orders->where(['orders.order_no' => $order_no])->all();
    //     $oneorder = Orders::find()->joinWith(['dealer', 'dealer.distributor'])->where(['order_no' => $order_no])->groupBy('order_no')->one();
    //     // echo'<pre>';print_r($oneorder);exit();
    //     return $this->render('_view', [
    //         'result' => $result,
    //         'order_details' => $oneorder,
    //     ]);
    // }


    private function findAllOrders($order_no)
    {
        return Orders::find()->where(['order_no' => $order_no])->andWhere(['!=','status',Orders::STATUS_DELETED])->all();
    }
}
