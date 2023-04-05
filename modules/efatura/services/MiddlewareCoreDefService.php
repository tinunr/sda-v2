<?php

namespace app\modules\efatura\services;

use Yii;
use app\modules\efatura\config\Config; 
use yii\web\NotFoundHttpException;
use app\modules\efatura\repositories\FaturaEletronicaRepository;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions; 
/**
 * 
 */
class MiddlewareCoreDefService
{
   
    
    /**
     * DFE Recurso para interagir com DFEs.
     * Lista de DFEs limitada em 10000 DFEs.
     */
    public static function listDEF()
    {
        $response = Config::MiddlewareApi()->get('/v1/def', [],  [
            'cv-ef-repository-code' => 'cv-ef-cli-sgiada-259077275',
        ]);
        // $response->set_option('cv-ef-repository-code', "cv-ef-cli-sgiada-259077275");
        print_r($response);
        die();
        if ($response->info->http_code == 200) {
            $response = $response->decode_response();
            print_r($response);
            die();
            if ($response->succeeded) {
                return $response->payload;
            }
        }
        return false;
    }


    /**
     * DFE Recurso para interagir com DFEs.
     * Lista de DFEs limitada em 10000 DFEs.
     */
    public static function listIssueReasonCode()
    {
        $response = Config::middlewareApi()->get('/v1/dfe/issue-reason-code');
        // $response->set_option('cv-ef-repository-code', "cv-ef-cli-sgiada-259077275");
       
        if ($response->info->http_code == 200) {
            $response = $response->decode_response(); 
            if ($response->succeeded) {
                return $response->payload;
            }
        }
        return false;
    }


    /**
     * DFE Recurso para interagir com DFEs.
     * Lista de DFEs limitada em 10000 DFEs.
     */
    public static function sendDEF($document_id, $def_type_id, $contingen = false)
    { 
        //  $data = self:: listIssueReasonCode();
        //  print_r($data);die();
        switch ($def_type_id) {
            case Config::DEF_TYPE['FTE']['id']:
                $iud = FaturaEletronicaRepository::createXML($document_id, $contingen);
                if (empty($iud)||!$iud || $iud ==null) {
                    throw new NotFoundHttpException('Ocoreiu um erro ao gerar o XML.');
                } 

try{
                 
               $httpClient = new Client(['verify' => false ]);
                $response = $httpClient->post(Config::API_BASE_URL.'/v1/dfe',
                    [
                        RequestOptions::HEADERS => [
                            'Accept' =>  'application/json',
                            'cv-ef-mw-core-transmitter-key'=> Config::TRANSMITTER_KEY,
                            'cv-ef-repository-code' => Config::REPOSITORIO,
                            // 'cv-ef-signature-type'=>'cv-ef-signature-type',
                            'cv-ef-iam-client-id'=> Config::PE_CLIENTE['oauthClientID'],
                            'cv-ef-iam-client-secret'=> Config::PE_CLIENTE['oauthClientSecret']
                        ],
                        RequestOptions::MULTIPART => [
                            [
                                'name' => 'file',
                                'contents' => fopen(Yii::getAlias('@efatura/'.$iud.'.xml'), 'r'), 
                                'headers'  => [ 'Content-Type' => 'application/octet-stream']
                            ]
                        ],
                    ]
                ); 
				
               unlink(Yii::getAlias('@efatura/'.$iud.'.xml'));  
                if($response->getStatusCode() == 200){ 
				
                    $responsesData = json_decode($response->getBody()->getContents());    
					//print_r($responsesData);die();
                    if(!empty($responsesData))  {
                    if(empty($responsesData->responses )){ 
					Yii::$app->getSession()->setFlash('warning','Falha na midware nenhum resposta'); 
                    return true;
					}
                    if($responsesData->responses[0]->succeeded){  
                        FaturaEletronicaRepository::setIUD($document_id,$iud);
                        Yii::$app->getSession()->setFlash('success', 'Fatura enviado com suvcesso.'); 
                       return true;

                    } 
					 Yii::$app->getSession()->setFlash('warning',$responsesData->responses[0]->messages[0]->description); 
                    return true;

                }
                      Yii::$app->getSession()->setFlash('warning','Ocoreu um erro ao enviara fatura');
                    }
                    
                
                 
                    return true;
					} catch (Exception $ex) { 
					 return false;
				}
                break;
            
            default:
                throw new NotFoundHttpException('Este tipo de documento ainda não esta definido para submissão.');
                break;
        }
        
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
  
}
