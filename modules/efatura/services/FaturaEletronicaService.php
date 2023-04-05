<?php

namespace app\modules\efatura\services;
 
use Yii; 
use app\modules\efatura\services\MiddlewareCoreDefService;
use app\modules\efatura\services\MiddlewareCoreService;
use app\modules\efatura\services\LuhnAlgorithm;
use app\modules\efatura\config\Config;
use app\modules\efatura\repositories\FaturaEletronicaRepository;

class FaturaEletronicaService 
{
   
    const CODIGO_TIPO_DOCUENTO = '01';
 

    public static function send($id)
    { 
        
      if( (($iud = self::createXml($id))!=null)){
          FaturaEletronicaRepository::setIUD($id, $iud);
          return MiddlewareCoreDefService::sendDEF(); 
      } 
        return false;
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

    /**
     * Zipar o XML
     */
    public static function zipXML($xmlFile)
    { 
        $zip_file_name_with_location = Yii::getAlias('@efatura/fatura-eletronica.zip'); 
        if(file_exists($zip_file_name_with_location) ){ 
                unlink ($zip_file_name_with_location);  
        } 
        touch($zip_file_name_with_location);
        $zip = new \ZipArchive; 
       
        if ($zip->open($zip_file_name_with_location, \ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($xmlFile, 'fatura-eletronica.xml');
            $zip->close();
        }
        return true;
    }


    /**
     * Emviar O XML
     * */ public static function enviarXML($document_id)
     {
         $xmlFile = self::createXML($document_id);
         self::zipXML(Yii::getAlias('@efatura/fatura-eletronica.xml'),'fatura-eletronica.xml');
         $xml = self::assinarXML($xmlFile);
         print_r($xml);die();
        
    }
}
