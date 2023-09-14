<?php

namespace app\modules\admin\models;

/**
 * ClientsSearch represents the model behind the search form of `app\models\Clients`.
 */
class ClientsSearch extends \app\models\Clients{
    /**
     * {@inheritdoc}
     */
    public function rules() : array{
        return [
            [['id', 'tg_user_id', 'cost', 'commission', 'min_count_withdrawal', 'tg_chat_id', 'tg_private_chat_id', 'tg_member_id'], 'integer'],
            [['shop', 'payment_alias', 'config_version', 'bot_token', 'robokassa', 'paykassa', 'freekassa', 'paypall', 'last_change'], 'safe'],
            [['balance', 'blocked_balance', 'test_balance', 'test_blocked_balance', 'profit', 'test_profit', 'total_withdrawal', 'test_total_withdrawal', 'total_withdrawal_profit', 'total_withdrawal_profit_test'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search($params){
        $query = \app\models\Clients::find();

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
            'tg_user_id' => $this->tg_user_id,
            'balance' => $this->balance,
            'blocked_balance' => $this->blocked_balance,
            'test_balance' => $this->test_balance,
            'test_blocked_balance' => $this->test_blocked_balance,
            'cost' => $this->cost,
            'profit' => $this->profit,
            'test_profit' => $this->test_profit,
            'commission' => $this->commission,
            'total_withdrawal' => $this->total_withdrawal,
            'test_total_withdrawal' => $this->test_total_withdrawal,
            'total_withdrawal_profit' => $this->total_withdrawal_profit,
            'total_withdrawal_profit_test' => $this->total_withdrawal_profit_test,
            'min_count_withdrawal' => $this->min_count_withdrawal,
            'last_change' => $this->last_change,
            'tg_chat_id' => $this->tg_chat_id,
            'tg_private_chat_id' => $this->tg_private_chat_id,
            'tg_member_id' => $this->tg_member_id,
        ]);

        $query->andFilterWhere(['like', 'shop', $this->shop])
            ->andFilterWhere(['like', 'payment_alias', $this->payment_alias])
            ->andFilterWhere(['like', 'config_version', $this->config_version])
            ->andFilterWhere(['like', 'bot_token', $this->bot_token])
            ->andFilterWhere(['like', 'robokassa', $this->robokassa])
            ->andFilterWhere(['like', 'paykassa', $this->paykassa])
            ->andFilterWhere(['like', 'freekassa', $this->freekassa])
            ->andFilterWhere(['like', 'paypall', $this->paypall]);

        return $dataProvider;
    }
}
