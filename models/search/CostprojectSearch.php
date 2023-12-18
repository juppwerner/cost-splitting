<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Costproject;
use app\models\Expense;


/**
 * CostprojectSearch represents the model behind the search form of `app\models\Costproject`.
 */
class CostprojectSearch extends Costproject
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'participants', 'currency', 'expensesAmount'], 'safe'],
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
        $query = Costproject::find()
            ->select(['costproject.*', 'expensesSum.expensesAmount'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id]);
        // add conditions that should always apply here
        $subQuery = Expense::find()
            ->select(new \yii\db\Expression('costprojectId, ROUND(SUM(amount * exchangeRate), 2) AS expensesAmount'))
            ->groupBy('costprojectId');
        $query->leftJoin(['expensesSum' => $subQuery], 'expensesSum.costprojectId = costproject.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'title',
                    'participants',
                    'currency',
                    'updated_at',
                    'expensesAmount' => [
                        'asc' => ['expensesSum.expensesAmount' => SORT_ASC],
                        'desc' => ['expensesSum.expensesAmount' => SORT_DESC],
                        'label' => (new Expense())->getAttributeLabel('expensesAmount')
                    ]
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
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'participants', $this->participants]);
        $query->andFilterWhere(['like', 'currency', $this->currency]);

        // filter by expenses amount
        if(!empty($this->expensesAmount))
            $query->andFilterCompare('expensesSum.expensesAmount', str_replace(',', '.', $this->expensesAmount));

        return $dataProvider;
    }
}
