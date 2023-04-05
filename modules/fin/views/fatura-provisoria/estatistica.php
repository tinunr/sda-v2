<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
$this->title = 'Estatistica';

?>
    <div class="my-menu">
        <?= Yii::$app->MyMenu->geteMenuCPI($model->id)?>
    </div>

    <div class="desciplina-perfil-view">

    
     <p style="text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-university"></i> CURSO</strong>
    </p>

    <?php 
        foreach (Yii::$app->MyQuery->cpiListFaculdadeEscola($model->id)->models as $value):?>
           <p style="text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-university"></i> <?=$value['nome']?></strong></p>

           <!-- /.box-header -->
            <div class="box-body no-padding">
              <table class="table table-condensed">
                <tr>
                  <th style="width: 40px">#</th>
                  <th>Curso</th>
                  <th style="width: 20px">VRN</th>
                  <th style="width: 20px">VRE</th>
                  <th style="width: 20px">TV</th>
                  <th style="width: 20px"></th>

                  <th style="width: 20px">1ªOP</th>
                  <th style="width: 20px">2ªOP</th>
                  <th style="width: 20px">3ªOP</th>
                  <th style="width: 20px">TC</th>
                </tr>
                 <?php $i=1 ;foreach (Yii::$app->MyQuery->cpiListFaculdadeEscolaCurso($model->id,$value['id'])->models as $curso):?>
                <tr>
                  <td style="width: 40px"> <?=$i?></td>
                  <td><?=$curso['nome']?></td>
                   <td style="width: 20px">
                        <span class="badge bg-light-green"><?= Yii::$app->MyQuery->cpiGetNumerVagasRegimeNormal($model->id,$curso['id'])?></span>
                 </td>
                  <td style="width: 20px">
                        <span class="badge bg-light-green"><?= Yii::$app->MyQuery->cpiGetNumerVagasRegimeEspecial($model->id,$curso['id'])?></span>
                 </td>
                  <td style="width: 20px">
                        <span class="badge bg-light-green"><?= Yii::$app->MyQuery->cpiGetNumerVagas($model->id,$curso['id'])?></span>
                 </td>
                  <th style="width: 20px"></th>

                 <td style="width: 20px">
                        <span class="badge bg-light-blue"><?= Yii::$app->MyQuery->cpiGetTotalCandidatoCursoOpcao1($model->id,$value['id'],$curso['id'])?></span>
                 </td>
                 <td style="width: 20px">
                        <span class="badge bg-red"><?= Yii::$app->MyQuery->cpiGetTotalCandidatoCursoOpcao2($model->id,$value['id'],$curso['id'])?></span>
                </td>
                <td style="width: 20px">
                    <span class="badge bg-yellow"><?= Yii::$app->MyQuery->cpiGetTotalCandidatoCursoOpcao3($model->id,$value['id'],$curso['id'])?></span>
                  </td>
                 <td style="width: 20px">
                        <span class="badge bg-green"><?= Yii::$app->MyQuery->cpiGetTotalCandidatoCurso($model->id,$value['id'],$curso['id'])?></span>
                 </td>

                </tr>
            <?php $i++; endforeach; ?>

              </table>
            </div>
            <!-- /.box-body -->
        
    <?php endforeach;?>


     <p style="text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-university"></i> LEGENDA</strong>
    </p>  

    <!-- /.box-header -->
            <div class="box-body no-padding">
              <table class="table table-condensed">
                <tr>
                  <td >#</td>
                  <td>VRN</td>
                  <td>Vagas Regime Normal</td>
                </tr>
                 <tr>
                  <td>#</td>
                  <td>VRE</td>
                  <td>Vagas Regime Especial</td>
                </tr>
                 <tr>
                  <td>#</td>
                  <td>TV</td>
                  <td>Total de Vagas</td>
                </tr>
                 <tr>
                  <td>#</td>
                  <td>OP</td>
                  <td>Opção</td>
                </tr>
                 <tr>
                  <td>#</td>
                  <td>TC</td>
                  <td>Total de Candidatos</td>
                </tr>
            </table>
            </div>



    </div>