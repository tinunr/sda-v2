<?php

namespace app\modules\efatura\services;

use app\modules\efatura\config\Config;
use Yii;

class MiddlewareCoreService
{ 

     /**
     * Devolve a data/hora atual da Plataforma Eletrónica.
     * */
    public static function getDataHoraPE()
    {
        $request = RestClient::api()->request()->get('/v1/data/pe-date-time');
        if ($request->info->http_code == 200) {
            $response = $request->decode_response();
            if ($response->succeeded) {
                return $response->payload;
            }
        }
        return false;
    }

    public static function taxpayerSearch($nif)
    {

        $api = new \RestClient([
            'base_url' => Config::API_BASE_URL,  
            'headers' => [
                'accept'=> 'application/json',  
                'cv-ef-mw-core-transmitter-key'=> Config::TRANSMITTER_KEY, 
                'cv-ef-repository-code' => Config::REPOSITORIO, 
                'cv-ef-iam-client-id'=> Config::PE_CLIENTE['oauthClientID'],
                'cv-ef-iam-client-secret'=> Config::PE_CLIENTE['oauthClientSecret']
            ], 
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false 
            ]
        ]);
        $result = $api->get("/v1/taxpayer/search", ['TaxId' => $nif]); 
        // print_r($result->decode_response());die();
        if($result->info->http_code == 200)
            return $result->decode_response();

        return false;

    }
     

    

    /**
     * Local DEF Enttity
     * Constituído por exatamente 45 carateres no seguinte formato:
     * @param int $repository codigo  de Repositório
     * @return  
     **/
    public static function getLocalDefEntity($document_type, $document_id)
    {

        switch ($document_type) {
            case $document_type == Config::TIPO_DOCUMENTO['FTE']:
                return FaturaEletronica::findOne($document_id);
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Local DEF Enttity
     * Constituído por exatamente 45 carateres no seguinte formato:
     * @param int $repository codigo  de Repositório
     * @return  
     **/
    public static function creatXML($document_type, $document_id)
    {

        switch ($document_type) {
            case $document_type == Config::TIPO_DOCUMENTO['FTE']:
                return FaturaEletronica::findOne($document_id);
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * 
     */
    public static function senToEFatura($document_type, $document_id)
    {
        $iud = self::getIUD($codigo_tipo_docuemtno, $document_id);



    } 

     /**
     * Identificador Único de DFE em Cabo Verde.
     * Constituído por exatamente 45 carateres no seguinte formato:
     * @param int $codigo_tipo_docuemtno codigo  de Repositório
     * @return string 'País + Repositório + Ano + Mês + Dia +NIF + LED + Tipo Doc. + Nº Doc + Código Aleatório  + DV'
     **/
    public static function getId($codigo_tipo_docuemtno, $document_id)
    {
        $model = self::getLocalDefEntity($codigo_tipo_docuemtno, $document_id);
        if (!$model) {
            return false;
        }
        return self::baseUID(). $codigo_tipo_docuemtno .   str_pad($model->getNumero(), 9, '0', STR_PAD_LEFT) . str_pad(mt_rand(), 9, '0', STR_PAD_LEFT) . ' DV';
    }

    /**
     * Local DEF Enttity
     * Constituído por exatamente 45 carateres no seguinte formato:
     * @param int $repository codigo  de Repositório
     * @return  
     **/
    public static function getDefEntity($document_id)
    {
        return FaturaEletronica::findOne($document_id); 
    }
 


    public static function assinarXML($xmlFile)
    {
        //TODO ! assinar xlm
        // Load the XML to be signed
        $doc = new \DOMDocument();
        $doc->load(Yii::getAlias('@efatura/fatura-eletronica.xml'));

        // Create a new Security object 
        $objDSig = new XMLSecurityDSig();
        // Use the c14n exclusive canonicalization
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        // Sign using SHA-256
        $objDSig->addReference(
            $doc, 
            XMLSecurityDSig::SHA256, 
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature')
        );

        // Create a new (private) Security key
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type'=>'private'));
        /*
        If key has a passphrase, set it using
        $objKey->passphrase = '<passphrase>';
        */
        // Load the private key
        $objKey->loadKey(Yii::getAlias('@app/modules/fin/e-fatura/certs/key.pem'), TRUE);

        // Sign the XML file
        $objDSig->sign($objKey);

        // Add the associated public key to the signature
        $objDSig->add509Cert(file_get_contents(Yii::getAlias('@app/modules/fin/e-fatura/certs/CV-FE-259077275-SSL.crt'), false));

        // Append the signature to the XML
        $objDSig->appendSignature($doc->documentElement);
        // Save the signed XML
        $doc->save(Yii::getAlias('@efatura/fatura-eletronicaSigned.xml'));
        return $xmlFile;



    }

    
}
