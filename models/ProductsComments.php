<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products_comments".
 *
 * @property int $id
 * @property int|null $product_id
 * @property string $name
 * @property string $email
 * @property int $rating
 * @property string $content
 * @property string|null $advantages
 * @property string|null $disadvantages
 *
 * @property Products $product
 */
class ProductsComments extends \yii\db\ActiveRecord
{

    public $uploaded_file = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'rating'], 'integer'],
            [['name', 'email', 'rating', 'content'], 'required'],
            [['content', 'advantages', 'disadvantages'], 'string', 'max' => 32000],
            [['name', 'email'], 'string', 'max' => 512],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],
            ['email', 'email'],
            ['rating', 'integer', 'min' => 1, 'max' => 5],
            ['uploaded_file', 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg', 'gif', 'txt']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'name' => 'Ник',
            'email' => 'Email',
            'rating' => 'Рейтинг',
            'content' => 'Текст отзыва',
            'advantages' => 'Преимущества',
            'disadvantages' => 'Недостатки',
            'uploaded_file' => "Фото или текст",
            //'attachment_path'
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if($this->isNewRecord) {
                $this->ip = Yii::$app->getRequest()->getUserIP();
                $this->useragent = Yii::$app->getRequest()->getUserAgent();
            }
            return true;
        }

        return false;
    }
}
