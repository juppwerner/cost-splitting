<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Order;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'ordered_at', 'expiresAtTimestamp'], 'integer'],
            [['purchaseType', 'paymentOptionCode', 'currency', 'paymentInfo'], 'safe'],
            [['amount'], 'number'],
            [['quantityRemaining', 'isConsumed'], 'safe'],
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
        $query = Order::find()
            ->where(['userId' => Yii::$app->user->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        // var_dump($this->isConsumed); die;

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'userId' => $this->userId,
            'amount' => $this->amount,
            'ordered_at' => $this->ordered_at,
            'expiresAtTimestamp' => $this->expiresAtTimestamp,

        ]);
        if($this->isConsumed==='NULL or 0')
                $query->andWhere('isConsumed IS NULL OR isConsumed=0');
        elseif(!empty($this->isConsumed) && (bool)$this->isConsumed===true)
            $query->andFilterWhere(['isConsumed' => 1]);
        

        $query->andFilterWhere(['like', 'purchaseType', $this->purchaseType])
            ->andFilterWhere(['like', 'paymentOptionCode', $this->paymentOptionCode])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'paymentInfo', $this->paymentInfo]);

        $query->andFilterCompare('quantityRemaining', $this->quantityRemaining);

        return $dataProvider;
    }
}
