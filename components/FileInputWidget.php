<?php
/**
 * @link https://www.diggin-data.de/
 * @copyright Copyright (c) 2023 Diggin' Data
 * @license https://www.diggin-data.de/license/
 */

namespace app\components;

use Yii;

/**
 * Overrides floor12/file widget to be able to add German translation
 * @author Joachim Werner <joachim.werner@diggin-data.de>
 * @since 0.5.0
 */
class FileInputWidget extends \floor12\files\components\FileInputWidget
{
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['files'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/messages',
        ];
    }



}
