<?php

namespace app\components\helpers;

use yii;
use yii\base\Component;

use app\modules\dsp\models\Processo;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\Pagamento;
use app\modules\cnt\models\Razao;
use app\models\Ano;
use app\modules\fin\models\PagamentoOrdem;

class UploadFileHelper extends Component
{

    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFile($dsp_processo_id)
    {
        $pacth = Yii::getAlias('@processos');
        $processo = Processo::findOne($dsp_processo_id);
        $ano = Ano::findOne($processo->bas_ano_id);
        $pacthAno = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }
        $pacthProcesso = $pacthAno . '/' . $processo->numero;
        if (!is_dir($pacthProcesso)) {
            $oldmask = umask(0);
            mkdir($pacthProcesso, 0777);
            umask($oldmask);
        }
        return $pacthProcesso . '/';
    }


    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFileDespesa($dsp_despesa_id)
    {
        $pacth = Yii::getAlias('@despesas');
        $despesa = Despesa::findOne($dsp_despesa_id);
        $ano = Ano::findOne($despesa->bas_ano_id);
        $pacthAno = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }
        $pacthProcesso = $pacthAno . '/' . $despesa->id;
        if (!is_dir($pacthProcesso)) {
            $oldmask = umask(0);
            mkdir($pacthProcesso, 0777);
            umask($oldmask);
        }
        return $pacthProcesso . '/';
    }



    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFileRecebimento($fin_recebimento_id)
    {
        $pacth = Yii::getAlias('@recebimentos');
        $recebimento = Recebimento::findOne($fin_recebimento_id);
        $ano = Ano::findOne($recebimento->bas_ano_id);
        $pacthAno = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }
        $pacthProcesso = $pacthAno . '/' . $recebimento->numero;
        if (!is_dir($pacthProcesso)) {
            $oldmask = umask(0);
            mkdir($pacthProcesso, 0777);
            umask($oldmask);
        }
        return $pacthProcesso . '/';
    }


    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFilePagamento($fin_pagamento_id)
    {
        $pacth = Yii::getAlias('@pagamentos');
        $pagamento = Pagamento::findOne($fin_pagamento_id);
        $ano = Ano::findOne($pagamento->bas_ano_id);
        $pacthAno = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }
        $pacthProcesso = $pacthAno . '/' . $pagamento->numero;
        if (!is_dir($pacthProcesso)) {
            $oldmask = umask(0);
            mkdir($pacthProcesso, 0777);
            umask($oldmask);
        }
        return $pacthProcesso . '/';
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFilePagamentoOrdem($fin_pagamento_ordem_id)
    {
        $pacth = Yii::getAlias('@pagamentos_ordem');
        $pagamentoOrdem = PagamentoOrdem::findOne($fin_pagamento_ordem_id);
        $ano = Ano::findOne($pagamentoOrdem->bas_ano_id);
        $pacthAno = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }
        $pacthPagamentoOrdem = $pacthAno . '/' . $pagamentoOrdem->numero;
        if (!is_dir($pacthPagamentoOrdem)) {
            $oldmask = umask(0);
            mkdir($pacthPagamentoOrdem, 0777);
            umask($oldmask);
        }
        return $pacthPagamentoOrdem . '/';
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFileTransferencia($fin_transferencia_id)
    {
        $pacth = Yii::getAlias('@transferencias');
        $trasferencia = \app\modules\fin\models\Transferencia::findOne($fin_transferencia_id);
		$pacthAno = $pacth . '/' . date('Y', strtotime($trasferencia->data));
		if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }
        $pacthProcesso = $pacthAno. '/' . $trasferencia->numero;
        if (!is_dir($pacthProcesso)) {
            $oldmask = umask(0);
            mkdir($pacthProcesso, 0777);
            umask($oldmask);
        }
        return $pacthProcesso . '/';
    }



    /**
     * Lists all User models.
     * @return mixed
     */
    public static function setUrlFileContabilidade($cnt_razao_id)
    {
        $pacth = Yii::getAlias('@contabilidade');
        $razao = Razao::findOne($cnt_razao_id);
        $ano = Ano::findOne($razao->bas_ano_id);
        $pacthAno = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthAno)) {
            $oldmask = umask(0);
            mkdir($pacthAno, 0777);
            umask($oldmask);
        }

        $pacthDiario = $pacthAno . '/' . $razao->diario->dir_file;
        if (!is_dir($pacthDiario)) {
            $oldmask = umask(0);
            mkdir($pacthDiario, 0777);
            umask($oldmask);
        }
        $pacthMes = $pacthDiario . '/' . $razao->bas_mes_id;
        if (!is_dir($pacthMes)) {
            $oldmask = umask(0);
            mkdir($pacthMes, 0777);
            umask($oldmask);
        }
        $pacth = $pacthMes . '/' . $razao->numero;
        if (!is_dir($pacth)) {
            $oldmask = umask(0);
            mkdir($pacth, 0777);
            umask($oldmask);
        }
        return $pacth . '/';
    }




    /**
     * @return mixed
     */
    public static function scanData($dir)
    {
        $files = array();
        // Is there actually such a folder/file?
        if (file_exists($dir)) {
            foreach (scandir($dir) as $f) {
                if (!$f || $f[0] == '.') {
                    continue; // Ignore hidden files
                }
                if (is_dir($dir . '/' . $f)) {
                    // The path is a folder
                    $files[] = array(
                        "name" => $f,
                        "type" => "folder",
                        "path" => $dir . '/' . $f,
                        "last_modified" => date("d/m/Y H:i:s", filemtime($dir . '/' . $f)), // Gets last  modified date file
                        "items" => self::scanData($dir . '/' . $f), // Recursively get the contents of the folder
                    );
                } else {
                    // It is a file
                    $files[] = array(
                        "name" => $f,
                        "type" => "file",
                        "path" => $dir . '/' . $f,
                        "size" => filesize($dir . '/' . $f), // Gets the size of this file
                        "last_modified" => date("d/m/Y H:i:s", filemtime($dir . '/' . $f)), // Gets last  modified date file

                    );
                }
            }
        }
        return $files;
    }
}
