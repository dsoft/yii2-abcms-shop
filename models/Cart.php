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
     * Get CartProducts belonging to this cart.
     * @return mixed
     */
    public function getCartProducts()
    {
        return $this->hasMany(CartProduct::className(), ['cartId' => 'id']);
    }
    
    /**
     * Returns User model
     * @return User
     */
    public function getUser()
    {
        if($this->userId){
            $class = Yii::$app->user->identityClass;
            $user = $class::findIdentity($this->userId);
            return $user;
        }
        return null;
    }
    
    /**
     * Return User name
     * @return string
     */
    public function getUserName(){
        $user = $this->user;
        if($user){
            return $user->getFullName();
        }
        return null;
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
     * Add cart hash to cookie
     */
    public function addToCookies(){
        $cookies = Yii::$app->getResponse()->getCookies();
        $cookies->add(new \yii\web\Cookie([
            'name' => 'cart',
            'value' => $this->hash,
            'expire' => time() + 86400 * 15, // 15 days
        ]));
    }
    
    /**
     * Return user cart from cookie hash or user ID.
     * Updates user ID if not set.
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
        if($cart && !$isGuest && !$cart->userId){ // Update userId if user is signed in
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
    
    private $_total = null;
    
    /**
     * Get total price of all products of this cart
     * @return integer
     */
    public function getTotal(){
        if($this->_total === null){
            $total = 0;
            $cartProducts = $this->getCartProducts()->with(['product'])->all();
            foreach($cartProducts as $cartProduct){
                $total += $cartProduct->getPrice();
            }
            $this->_total = $total;
        }
        return $this->_total;
    }
    
    /**
     * Remove CartProduct from this cart
     * @param int $cartProductId
     * @return boolean
     */
    public function removeProduct($cartProductId){
        $deleted = CartProduct::deleteAll(['cartId'=>$this->id, 'id'=>$cartProductId]);
        return $deleted ? TRUE : FALSE;
    }
    
    /**
     * Close cart and all Cart Products
     * @return boolean
     */
    public function close(){
        $allSaved = true;
        foreach($this->cartProducts as $cartProduct){
            if(!$cartProduct->close()){
                $allSaved = false;
            }
        }
        if($allSaved){
            $this->closed = 1;
            $allSaved = $this->save(false);
        }
        return $allSaved;
    }
}
