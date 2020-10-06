<?php


namespace odilov\treemodule\widget;


use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use yii\web\JsExpression;
use odilov\treemodule\assets\TreeInputAssset;

class TreeInputWidget extends InputWidget
{

    public $titleAttribute = 'name';

    public $query;

    public $isMultiple = true;
    public $cascadeSelect = true;
    public $collapse = true;
    public $selectableLastNode = false;

    public function run()
    {

        return $this->renderTreeInput();


    }

    public function renderTreeInput()
    {
        $hiddenInput = $this->renderInputHtml('hidden');
        $inputId = $this->options['id'] . "-input";
        $options = $this->options;
        $options['id'] = $inputId;
        $input = Html::input('text', '', '', $options);

        TreeInputAssset::register($this->getView());

        $js = "
             $('#{$inputId}').comboTree({
                source : " . Json::encode($this->getMainItems()) . ",
                hiddenInput : $('#{$this->options['id']}'),
                isMultiple : " . new JsExpression($this->getIsMultipleValue()) . ",
                cascadeSelect :  " . new JsExpression($this->getCascadeSelectValue() ) . ",
                collapse : " . new JsExpression($this->getCollapseValue()) . ",
                selectableLastNode : " . new JsExpression($this->getSelectableLastNode()) . ",
                selected : [".new JsExpression($this->getInputValue())."],
             });        
        ";
        $this->getView()->registerJs($js);

        return $hiddenInput . $input;
    }

    public function getMainItems()
    {
        return $this->query
            ->andWhere(['parent_id' => null])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
    }

    public function getInputValue()
    {
        if ($this->hasModel()) {
            return Html::getAttributeValue($this->model, $this->attribute);
        }
        return $this->value;
    }

    private function getModelClass()
    {
        return $this->query->modelClass;
    }

    private function getCascadeSelectValue()
    {
        return $this->cascadeSelect ? 'true' : 'false';
    }

    private function getCollapseValue()
    {
        return $this->collapse ? 'true' : 'false';
    }

    private function getIsMultipleValue()
    {
        return $this->isMultiple ? 'true' : 'false';
    }

    private function getSelectableLastNode()
    {
        return $this->selectableLastNode ? 'true' : 'false';
    }

}