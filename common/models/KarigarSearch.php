<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\karigar;

use Yii;

/**
 * KarigarSearch represents the model behind the search form of `common\models\karigar`.
 */
class KarigarSearch extends karigar
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'parent_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['name', 'address', 'city', 'state', 'created_dt', 'updated_dt'], 'safe'],
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

        $query = karigar::find()->joinWith(['user','city','state','dealer'])->where(['!=','karigar.status',Karigar::STATUS_DELETED])->orderBy(['karigar.id'=>SORT_DESC]);

        if(Yii::$app->user->identity->role_id==User::DEALER){
            $query->where(['karigar.parent_id'=>Dealer::getDealerId(Yii::$app->user->identity->id)]);
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if(isset($params['KarigarSearch']['search']) && !empty($params['KarigarSearch']['search'])){
            $query->andFilterWhere(['or',
            ['like','karigar.name',$this->search],
            ['like','karigar.city',$this->search],
            ['like','karigar.state',$this->search],
            ['like','karigar.address',$this->search],
            ['like','karigar.user_id',$this->search],
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
