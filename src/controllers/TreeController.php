<?php


namespace odilov\treemodule\controllers;


use yii\db\Query;
use yii\helpers\Json;
use odilov\treemodule\models\Tree;
use Yii;

class TreeController extends \yii\web\Controller
{

    public $enableCsrfValidation = false;

    public function actionSaveTree()
    {
        $tree = Yii::$app->request->post('tree');
        $tableName = Yii::$app->request->post('tableName');
        $query = new Query();

        foreach ($tree as $t){

            $id = $t['id'];
            $model = $query->select('*')
                ->from($tableName)
                ->where(['id' => $id])
                ->one();

            if ($model != null){
                $sort = $t['order'];
                $level = $model['level'];
                $parentId = isset($t['parentId']) ? $t['parentId'] :  '';
                if ($parentId != ''){
                    $parent = $query->select('*')
                        ->from($tableName)
                        ->where(['id' => $parentId])
                        ->one();
                    if ($parent != null){
                        $level = $parent['level'] + 1;
                    }
                }

                $connection = Yii::$app->db;

                $connection->createCommand()->update($tableName, [
                    'sort' => $sort,
                    'parent_id' => $parentId,
                    'level' => $level,

                ], 'id ='.$id)->execute();

            }
        }
    }
}