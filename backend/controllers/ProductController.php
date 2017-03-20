<?php

namespace abcms\shop\backend\controllers;

use Yii;
use abcms\shop\models\Product;
use abcms\shop\models\ProductSearch;
use abcms\library\base\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use abcms\gallery\module\models\GalleryImage;
use abcms\shop\models\ProductVariation;
use abcms\shop\models\ProductVariationAttribute;
use yii\base\Model;
use yii\helpers\Url;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends AdminController
{
    

    /**
     * Activate/Deactivate an existing model.
     * If action is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $model->activate()->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $variationId = null)
    {
        $model = $this->findModel($id);
        $imageDataProvider = new ActiveDataProvider([
            'query' => GalleryImage::find()->andWhere(['albumId'=>$model->albumId]),
        ]);
        
        // variatios related code
        $variationDataProvider = new ActiveDataProvider([
            'query' => ProductVariation::find()->andWhere(['productId'=>$model->id]),
        ]);
        $variation = null;
        if($variationId) { // Load existing
            $variation = ProductVariation::findOne($variationId);
        }
        if(!$variation){
            $variation = new ProductVariation();
            $variation->loadDefaultValues();
        }
        
        $attributes = [];
        $postCount = count(Yii::$app->request->post('ProductVariationAttribute', []));
        if($postCount){
            for($i = 0; $i < $postCount; $i++) {
                $attributes[] = new ProductVariationAttribute();
            }
        }
        else{
            $attributes = $variation->productVariationAttributes 
                    ? $variation->productVariationAttributes
                    : [new ProductVariationAttribute()];   
        }
        
        $variationFormFocused = false;
        
        if($variation->load(Yii::$app->request->post()) && Model::loadMultiple($attributes, Yii::$app->request->post())) { // Load, validate and save form
            $variation->productId = $model->id;
            $valid = $variation->validate();
            $valid = Model::validateMultiple($attributes) && $valid;
            if($valid){
                if($variation->save(false)){
                    ProductVariationAttribute::deleteAll(['variationId'=>$variation->id]);
                    foreach($attributes as $attribute){
                        $attribute->variationId = $variation->id;
                        $attribute->save(false);
                    }
                    Yii::$app->session->setFlash('message', 'Data saved successfully.');
                    return $this->redirect(Url::current(['variationId' => null]));
                }
            }
            else{
                $variationFormFocused = true;
            }
        } 
        if($variationId){
            $variationFormFocused = true;
        }
        
        return $this->render('view', [
            'model' => $model,
            'imageDataProvider' => $imageDataProvider,
            'variationDataProvider' => $variationDataProvider,
            'variation' => $variation,
            'attributes' => $attributes,
            'variationFormFocused' => $variationFormFocused,
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
        if(isset($this->createScenario)) {
            $model->scenario = $this->createScenario;
        }
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->saveCustomFields();
            $model->saveImages();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->saveCustomFields();
            $model->saveImages();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * Deletes an existing ProductVariation model.
     * If deletion is successful, the browser will be redirected to the product detail page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteVariation($id)
    {
        $model = $this->findVariationModel($id);
        $productId = $model->productId;
        $model->delete();
        Yii::$app->session->setFlash('message', 'Variation deleted successfully.');
        return $this->redirect(['view', 'id'=>$productId]);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Finds the ProductVariation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductVariation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findVariationModel($id)
    {
        if (($model = ProductVariation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
