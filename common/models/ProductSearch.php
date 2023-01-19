<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Products;

/**
 * ProductSearch represents the model behind the search form of `common\models\Products`.
 */
class ProductSearch extends Products
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'point', 'tax', 'purchase_rate', 'mrp', 'discount', 'minimum_stock', 'order_level', 'status', 'created_by', 'updated_by'], 'integer'],
            [['item_group', 'item_name', 'size', 'pack', 'unit', 'hsn', 'barcode', 'personal_code', 'created_dt', 'updated_dt','search'], 'safe'],
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
        $query = Products::find()->joinWith('taxName')->where(['!=','products.status',Products::STATUS_DELETED])->orderBy(['products.id'=>SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(isset($params['ProductSearch']['search']) && !empty($params['ProductSearch']['search'])){
            $query->andFilterWhere(['or',
            ['like','item_group',$this->search],
            ['like','item_name',$this->search],
            ['like','size',$this->search],
            ['like','pack',$this->search],
            ['like','unit',$this->search],
            ['like','hsn',$this->search],
            ['like','barcode',$this->search],
            ['like','personal_code',$this->search],
            ['like','point',$this->search],
            ['like',TaxMaster::tableName().'.name',$this->search],
            ['like','purchase_rate',$this->search],
            ['like','mrp',$this->search],
            ['like','discount',$this->search],
            ['like','minimum_stock',$this->search],
            ['like','order_level',$this->search],
            ['like',$this::tableName().'.status',$this->search],
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
            'point' => $this->point,
            'tax' => $this->tax,
            'purchase_rate' => $this->purchase_rate,
            'mrp' => $this->mrp,
            'discount' => $this->discount,
            'minimum_stock' => $this->minimum_stock,
            'order_level' => $this->order_level,
            'status' => $this->status,
            'created_dt' => $this->created_dt,
            'created_by' => $this->created_by,
            'updated_dt' => $this->updated_dt,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'item_group', $this->item_group])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'pack', $this->pack])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'hsn', $this->hsn])
            ->andFilterWhere(['like', 'barcode', $this->barcode])
            ->andFilterWhere(['like', 'personal_code', $this->personal_code]);

        return $dataProvider;
    }
}
