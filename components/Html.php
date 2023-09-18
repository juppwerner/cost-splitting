<?php
namespace app\components;

use yii\bootstrap4\Html as BaseHtml;


class Html extends BaseHtml
{
 /**
     * Composes icon HTML for bootstrap Glyphicons.
     * @param string $name icon short name, for example: 'star'
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. There are also a special options:
     *
     * - tag: string, tag to be rendered, by default 'span' is used.
     * - prefix: string, prefix which should be used to compose tag class, by default 'glyphicon glyphicon-' is used.
     *
     * @return string icon HTML.
     * @see https://getbootstrap.com/components/#glyphicons
     */
    public static function icon($name, $append = ' ')
    {
        return static::tag('i', '', ['data-feather'=>$name]).$append;
    }
} 

