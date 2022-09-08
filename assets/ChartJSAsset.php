<?php
namespace app\assets;

use yii\web\AssetBundle;

class ChartJSAsset extends AssetBundle
{
    public $sourcePath = null;
    public $baseUrl = 'https://cdn.jsdelivr.net/npm/chart.js@3.6.0';
    public $js = [
        ['dist/chart.min.js', 'integrity' => 'sha256-7lWo7cjrrponRJcS6bc8isfsPDwSKoaYfGIHgSheQkk='],
    ];
    public $jsOptions = [
        'crossorigin' => 'anonymous',
    ];
}
