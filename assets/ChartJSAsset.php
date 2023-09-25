<?php
namespace app\assets;

use yii\web\AssetBundle;

class ChartJSAsset extends AssetBundle
{
    public $sourcePath = null;
    public $baseUrl = 'https://cdn.jsdelivr.net/npm/chart.js@3.6.0';
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.umd.js'
    ];
    public $jsOptions = [
        'crossorigin' => 'anonymous',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
