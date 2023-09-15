<?php

namespace app\modules\admin\models;

/**
 * ProductsSearch represents the model behind the search form of `app\models\Products`.
 */
class ProductsSearch extends \app\models\Products{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['id', 'access_days', 'discount', 'status', 'client_id'], 'integer'],
            [['name'], 'safe'],
            [['price'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function scenarios() : array{
        // bypass scenarios() implementation in the parent class
        return parent::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params) : \yii\data\ActiveDataProvider{
        $query = \app\models\Products::find();

        // add conditions that should always apply here

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query
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
            'price' => $this->price,
            'access_days' => $this->access_days,
            'discount' => $this->discount,
            'status' => $this->status,
            'client_id' => $this->client_id
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}