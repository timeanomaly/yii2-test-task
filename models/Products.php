<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string|null $title
 *
 * @property ProductsComments[] $productsComments
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[ProductsComments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductsComments()
    {
        return $this->hasMany(ProductsComments::className(), ['product_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function addComment(ProductsComments $comment)
    {
        $comment->product_id=$this->id;
        return $comment->save();
    }
}
