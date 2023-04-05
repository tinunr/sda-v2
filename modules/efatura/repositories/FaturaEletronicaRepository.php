<?php
namespace app\modules\efatura\repositories;
use Yii;
use app\modules\fin\models\FaturaEletronica;
use app\modules\dsp\models\Person;
use app\modules\efatura\config\Config; 
use app\modules\efatura\services\LuhnAlgorithm;
use app\modules\efatura\models\ContingencyNumber;

class FaturaEletronicaRepository
{
    const DEF_ID = '01';
    
    
    public  static function  fatura($id)
    {
        return FaturaEletronica::findOne($id);
    }

    public  function  getPerson($id)
    {
        $fatura = self::fatura($id);
        return Person::findOne($fatura->dsp_person_id);
    }

    public  function  itemsLine($id)
    {
        $fatura = self::fatura($id);
        return $fatura->items;

    }

    public static  function  setIUD($id, $iud)
    {
        $fatura = self::fatura($id);
        $fatura->iud = $iud;
        $fatura->save(false); 
        return $fatura;

    }

    /**
     * Serviço de despacho sem IVA - base tributavel Total gerarl - base tributas ( )
     * Serviço de despacho com IVA - base tributavel
     */
     public  function  linesItem($id)
    {
        $fatura = self::fatura($id); 
        return [
            '1002'=>[
                'id'=>1002,
                'iva'=>true,
                'descricao'=>'Serviço de despacho com IVA - base tributavels',
                'valor'=>round(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($fatura->fin_fatura_definitiva_id), 0),
            ], 
        ];

    }

