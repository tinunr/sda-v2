<?php

namespace app\modules\fin\repositories;

use yii\db\Query;

class ValoresDoClienteRespository
{

    /**
     * Lists all User models.sss
     * @return mixed
     */
    public static function valorAFavorProcesso()
    {
        $query1 = (new \yii\db\Query())
            ->select([
                'A.id', 'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)", 'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`D`.`numero`, '/', `D`.`bas_ano_id`) SEPARATOR ',')", 'nord' => 'C.id', 'mercadoria' => 'A.descricao', 'dsp_person_id' => 'A.nome_fatura', 'person' => 'B.nome', 'valor_recebido' => 'SUM(E.valor_recebido)', 'A.data'
            ])
            ->from('dsp_processo A')
            ->leftJoin('fin_fatura_provisoria D', 'A.id=D.dsp_processo_id')
            ->innerJoin('fin_receita E', 'D.id = E.dsp_fataura_provisoria_id')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->where('A.status!=11')
            ->groupBy('A.id')
            ->orderBy('A.data');
        // print_r($query1->count());
        // die();
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
    public static function valorAFavorAdiantamento()
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
    public static function valorAFavorAvisoCredito()
    {

        $query = new Query;
        $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero", "CONCAT(B.numero, '/', B.bas_ano_id) AS numero_pr", 'A.valor', 'total_pago' => 'SUM(C.valor_pago)', 'personName' => 'D.nome'])
            ->from('fin_nota_credito A')
            ->leftJoin('dsp_processo B', 'B.id = A.dsp_processo_id')
            ->leftJoin('fin_despesa C', 'A.id = C.fin_nota_credito_id')
            ->leftJoin('dsp_person D', 'D.id = A.dsp_person_id')
            ->where('A.status=1')
            ->orderBy('A.numero')
            ->groupBy('A.numero')
            ->having('A.valor > total_pago');
        $total_saldo = 0;
        foreach ($query->all() as $key => $value) {
            $saldo = ($value['valor'] - $value['total_pago']);
            $total_saldo = $total_saldo + $saldo;
        }
        return $total_saldo;
    }

    /**
     * Lists all User models.sss
     * @return mixed
     */
    public static function valorAFavorNotaCredito()
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
    public static function valorADeverProcesso()
    {

        $query1 = (new \yii\db\Query())
            ->select([
                'A.id', 'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)", 'nord' => 'C.id', 'mercadoria' => 'A.descricao', 'dsp_person_id' => 'A.nome_fatura', 'person' => 'B.nome', 'A.data', 'total_pago' => 'CASE WHEN SUM(D.valor_pago) > 0 THEN SUM(D.valor_pago) ELSE 0 END'
            ])
            ->from('dsp_processo A')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->leftJoin('fin_despesa D', 'D.status = 1 AND D.of_acount=0 AND D.dsp_processo_id = A.id')
            ->where('A.status!=11')
            ->groupBy('A.id')
            ->having('total_pago >0');

        $query2 = (new \yii\db\Query())
            ->select([
                'Z.id', 'Z.numero', 'Z.nord', 'Z.mercadoria', 'Z.person', 'Z.data', 'valor_despesa' => 'SUM(A.valor)', 'Z.total_pago'
            ])
            ->from(['Z' => $query1])
            ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = Z.id')
            ->groupBy('Z.id');



        $query3 = (new \yii\db\Query())
            ->select([
                'W.id', 'W.numero', 'W.nord', 'W.mercadoria', 'W.person', 'W.data', 'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) ELSE 0 END', 'W.valor_despesa', 'W.total_pago', 'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['W' => $query2])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
            ->leftJoin('fin_receita B', 'B.dsp_fataura_provisoria_id = A.id')
            ->groupBy('W.id');

        $query = (new \yii\db\Query())
            ->select([
                'P.id', 'P.numero', 'P.nord', 'P.mercadoria', 'P.person', 'P.data', 'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+P.valor_recebido ELSE P.valor_recebido END', 'P.valor_despesa', 'P.total_pago', 'P.fatura_provisorias'
            ])
            ->from(['P' => $query3])
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
