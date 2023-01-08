<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Orders;
use Yii;

/**
 * OrdersSearch represents the model behind the search form of `common\models\Orders`.
 */
class OrdersSearch extends Orders
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'item_id', 'qty', 'rate', 'amount', 'status', 'created_by', 'updated_by'], 'integer'],
            [['order_no', 'item_name', 'pack', 'created_dt', 'updated_dt','search'], 'safe'],
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
        $query = Orders::find();
        if(!in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){
            if(isset($params['receieved'])&&($params['receieved']==1)){
                if(Yii::$app->user->identity->role_id==User::DISTRIBUTOR){
                    $query->joinWith(['dealer','dealer.distributor']);
                    $query->where(['distributor.user_id'=>Yii::$app->user->identity->id]);
                }
            }
            if(isset($params['sent'])&&($params['sent']==1)){
                $query->where(['orders.parent_id'=>Yii::$app->user->identity->id]);
            }
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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
            ->andFilterWhere(['like', 'pack', $this->pack]);

        return $dataProvider;
    }
}
