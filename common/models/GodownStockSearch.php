<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GodownStock;
use Yii;

/**
 * GodownStockSearch represents the model behind the search form of `common\models\GodownStock`.
 */
class GodownStockSearch extends GodownStock
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'item_id', 'qty', 'rate', 'amount', 'status', 'created_by', 'updated_by'], 'integer'],
            [['order_no', 'item_name', 'barcode', 'created_dt', 'updated_dt','search','all_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GodownStock::find()->select([GodownStock::tableName().'.*','SUM('.GodownStock::tableName().'.qty) as total_qty','SUM('.GodownStock::tableName().'.amount) as total_amount','MAX('.GodownStock::tableName().'.created_dt) as latest_created_dt','GROUP_CONCAT('.GodownStock::tableName().'.id) as all_ids'])->andWhere(['!=',GodownStock::tableName().'.status',GodownStock::STATUS_DELETED])->groupBy('item_id')->orderBy([GodownStock::tableName().'.id'=>SORT_DESC]);

        // if(!in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){
            //if(Yii::$app->user->identity->role_id==User::DISTRIBUTOR){
                $query->joinWith(['order','order.dealer','order.dealer.distributor']);
                //$query->andWhere(['distributor.user_id'=>Yii::$app->user->identity->id]);
            //}
        // if(isset($params['sent'])&&($params['sent']==1)){
            // if(Yii::$app->user->identity->role_id==User::SUPER_ADMIN){
                $query->andWhere([GodownStock::tableName().'.parent_id'=>Yii::$app->user->identity->id]);
            // }else{
                // $query->andWhere(['orders.parent_id'=>Yii::$app->user->identity->id]);
            // }
            //  echo'<pre>';print_r($query->all());exit();
                    
        // }
    // }
    // else{
    //     $query->joinWith(['order','order.dealer','order.dealer.distributor','order.distributor']);
    // }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(isset($params['item_id']) && !empty($params['item_id'])){
            $query->andFilterWhere(['=',GodownStock::tableName().'.item_id',$params['item_id']]);
        }
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'item_id' => $this->item_id,
            'qty' => $this->qty,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'status' => $this->status,
            'created_dt' => $this->created_dt,
            'created_by' => $this->created_by,
            'updated_dt' => $this->updated_dt,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'order_no', $this->order_no])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'barcode', $this->barcode]);
        return $dataProvider;
    }

    public function searchItemWise($item_id){
        $query = GodownStock::find()->select([GodownStock::tableName().'.*','SUM('.GodownStock::tableName().'.qty) as total_qty','SUM('.GodownStock::tableName().'.amount) as total_amount','MAX('.GodownStock::tableName().'.created_dt) as latest_created_dt','GROUP_CONCAT('.GodownStock::tableName().'.id) as all_ids'])->andWhere(['!=',GodownStock::tableName().'.status',GodownStock::STATUS_DELETED])->groupBy('item_id')->orderBy([GodownStock::tableName().'.id'=>SORT_DESC]);
        $query->joinWith(['order','order.dealer','order.dealer.distributor']);
        $query->andWhere([GodownStock::tableName().'.parent_id'=>Yii::$app->user->identity->id]);
        if(isset($item_id) && !empty($item_id)){
            $query->andFilterWhere(['=',GodownStock::tableName().'.item_id',$item_id]);
        }
        return $query;
    }
}
