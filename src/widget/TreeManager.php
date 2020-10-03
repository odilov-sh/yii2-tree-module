<?php


namespace odilov\treemodule\widget;


use odilov\treemodule\assets\TreeManagerAssset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class TreeManager extends Widget
{


    public $options = [];

    public $listContainerOptions = [];

    public $query;

    public function init()
    {
        parent::init();

        if (!ArrayHelper::keyExists('id', $this->options )){
            ArrayHelper::setValue($this->options, 'id', $this->getId());
        }
    }

    public function run()
    {
        echo $this->renderTree();
    }

    public function renderTree()
    {
        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        Html::addCssClass($this->options, 'tree-div');
        $list = $this->renderList();

        $this->registerAssets();

        return Html::tag($tag, $list, $this->options);

    }

    public function renderList()
    {
        $listContainerTag = ArrayHelper::remove($this->listContainerOptions, 'tag', 'ul');
        $id = ArrayHelper::getValue($this->options, 'id');
        $listId = $id."-tree";

        if (!ArrayHelper::keyExists('id', $this->listContainerOptions )){
            ArrayHelper::setValue($this->listContainerOptions, 'id', $listId);
        }

        Html::addCssClass($this->listContainerOptions, 'tree-ul');

        $items = $this->renderItems();

        return Html::tag($listContainerTag, $items, $this->listContainerOptions);
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

        $name = Html::tag('div', $item->name);

        $modelClass = $this->getModelClass();
        $childs = '';
        $childItems = $modelClass::find()->where(['parent_id' => $item->id])->all();

        if (!empty($childItems)) {

            foreach ($item->childs as $child) {
                $childs .= $this->renderItem($child);
            }
            $childs = Html::tag('ul', $childs, ['class' => 'tree-child-list-wrap']);
        }

        return Html::tag('li', $name . $childs, ['id' => $item->id]);

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

            $('#".$listId."').sortableLists( options );
        });
";

        $this->getView()->registerJs($js);

    }

    private function getModelClass()
    {
        return $this->query->modelClass;
    }

}