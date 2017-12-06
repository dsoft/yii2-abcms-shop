<?php

namespace abcms\shop\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shop_category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parentId
 * @property integer $active
 * @property integer $deleted
 * @property integer $ordering
 */
class Category extends \abcms\library\base\BackendActiveRecord
{
    
    /**
     * @var int Number of products under this category
     */
    public $productCount = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parentId', 'active', 'ordering'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => \abcms\multilanguage\behaviors\ModelBehavior::className(),
                'attributes' => [
                    'name',
                ],
            ],
            [
                'class' => \abcms\structure\behaviors\CustomFieldsBehavior::className(),
            ],
            [
                'class' => \abcms\library\behaviors\SeoBehavior::className(),
                'route' => '/shop/category',
                'titleAttribute' => 'name',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parentId' => 'Parent',
            'active' => 'Active',
            'deleted' => 'Deleted',
            'ordering' => 'Ordering',
        ];
    }

    /**
     * Get Category model that this category belongs to
     * @return mixed
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parentId']);
    }

    /**
     * Get parent category name
     * @return string|null
     */
    public function getParentName()
    {
        return $this->parent ? $this->parent->name : null;
    }
    
    /**
     * Get children belonging to this category.
     * @return mixed
     */
    public function getChildren()
    {
        return $this->hasMany(Category::className(), ['parentId' => 'id'])->andWhere([
            'active' => 1,
        ])->orderBy(['ordering'=>SORT_ASC, 'name'=>SORT_ASC]);
    }

    /**
     * Return parents list array, to be used in drop down lists.
     * @return array
     */
    public static function getParentsList($excludeId = null)
    {
        $query = self::find()->andWhere(['parentId' => null])->orderBy('name ASC');
        if($excludeId) {
            $query->andWhere(['not in', 'id', $excludeId]);
        }
        $models = $query->all();
        return ArrayHelper::map($models, 'id', 'name');
    }

    /**
     * Return categories list array, to be used in drop down lists.
     * @return array
     */
    public static function getCategoriesList()
    {
        $query = Category::find()->orderBy('name ASC');
        $models = $query->all();
        return ArrayHelper::map($models, 'id', 'name');
    }
    
    /**
     * Get categories models with children, used in frontend.
     * @return Category[]
     */
    public static function getFrontendCategories(){
        $query = self::find();
        $query->andWhere(['parentId' => null, 'active'=>1])->orderBy(['ordering'=>SORT_ASC, 'name'=>SORT_ASC]);
        $query->with(['children']);
        $models = $query->all();
        return $models;
    }
    
    /**
     * Get sub categories ids
     * @param int $categoryId
     * @return array
     */
    public static function getChildrenIds($categoryId = null){
        if($categoryId){
            $ids = Category::find()->select('id')->andWhere(['parentId' => $categoryId, 'active'=>1])->column();
        }
        else{
            $categories = self::find()->andWhere(['parentId' => null, 'active'=>1])->with(['children'])->asArray()->all();
            $ids = [];
            foreach($categories as $category){
                $ids[$category['id']] = [];
                foreach($category['children'] as $child){
                    $ids[$category['id']][] = $child['id'];
                }
            }
        }
        return $ids;
    }

}
