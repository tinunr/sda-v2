<?php

namespace app\modules\fin\components;


use yii;
use yii\base\Component;
use yii\db\Query;
use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\dsp\models\Person;

/**
 *
 */
class FinQuery extends Component
{

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespachanteItem($dsp_processo_id)
   {
      $command = Yii::$app->db->createCommand('SELECT C.id, C.descricao , SUM(B.valor) as valor
          FROM fin_fatura_provisoria A
          LEFT JOIN fin_fatura_provisoria_item B
          ON(A.id = B.dsp_fatura_provisoria_id )
          LEFT JOIN dsp_item C
          ON(C.id = B.dsp_item_id)
          WHERE A.dsp_processo_id =:dsp_processo_id 
          AND C.dsp_person_id = :daAgencia AND A.status = :estado
          GROUP BY C.id');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':daAgencia' => 1, ':estado' => 1]);
      $result = $command->queryAll();
      return $result;
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespesaItem($dsp_processo_id)
   {
      $command = Yii::$app->db->createCommand('SELECT C.id, C.descricao , SUM(B.valor) as valor
               FROM fin_despesa A
          LEFT JOIN fin_despesa_item B
                 ON(A.id = B.fin_despesa_id )
          LEFT JOIN dsp_item C
                 ON(C.id = B.item_id )
              WHERE A.dsp_processo_id =:dsp_processo_id AND A.status =:estado AND A.saldo = 0
           GROUP BY C.id');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':estado' => 1]);
      $result = $command->queryAll();
      return $result;
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespachanteItemData($dsp_processo_id, $data)
   {
      $command = Yii::$app->db->createCommand('SELECT C.id, C.descricao , SUM(B.valor) as valor
          FROM fin_fatura_provisoria A
          LEFT JOIN fin_fatura_provisoria_item B
          ON(A.id = B.dsp_fatura_provisoria_id )
          LEFT JOIN dsp_item C
          ON(C.id = B.dsp_item_id)
          WHERE A.dsp_processo_id =:dsp_processo_id 
          AND C.dsp_person_id = :daAgencia AND A.status = :estado AND A.updated_at > :data
          GROUP BY C.id');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':daAgencia' => 1, ':estado' => 1, ':data' => $data]);
      $result = $command->queryAll();
      return $result;
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespesaItemData($dsp_processo_id, $data)
   {
      $command = Yii::$app->db->createCommand('SELECT C.id, C.descricao , SUM(B.valor) as valor
               FROM fin_despesa A
          LEFT JOIN fin_despesa_item B
                 ON(A.id = B.fin_despesa_id )
          LEFT JOIN dsp_item C
                 ON(C.id = B.item_id )
              WHERE A.dsp_processo_id =:dsp_processo_id AND A.status =:estado AND A.updated_at > :data AND A.fin_nota_credito_id IS NULL AND A.saldo = 0
           GROUP BY C.id');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':estado' => 1, ':data' => $data]);
      $result = $command->queryAll();
      return $result;
   }






   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function isNotaCredito($dsp_processo_id)
   {
      $valorNC = $this->getValorProcesso($dsp_processo_id) - ($this->getValorDespesa($dsp_processo_id) + $this->getValorDespachante($dsp_processo_id));
      // print_r($this->getValorDespachante($dsp_processo_id));die();    
      return $valorNC;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function isNotaCreditoData($dsp_processo_id, $data)
   {
      $valorNC = $this->getValorProcessoData($dsp_processo_id, $data) - ($this->getValorDespesaData($dsp_processo_id, $data) + $this->getValorDespachanteData($dsp_processo_id, $data));
      // print_r($valorNC);die();    
      return $valorNC;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorProcesso($dsp_processo_id)
   {
      $command = Yii::$app->db->createCommand('SELECT  SUM(B.valor) AS vl
          FROM fin_fatura_provisoria A
          LEFT JOIN fin_fatura_provisoria_item B
          ON(A.id = B.dsp_fatura_provisoria_id )
          LEFT JOIN dsp_item C
          ON(C.id = B.dsp_item_id)
          WHERE A.dsp_processo_id =:dsp_processo_id AND A.status =1');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id]);
      $result = $command->queryScalar();
      return $result;
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorProcessoData($dsp_processo_id, $data)
   {
      $command = Yii::$app->db->createCommand('SELECT  SUM(B.valor) AS vl
          FROM fin_fatura_provisoria A
          LEFT JOIN fin_fatura_provisoria_item B
          ON(A.id = B.dsp_fatura_provisoria_id )
          LEFT JOIN dsp_item C
          ON(C.id = B.dsp_item_id)
          WHERE A.dsp_processo_id =:dsp_processo_id AND A.status =1 AND A.data > :data');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':data' => $data]);
      $result = $command->queryScalar();
      return $result;
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorDespesa($dsp_processo_id)
   {
      $command = Yii::$app->db->createCommand('SELECT SUM(B.valor) as valor
               FROM fin_despesa A
          LEFT JOIN fin_despesa_item B
                 ON(A.id = B.fin_despesa_id )
          LEFT JOIN dsp_item C
                 ON(C.id = B.item_id )
              WHERE A.dsp_processo_id =:dsp_processo_id AND A.status = 1 AND A.saldo = 0 ');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id]);
      $result = $command->queryScalar();
      return $result;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorDespesaData($dsp_processo_id, $data)
   {
      $command = Yii::$app->db->createCommand('SELECT SUM(B.valor) as valor
               FROM fin_despesa A
          LEFT JOIN fin_despesa_item B
                 ON(A.id = B.fin_despesa_id )
          LEFT JOIN dsp_item C
                 ON(C.id = B.item_id )
              WHERE A.dsp_processo_id =:dsp_processo_id AND A.status = 1 AND A.saldo = 0 AND A.updated_at>:data AND A.fin_nota_credito_id IS NULL');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':data' => $data]);
      $result = $command->queryScalar();
      return $result;
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorDespachante($dsp_processo_id)
   {
      $command = Yii::$app->db->createCommand('SELECT  SUM(B.valor) AS vl
          FROM fin_fatura_provisoria A
          LEFT JOIN fin_fatura_provisoria_item B
          ON(A.id = B.dsp_fatura_provisoria_id )
          LEFT JOIN dsp_item C
          ON(C.id = B.dsp_item_id)
          WHERE A.dsp_processo_id =:dsp_processo_id AND A.status=1 AND C.dsp_person_id = :isAgencia');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':isAgencia' => 1]);
      $result = $command->queryScalar();
      return $result;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorDespachanteData($dsp_processo_id, $data)
   {
      $command = Yii::$app->db->createCommand('SELECT  SUM(B.valor) AS vl
          FROM fin_fatura_provisoria A
          LEFT JOIN fin_fatura_provisoria_item B
          ON(A.id = B.dsp_fatura_provisoria_id )
          LEFT JOIN dsp_item C
          ON(C.id = B.dsp_item_id)
          WHERE A.dsp_processo_id =:dsp_processo_id AND A.status=1 AND C.dsp_person_id = :isAgencia AND A.updated_at > :data');
      $command->bindValues([':dsp_processo_id' => $dsp_processo_id, ':isAgencia' => 1, ':data' => $data]);
      $result = $command->queryScalar();
      return $result;
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function IsRecebidoPago($dsp_processo_id)
   {
      $saldoReceita = (new \yii\db\Query())
         ->from('fin_receita A')
         ->leftJoin('fin_fatura_provisoria B', 'B.id = A.dsp_fataura_provisoria_id')
         ->where(['B.dsp_processo_id' => $dsp_processo_id])
         ->andWhere(['A.status' => 1])
         ->sum('A.saldo');

      $saldoDespesa = (new \yii\db\Query())
         ->from('fin_despesa A')
         ->where(['A.dsp_processo_id' => $dsp_processo_id])
         ->andWhere(['A.status' => 1])
         ->andWhere(['A.fin_nota_credito_id' => null])
         ->sum('A.saldo');
      if ($saldoDespesa > 0 || $saldoReceita > 0) {
         return true;
      }
      return false;
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function existFpProcesso($id)
   {
      $result = (new \yii\db\Query())
         ->from('fin_fatura_provisoria A')
         ->where(['A.dsp_processo_id' => $id])
         ->andWhere(['A.status' => 1])
         ->all();
      if (empty($result)) {
         return false;
      }

      return true;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function existFpProcessoParaFD($id)
   {
      return FaturaProvisoria::find()
         ->where(['dsp_processo_id' => $id])
         ->andWhere(['status' => 1])
         ->all();
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function existFpProcessoParaFDData($id, $data)
   {
      return FaturaProvisoria::find()
         ->where(['dsp_processo_id' => $id])
         ->andWhere(['status' => 1])
         ->andWhere(['<', 'updated_at', $data])
         ->all();
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function personAdiantamenro()
   {
      return (new \yii\db\Query())
         ->select(['A.dsp_person_id', 'E.nome'])
         ->from('fin_recebimento A')
         ->leftJoin('fin_fatura_provisoria B', 'A.dsp_fatura_provisoria_id = B.id')
         ->leftJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_despesa D', 'B.id = D.dsp_fatura_provisoria_id')
         ->leftJoin('dsp_person E', 'E.id = A.dsp_person_id')
         ->where('A.fin_recebimento_tipo_id =2')
         ->orderBy('E.nome')
         ->groupBy('A.dsp_person_id')
         ->all();
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespesaFaturaItem($id, $dsp_person_id)
   {
      $command = Yii::$app->db->createCommand('SELECT C.id, C.descricao , SUM(B.valor) as valor
           FROM fin_fatura_provisoria A
           LEFT JOIN fin_fatura_provisoria_item B
           ON(A.id = B.dsp_fatura_provisoria_id)
           LEFT JOIN dsp_item C
           ON(C.id = B.dsp_item_id)
           WHERE A.id =:id AND A.status = :estado AND B.item_origem_id != :item_origem_id AND C.dsp_person_id != :isAgencia AND C.dsp_person_id = :dsp_person_id
           GROUP BY C.id');
      $command->bindValues([':id' => $id, ':item_origem_id' => 'D', ':estado' => 1, ':isAgencia' => 1, ':dsp_person_id' => $dsp_person_id]);
      $result = $command->queryAll();
      return $result;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getFaturaProvisoriaData(array $fin_fatura_provisoria_id)
   {
      return FaturaProvisoria::find()
         ->where(['id' => $fin_fatura_provisoria_id])
         ->andWhere(['status' => 1])
         ->all();
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespachanteItemParceal(array $fin_fatura_provisoria_id)
   {

      return (new \yii\db\Query())
         ->select(['C.id', 'C.descricao', '(SUM(B.valor)-fin_reembolso_processo_item(A.dsp_processo_id, C.id)) as valor'])
         ->from('fin_fatura_provisoria A')
         ->leftJoin('fin_fatura_provisoria_item B', 'A.id = B.dsp_fatura_provisoria_id')
         ->leftJoin('dsp_item C', 'C.id = B.dsp_item_id')
         ->where(['A.id' => $fin_fatura_provisoria_id])
         ->andWhere(['C.dsp_person_id' => 1])
         ->andWhere(['A.status' => 1])
         ->groupBy('C.id')
         ->all();
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function listDespesaItemParceal(array $fin_fatura_provisoria_id)
   {


      return (new \yii\db\Query())
         ->select(['C.id', 'C.descricao', '(SUM(B.valor)-fin_reembolso_processo_item(A.dsp_processo_id, C.id)) as valor'])
         ->from('fin_despesa A')
         ->leftJoin('fin_despesa_item B', 'A.id = B.fin_despesa_id ')
         ->leftJoin('dsp_item C', 'C.id = B.item_id')
         ->where(['A.dsp_fatura_provisoria_id' => $fin_fatura_provisoria_id])
         ->andWhere(['A.saldo' => 0])
         ->andWhere(['A.status' => 1])
         ->groupBy('C.id')
         ->all();
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function IsRecebidoPagoParceal(array $fin_fatura_provisoria_id)
   {
      $saldoReceita = (new \yii\db\Query())
         ->from('fin_receita A')
         ->leftJoin('fin_fatura_provisoria B', 'B.id = A.dsp_fataura_provisoria_id')
         ->where(['A.dsp_fataura_provisoria_id' => $fin_fatura_provisoria_id])
         ->andWhere(['A.status' => 1])
         ->sum('A.saldo');

      $saldoDespesa = (new \yii\db\Query())
         ->from('fin_despesa A')
         ->where(['A.dsp_fatura_provisoria_id' => $fin_fatura_provisoria_id])
         ->andWhere(['A.status' => 1])
         ->andWhere(['A.fin_nota_credito_id' => null])
         ->sum('A.saldo');
      if ($saldoDespesa > 0 || $saldoReceita > 0) {
         return true;
      }
      return false;
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorProcessoParceal(array $fin_fatura_provisoria_id)
   {
      return (new \yii\db\Query())
         ->from('fin_fatura_provisoria A')
         ->leftJoin('fin_fatura_provisoria_item B', 'A.id = B.dsp_fatura_provisoria_id')
         ->leftJoin('dsp_item C', 'C.id = B.dsp_item_id')
         ->where(['A.id' => $fin_fatura_provisoria_id])
         ->andWhere(['A.status' => 1])
         ->sum('B.valor');
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorDespesaParceal(array $fin_fatura_provisoria_id)
   {
      return (new \yii\db\Query())
         ->from('fin_despesa A')
         ->leftJoin('fin_despesa_item B', 'A.id = B.fin_despesa_id')
         ->leftJoin('dsp_item C', 'C.id = B.item_id')
         ->where(['A.dsp_fatura_provisoria_id' => $fin_fatura_provisoria_id])
         ->andWhere(['A.status' => 1])
         ->sum('B.valor');
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorDespachanteParceal(array $fin_fatura_provisoria_id)
   {
      return (new \yii\db\Query())
         ->from('fin_fatura_provisoria A')
         ->leftJoin('fin_fatura_provisoria_item B', 'A.id = B.dsp_fatura_provisoria_id')
         ->leftJoin('dsp_item C', 'C.id = B.dsp_item_id')
         ->where(['A.id' => $fin_fatura_provisoria_id])
         ->andWhere('C.dsp_person_id=1')
         ->andWhere(['A.status' => 1])
         ->sum('B.valor');
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function getValorReembolso(array $fin_fatura_provisoria_id)
   {

      // $fatura = FaturaProvisoria::find()->where(['id'=>$fin_fatura_provisoria_id])->one();

      return (new \yii\db\Query())
         ->from('fin_recebimento_item A')
         ->leftJoin('fin_recebimento B', 'B.id = A.fin_recebimento_id')
         ->leftJoin('dsp_item C', 'C.id = A.dsp_item_id')
         ->where(['B.dsp_fatura_provisoria_id' => $fin_fatura_provisoria_id])
         ->andWhere('B.fin_recebimento_tipo_id=4')
         ->andWhere(['A.status' => 1])
         ->sum('A.valor_recebido');
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function isNotaCreditoParceal(array $fin_fatura_provisoria_id)
   {
      // print_r($this->getValorDespesaParceal($fin_fatura_provisoria_id));die();
      $valorNC = $this->getValorProcessoParceal($fin_fatura_provisoria_id) - (($this->getValorDespesaParceal($fin_fatura_provisoria_id) - $this->getValorReembolso($fin_fatura_provisoria_id)) + $this->getValorDespachanteParceal($fin_fatura_provisoria_id));
      return $valorNC;
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorRecebido(array $fin_fatura_provisoria_id)
   {
      return (($this->getValorDespesaParceal($fin_fatura_provisoria_id) - $this->getValorReembolso($fin_fatura_provisoria_id)) + $this->getValorDespachanteParceal($fin_fatura_provisoria_id));
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function checkDespesaFaturaProcesso($dsp_processo_id)
   {
      $query = \app\modules\fin\models\Despesa::find()
         ->andWhere(['status' => 1])
         ->andWhere([
            'or',
            ['<=', 'dsp_fatura_provisoria_id', 0],
            ['dsp_fatura_provisoria_id' => null]
         ])
         ->andWhere([
            'or',
            ['<=', 'fin_nota_credito_id', 0],
            ['fin_nota_credito_id' => null]
         ])
         ->andWhere([
            'or',
            ['<=', 'fin_aviso_credito_id', 0],
            ['fin_aviso_credito_id' => null]
         ])
         ->andWhere(['dsp_processo_id' => $dsp_processo_id]);
      $count = $query->count();
      // print_r($count);die();
      if ($count > 0) {
         $faturas = \app\modules\fin\models\FaturaProvisoria::find()
            ->where(['dsp_processo_id' => $dsp_processo_id])
            ->andWhere(['status' => 1]);
         if ($faturas->count() > 1) {
            return true;
         } else {
            $fatura = $faturas->one();
            if (empty($fatura)) {
               return false;
            }
            $query2 = \app\modules\fin\models\Despesa::find()
               ->andWhere(['status' => 1])
               ->andWhere([
                  'or',
                  ['<=', 'dsp_fatura_provisoria_id', 0],
                  ['dsp_fatura_provisoria_id' => null]
               ])
               ->andWhere([
                  'or',
                  ['<=', 'fin_nota_credito_id', 0],
                  ['fin_nota_credito_id' => null]
               ])
               ->andWhere([
                  'or',
                  ['<=', 'fin_aviso_credito_id', 0],
                  ['fin_aviso_credito_id' => null]
               ])
               ->andWhere(['dsp_processo_id' => $dsp_processo_id]);


            foreach ($query2->all() as $key => $value) {
               $value->dsp_fatura_provisoria_id = $fatura->id;
               $value->save(false);
            }
         }
      } else {
         return false;
      }
      return false;
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function outrasDespesaFaturaDefinitiva($fin_fatura_definitiva_id)
   {

      $query = new Query;
      return $query->select(['B.id', 'B.descricao', 'A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->andWhere(['not', ['B.id' => Yii::$app->params['honorario_and_iva']]])
         ->andWhere(['B.dsp_person_id' => Person::ID_AGENCIA])
         ->all();
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function despesaFaturaDefinitiva($fin_fatura_definitiva_id)
   {

      $query = new Query;
      return $query->select(['B.id', 'B.descricao', 'A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->andWhere(['not', ['B.dsp_person_id' => Person::ID_AGENCIA]])
         ->all();
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorHonorarioFaturaDefinitiva($fin_fatura_definitiva_id)
   {
      $query = new Query;
      return $query->select(['A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->andWhere(['B.id' => 1002])
         ->scalar();
   }

   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorIvaHonorarioFaturaDefinitiva($fin_fatura_definitiva_id)
   {
      $query = new Query;
      return $query->select(['A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->andWhere(['B.id' => 1003])
         ->scalar();
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function provissoriaProcessoOne($dsp_processo_id)
   {
      return FaturaProvisoria::find()->where(['dsp_processo_id' => $dsp_processo_id])->one();
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function fpFaturaDefinitiva($fin_fatura_definitiva_id)
   {
      $recebimentos =  (new \yii\db\Query())->select([
         'fatura_provisorias' => "GROUP_CONCAT(CONCAT(B.numero, '/', B.bas_ano_id))", 
         'recibos' => "GROUP_CONCAT(CONCAT(E.numero, '/', E.bas_ano_id))"
      ])
         ->from('fin_fatura_definitiva_provisoria A')
         ->leftJoin('fin_fatura_provisoria B', 'A.fin_fatura_provisoria_id = B.id')
         ->innerJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_recebimento_item D', 'C.id = D.fin_receita_id')
         ->leftJoin('fin_recebimento E', 'E.id = D.fin_recebimento_id AND E.status=1')
         ->where('B.status=1') 
         ->andWhere(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->groupBy('A.fin_fatura_definitiva_id')
         ->one();

      $encontro_de_contas_a =  (new \yii\db\Query())->select([
         'fatura_provisorias' => "GROUP_CONCAT(CONCAT(B.numero, '/', B.bas_ano_id))", 'recibos' => "GROUP_CONCAT(CONCAT(E.numero, '/', E.bas_ano_id))"
      ])
         ->from('fin_fatura_definitiva_provisoria A')
         ->leftJoin('fin_fatura_provisoria B', 'A.fin_fatura_provisoria_id = B.id')
         ->innerJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_of_accounts_item D', 'C.id = D.fin_receita_id')
         ->leftJoin('fin_of_accounts E', 'E.id = D.fin_of_account_id AND E.status=1')
         ->where('B.status=1') 
         ->andWhere(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->groupBy('A.fin_fatura_definitiva_id')
         ->one();

      $encontro_de_contas_b =  (new \yii\db\Query())->select([
         'fatura_provisorias' => "GROUP_CONCAT(CONCAT(B.numero, '/', B.bas_ano_id))", 'recibos' => "GROUP_CONCAT(CONCAT(D.numero, '/', D.bas_ano_id))"
      ])
         ->from('fin_fatura_definitiva_provisoria A')
         ->leftJoin('fin_fatura_provisoria B', 'A.fin_fatura_provisoria_id = B.id')
         ->innerJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_of_accounts D', 'C.id = D.fin_receita_id AND D.status=1')
         ->leftJoin('fin_of_accounts_item E', 'E.fin_of_account_id = D.id ')
         ->where('B.status=1')
         ->andWhere(['A.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->groupBy('A.fin_fatura_definitiva_id')
         ->one();
      return [
         'fatura_provisorias' => empty($recebimentos['fatura_provisorias']) ? '' : 'FP: ' . $recebimentos['fatura_provisorias'],
         'recibos' => (empty($recebimentos['recibos']) ? '' : 'REC: ' . $recebimentos['recibos']) . (empty($encontro_de_contas_a['recibos']) ? '' : ',EC: ' . $encontro_de_contas_a['recibos']) . (empty($encontro_de_contas_b['recibos']) ? '' : ',EC: ' . $encontro_de_contas_b['recibos']),
      ];
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorBaseHonorario($fin_fatura_definitiva_id)
   {
      $tipo = 1;
      $valor = null;
      $descricao = null;
      $forma = null;
      $faruraDefinitiva = FaturaDefinitiva::findOne($fin_fatura_definitiva_id);
      if (empty($faruraDefinitiva->dsp_regime_item_valor) && empty($faruraDefinitiva->dsp_regime_item_tabela_anexa_valor)) {


         $faturaProvisoria = FaturaProvisoria::find()->where(['dsp_processo_id' => $faruraDefinitiva->dsp_processo_id])->one();
         if (!empty($faturaProvisoria)) {
            # code...
            if (empty($faturaProvisoria->dsp_regime_item_tabela_anexa_valor)) {
               # code...
               $forma = $faturaProvisoria->dsp_regime_item_valor;
            } else {
               $forma = $faturaProvisoria->dsp_regime_item_tabela_anexa_valor;
            }
         }
      } else {
         if (empty($faruraDefinitiva->dsp_regime_item_tabela_anexa_valor)) {
            $forma = $faruraDefinitiva->dsp_regime_item_valor;
         } else {
            $forma = $faruraDefinitiva->dsp_regime_item_tabela_anexa_valor;
         }
      }

      $pos_espaco = strpos($forma, ' '); // perceba que há um espaço aqui
      $valor = substr($forma, 0, $pos_espaco);
      $descricao = substr($forma, $pos_espaco, strlen($forma));
      if ($descricao == 'Ad-valorem') {
         $tipo = 0;
      }

      return [
         'valor' => $valor,
         'descricao' => $descricao,
         'tipo' => $tipo,
      ];
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function recebimetoDaFaturaDefinitiva($fin_fatura_definitiva_id)
   {
      return (new \yii\db\Query())->select(['fatura_provisorias' => "CONCAT(C.numero, '/', C.bas_ano_id)", 'data_fatura' => 'C.data', 'recibos' => "CONCAT(E.numero, '/', E.bas_ano_id)", 'data_recebimento' => 'E.data', 'A.valor_recebido'])
         ->from('fin_recebimento_item A')
         ->leftJoin('fin_recebimento E', 'E.id = A.fin_recebimento_id')
         ->innerJoin('fin_receita B', 'B.id = A.fin_receita_id')
         ->leftJoin('fin_fatura_provisoria C', 'C.id = B.dsp_fataura_provisoria_id')
         ->leftJoin('fin_fatura_definitiva_provisoria D', 'D.fin_fatura_provisoria_id = C.id')
         ->where('E.status=1')
         ->andWhere('B.status=1')
         ->andWhere(['D.fin_fatura_definitiva_id' => $fin_fatura_definitiva_id])
         ->andWhere(['>','D.fin_fatura_definitiva_id' ,0 ])
         ->all();
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function disponibilidadeFinanceira()
   {
      return (new \yii\db\Query())->select([
         'B.sigla', 'B.descricao', 'A.numero', 'A.saldo', 'tipo' => new \yii\db\Expression("CASE WHEN  B.id =1 THEN 'CAIXA' ELSE 'BANCO' END")
      ])
         ->from('fin_banco_conta A')
         ->leftJoin('fin_banco B', 'A.fin_banco_id = B.id')
         ->where('A.status=1')
         ->andWhere('A.cnt_plano_conta_id is not null')
         ->orderBy('B.id', 'B.sigla')
         ->all();
   }




   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorAFavorProcesso()
   {
      $query1 = (new \yii\db\Query())
         ->select([
            'A.id', 'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)", 'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`D`.`numero`, '/', `D`.`bas_ano_id`) SEPARATOR ',')", 'nord' => 'C.id', 'mercadoria' => 'A.descricao', 'dsp_person_id' => 'A.nome_fatura', 'person' => 'B.nome', 'valor_recebido' => 'SUM(E.valor_recebido)', 'A.data'
         ])
         ->from('dsp_processo A')
         ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
         ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
         ->leftJoin('fin_fatura_provisoria D', 'A.id=D.dsp_processo_id')
         ->innerJoin('fin_receita E', 'D.id = E.dsp_fataura_provisoria_id')
         ->where('A.status!=11')
         ->groupBy('A.id')
         ->orderBy('A.data');

      $query2 = (new \yii\db\Query())
         ->select([
            'Z.id', 'Z.numero', 'Z.fatura_provisorias', 'Z.nord', 'Z.mercadoria', 'Z.dsp_person_id', 'Z.person', 'Z.data', 'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+Z.valor_recebido ELSE Z.valor_recebido END'
         ])
         ->from(['Z' => $query1])
         ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = Z.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
         ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
         ->groupBy('Z.id')
         ->orderBy('Z.data');


      $query3 = (new \yii\db\Query())
         ->select([
            'X.id', 'X.numero', 'X.fatura_provisorias', 'X.nord', 'X.mercadoria', 'X.dsp_person_id', 'X.person', 'X.data', 'X.valor_recebido', 'total_pago' => 'CASE WHEN SUM(A.valor_pago) > 0 THEN SUM(A.valor_pago) ELSE 0 END'
         ])
         ->from(['X' => $query2])
         ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = X.id')
         ->where(' X.valor_recebido > 0')
         ->groupBy('X.id')
         ->orderBy('X.data');


      $query4 = (new \yii\db\Query())
         ->select([
            'W.id', 'W.numero', 'W.fatura_provisorias', 'W.nord', 'W.mercadoria', 'W.dsp_person_id', 'W.person', 'W.data', 'W.valor_recebido', 'valor_despesa' => 'SUM(B.valor)', 'W.total_pago'
         ])
         ->from(['W' => $query3])
         ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
         ->innerJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id AND C.valor_recebido > 0')
         ->leftJoin('fin_fatura_provisoria_item B', 'A.id = B.dsp_fatura_provisoria_id ')
         ->innerJoin('dsp_item D', 'D.id = B.dsp_item_id AND D.dsp_person_id != 1')
         ->groupBy('W.id')
         ->orderBy('W.data');

      $query = (new \yii\db\Query())
         ->select([
            'P.id', 'P.numero', 'P.fatura_provisorias', 'P.nord', 'P.mercadoria', 'P.dsp_person_id', 'P.person', 'P.data', 'P.valor_recebido', 'P.valor_despesa', 'total_pago' => 'CASE WHEN SUM(A.valor) > 0 THEN SUM(A.valor)+P.total_pago ELSE P.total_pago END'
         ])
         ->from(['P' => $query4])
         ->leftJoin('fin_nota_credito A', 'A.status = 1 AND A.dsp_processo_id = P.id')
         ->groupBy('P.id')
         ->having('valor_despesa > total_pago');
      $data  =  $query->all();
      $total_saldo = 0;
      foreach ($data as $key => $value) {
         $saldo = ($value['valor_recebido'] < $value['valor_despesa']) ? ($value['valor_recebido'] - $value['total_pago']) : ($value['valor_despesa'] - $value['total_pago']);
         $total_saldo = $total_saldo + $saldo;
      }
      //   print_r($valor_recebido);die();

      //    $valor_despesa  =  $query->sum('valor_despesa');
      //    $total_pago  =  $query->sum('total_pago');
      //    $saldo = ($valor_recebido<$valor_despesa)?($valor_recebido-$total_pago):($valor_despesa-$total_pago);
      return $total_saldo;
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorAFavorAdiantamento()
   {
      $query = new Query;
      $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'A.data', 'valor_recebido' => 'SUM(A.valor)', 'valor_despesa' => 'SUM(D.valor)', 'total_pago' => 'SUM(D.valor_pago)'])
         ->from('fin_recebimento A')
         ->leftJoin('fin_despesa D', 'A.id = D.fin_recebimento_id')
         ->where('A.fin_recebimento_tipo_id =2')
         ->groupBy('A.id')
         ->orderBy('A.numero')
         ->having('valor_recebido > total_pago');
      $total_saldo = 0;
      foreach ($query->all() as $key => $value) {
         $total_saldo = $total_saldo + ($value['valor_recebido'] - $value['total_pago']);
      }
      return $total_saldo;
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorAFavorNotaCredito()
   {

      $query = new Query;
      $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'mercadoria' => 'A.descricao', 'valor_recebido' => 'A.valor', 'valor_despesa' => 'SUM(B.valor)', 'total_pago' => 'SUM(valor_pago)'])
         ->from('fin_aviso_credito A')
         ->leftJoin('fin_despesa B', 'A.id = B.fin_aviso_credito_id')
         ->where('A.status=1')
         ->groupBy('A.id')
         ->orderBy('A.numero')
         ->having('valor_despesa > total_pago')
         ->andHaving('valor_recebido > 0');
      $total_saldo = 0;
      foreach ($query->all() as $key => $value) {
         $total_saldo = $total_saldo + ($value['valor_recebido'] - $value['total_pago']);
      }
      return $total_saldo;
   }



   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function valorAFavorAvisoCredito()
   {

      $query = new Query;
      $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero", "CONCAT(B.numero, '/', B.bas_ano_id) AS numero_pr", 'A.valor', 'total_pago' => 'SUM(C.valor_pago)', 'personName' => 'D.nome'])
         ->from('fin_nota_credito A')
         ->leftJoin('dsp_processo B', 'B.id = A.dsp_processo_id')
         ->leftJoin('fin_despesa C', 'A.id = C.fin_nota_credito_id')
         ->leftJoin('dsp_person D', 'D.id = A.dsp_person_id')
         ->where('A.status=1')
         ->andWhere(['>','C.saldo',0])
         ->andWhere(['C.status'=>1])
             ->orderBy(['A.bas_ano_id'=>SORT_DESC,'A.numero'=>SORT_DESC])
             ->groupBy(['A.bas_ano_id','A.numero']);
      $total_saldo = 0;
      foreach ($query->all() as $key => $value) {
         $saldo = ($value['valor'] - $value['total_pago']);
         $total_saldo = $total_saldo + $saldo;
      }
      return $total_saldo;
   }






   /**
    * calcula valor a dever ao cliente baseado em processo
    * @return scalar
    */
   public function valorADeverProcesso()
   {

      $query1 = (new \yii\db\Query())
         ->select([
            'A.id', 'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)", 'nord' => 'C.id', 'mercadoria' => 'A.descricao', 'dsp_person_id' => 'A.nome_fatura', 'person' => 'B.nome', 'A.data', 'total_pago' => 'CASE WHEN SUM(D.valor_pago) > 0 THEN SUM(D.valor_pago) ELSE 0 END'
         ])
         ->from('dsp_processo A')
         ->innerJoin('fin_despesa D', 'D.status = 1 AND D.of_acount=0 AND D.dsp_processo_id = A.id')
         ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
         ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
         ->where('A.status!=11')
         ->groupBy('A.id')
         ->having('total_pago >0');

      // calcular valor despesas
      $query2 = (new \yii\db\Query())
         ->select([
            'Z.id', 'Z.numero', 'Z.nord', 'Z.mercadoria', 'Z.person', 'Z.data', 'valor_despesa' => 'SUM(A.valor)', 'Z.total_pago'
         ])
         ->from(['Z' => $query1])
         ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = Z.id')
         ->groupBy('Z.id');


      // valor recebido
      $query3 = (new \yii\db\Query())
         ->select([
            'W.id', 'W.numero', 'W.nord', 'W.mercadoria', 'W.person', 'W.data', 'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) ELSE 0 END', 'W.valor_despesa', 'W.total_pago', 'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
         ])
         ->from(['W' => $query2])
         ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
         ->leftJoin('fin_receita B', 'B.dsp_fataura_provisoria_id = A.id')
         ->groupBy('W.id');

      $query4 = (new \yii\db\Query())
         ->select([
            'WW.id', 'WW.numero', 'WW.nord', 'WW.mercadoria', 'WW.person', 'WW.data', 'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) + WW.valor_recebido ELSE WW.valor_recebido END', 'WW.valor_despesa', 'WW.total_pago', 'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
         ])
         ->from(['WW' => $query3])
         ->leftJoin('fin_fatura_definitiva A', 'A.dsp_processo_id = WW.id AND A.status = 1 AND A.fin_fatura_definitiva_serie =2')
         ->leftJoin('fin_receita B', 'B.fin_fataura_definitiva_id = A.id')
         ->groupBy('WW.id');

      $query = (new \yii\db\Query())
         ->select([
            'P.id', 'P.numero', 'P.nord', 'P.mercadoria', 'P.person', 'P.data', 'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+P.valor_recebido ELSE P.valor_recebido END', 'P.valor_despesa', 'P.total_pago', 'P.fatura_provisorias'
         ])
         ->from(['P' => $query4])
         ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = P.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
         ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
         ->groupBy('P.id')
         ->having('valor_recebido < total_pago')
         ->orderBy('P.numero');
      $total_saldo = 0;
      foreach ($query->all() as $key => $value) {
         $saldo = ($value['valor_recebido'] < $value['valor_despesa']) ? ($value['total_pago'] - $value['valor_recebido']) : ($value['total_pago'] - $value['valor_despesa']);
         $total_saldo = $total_saldo + $saldo;
      }

      return $total_saldo;
   }
}
