<?php

namespace abcms\shop\models;

use Yii;
use abcms\library\behaviors\TimeBehavior;

/**
 * This is the model class for table "shop_cart".
 *
 * @property integer $id
 * @property string $hash
 * @property integer $userId
 * @property string $createdTime
 * @property string $updatedTime
 * @property integer $closed
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['createdTime', 'updatedTime'],
                    self::EVENT_BEFORE_UPDATE => ['updatedTime'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
            'userId' => 'User',
            'createdTime' => 'Created Time',
            'updatedTime' => 'Updated Time',
            'closed' => 'Closed',
        ];
    }
    
    /**
     * Find cart by hash value.
     * @param string $hash
     * @return Cart|null
     */
    public static function findCartByHash($hash){
        if(!$hash){
            return null;
        }
        $model = Cart::findOne(['hash'=>$hash, 'closed'=>0]);
        return $model;
    }
    
    /**
     * Find latest cart by user ID.
     * @param int $userId
     * @return Cart|null
     */
    public static function findCartByUserId($userId){
        if(!$userId){
            return null;
        }
        $model = Cart::find()->andWhere(['userId'=>$userId, 'closed'=>0])->orderBy(['updatedTime'=>SORT_DESC])->one();
        return $model;
    }
    
    /**
     * Create new cart
     * @return \self
     */
    public static function createCart(){
        $model = new self();
        $model->hash = Yii::$app->security->generateRandomString();
        $model->userId = Yii::$app->user->id;
        $model->save(false);
        return $model;
    }
    
    /**
     * Return user cart if already available or create a new one.
     * @return Cart
     */
    public static function getCurrentCart(){
        $cookies = Yii::$app->request->cookies;
        $isGuest = Yii::$app->user->isGuest;
        $cartHash = $cookies->getValue('cart');
        $cart = null;
        if($cartHash){ // If saved in cookies
            $cart = self::findCartByHash($cartHash);
        }
        if(!$cart && !$isGuest){ // if user is signed in
            $cart = self::findCartByUserId(Yii::$app->user->id);
        }
        if(!$cart){ // Create new one
            $cart = self::createCart();
        }
        if(!$isGuest && !$cart->userId){ // Update userId if user is signed in
            $cart->userId = Yii::$app->user->id;
            $cart->save(false);
        }
        return $cart;
    }
    
    /**
     * Add product to this cart
     * @param int $productId
     * @param int|null $variationId
     * @return boolean
     */
    public function addProduct($productId, $variationId = null){
        $model = new CartProduct();
        $model->cartId = $this->id;
        $model->productId = $productId;
        $model->variationId = $variationId;
        return $model->save(false);
    }
}
