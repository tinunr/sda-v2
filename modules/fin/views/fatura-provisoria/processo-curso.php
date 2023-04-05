<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
?>
    <div class="my-menu">
        <?= Yii::$app->MyMenu->geteMenuCPI($id)?>
    </div>

    <div class="desciplina-perfil-view">






        <div class="page-header">
            <h4>Curos</h4>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <tbody>
                    <tr>
                        <th>Ações</th>
                        <th>Curso</th>
                        <th>Faculdade / Escola</th>
                        <th>Totol processo</th>
                    </tr>
                    <?php foreach ($listCandidatosCurso as $value) :   ?>
                        <tr>
                            <td>
                                <?= Html::a('<i class="fa fa-fw fa-edit"></i> Abrir', ['listar-processo-curso','id'=>$id,'cpi_curso_id'=>$value['id'],'faculdade_id'=>$value['faculdade_id'] ],['class'=>'btn btn-xs']) ?>
                            <td>
                                 <?= $value['curso'] ?>
                            </td>
                            <td>
                                <?=$value['faculdade'];?>
                            <td>
                                <?=$value['numerCandidato'];?>
                            </td>
                        </tr>
                        <?php endforeach;?>
                </tbody>
            </table>
            <!-- /.table -->
        </div>
        <!-- /.mail-box-messages -->







    </div>
