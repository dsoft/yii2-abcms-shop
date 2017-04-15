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
 * @property integer $typeId Shopping Cart or Wish Cart
 */
class Cart extends \yii\db\ActiveRecord
{
    
    const TYPE_SHOPPING_CART = 1; 
    const TYPE_WISH_CART = 2;
    
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
            'typeId' => 'Type',
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
            $user = $class::findUserById($this->userId);
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
     * Return types array that can be used in drop down lists
     * @return array
     */
    public static function getTypesList()
    {
        $array = [
            self::TYPE_SHOPPING_CART => 'Shopping Cart',
            self::TYPE_WISH_CART => 'Wish Cart',
        ];
        return $array;
    }

    /**
     * Returns type name
     * @return string
     */
    public function getType()
    {
        $list = self::getTypesList();
        return isset($list[$this->typeId]) ? $list[$this->typeId] : null;
    }
    
    /**
     * Find cart by hash value.
     * @param string $hash
     * @param integer $typeId
     * @return Cart|null
     */
    public static function findCartByHash($hash, $typeId = self::TYPE_SHOPPING_CART){
        if(!$hash){
            return null;
        }
        $model = Cart::findOne(['hash'=>$hash, 'closed'=>0, 'typeId'=>$typeId]);
        return $model;
    }
    
    /**
     * Find latest cart by user ID.
     * @param int $userId
     * @param integer $typeId
     * @return Cart|null
     */
    public static function findCartByUserId($userId, $typeId = self::TYPE_SHOPPING_CART){
        if(!$userId){
            return null;
        }
        $model = Cart::find()->andWhere(['userId'=>$userId, 'closed'=>0, 'typeId'=>$typeId])->orderBy(['updatedTime'=>SORT_DESC])->one();
        return $model;
    }
    
    /**
     * Create new cart
     * @param integer $typeId
     * @return \self
     */
    public static function createCart($typeId = self::TYPE_SHOPPING_CART){
        $model = new self();
        $model->hash = Yii::$app->security->generateRandomString();
        $model->userId = Yii::$app->user->id;
        $model->typeId = $typeId;
        $model->save(false);
        return $model;
    }
    
    /**
     * Add cart hash to cookie
     */
    public function addToCookies(){
        $cookies = Yii::$app->getResponse()->getCookies();
        $name = $this->typeId == self::TYPE_WISH_CART ? 'wish-cart' : 'cart';
        $expire = $this->typeId == self::TYPE_WISH_CART ? (time() + 86400 * 30) : (time() + 86400 * 15); // 30 or 15 days
        $cookies->add(new \yii\web\Cookie([
            'name' => $name,
            'value' => $this->hash,
            'expire' => $expire,
        ]));
    }
    
    /**
     * Return user cart from cookie hash or user ID.
     * Updates user ID if not set.
     * @param integer $typeId
     * @return Cart
     */
    public static function getCurrentCart($typeId = self::TYPE_SHOPPING_CART){
        $cookies = Yii::$app->request->cookies;
        $isGuest = Yii::$app->user->isGuest;
        $cookieName = $typeId == self::TYPE_WISH_CART ? 'wish-cart' : 'cart';
        $cartHash = $cookies->getValue($cookieName);
        $cart = null;
        if($cartHash){ // If saved in cookies
            $cart = self::findCartByHash($cartHash, $typeId);
        }
        if(!$cart && !$isGuest){ // if user is signed in
            $cart = self::findCartByUserId(Yii::$app->user->id, $typeId);
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
    
    /**
     * Check if all products chosen in this cart are available
     * @return boolean
     */
    public function areProductsAvailable()
    {
        $cartProducts = $this->cartProducts;
        foreach($cartProducts as $cartProduct){
            $product = $cartProduct->product;
            if(!$product->isAvailable($cartProduct->variationId)){
                return false;
            }
        }
        return true;
    }
}
