<?php
/**
 * CoreZoneVillage
 *
 * This is the ActiveQuery class for [[\ommu\core\models\CoreZoneVillage]].
 * @see \ommu\core\models\CoreZoneVillage
 * 
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2018 OMMU (www.ommu.co)
 * @created date 26 April 2018, 20:40 WIB
 * @modified date 30 January 2019, 16:09 WIB
 * @link https://github.com/ommu/mod-core
 *
 */

namespace ommu\core\models\query;

class CoreZoneVillage extends \yii\db\ActiveQuery
{
	/*
	public function active()
	{
		return $this->andWhere('[[status]]=1');
	}
	*/

	/**
	 * {@inheritdoc}
	 */
	public function published() 
	{
		return $this->andWhere(['publish' => 1]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function unpublish() 
	{
		return $this->andWhere(['publish' => 0]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleted() 
	{
		return $this->andWhere(['publish' => 2]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function suggest() 
	{
		return $this->select(['village_id', 'district_id', 'village_name', 'zipcode'])
			->published();
	}

	/**
	 * {@inheritdoc}
	 * @return \ommu\core\models\CoreZoneVillage[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return \ommu\core\models\CoreZoneVillage|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
