<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PendingOrders;
use Yii;

/**
 * PendingOrdersSearch represents the model behind the search form of `common\models\PendingOrders`.
 */
class PendingOrdersSearch extends PendingOrders
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'old_order_id', 'parent_id', 'order_no', 'item_id', 'qty', 'order_qty', 'rate', 'amount', 'salesman_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['item_name', 'pack', 'created_dt', 'updated_dt','search'], 'safe'],
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
        $query = PendingOrders::find()->select([PendingOrders::tableName().'.*','GROUP_CONCAT('.PendingOrders::tableName().'.discount) as all_discount','GROUP_CONCAT('.PendingOrders::tableName().'.amount) as all_amount','GROUP_CONCAT('.PendingOrders::tableName().'.qty) as all_qty','GROUP_CONCAT('.PendingOrders::tableName().'.rate) as all_rate','GROUP_CONCAT('.PendingOrders::tableName().'.item_id) as all_item_id'])->andWhere(['!=',PendingOrders::tableName().'.status',Orders::STATUS_DELETED])->orderBy([PendingOrders::tableName().".id"=>SORT_DESC])->groupBy(PendingOrders::tableName().'.order_no');

        if(!in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){
                //if(Yii::$app->user->identity->role_id==User::DISTRIBUTOR){
                    $query->joinWith(['order','order.dealer','order.dealer.distributor']);
                    //$query->andWhere(['distributor.user_id'=>Yii::$app->user->identity->id]);
                //}
            // if(isset($params['sent'])&&($params['sent']==1)){
                 $query->andWhere(['orders.parent_id'=>Yii::$app->user->identity->id]);
                        
            // }
        }else{
            $query->joinWith(['order','order.dealer','order.dealer.distributor','order.distributor']);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if(isset($params['order_no']) && !empty($params['order_no'])){
            $query->andWhere([PendingOrders::tableName().'.order_no'=>$params['order_no']]);
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
            'old_order_id' => $this->old_order_id,
            'parent_id' => $this->parent_id,
            'order_no' => $this->order_no,
            'item_id' => $this->item_id,
            'qty' => $this->qty,
            'order_qty' => $this->order_qty,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'salesman_id' => $this->salesman_id,
            'status' => $this->status,
            'created_dt' => $this->created_dt,
            'created_by' => $this->created_by,
            'updated_dt' => $this->updated_dt,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pack', $this->pack]);

        return $dataProvider;
    }
}
