<?php


namespace odilov\treemodule\models;


use yii\helpers\Html;

class Tree extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tree';
    }


    public function renderItem($item)
    {
        $name = Html::tag('div', $item->name);
        $childs = '';
        if ($item->hasChilds){
            foreach ($item->childs as $child){
                $childs .= $this->renderItem($child);
            }
            $childs = Html::tag('ul', $childs, ['class' => 'tree-child-list-wrap']);
        }
        return Html::tag('li', $name.$childs, ['id' => $item->id]);
    }

}