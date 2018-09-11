<?php

namespace svsoft\yii\content\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PropertySearch represents the model behind the search form of `svsoft\yii\content\models\Property`.
 */
class PropertySearch extends Property
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id', 'parent_type_id', 'type_id', 'multiple'], 'integer'],
            [['name', 'slug'], 'safe'],
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
        $query = Property::find();

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
            'property_id' => $this->property_id,
            'parent_type_id' => $this->parent_type_id,
            'type_id' => $this->type_id,
            'multiple' => $this->multiple,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'label', $this->label]);

        return $dataProvider;
    }
}
