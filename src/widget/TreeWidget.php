<?php


namespace odilov\treemodule\widget;

use Yii;
use odilov\treemodule\assets\TreeManagerAssset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class TreeWidget extends Widget
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

        $actionColumn = Html::tag('span', $action->renderDataCell($item, $item->id, 1) , [
            'class' => 'pull-right action-column'
        ]);

        $name = Html::tag('div', $item->name.$actionColumn);

        $modelClass = $this->getModelClass();
        $childs = '';
        $childItems = $modelClass::find()->where(['parent_id' => $item->id])->orderBy(['sort' => SORT_ASC])->all();

        if (!empty($childItems)) {

            foreach ($childItems as $child) {
                $childs .= $this->renderItem($child);
            }
            $childs = Html::tag('ul', $childs, ['class' => 'tree-child-list-wrap']);
        }

        return Html::tag('li', $name  . $childs, ['id' => $item->id]);

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
                $('#{$this->saveButtonOptions['id']}').click(function(e){
                e.preventDefault()
                var tree =  $('#{$this->listContainerOptions['id']}').sortableListsToArray()
                var url = '{$url}'
                var tableName = '{$tableName}'
                $.ajax({
                    url: url,
                    data: {
                        'tree' : tree,
                        'tableName' : tableName,
                    },
                    type: 'post',
                })
            })
        ";
        $this->getView()->registerJs($js);
        return Html::button($label, $this->saveButtonOptions);
    }

    public function registerAssets()
    {

        TreeManagerAssset::register($this->getView());
        $listId = ArrayHelper::getValue($this->listContainerOptions, 'id');

        $js = "
        
        $(function()
        {
            var options = {
                placeholderCss: {'background-color': '#ff8'},
                hintCss: {'background-color':'#bbf'},
                isAllowed: function( cEl, hint, target )
                {
                    if( target.data('module') === 'c' && cEl.data('module') !== 'c' )
                    {
                        hint.css('background-color', '#ff9999');
                        return false;
                    }
                    else
                    {
                        hint.css('background-color', '#99ff99');
                        return true;
                    }
                },
                opener: {
                    active: true,
                    as: 'html', 
                      close: '<span class=\"glyphicon glyphicon-minus c3\"></span>',
                    open: '<span class=\"glyphicon glyphicon-plus\"></span>',
                    openerCss: {
                        'display': 'inline-block',
                        //'width': '18px', 'height': '18px',
                        'float': 'left',
                        'margin-left': '-35px',
                        'margin-right': '5px',
                        //'background-position': 'center center', 'background-repeat': 'no-repeat',
                        'font-size': '1.1em'
                    }
                },
                ignoreClass: 'clickable'
            };

            var optionsPlus = {
                insertZonePlus: true,
                placeholderCss: {'background-color': '#ff8'},
                hintCss: {'background-color':'#bbf'},
                opener: {
                    active: true,
                    as: 'html',  // if as is not set plugin uses background image
                    close: '<span class=\"glyphicon glyphicon-minus c3\"></span>',
                    open: '<span class=\"glyphicon glyphicon-plus\"></span>',
                    openerCss: {
                        'display': 'inline-block',
                        'float': 'left',
                        'margin-left': '-35px',
                        'margin-right': '5px',
                        'font-size': '1.1em'
                    }
                }
            };

            $('#" . $listId . "').sortableLists( options );
        });
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