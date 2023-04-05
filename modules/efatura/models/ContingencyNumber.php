<?php

namespace app\modules\efatura\models;

use Yii;

/**
 * This is the model class for table "tipologias".
 *
 * @property int $id
 * @property string $descricao
 */
class ContingencyNumber extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_Contingency_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ano','number'], 'required'],
            [['ano','number'], 'integer' ],
        ];
    }

     

    public static  function getNumber()
    {
        $ano = date('Y');
        if(($n = self::find()->where(['ano' =>$ano])->one()) == null ) {
            $model = new ContingencyNumber();
            $model->ano =$ano;
            $model->number = 1;
            $model->save();
           return $ano .'/1';
        }
        $n->number +=1; 
        $n->save(); 
        
        return $ano .'/'. $n->number;
    }
}
