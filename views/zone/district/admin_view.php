<?php
/**
 * Core Zone Districts (core-zone-district)
 * @var $this app\components\View
 * @var $this ommu\core\controllers\zone\DistrictController
 * @var $model ommu\core\models\CoreZoneDistrict
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2017 OMMU (www.ommu.co)
 * @created date 15 September 2017, 10:26 WIB
 * @modified date 30 January 2019, 17:14 WIB
 * @link https://github.com/ommu/mod-core
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Districts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->district_name;

$this->params['menu']['content'] = [
	['label' => Yii::t('app', 'Detail'), 'url' => Url::to(['view', 'id'=>$model->district_id]), 'icon' => 'eye', 'htmlOptions' => ['class'=>'btn btn-info btn-sm']],
	['label' => Yii::t('app', 'Update'), 'url' => Url::to(['update', 'id'=>$model->district_id]), 'icon' => 'pencil', 'htmlOptions' => ['class'=>'btn btn-primary btn-sm']],
	['label' => Yii::t('app', 'Delete'), 'url' => Url::to(['delete', 'id'=>$model->district_id]), 'htmlOptions' => ['data-confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'), 'data-method'=>'post', 'class'=>'btn btn-danger btn-sm'], 'icon' => 'trash'],
];
?>

<div class="core-zone-district-view">

<?php echo DetailView::widget([
	'model' => $model,
	'options' => [
		'class'=>'table table-striped detail-view',
	],
	'attributes' => [
		'district_id',
		[
			'attribute' => 'publish',
			'value' => $this->quickAction(Url::to(['publish', 'id'=>$model->primaryKey]), $model->publish),
			'format' => 'raw',
		],
		'district_name',
		[
			'attribute' => 'cityName',
			'value' => function ($model) {
				$cityName = isset($model->city) ? $model->city->city_name : '-';
				if($cityName != '-')
					return Html::a($cityName, ['zone/city/view', 'id'=>$model->city_id], ['title'=>$cityName]);
				return $cityName;
			},
			'format' => 'html',
		],
		[
			'attribute' => 'provinceName',
			'value' => function ($model) {
				$provinceName = isset($model->city->province) ? $model->city->province->province_name : '-';
				if($provinceName != '-')
					return Html::a($provinceName, ['zone/province/view', 'id'=>$model->city->province_id], ['title'=>$provinceName]);
				return $provinceName;
			},
			'format' => 'html',
		],
		[
			'attribute' => 'countryName',
			'value' => function ($model) {
				$countryName = isset($model->city->province->country) ? $model->city->province->country->country_name : '-';
				if($countryName != '-')
					return Html::a($countryName, ['zone/country/view', 'id'=>$model->city->province->country_id], ['title'=>$countryName]);
				return $countryName;
			},
			'format' => 'html',
		],
		'mfdonline',
		[
			'attribute' => 'checked',
			'value' => $this->filterYesNo($model->checked),
		],
		[
			'attribute' => 'creation_date',
			'value' => Yii::$app->formatter->asDatetime($model->creation_date, 'medium'),
		],
		[
			'attribute' => 'creationDisplayname',
			'value' => isset($model->creation) ? $model->creation->displayname : '-',
		],
		[
			'attribute' => 'modified_date',
			'value' => Yii::$app->formatter->asDatetime($model->modified_date, 'medium'),
		],
		[
			'attribute' => 'modifiedDisplayname',
			'value' => isset($model->modified) ? $model->modified->displayname : '-',
		],
		[
			'attribute' => 'updated_date',
			'value' => Yii::$app->formatter->asDatetime($model->updated_date, 'medium'),
		],
		'slug',
		[
			'attribute' => 'villages',
			'value' => function ($model) {
				$villages = $model->getVillages(true);
				return Html::a($villages, ['zone/village/manage', 'district'=>$model->primaryKey, 'publish'=>1], ['title'=>Yii::t('app', '{count} villages', ['count'=>$villages])]);
			},
			'format' => 'html',
		],
	],
]) ?>

</div>