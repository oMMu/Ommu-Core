<?php
/**
 * Modules
 * 
 * @author Putra Sudaryanto <putra@ommu.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2017 OMMU (www.ommu.id)
 * @created date 24 December 2017, 20:11 WIB
 * @link https://github.com/ommu/mod-core
 *
 * This is the model class for table "ommu_core_modules".
 *
 * The followings are the available columns in table "ommu_core_modules":
 * @property integer $id
 * @property string $module_id
 * @property integer $installed
 * @property integer $enabled
 * @property string $creation_date
 * @property integer $creation_id
 * @property string $modified_date
 * @property integer $modified_id
 *
 */

namespace ommu\core\models;

use Yii;
use yii\helpers\Url;
use app\models\Users;

class Modules extends \app\components\ActiveRecord
{
	use \ommu\traits\UtilityTrait;
	
	public $gridForbiddenColumn = ['installed', 'modified_date', 'modifiedDisplayname'];

	public $creationDisplayname;
	public $modifiedDisplayname;

	const CACHE_ENABLE_MODULE_IDS = 'enabledModuleIds';

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'ommu_core_modules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			[['module_id'], 'required'],
			[['installed', 'enabled', 'creation_id', 'modified_id'], 'integer'],
			[['creation_date', 'modified_date'], 'safe'],
			[['module_id'], 'string', 'max' => 64],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'ID'),
			'module_id' => Yii::t('app', 'Module'),
			'installed' => Yii::t('app', 'Installed'),
			'enabled' => Yii::t('app', 'Enabled'),
			'creation_date' => Yii::t('app', 'Creation Date'),
			'creation_id' => Yii::t('app', 'Creation'),
			'modified_date' => Yii::t('app', 'Modified Date'),
			'modified_id' => Yii::t('app', 'Modified'),
			'creationDisplayname' => Yii::t('app', 'Creation'),
			'modifiedDisplayname' => Yii::t('app', 'Modified'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreation()
	{
		return $this->hasOne(Users::className(), ['user_id' => 'creation_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getModified()
	{
		return $this->hasOne(Users::className(), ['user_id' => 'modified_id']);
	}

	/**
	 * Set default columns to display
	 */
	public function init()
	{
        parent::init();

        if (!(Yii::$app instanceof \app\components\Application)) {
            return;
        }

        if (!$this->hasMethod('search')) {
            return;
        }

		$this->templateColumns['_no'] = [
			'header' => '#',
			'class' => 'app\components\grid\SerialColumn',
			'contentOptions' => ['class' => 'text-center'],
		];
		$this->templateColumns['module_id'] = [
			'attribute' => 'module_id',
			'value' => function($model, $key, $index, $column) {
				return $model->module_id;
			},
		];
		$this->templateColumns['creation_date'] = [
			'attribute' => 'creation_date',
			'value' => function($model, $key, $index, $column) {
				return Yii::$app->formatter->asDatetime($model->creation_date, 'medium');
			},
			'filter' => $this->filterDatepicker($this, 'creation_date'),
		];
		$this->templateColumns['creationDisplayname'] = [
			'attribute' => 'creationDisplayname',
			'value' => function($model, $key, $index, $column) {
				return isset($model->creation) ? $model->creation->displayname : '-';
				// return $model->creationDisplayname;
			},
			'visible' => !Yii::$app->request->get('creation') ? true : false,
		];
		$this->templateColumns['modified_date'] = [
			'attribute' => 'modified_date',
			'value' => function($model, $key, $index, $column) {
				return Yii::$app->formatter->asDatetime($model->modified_date, 'medium');
			},
			'filter' => $this->filterDatepicker($this, 'modified_date'),
		];
		$this->templateColumns['modifiedDisplayname'] = [
			'attribute' => 'modifiedDisplayname',
			'value' => function($model, $key, $index, $column) {
				return isset($model->modified) ? $model->modified->displayname : '-';
				// return $model->modifiedDisplayname;
			},
			'visible' => !Yii::$app->request->get('modified') ? true : false,
		];
		$this->templateColumns['installed'] = [
			'attribute' => 'installed',
			'value' => function($model, $key, $index, $column) {
				return $this->filterYesNo($model->installed);
			},
			'filter' => $this->filterYesNo(),
			'contentOptions' => ['class' => 'text-center'],
		];
		$this->templateColumns['enabled'] = [
			'attribute' => 'enabled',
			'value' => function($model, $key, $index, $column) {
				$url = Url::to(['enabled', 'id' => $model->primaryKey]);
				return $this->quickAction($url, $this->getEnableCondition($model->enabled, $model->module_id) ? 1 : 0, 'Yes,No#Enable,Disable');
			},
			'filter' => $this->filterYesNo(),
			'contentOptions' => ['class' => 'text-center'],
			'format' => 'raw',
		];
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
        if ($column != null) {
            $model = self::find();
            if (is_array($column)) {
                $model->select($column);
            } else {
                $model->select([$column]);
            }
            $model = $model->where(['id' => $id])->one();
            return is_array($column) ? $model : $model->$column;

        } else {
            $model = self::findOne($id);
            return $model;
        }
	}

	/**
	 * User get information
	 */
	public function getEnableCondition($enabled, $module)
	{
		return $enabled == 1 && Yii::$app->hasModule($module) ? true : false;
	}

	/**
	 * Mengembalikan daftar module yang dalam kondisi aktif.
	 *
	 * @return array
	 */
	public static function getEnableIds()
	{
		$enabledModules = Yii::$app->cache->get(self::CACHE_ENABLE_MODULE_IDS);
        if ($enabledModules === false) {
			$enabledModules = [];
			foreach (self::find()
				->andWhere(['enabled' => '1'])
				->all() as $item) {
				$enabledModules[] = $item->module_id;
			}
			Yii::$app->cache->set(self::CACHE_ENABLE_MODULE_IDS, $enabledModules);
		}
		return $enabledModules;
	}

	/**
	 * after find attributes
	 */
	public function afterFind()
	{
		parent::afterFind();

		// $this->creationDisplayname = isset($this->creation) ? $this->creation->displayname : '-';
		// $this->modifiedDisplayname = isset($this->modified) ? $this->modified->displayname : '-';
	}

	/**
	 * before validate attributes
	 */
	public function beforeValidate()
	{
        if (parent::beforeValidate()) {
            if ($this->isNewRecord) {
                if ($this->creation_id == null) {
                    $this->creation_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
                }
            } else {
                if ($this->modified_id == null) {
                    $this->modified_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
                }
            }
        }
        return true;
	}

	/**
	 * After save attributes
	 */
	public function afterSave($insert, $changedAttributes)
	{
		Yii::$app->cache->delete(self::CACHE_ENABLE_MODULE_IDS);
        parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * After delete attributes
	 */
	public function afterDelete()
	{
		Yii::$app->cache->delete(self::CACHE_ENABLE_MODULE_IDS);
        parent::afterDelete();
	}
}
