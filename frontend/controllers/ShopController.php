<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\GodownStock;
use common\models\GodownStockSearch;
use common\models\Orders;
use common\models\ShopStock;
use common\models\ShopStockSearch;
use common\models\User;
use Exception;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class ShopController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
                $this->enableCsrfValidation = false;
                return true;
        }
        return false;
    }

    public function actionIndex(){
        $searchmodel=new ShopStockSearch();
        $data=$searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'data'=>$data,
            'searchmodel'=>$searchmodel,
        ]);
    }

    public function actionCreate($order_no){
        $model=Orders::find()->where(['order_no'=>$order_no])->all();
        foreach($model as $val){
            $val->scenario='Inward';
        }
        if(Yii::$app->request->isPost){
            try{
                $transaction=Yii::$app->db->beginTransaction();
                $inward_val=Yii::$app->request->post('Orders');
                foreach($inward_val as $i => $value){
                    $new_model_god=new GodownStock();
                    $new_model_shop=new ShopStock();
                    $new_model_shop->parent_id=$new_model_god->parent_id=$value['id'];
                    $new_model_shop->order_no=$new_model_god->order_no=$model[$i]->order_no;
                    $new_model_shop->item_id=$new_model_god->item_id=$value['item_id'];
                    $new_model_shop->item_name=$new_model_god->item_name=$value['item_name'];
                    $new_model_shop->barcode=$new_model_god->barcode=$value['barcode'];
                    $new_model_shop->rate=$new_model_god->rate=$value['rate'];
                    if(($value['inward_type']==GodownStock::INWARD_TYPE_GODOWN)){
                        if($model[$i]->qty==$value['qty']){
                            $new_model_god->qty=$value['qty'];
                            $new_model_god->amount=$value['amount'];
                            $new_model_god->status=GodownStock::STATUS_ACTIVE;
                            $new_model_god->save(false);
                        }else{
                            $new_model_god->qty=$value['qty'];
                            $new_model_god->amount=$value['amount'];
                            $new_model_god->status=GodownStock::STATUS_ACTIVE;
                            $new_model_shop->qty=$model[$i]->qty-$value['qty'];
                            $new_model_shop->amount=$model[$i]->amount-$value['amount'];
                            $new_model_shop->status=ShopStock::STATUS_ACTIVE;
                            // echo'<pre>';print_r($new_model_shop);exit();
                            $new_model_shop->save(false);
                            $new_model_god->save(false);
                        }
                    }else{
                        if($model[$i]->qty==$value['qty']){
                            $new_model_shop->qty=$value['qty'];
                            $new_model_shop->amount=$value['amount'];
                            $new_model_shop->status=ShopStock::STATUS_ACTIVE;
                            $new_model_shop->save(false);
                        }else{
                            $new_model_shop->qty=$value['qty'];
                            $new_model_shop->amount=$value['amount'];
                            $new_model_shop->status=ShopStock::STATUS_ACTIVE;
                            $new_model_god->qty=$model[$i]->qty-$value['qty'];
                            $new_model_god->amount=$model[$i]->amount-$value['amount'];
                            $new_model_god->status=GodownStock::STATUS_ACTIVE;
                            // echo'<pre>';print_r($new_model_shop);exit();
                            $new_model_god->save(false);
                            $new_model_shop->save(false);
                        }
                    }
                    
                }
                $transaction->commit();
                Yii::$app->session->setFlash("success","Stock inward successfully!");
                return $this->redirect(Url::to(['order/index','sent'=>true]));
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash("error",$e->getMessage());
                return $this->redirect(Url::to(['order/index','sent'=>true]));
            }
        }
        // $model=new GodownStock();
        return $this->renderAjax('create',[
            'model'=>$model
        ]);
    }
}

?>