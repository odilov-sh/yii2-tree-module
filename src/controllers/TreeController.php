<?php


namespace odilov\treemodule\controllers;


use yii\helpers\Json;
use odilov\treemodule\models\Tree;
use Yii;

class TreeController extends \yii\web\Controller
{

    public function actionSave()
    {
        return "salom";
    }

    public function actionSaveTree()
    {
        $this->enableCsrfValidation = false;
        $tree = Yii::$app->request->post('tree');

        foreach ($tree as $t){

            $model = Tree::findOne($t['id']);
            if ($model != null){

                $model->sort = $t['order'];
                $parentId = $t['parentId'];
                $model->parent_id = $parentId;

                if ($parentId != null){

                    $parent = Tree::findOne($parentId);
                    if ($parent){
                        $model->level = $parent->level + 1;
                    }

                }

                $model->save();

            }

        }

    }

}