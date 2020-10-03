<?php


namespace odilov\treemodule\models;


trait TreeTrait
{

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

}