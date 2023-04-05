<?php
namespace app\modules\cnt\repositories;


use yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\data\ActiveDataProvider;
use yii\base\Exception;

use app\modules\cnt\models\Razao;
use app\modules\cnt\models\Documento;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\Pagamento;
use app\modules\fin\models\recebimento;
use app\modules\fin\models\Transferencia;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\fin\models\Despesa;


/**
* 
*/
class RazaoRepository
{
    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function listExtratoTerceiro(array $data )
    {
        // print_r($data);die();
        $query = new Query();
        $query->select([
                        'cnt_plano_terceiro_id'=>'A.cnt_plano_terceiro_id',
                        'cnt_plano_terceiro_name'=>'C.nome',
                        ])
                ->from('cnt_razao_item A')
                ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
                ->leftJoin('dsp_person C','C.id = A.cnt_plano_terceiro_id')
                ->where(['B.status'=>1])
                ->andWhere(['B.bas_ano_id' => $data['bas_ano_id']])
                ->andWhere(['>=','B.bas_mes_id',$data['begin_mes']])
                ->andWhere(['<=','B.bas_mes_id',$data['end_mes']]) 
                ->groupBy(['A.cnt_plano_terceiro_id'])
                ->orderBy('C.nome');
            $query->andFilterWhere([
                'A.cnt_plano_conta_id' => $data['cnt_plano_conta_id'],
                'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
            ]);

        return $query->all();
    }


    /**
      * @return \yii\db\ActiveQuery
      */
    public function queryExtrato(array $data )
    {
      // print_r($data);die();
      $query = new Query();
        $query->select(['A.id',
                        'B.bas_mes_id',
                        'B.bas_ano_id',
                        'A.cnt_plano_conta_id',
                        'descricao'=>'A.descricao',
                        'B.cnt_diario_id',
                        'debito'=>new \yii\db\Expression("CASE WHEN  A.cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),
                        'credito'=>new \yii\db\Expression("CASE WHEN  A.cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),
                        'cnt_documento_id',
                        'data'=>'B.documento_origem_data',
                        'num_doc'=>'B.numero',
                        'terceiro'=>'A.cnt_plano_terceiro_id'
                        ])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->where(['B.status'=>1])
        ->andWhere(['A.cnt_plano_conta_id' => $data['cnt_plano_conta_id']])
        ->andWhere(['B.bas_ano_id' => $data['bas_ano_id']])
        ->andWhere(['>=','B.bas_mes_id',$data['begin_mes']])
        ->andWhere(['<=','B.bas_mes_id',$data['end_mes']])
        ->orderBy('B.documento_origem_data');
        $query->andFilterWhere([
            'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
        ]);
        
        return $query;
    }

    /**
      * @return \yii\db\ActiveQuery
      */
    public function queryExtratoSaldoMesAnterior(array $data )
    {
      $mes = ((int)$data['begin_mes']-1);

      $query = new Query();
        $query->select([
          'debito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN SUM(A.valor) ELSE 0 END"),'credito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN SUM(A.valor) ELSE 0 END")])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->where(['B.status'=>1])
        ->andWhere(['B.bas_ano_id' => (int)$data['bas_ano_id']])
        ->andWhere(['<=','B.bas_mes_id',$mes])
        ->andWhere(['A.cnt_plano_conta_id' => (int)$data['cnt_plano_conta_id']])
        ->groupBy('A.cnt_natureza_id')
        ->orderBy('B.documento_origem_data');
        $query->andFilterWhere([
          'A.cnt_plano_conta_id' => (int)$data['cnt_plano_conta_id'],
          'A.cnt_plano_terceiro_id' => (int)$data['cnt_plano_terceiro_id'],
        ]);
        $result = $query->one();
       // print_r($result);die();
       if (!empty($result)) {
        return (int)$result['debito'] - (int)$result['credito'];
           
       }
       return 0;
    }
}
