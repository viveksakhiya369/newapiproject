<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Dealer;
use Yii;

/**
 * DealerSearch represents the model behind the search form of `common\models\Dealer`.
 */
class DealerSearch extends Dealer
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'parent_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['dealer_name', 'address', 'city', 'taluka', 'state', 'gstin', 'pan', 'owner_name', 'created_dt', 'updated_dt','search'], 'safe'],
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
        $query = Dealer::find()->joinWith(['user','city','state','distributor'])->where(['!=','dealer.status',Dealer::STATUS_DELETED]);

        if(Yii::$app->user->identity->role_id==User::DISTRIBUTOR){
            $query->where(['dealer.parent_id'=>Distributor::getDistributorId(Yii::$app->user->identity->id)]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(isset($params['DealerSearch']['search']) && !empty($params['DealerSearch']['search'])){
            $query->andFilterWhere(['or',['like','user.mobile_num',$this->search],
        ['like','dealer_name',$this->search],
        ['like', 'address', $this->search],
        ['like', 'city.name', $this->search],
        ['like', 'taluka', $this->search],
        ['like', 'states.name', $this->search],
        ['like', 'gstin', $this->search],
        ['like', 'pan', $this->search],
        ['like', 'owner_name', $this->search]]);
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

        $query->andFilterWhere(['like', 'dealer_name', $this->dealer_name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'taluka', $this->taluka])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'gstin', $this->gstin])
            ->andFilterWhere(['like', 'pan', $this->pan])
            ->andFilterWhere(['like', 'owner_name', $this->owner_name]);

        return $dataProvider;
    }
}
