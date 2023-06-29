<?php

namespace app\modules\admin\models;

use app\models\Orders;
use yii\data\ActiveDataProvider;

/**
 * OrdersSearch represents the model behind the search form of `app\models\Orders`.
 */
class OrdersSearch extends Orders{
    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['id', 'tg_user_id', 'status', 'count', 'access_days', 'is_test', 'client_id'], 'integer'],
            [['method', 'shop', 'position_name', 'created_time', 'resulted_time', 'web_app_query_id', 'currency', 'paypal_order_id'], 'safe'],
            [['count_in_currency', 'commission'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(){
        // bypass scenarios() implementation in the parent class
        return parent::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params){
        $query = Orders::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [ 
                'attributes' => [], // Отключение сортировки 
            ], 
            'pagination' => [ 
                'pageSize' => 25, 
            ] 
        ]);

        $this->load($params);

        if(!$this->validate()){
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'tg_user_id' => $this->tg_user_id,
            'status' => $this->status,
            'count' => $this->count,
            'access_days' => $this->access_days,
            'created_time' => $this->created_time,
            'resulted_time' => $this->resulted_time,
            'is_test' => $this->is_test,
            'count_in_currency' => $this->count_in_currency,
            'commission' => $this->commission,
            'client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['like', 'method', $this->method])
            ->andFilterWhere(['like', 'shop', $this->shop])
            ->andFilterWhere(['like', 'position_name', $this->position_name])
            ->andFilterWhere(['like', 'web_app_query_id', $this->web_app_query_id])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'paypal_order_id', $this->paypal_order_id]);

        return $dataProvider;
    }
}