<?php
namespace app\widgets;

use thrieu\grid\FilterStateInterface;
use thrieu\grid\FilterStateTrait;

class GridView extends \kartik\grid\GridView implements FilterStateInterface {
    
    use FilterStateTrait;

    public $bordered = false;
    public $striped = true;
    public $condensed = true;
}
