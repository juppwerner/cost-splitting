<?php

namespace app\modules\exchangerate\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\exchangerate\models\Exchangerate;

/**
 * ExchangerateSearch represents the model behind the search form of `app\modules\exchangerate\models\Exchangerate`.
 */
class ExchangerateSearch extends Exchangerate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['histDate', 'currencyCode'], 'safe'],
            [['exchangeRate'], 'number'],
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
        $query = Exchangerate::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
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
            'histDate' => $this->histDate,
            'exchangeRate' => $this->exchangeRate,
        ]);

        $query->andFilterWhere(['like', 'currencyCode', $this->currencyCode]);

        return $dataProvider;
    }
}
