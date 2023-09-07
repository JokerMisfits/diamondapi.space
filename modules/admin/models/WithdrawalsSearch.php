<?php

namespace app\modules\admin\models;

/**
 * WithdrawalsSearch represents the model behind the search form of `app\models\Withdrawals`.
 */
class WithdrawalsSearch extends \app\models\Withdrawals{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['id', 'count', 'status', 'is_test', 'commission', 'tg_member_id', 'client_id'], 'integer'],
            [['shop', 'card_number', 'comment', 'created_time', 'confirmed_time', 'resulted_time', 'confirmation_link'], 'safe']
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
        $query = \app\models\Withdrawals::find();

        // add conditions that should always apply here

        $dataProvider = new \yii\data\ActiveDataProvider([
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
            'count' => $this->count,
            'status' => $this->status,
            'is_test' => $this->is_test,
            'commission' => $this->commission,
            'created_time' => $this->created_time,
            'confirmed_time' => $this->confirmed_time,
            'resulted_time' => $this->resulted_time,
            'tg_member_id' => $this->tg_member_id,
            'client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['like', 'shop', $this->shop])
            ->andFilterWhere(['like', 'card_number', $this->card_number])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'confirmation_link', $this->confirmation_link]);

        return $dataProvider;
    }
}