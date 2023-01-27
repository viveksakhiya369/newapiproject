<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\CustomModel;
use common\models\GodownStock;
use common\models\GodownStockSearch;
use common\models\Orders;
use common\models\ShopStock;
use common\models\User;
use Exception;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class GodownController extends Controller{

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
        $searchmodel=new GodownStockSearch();
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

    public function actionCreateStock(){
        $model=[new GodownStock()];
        if(Yii::$app->request->isPost){
            try{
                $transaction=Yii::$app->db->beginTransaction();
                $arr=Yii::$app->request->post('GodownStock');
                $new_arr = array();
                foreach ($arr as $item) {
                    if (isset($new_arr[$item['item_id']])) {
                        $new_arr[$item['item_id']]['qty'] += $item['qty'];
                        $new_arr[$item['item_id']]['amount'] += $item['amount'];
                        continue;
                    }
                    
                    $new_arr[$item['item_id']] = $item;
                }
                $arr = array_values($new_arr);
                $post_data[$model[0]->formName()] = $arr;
                $model = CustomModel::createMultipleWithCustomArr(GodownStock::className(), $arr);
                CustomModel::loadMultiple($model, $post_data);
                foreach($model as $onemodel){
                    $onemodel->status=GodownStock::STATUS_ACTIVE;
                    if(!($onemodel->save(false))){
                        $transaction->rollBack();
                        break;
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success','Items has been added in Godown');
                return $this->redirect(Url::to(['godown/index']));
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error',$e->getMessage());
                return $this->redirect(Url::to(['godown/index']));
            }
            echo'<pre>';print_r($model);exit();
        }
        return $this->render('create_stock',[
            'model'=>$model,
        ]);
    }

    public function actionSubmitToShop(){
        $searchmodel= new GodownStockSearch();
        $model=$searchmodel->search(Yii::$app->request->queryParams)->getModels();
        if(Yii::$app->request->isPost){
            try{
                $transaction=Yii::$app->db->beginTransaction();
                $new_post_arr=Yii::$app->request->post('GodownStock');
                $old_god_stocks=GodownStock::find()->andWhere(['created_by'=>Yii::$app->user->identity->id,'item_id'=>Yii::$app->request->get('item_id')])->andWhere(['!=','status',GodownStock::STATUS_DELETED])->all();
                $old_total_qty=0;
                $old_total_amt=0;
                foreach($old_god_stocks as $i => $value){
                    $old_total_qty=$old_total_qty+$value->qty;
                    $old_total_amt=$old_total_amt+$value->amount;
                    $value->status=GodownStock::STATUS_DELETED;
                    $value->save(false);
                }
                foreach($new_post_arr as $key => $val){
                        if($val['total_qty']==$old_total_qty){
                            $shop_model=new ShopStock();
                            $shop_model->parent_id=isset($old_god_stocks[$key]->parent_id) ? $old_god_stocks[$key]->parent_id : "";
                            $shop_model->order_no=isset($old_god_stocks[$key]->order_no) ? $old_god_stocks[$key]->order_no : "";
                            $shop_model->item_id=$val['item_id'];
                            $shop_model->item_name=$val['item_name'];
                            $shop_model->barcode=$val['barcode'];
                            $shop_model->rate=$val['rate'];
                            $shop_model->status=ShopStock::STATUS_ACTIVE;
                            $shop_model->qty=$old_total_qty;
                            $shop_model->amount=$old_total_amt;
                            $shop_model->save(false);
                        }else{
                            $new_god_model=new GodownStock();
                            $new_shop_model= new ShopStock();
                            $new_god_model->parent_id=$new_shop_model->parent_id=isset($old_god_stocks[$key]->parent_id) ? $old_god_stocks[$key]->parent_id : "";
                            $new_god_model->order_no=$new_shop_model->order_no=isset($old_god_stocks[$key]->order_no) ? $old_god_stocks[$key]->order_no : "";
                            $new_god_model->item_id=$new_shop_model->item_id=$val['item_id'];
                            $new_god_model->item_name=$new_shop_model->item_name=$val['item_name'];
                            $new_god_model->barcode=$new_shop_model->barcode=$val['barcode'];
                            $new_god_model->rate=$new_shop_model->rate=$val['rate'];
                            $new_shop_model->status=ShopStock::STATUS_ACTIVE;
                            $new_god_model->status=GodownStock::STATUS_ACTIVE;
                            $new_shop_model->qty=$val['total_qty'];
                            $new_shop_model->amount=$val['total_amount'];
                            $new_god_model->qty=$old_total_qty-$val['total_qty'];
                            $new_god_model->amount=$old_total_amt-$val['total_amount'];
                            $new_god_model->save(false);
                            $new_shop_model->save(false);
                        }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success','Your stock are moved in shop');
                    return $this->redirect(Url::to(['godown/index']));
                }
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error',$e->getMessage());
                return $this->redirect(Url::to(['godown/index']));
            }
           
        }
        return $this->renderAjax('_submit_to_shop_from',[
            'model'=>$model,
        ]);
    }
}

?>