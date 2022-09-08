<?php

namespace app\modules\lookup\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\lookup\models\Lookup;

/**
 * LookupSearch represents the model behind the search form about `backend\modules\lookup\models\Lookup`.
 */
class LookupSearch extends Lookup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active', 'sort_order', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'integer'],
            [['type', 'code', 'name', 'name_de', 'comment'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Lookup::find()->indexBy('id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => \app\widgets\GridView::getMergedFilterStateParams(),
            ],
            'sort' => [
                'defaultOrder'  => ['type'=>SORT_ASC, 'sort_order'=>SORT_ASC],
                'params' => \app\widgets\GridView::getMergedFilterStateParams(),
            ],
        ]);

        $this->load(\app\widgets\GridView::getMergedFilterStateParams());

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'active' => $this->active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
