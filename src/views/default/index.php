<?php
    use odilov\treemodule\models\Tree;
    \odilov\treemodule\models\TreeManagerAssset::register($this);

use yii\grid\DataColumn;
use yii\helpers\Html;
    $this->title = 'Treemanager';
    $js = <<<JS
 $(function()
        {
            var options = {
                placeholderCss: {'background-color': '#ff8'},
                hintCss: {'background-color':'#bbf'},
                onChange: function( cEl )
                {
                    console.log( 'onChange' );
                },
                complete: function( cEl )
                {
                    console.log( 'complete' );
                },
                isAllowed: function( cEl, hint, target )
                {
                    // Be carefull if you test some ul/ol elements here.
                    // Sometimes ul/ols are dynamically generated and so they have not some attributes as natural ul/ols.
                    // Be careful also if the hint is not visible. It has only display none so it is at the previous place where it was before(excluding first moves before showing).
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
                    as: 'html',  // if as is not set plugin uses background image
                      close: '<span class="glyphicon glyphicon-minus c3"></span>',
                    open: '<span class="glyphicon glyphicon-plus"></span>',
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
                    close: '<span class="glyphicon glyphicon-minus c3"></span>',
                    open: '<span class="glyphicon glyphicon-plus"></span>',
                    openerCss: {
                        'display': 'inline-block',
                        'float': 'left',
                        'margin-left': '-35px',
                        'margin-right': '5px',
                        'font-size': '1.1em'
                    }
                }
            };

            $('.w1-tree').sortableLists( options );
            $('.w2-tree').sortableLists( options );

            $('#toArrBtn').on( 'click', function(){ console.log( $('#sTree2').sortableListsToArray() ); } );
            $('#toHierBtn').on( 'click', function() { console.log( $('#sTree2').sortableListsToHierarchy() ); } );
            $('#toStrBtn').on( 'click', function() { console.log( $('#sTree2').sortableListsToString() ); } );
            
            
            $('#saveBtn').click(function(e){
                
                e.preventDefault()
                var tree =  $('#sTree2').sortableListsToArray()
                console.log(tree)
                var url = $(this).data('url')
                var csrf = $(this).data('csrf')
                $.ajax({
                    url: url,
                    data: {
                        'tree' : tree,
                        '_csrf-frontend' : csrf,
                    },
                    type: 'post',
                    success: function(result){
                        console.log(result)
                    }
                })
                
                
            })
            
        });

JS;

    $this->registerJs($js);

    $main = Tree::getMain();



    
?>
<div id="w1" class="tree-div">
    <ul class="tree-ul w1-tree">
        <?php foreach ($main as $m): ?>
            <?= $m->renderItem($m) ?>

            <?php
            /*   $action =  Yii::createObject([
                   'class' => \yii\grid\ActionColumn::className(),
               ]);

               echo $action->renderDataCell($m, $m->id, 1);*/


            ?>

        <?php endforeach ?>
    </ul>

</div>
<div id="w2" class="tree-div">
    <ul class="tree-ul w2-tree">
        <?php foreach ($main as $m): ?>
            <?= $m->renderItem($m) ?>

            <?php
            /*   $action =  Yii::createObject([
                   'class' => \yii\grid\ActionColumn::className(),
               ]);

               echo $action->renderDataCell($m, $m->id, 1);*/


            ?>

        <?php endforeach ?>
    </ul>

</div>


    <?= Html::a('Save', "#", [

            'class' => 'btn btn-success',
            'id' => 'saveBtn',

            'data' => [
                    'url' => Yii::$app->urlManager->createUrl(['treemanager/tree/save-tree']),
                    'csrf' => Yii::$app->request->csrfToken,
            ]
    ]) ?>
<section>
    <p><strong>JQuery sortable lists</strong> also supports export methods. Look at the console log to see the result of buttons below.</p>
    <span id="toArrBtn" class="btn">To array</span>
    <span id="toHierBtn" class="btn">To hierarchy</span>
    <span id="toStrBtn" class="btn">To string</span>
</section>