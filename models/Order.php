<?php

namespace abcms\shop\models;

use Yii;
use abcms\library\behaviors\IpAddressBehavior;
use abcms\library\behaviors\TimeBehavior;

/**
 * This is the model class for table "shop_order".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $cartId
 * @property string $total
 * @property string $note
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $phone
 * @property integer $country
 * @property string $city
 * @property string $address
 * @property integer $status
 * @property string $createdTime
 * @property string $updatedTime
 * @property string $ipAddress
 */
class Order extends \yii\db\ActiveRecord
{

    const STATUS_PENDING_PAYMENT = 1;
    const STATUS_PAID = 2;
    const STATUS_IN_PROCESS = 3;
    const STATUS_SHIPPED = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstName', 'lastName', 'email', 'phone', 'country', 'city', 'address'], 'required'],
            [['country'], 'integer'],
            [['note', 'firstName', 'lastName', 'email', 'phone', 'city', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => IpAddressBehavior::className(),
            ],
            [
                'class' => TimeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['createdTime', 'updatedTime'],
                    self::EVENT_BEFORE_UPDATE => ['updatedTime'],
                ],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User',
            'cartId' => 'Cart ID',
            'total' => 'Total',
            'note' => 'Note',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'country' => 'Country',
            'city' => 'City',
            'address' => 'Address',
            'status' => 'Status',
            'createdTime' => 'Created Time',
            'updatedTime' => 'Updated Time',
            'ipAddress' => 'Ip Address',
        ];
    }

    /**
     * Returns User model
     * @return User
     */
    public function getUser()
    {
        if($this->userId) {
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
    public function getUserName()
    {
        $user = $this->user;
        if($user) {
            return $user->getFullName();
        }
        return null;
    }
    
    public static function getStatusList(){
        $array = [
            self::STATUS_PENDING_PAYMENT => 'Pending Payment',
            self::STATUS_PAID => 'Paid',
            self::STATUS_IN_PROCESS => 'In Process',
            self::STATUS_SHIPPED => 'Shipped',
        ];
        return $array;
    }

    /**
     * Returns status name
     * @return string
     */
    public function getStatusName()
    {
        $list = self::getStatusList();
        return isset($list[$this->status]) ? $list[$this->status] : null;
    }

    /**
     * Creates new order.
     * @param Cart $cart
     * @param yii\base\Model $address
     * @param int $userId
     * @param strig $note
     * @return boolean
     */
    public static function createOrder($cart, $address, $userId, $note)
    {
        $order = new Order();
        $order->setAttributes($address->attributes);
        $order->userId = $userId;
        $order->cartId = $cart->id;
        $order->total = $cart->getTotal();
        $order->note = $note;
        $order->status = Order::STATUS_PENDING_PAYMENT;
        if($order->save(false)) {
            $cart->close();
            return true;
        }
        return false;
    }

}
