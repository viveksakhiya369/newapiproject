<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Points;

/**
 * PointsSearch represents the model behind the search form of `common\models\Points`.
 */
class PointsSearch extends Points
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sender_id', 'receiver_id', 'item_id', 'quantity', 'points', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt', 'updated_dt'], 'safe'],
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
        $query = Points::find();

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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'points' => $this->points,
            'status' => $this->status,
            'created_dt' => $this->created_dt,
            'created_by' => $this->created_by,
            'updated_dt' => $this->updated_dt,
            'updated_by' => $this->updated_by,
        ]);

        return $dataProvider;
    }
}
