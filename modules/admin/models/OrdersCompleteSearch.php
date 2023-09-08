<?php

namespace app\modules\admin\models;

/**
 * OrdersCompleteSearch represents the model behind the search form of `app\models\OrdersComplete`.
 */
class OrdersCompleteSearch extends \app\models\OrdersComplete{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['id', 'order_id', 'client_id'], 'integer'],
            [['shop', 'method', 'payment_method', 'revise'], 'safe'],
            [['fee'], 'number'],
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
     *
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = \app\models\OrdersComplete::find();

        // add conditions that should always apply here

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [ 
                //'attributes' => [],// Отключение сортировки 
                'defaultOrder' => [
                    'revise' => SORT_ASC
                ]
            ], 
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
            'fee' => $this->fee,
            'order_id' => $this->order_id,
            'client_id' => $this->client_id
        ]);

        $query->andFilterWhere(['like', 'shop', $this->shop])
            ->andFilterWhere(['like', 'method', $this->method])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'revise', $this->revise]);

        return $dataProvider;
    }
}