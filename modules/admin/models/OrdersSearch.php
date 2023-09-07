<?php

namespace app\modules\admin\models;

/**
 * OrdersSearch represents the model behind the search form of `app\models\Orders`.
 */
class OrdersSearch extends \app\models\Orders{
    
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['id', 'tg_user_id', 'status', 'count', 'access_days', 'is_test', 'tg_member_id', 'client_id'], 'integer'],
            [['method', 'shop', 'position_name', 'created_time', 'resulted_time', 'web_app_query_id', 'currency', 'paypal_order_id'], 'safe'],
            [['count_in_currency', 'commission'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function scenarios() : array{
        return parent::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params) : \yii\data\ActiveDataProvider{
        $query = \app\models\Orders::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [ 
                //'attributes' => [],// Отключение сортировки 
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ], 
            'pagination' => [ 
                'pageSize' => 25, 
            ] 
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tg_user_id' => $this->tg_user_id,
            'status' => $this->status,
            'count' => $this->count,
            'access_days' => $this->access_days,
            'is_test' => $this->is_test,
            'count_in_currency' => $this->count_in_currency,
            'commission' => $this->commission,
            'tg_member_id' => $this->tg_member_id,
            'client_id' => $this->client_id
        ]);

        $query->andFilterWhere(['like', 'method', $this->method])
            ->andFilterWhere(['like', 'shop', $this->shop])
            ->andFilterWhere(['like', 'position_name', $this->position_name])
            ->andFilterWhere(['like', 'web_app_query_id', $this->web_app_query_id])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'paypal_order_id', $this->paypal_order_id]);

        if(isset($this->created_time) && $this->created_time !== ''){
            $query->andFilterWhere(['like', 'created_time', \Yii::$app->formatter->asDatetime(new \DateTime($this->created_time), 'php:Y-m-d')]); 
        }
        if(isset($this->resulted_time) && $this->resulted_time !== ''){
            $query->andFilterWhere(['like', 'resulted_time', \Yii::$app->formatter->asDatetime(new \DateTime($this->resulted_time), 'php:Y-m-d')]); 
        }

        return $dataProvider;
    }
}