<?php

namespace app\models\queries;

use Yii;

/**
 * This is the ActiveQuery class for [[Listfilter]].
 *
 * @see Listfilter
 */
class ListfilterQuery extends \yii\db\ActiveQuery
{
    public function forUser()
    {
        return $this->andWhere('[[userId]]='.Yii::$app->user->id);
    }
    
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Listfilter[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Listfilter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
