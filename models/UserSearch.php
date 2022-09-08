<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * ChecklistItemSearch represents the model behind the search form of `app\models\ChecklistItem`.
 */
class UserSearch extends \app\models\User
{
    public $roleName;
    public $roleDescription;
    public $userId;
    public $userFullName;
    public $username;
    public $userEmail;

    // {{{ rules
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['roleName', 'roleDescription', 'userId', 'userFullName', 'username', 'userEmail'], 'safe'],
        ];
    } // }}} 
    // {{{ scenarios
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    } // }}} 
    // {{{ search
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /**
         * Using Query and ActiveDataProvider?
         */
        $query = (new \yii\db\Query())
            ->select(['roleName', 'roleDescription', 'userId', 'userFullName', 'username', 'userEmail'])
            ->from('v_role_users');

        $dataProvider = new ActiveDataProvider([
            'db' => Yii::$app->db,
            'query' => $query,
            // 'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
                'params'        => \app\widgets\GridView::getMergedFilterStateParams(null, $params),
            ],
            'sort' => [
                'attributes' => [
                    'roleName',
                    'roleDescription',
                    'userId',
                    'userFullName',
                    'username',
                    'userEmail',
                ],
                'defaultOrder' => [
                    'roleName' => SORT_ASC,
                    'userFullName' => SORT_ASC
                ],
                'params'        => \app\widgets\GridView::getMergedFilterStateParams(),
            ],
        ]);

        // Filter model
        $this->load(\app\widgets\GridView::getMergedFilterStateParams(null, $params));

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'userId' => $this->userId,
        ]);
        //$query->andFilterWhere(['in', 'market', $this->market]);

        // grid filtering conditions
        $query->andFilterWhere(['like', 'roleName', $this->roleName])
            ->andFilterWhere(['like', 'roleDescription', $this->roleDescription])
            ->andFilterWhere(['like', 'userFullName', $this->userFullName])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'userEmail', $this->userEmail]);
        
        return $dataProvider;
    } // }}} 

};
