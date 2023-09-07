<?php

namespace app\modules\admin\models;

/**
 * TgMembersSearch represents the model behind the search form of `app\models\TgMembers`.
 */
class TgMembersSearch extends \app\models\TgMembers{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['id', 'tg_user_id', 'is_filed'], 'integer'],
            [['tg_username', 'tg_first_name', 'tg_last_name', 'tg_bio', 'tg_type', 'last_change'], 'safe']
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
        $query = \app\models\TgMembers::find();

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
            'is_filed' => $this->is_filed,
            'last_change' => $this->last_change,
        ]);

        $query->andFilterWhere(['like', 'tg_username', $this->tg_username])
            ->andFilterWhere(['like', 'tg_first_name', $this->tg_first_name])
            ->andFilterWhere(['like', 'tg_last_name', $this->tg_last_name])
            ->andFilterWhere(['like', 'tg_bio', $this->tg_bio])
            ->andFilterWhere(['like', 'tg_type', $this->tg_type]);

        return $dataProvider;
    }
}