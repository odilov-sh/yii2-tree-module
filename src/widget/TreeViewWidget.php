<?php


namespace odilov\treemodule\widget;

use Yii;
use odilov\treemodule\assets\TreeManagerAssset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class TreeViewWidget extends Widget
{

    public $query;
    public $options = [];
    public $listContainerOptions = [];
    public $saveButtonOptions = [];
    public $actionColumn = [];

    public function run()
    {
        echo $this->renderTree();
    }

    public function renderTree()
    {
        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        ArrayHelper::setValue($this->options, 'id', $this->getId());
        Html::addCssClass($this->options, 'tree-div');
        $list = $this->renderList();
        $this->registerAssets();
        $saveButton = $this->renderSaveButton();
        $content = $list . $saveButton;

        return Html::tag($tag, $content, $this->options);

    }

    public function renderList()
    {

        if (!ArrayHelper::keyExists('id', $this->listContainerOptions)) {
            $listId = $this->id . "-tree";
            ArrayHelper::setValue($this->listContainerOptions, 'id', $listId);
        }

        Html::addCssClass($this->listContainerOptions, 'tree-ul');
        $items = $this->renderItems();
        return Html::tag('ul', $items, $this->listContainerOptions);
    }

    public function renderItems()
    {
        $result = '';
        $mainItems = $this->query->andWhere(['parent_id' => null])->orderBy(['sort' => SORT_ASC])->all();
        foreach ($mainItems as $item) {
            $result .= $this->renderItem($item);
        }
        return $result;
    }

    public function renderItem($item)
    {

        $action = Yii::createObject([
            'class' => \yii\grid\ActionColumn::className(),
        ]);

        $actionColumn = Html::tag('span', $action->renderDataCell($item, $item->id, 1), [
            'class' => 'pull-right tree-action-column'
        ]);

        $name = Html::tag('div', $item->name . $actionColumn, ['class' => 'item-container']);
        $modelClass = $this->getModelClass();

        $childs = '';
        $childItems = $modelClass::find()->where(['parent_id' => $item->id])->orderBy(['sort' => SORT_ASC])->all();

        if (!empty($childItems)) {

            foreach ($childItems as $child) {
                $childs .= $this->renderItem($child);
            }
            $childs = Html::tag('ul', $childs, ['class' => 'tree-child-list-wrap']);
        }

        return Html::tag('li', $name . $childs, ['id' => $item->id]);

    }

    public function renderActionColumn()
    {

    }

    public function renderSaveButton()
    {
        $label = ArrayHelper::remove($this->saveButtonOptions, 'label', Yii::t('app', 'Save'));
        if (!isset($this->saveButtonOptions['class'])) {
            Html::addCssClass($this->saveButtonOptions, 'btn btn-success');
        }

        Html::addCssClass($this->saveButtonOptions, 'save-tree-button');

        if (!isset($this->saveButtonOptions['id'])) {

            $saveButtonId = $this->id . "-save-button";
            $this->saveButtonOptions['id'] = $saveButtonId;
        }

        $url = Url::to(['/treemodule/tree/save-tree']);
        $tableName = $this->getTableName();
        $treeId = $this->listContainerOptions['id'];
        $js = "
                var selector =  $('#{$this->saveButtonOptions['id']}')
                var treeSelector =  $('#{$this->listContainerOptions['id']}')
                var url = '{$url}'
                var tableName = '{$tableName}' 
                saveTree(selector, treeSelector, url, tableName)
        ";
        $this->getView()->registerJs($js);
        $button =  Html::button($label, $this->saveButtonOptions);
        $loaderArea = Html::tag('div', '', ['class'=> 'tree-loader-area']);
        return Html::tag('div', $button.$loaderArea, ['style' => "display:flex; align-items: center"]);
    }

    public function registerAssets()
    {

        TreeManagerAssset::register($this->getView());
        $listId = ArrayHelper::getValue($this->listContainerOptions, 'id');
        $js = "
            var selector = $('#{$listId}')
            setSortableList(selector)
        ";
        $this->getView()->registerJs($js);
    }

    private function getModelClass()
    {
        return $this->query->modelClass;
    }

    private function getTableName()
    {
        $modelClass = $this->getModelClass();
        return $modelClass::tableName();
    }

}