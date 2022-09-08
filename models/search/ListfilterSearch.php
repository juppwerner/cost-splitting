<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Listfilter;

/**
 * ListfilterSearch represents the model behind the search form about `app\models\Listfilter`.
 */
class ListfilterSearch extends Listfilter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sortorder'], 'integer'],
            [['name', 'route', 'filterState'], 'safe'],
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
        $query = Listfilter::find()->forUser();

        $dataProvider = new ActiveDataProvider([
            'query'             => $query,
            'pagination'        => [
                'params'        => \app\widgets\GridView::getMergedFilterStateParams(null, $params),
            ],
            'sort' => [
                'defaultOrder'  => ['sortorder'=>SORT_ASC, 'name'=>SORT_ASC],
                'params'        => \app\widgets\GridView::getMergedFilterStateParams(null, $params),
            ],
        ]);

        $this->load(\app\widgets\GridView::getMergedFilterStateParams(null, $params));

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sortorder' => $this->sortorder, 
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'filterState', $this->filterState]);

        return $dataProvider;
    }
}
