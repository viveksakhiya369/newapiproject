<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Salesman;

/**
 * SalesmanSearch represents the model behind the search form of `common\models\Salesman`.
 */
class SalesmanSearch extends Salesman
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'parent_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['name', 'address', 'city', 'state', 'created_dt', 'updated_dt','search'], 'safe'],
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
        // $query = Salesman::find();
        $query = Salesman::find()->joinWith(['user','city','state'])->where(['!=','sales_officer.status',Salesman::STATUS_DELETED]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(isset($params['SalesmanSearch']['search']) && !empty($params['SalesmanSearch']['search'])){
            $query->andFilterWhere(['or',
            ['like','sales_officer.name',$this->search],
            ['like','sales_officer.city',$this->search],
            ['like','sales_officer.state',$this->search],
            ['like','sales_officer.address',$this->search],
            ['like','sales_officer.user_id',$this->search],
            ['like','user.mobile_num',$this->search],
        ]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'created_dt' => $this->created_dt,
            'created_by' => $this->created_by,
            'updated_dt' => $this->updated_dt,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }
}
