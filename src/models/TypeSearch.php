<?php

namespace svsoft\yii\content\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TypeSearch represents the model behind the search form of `svsoft\yii\content\models\Type`.
 */
class TypeSearch extends Type
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'simple'], 'integer'],
            [['name', 'value_field', 'classname', 'label'], 'safe'],
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
        $query = Type::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type_id' => $this->type_id,
            'simple' => $this->simple,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'value_field', $this->value_field])
            ->andFilterWhere(['like', 'classname', $this->classname])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
