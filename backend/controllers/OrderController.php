<?php

namespace abcms\shop\backend\controllers;

use Yii;
use abcms\shop\models\Order;
use abcms\shop\models\OrderSearch;
use abcms\library\base\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use abcms\shop\models\CartProduct;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends AdminController
{
    


    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => CartProduct::find()->andWhere(['cartId'=>$model->cartId]),
        ]);
        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->status === Order::STATUS_PENDING_PAYMENT){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $post = Yii::$app->request->post('Order');
        if (isset($post['status'])) {
            $model->status = $post['status'];
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
