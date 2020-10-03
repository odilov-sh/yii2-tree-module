<?php


namespace frontend\modules\treemanager\controllers;


use yii\helpers\Json;
use frontend\modules\treemanager\models\Tree;
use Yii;

class TreeController extends \yii\web\Controller
{

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