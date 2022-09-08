<?php
namespace app\components;

use nemmo\attachments\behaviors\FileBehavior as BaseFileBehavior;
use nemmo\attachments\models\File;

class FileBehavior extends BaseFileBehavior
{
    /**
     * @return File[]
     * @throws \Exception
     */
    public function getFilesSorted($sortOrder)
    {
        $fileQuery = File::find()
            ->where([
                'itemId' => $this->owner->id,
                'model' => $this->getModule()->getShortClass($this->owner)
            ]);
        if(is_null($sortOrder))
            $fileQuery->orderBy(['id' => SORT_ASC]);
        else
            $fileQuery->orderBy($sortOrder);

        return $fileQuery->all();
    }

    public function addFiles()
    {
        $result = array();
        $files = explode('|', $this->owner->filenames);

        // $userTempDir = $this->getModule()->getUserDirPath();
        foreach ($files as $file) {
            if (!($result2 = $this->getModule()->attachFile($file, $this->owner))) {
                throw new \Exception(\Yii::t('yii', 'File upload failed.'));
            }
            $result[$file] = $result2->id;
        }
        return $result;
    }
    public function detachFile($id)
    {
        return $this->getModule()->detachFile($id);
    }
}
