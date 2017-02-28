<?php

namespace abcms\shop\backend\controllers;

use Yii;
use abcms\shop\models\Brand;
use abcms\shop\models\BrandSearch;
use abcms\library\base\AdminController;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

/**
 * BrandController implements the CRUD actions for Brand model.
 */
class BrandController extends AdminController
{

    /**
     * Lists all Brand models.
     * Also handles creation and updates.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        $searchModel = new BrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if($id) { // Load existing
            $model = $this->findModel($id);
        }
        else { // Create new
            $model = new Brand();
            $model->loadDefaultValues();
        }
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('message', 'Data saved successfully.');
            return $this->redirect(Url::current(['id' => null]));
        } 
        $formFocused = false;
        if($id || $model->hasErrors()){
            $formFocused = true;
        }

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'formFocused' => $formFocused,
        ]);
    }

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
     * Deletes an existing Brand model.
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
     * Finds the Brand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Brand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model = Brand::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
