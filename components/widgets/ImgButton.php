<?php

namespace app\components\widgets;


use yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;

/**
*
*/
class ImgButton extends Component
{

   /**
     * Lists all User models.
     * @return mixed
     */
    public function ImgCaixaStaus($id)
    { 
        if ($id ==1) {
            $img =  Html::img(Url::to('@web/img/24/open.png'));                      
           }elseif($id ==2){
            $img =  Html::img(Url::to('@web/img/24/lock.png'));              
           }
        
        return $img;
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function Status($id)
    { 
        if ($id ==0) {
            $img =  Html::img(Url::to('@web/img/24/status-busy.png'));                      
           }else{
            $img =  Html::img(Url::to('@web/img/24/status.png'));              
           }
        
        return $img;
    }

    /**
   * Lists all User models.
   * @return mixed
   */
  public function statusDesp($id)
  {
    if ($id == 0) {
      $img =  Html::img(Url::to('@web/img/24/status.png'));
    } else {
      $img =  Html::img(Url::to('@web/img/24/status-busy.png'));
    }

    return $img;
  }

    /**
   * Lists all User models.
   * @return mixed
   */
  public function statusSend($status, $send)
  {
    if ($status == 0) {
      $img =  Html::img(Url::to('@web/img/24/status-busy.png'));
    } else {
      if ($send == 0) {
        $img =  Html::img(Url::to('@web/img/24/status-offline.png'));
      } elseif ($send == 1) {
        $img =  Html::img(Url::to('@web/img/24/status-away.png'));
      } else {
        $img =  Html::img(Url::to('@web/img/24/status.png'));
      }
    }

    return $img;
  }

  /**
   * Lists all User models.
   * @return mixed
   */
  public function statusOrdemPagamento($status, $send)
  {
    if ($status == 0) {
      $img =  Html::img(Url::to('@web/img/24/status-busy.png'));
    } else {
      if ($send == 0) {
        $img =  Html::img(Url::to('@web/img/24/status-offline.png'));
      } elseif ($send == 1) {
        $img =  Html::img(Url::to('@web/img/24/status-away.png'));
      } else {
        $img =  Html::img(Url::to('@web/img/24/status.png'));
      }
    }

    return $img;
  }


    /**
     * Lists all User models.
     * @return mixed
     */
    public function ProcessStatus($id)
    { 
        if ($id ==1) {
            $img =  Html::img(Url::to('@web/img/24/por_atribuir.png'));                      
        }elseif($id ==2){
          $img =  Html::img(Url::to('@web/img/24/execute.png'));              
        }elseif($id ==3){
          $img =  Html::img(Url::to('@web/img/24/pending.png'));                      
        }elseif($id ==4){
          $img =  Html::img(Url::to('@web/img/24/registrado.png'));                      
        }elseif($id ==5){
          $img =  Html::img(Url::to('@web/img/24/liquidado.png')); 
        }elseif($id ==6){
          $img =  Html::img(Url::to('@web/img/24/pago.png'));              
        }elseif($id ==7){
          $img =  Html::img(Url::to('@web/img/24/received.png'));                      
        }elseif($id ==8){
          $img =  Html::img(Url::to('@web/img/24/receitado.png'));                      
        }elseif($id ==9){
          $img =  Html::img(Url::to('@web/img/24/done.png')); 
        }elseif($id ==10){
          $img =  Html::img(Url::to('@web/img/24/done_parceal.png')); 
        }elseif($id ==11){
          $img =  Html::img(Url::to('@web/img/24/redBall.png')); 
        }else{
          $img =  Html::img(Url::to('@web/img/24/empty.png')); 
        }
        
        
        return $img;
    }


    /**
     * Lists all User models.
     * @return mixed
     */
    public function ImgCaixaOpercacao($id)
    { 
        if ($id ==1) {
            $img =  Html::img(Url::to('@web/img/24/received.png'));                      
           }elseif($id ==2){
            $img =  Html::img(Url::to('@web/img/24/payment.png'));              
           }elseif($id ==3){
            $img =  Html::img(Url::to('@web/img/24/transfer.png'));                      
           }elseif($id ==4){
            $img =  Html::img(Url::to('@web/img/24/open.png'));                      
           }elseif($id ==5){
            $img =  Html::img(Url::to('@web/img/24/lock.png')); 
          }
        
        return $img;
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function ImgDocumentoPagamento($id)
    { 
        if ($id ==1) {
            $img =  Html::img(Url::to('@web/img/24/dinheiro.png'));                      
           }elseif($id ==2){
            $img =  Html::img(Url::to('@web/img/24/cheque.png'));              
           }elseif($id ==3){
            $img =  Html::img(Url::to('@web/img/24/deposito.png'));
          }else{
            $img =  Html::img(Url::to('@web/img/24/empty.png'));

          }
        
        return $img;
    }



    /**
     * Lists all User models.
     * @return mixed
     */
    public function Img($name)
    {      
        return Html::img(Url::to('@web/img/24/'.$name.'.png'));
    }

  
    
    
}
?>