    public static  function  total($id)
    {
        $fatura = self::fatura($id); 
        return [
            'valor_honorario'=> round(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($fatura->fin_fatura_definitiva_id), 0), 
            'valor_iva'=>round(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($fatura->fin_fatura_definitiva_id),0), 
            'valor_total'=>round((Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($fatura->fin_fatura_definitiva_id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($fatura->fin_fatura_definitiva_id)),0), 
        ]; 
    }

     /**
     * Identificador Único de DFE em Cabo Verde.
     * Constituído por exatamente 45 carateres no seguinte formato:
     * @param int $codigo_tipo_docuemtno codigo  de Repositório
     * @return string 'País + Repositório + Ano + Mês + Dia +NIF + LED + Tipo Doc. + Nº Doc + Código Aleatório  + DV'
     **/
    public static function getIUD(int $id, int $numeroDocumento):string
    { 
         $fatura = self::fatura($id);  
        $numero =   (Config::REPOSITORIO.substr(date('Y',strtotime($fatura->data)),-2).date('m',strtotime($fatura->data)).date('d',strtotime($fatura->data)).Config::SOFTWARE['TRANSMITTER_TAX_ID'].str_pad(Config::LED, 5, 0, STR_PAD_LEFT). str_pad(self::DEF_ID,2, 0, STR_PAD_LEFT).str_pad($numeroDocumento, 9, 0, STR_PAD_LEFT) . str_pad(mt_rand(), 10, 0, STR_PAD_LEFT));  
        $luhnNumer =  LuhnAlgorithm::create($numero);  
        return Config::PAIS_ID . $luhnNumer;
    }


    public static function removerEspacoConsecutivo($str){
       return htmlspecialchars(preg_replace('/\s\s+/', ' ', trim($str)) , ENT_NOQUOTES);
    } 

    /**
     * Local DEF Enttity
     * Constituído por exatamente 45 carateres no seguinte formato:
     * @param int $repository codigo  de Repositório
     * @return  
     **/
    public static function  createXML(int $id, bool $isContingency = false)
    {
        $formatter = Yii::$app->formatter;
        $model = FaturaEletronicaRepository::fatura($id); 
        $linesItem = FaturaEletronicaRepository::linesItem($id); 
        $iud = self::getIUD($id, $model->numero);
        $totalLines = self::total($id);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'; 
            $xml .= '<Dfe xmlns="urn:cv:efatura:xsd:v1.0" Version="1.0" Id="'.$iud.'" DocumentTypeCode="1">'; 
          /**
           *  Amostra de DFE no ambiente efetivo de produção.Os DFEs marcados com <IsSpecimen>true</IsSpecimen> serão eliminados depois de 24 horas.
           * <IsSpecimen>true</IsSpecimen>
           * */ 
                $xml .= '<Invoice>';	
                // Identificação do DFE	
                    $xml .= '<LedCode>'.Config::LED.'</LedCode>';
                    $xml .= '<Serie>'.Config::SERIE.'</Serie>';
                    $xml .= '<DocumentNumber>'.$model->numero.'</DocumentNumber>';
                    $xml .= '<IssueDate>'.$model->data.'</IssueDate>';
                    $xml .= '<IssueTime>'.$formatter->asTime($model->created_at).'</IssueTime>';
                    // Data de Vencimento de DFE (DueDate)
                    // $xml .= '<DueDate>2021-01-01</DueDate>';

                    // $xml .= '<OrderReference>';
                    //     $xml .= '<Id>12345</Id>';
                    // $xml .= '</OrderReference>';

                    $xml .= '<TaxPointDate>'.$model->data.'</TaxPointDate>';
                    
                    $xml .= '<EmitterParty>';
                        $xml .= '<TaxId CountryCode="CV">'.Config::CONPANY['NIF'].'</TaxId>';
                        $xml .= '<Name>'.Config::CONPANY['NAME'].'</Name>';
                        $xml .= '<Address CountryCode="'.Config::CONPANY['COUNTRY_CODE'].'">';
                            $xml .= '<AddressDetail>'.Config::CONPANY['ADRESS_DETAIL'].'</AddressDetail>';
                            $xml .= '<AddressCode>'.Config::CONPANY['ADRESS_CODE'].'</AddressCode>';
                        $xml .= '</Address>';
                        $xml .= '<Contacts>';
                            $xml .= '<Telephone>'.Config::CONPANY['TELEPHONE'].'</Telephone>'; 
                            $xml .= '<Email>'.Config::CONPANY['EMAIL'].'</Email>';
                            $xml .= '<Website>'.Config::CONPANY['WEBSITE'].'</Website>';
                        $xml .= '</Contacts>';
                    $xml .= '</EmitterParty>';
                    
                    $xml .= '<ReceiverParty>';
                        $xml .= '<TaxId CountryCode="CV">'.$model->person->nif.'</TaxId>';
                        $xml .= '<Name>'.self::removerEspacoConsecutivo($model->person->nome).'</Name>';
                        $xml .= '<Address CountryCode="CV">';
                            $xml .= '<AddressDetail>'.trim($model->person->endereco).'</AddressDetail>';
                            // $xml .= '<AddressCode>CV774741741037410321</AddressCode>';
                        $xml .= '</Address>';
                        $xml .= '<Contacts>';
                        if(!empty($model->person->telefone)):
                            $xml .= '<Telephone>'.trim($model->person->telefone).'</Telephone>';
                        endif;
                        if(!empty($model->person->telemovel)):
                            $xml .= '<Mobilephone>'.trim($model->person->telemovel).'</Mobilephone>';
                        endif;
                        if(!empty($model->person->fax)):
                            $xml .= '<Telefax>'.trim($model->person->fax).'</Telefax>';
                        endif; 
                         if(!empty($model->person->email)):
                            $xml .= '<Email>'.trim($model->person->email).'</Email>';
                        endif; 
                        //  if(!empty($model->person->website)):
                            $xml .= '<Website>https://wesite-cliente.cv</Website>';
                        // endif;   
                        $xml .= '</Contacts>';
                    $xml .= '</ReceiverParty>';
                    
                    $xml .= '<Lines>';
                        // print_r($linesItem);die();
                    foreach($linesItem as $key=>$line):
                        $xml .= '<Line LineTypeCode="N">';
                            $xml .= '<Id>'.$line['id'].'</Id>';
                            $xml .= '<Quantity UnitCode="EA">1</Quantity>';
                            $xml .= '<Price>'.$line['valor'].'</Price>';
                            $xml .= '<PriceExtension>'.$line['valor'].'</PriceExtension>';
                            // $xml .= '<Discount>0</Discount>';
                            $xml .= '<NetTotal>'.$line['valor'].'</NetTotal>';
                            if($line['iva']):
                            $xml .= '<Tax TaxTypeCode="IVA">';
                                $xml .= '<TaxPercentage>15</TaxPercentage>';
                            $xml .= '</Tax>';
                            else:
                                $xml .= '<Tax TaxTypeCode="NA">';
                                $xml .= '<TaxExemptionReasonCode>1</TaxExemptionReasonCode>';
                            $xml .= '</Tax>';
                            endif;
                            $xml .= '<Item>';
                                $xml .= '<Description>'.$line['descricao'].'</Description>';
                                $xml .= '<EmitterIdentification>'.$line['id'].'</EmitterIdentification>';
                                // $xml .= '<HazardousRiskIndicator>false</HazardousRiskIndicator>';
                                // $xml .= '<ExtraProperties>';
                                //     $xml .= '<Property Name="Cor">Preto</Property>';
                                //     $xml .= '<Property Name="Peso">12</Property>';
                                // $xml .= '</ExtraProperties>';
                            $xml .= '</Item>'; 
                            $xml .= '</Line>'; 
                    endforeach;
                    $xml .= '</Lines>'; 
                    $xml .= '<Totals>';
                        $xml .= '<PriceExtensionTotalAmount>'.$totalLines['valor_honorario'].'</PriceExtensionTotalAmount>';
                        $xml .= '<ChargeTotalAmount>0</ChargeTotalAmount>';
                        $xml .= '<DiscountTotalAmount>0</DiscountTotalAmount>';
                        $xml .= '<NetTotalAmount>'.$totalLines['valor_honorario'].'</NetTotalAmount>';
                        // $xml .= '<Discount>10</Discount>';
                        $xml .= '<TaxTotalAmount>'.$totalLines['valor_iva'].'</TaxTotalAmount>';
                        // $xml .= '<PayableRoundingAmount>-10</PayableRoundingAmount>';
                        // $xml .= '<PayableAmount>3270</PayableAmount>';
                        $xml .= '<WithholdingTaxTotalAmount>0</WithholdingTaxTotalAmount>';
                        $xml .= '<PayableAmount>'.$totalLines['valor_total'].'</PayableAmount>';
                    $xml .= '</Totals>';
                    
                    // $xml .= '<Payments>';
                    //     $xml .= '<PaymentDueDate>2021-01-01</PaymentDueDate>';
                    //     $xml .= '<PaymentTerms>';
                    //         $xml .= '<Note>Juros de 10% a partir da data de vencimento</Note>';
                    //     $xml .= '</PaymentTerms>';
                    //     $xml .= '<PayeeFinancialAccount>';
                    //         $xml .= '<NIB>123456789012345678901</NIB>';
                    //         $xml .= '<Name>Opção de conta de pagamento 1</Name>';
                    //     $xml .= '</PayeeFinancialAccount>';
                    //     $xml .= '<PayeeFinancialAccount>';
                    //         $xml .= '<NIB>123456789012345678902</NIB>';
                    //         $xml .= '<Name>Opção de conta de pagamento 2</Name>';
                    //     $xml .= '</PayeeFinancialAccount>';
                    // $xml .= '</Payments>';
                    
                    // $xml .= '<Delivery>';
                    //     $xml .= '<DeliveryDate>2021-01-01</DeliveryDate>';
                    //     $xml .= '<Address CountryCode="CV">';
                    //         $xml .= '<City>Praia</City>';
                    //         $xml .= '<Region>Região</Region>';
                    //         $xml .= '<Street>Rua XPTO</Street>';
                    //         $xml .= '<StreetDetail>Ao lado Bar X</StreetDetail>';
                    //         $xml .= '<BuildingNumber>12345</BuildingNumber>';
                    //         $xml .= '<PostalCode>12345</PostalCode>';
                    //         $xml .= '<AddressDetail>Ao lado da padaria</AddressDetail>';
                    //         $xml .= '<AddressCode>CV774741741037410321</AddressCode>';
                    //     $xml .= '</Address>';
                    // $xml .= '</Delivery>';
                    
                    // $xml .= '<Note>Notas/Observações opcionais</Note>';
                    
                    // $xml .= '<ExtraFields>';
                    //     $xml .= '<QualquerCampo QualquerAtributo="Qualquer Valor"> Campo com quarquer estrutura </QualquerCampo>';
                    //     $xml .= '<Segredo>Valor encriptado...</Segredo>';
                    // $xml .= '</ExtraFields>';
                $xml .= '</Invoice>';
                
                $xml .= '<Transmission>';
                    $xml .= '<IssueMode>'.($isContingency?2:1).'</IssueMode>';
                    $xml .= '<TransmitterTaxId CountryCode="CV">'.Config::SOFTWARE['TRANSMITTER_TAX_ID'].'</TransmitterTaxId>';
                    $xml .= '<Software>';
                        $xml .= '<Code>'.Config::SOFTWARE['CODE'].'</Code>';
                        $xml .= '<Name>'.Config::SOFTWARE['NAME'].'</Name>';
                        $xml .= '<Version>'.Config::SOFTWARE['VERSION'].'</Version>';
                    $xml .= '</Software>';
                    if($isContingency): 
                    $xml .= '<Contingency>';
                        $xml .= '<LedCode>'.Config::LED.'</LedCode>';
                        // $xml .= '<IUC>'.ContingencyNumber::getNumber().'</IUC>';
                        $xml .= '<IssueDate>'.date('Y-m-d').'</IssueDate>';
                        $xml .= '<IssueTime>'.date('H:i:s').'</IssueTime>';
                        $xml .= '<ReasonTypeCode>2</ReasonTypeCode>';
                    $xml .= '</Contingency>';
                    endif;
                $xml .= '</Transmission>';
                $xml .= '<RepositoryCode>'.Config::REPOSITORIO.'</RepositoryCode>';
            $xml .= '</Dfe>'; ;
        // Escreve o arquivo
        $fp = fopen(Yii::getAlias('@efatura/'.$iud.'.xml') , 'w+');
        fwrite($fp, $xml);
        fclose($fp);
        return $iud;
        
    }








}