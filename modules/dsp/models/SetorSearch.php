<?php
namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\Setor;

/**
 * SetorSearch represents the model behind the search form about `backend\models\Setor`.
 */
class SetorSearch extends Setor
{
    public $globalSearch;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['globalSearch'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Setor::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

     
        $query->andFilterWhere(['or',['like', 'descricao', $this->globalSearch],
                                     ['sigla'=>$this->globalSearch ],
                                ]);

        return $dataProvider;
    }


   
}
