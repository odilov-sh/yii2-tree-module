<?php


namespace frontend\modules\treemanager\models;


use yii\helpers\Html;

class Tree extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tree';
    }

    public function getChilds()
    {
        return $this->hasMany(Tree::className(), ['parent_id' => 'id']);
    }

    public function getHasChilds()
    {
        return count($this->childs) > 0;
    }

    public static function getMain()
    {
        return static::find()->where(['parent_id' => null])->orderBy(['sort' => SORT_ASC])->all();
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