<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;
/**
 * This is the model class for table "et_ophciexamination_overallmanagementplan".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $target_iop
 * @property integer $clinic_internal_id
 * @property integer $photo_id
 * @property integer $oct_id
 * @property integer $hfa_id
 * @property integer $gonio_id
 * @property string $comments
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Gender $clinic_internal
 * @property Gender $photo
 * @property Gender $oct
 * @property Gender $hfa
 * @property Gender $gonio
 */

class Element_OphCiExamination_OverallManagementPlan  extends  \SplitEventTypeElement
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophciexamination_overallmanagementplan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('event_id, left_target_iop , right_target_iop  ,left_clinic_internal_id , right_clinic_internal_id  ,
				 left_photo_id , right_photo_id , left_oct_id , right_oct_id , left_hfa_id , right_hfa_id ,
				left_gonio_id, right_gonio_id, left_comments , right_comments , eye_id', 'safe'),
			array('left_target_iop ,left_clinic_internal_id , left_photo_id , left_oct_id , left_hfa_id , left_gonio_id ',
				'requiredIfSide', 'side' => 'left'),
			array('right_target_iop ,right_clinic_internal_id , right_photo_id , right_oct_id , right_hfa_id , right_gonio_id ',
				'requiredIfSide', 'side' => 'right'),
			array('id, event_id, left_target_iop , right_target_iop  ,left_clinic_internal_id , right_clinic_internal_id  ,
				 left_photo_id , right_photo_id , left_oct_id , right_oct_id , left_hfa_id , right_hfa_id ,
				left_gonio_id, right_gonio_id, left_comments , right_comments , eye_id, ', 'safe', 'on' => 'search'),
			array('left_target_iop, right_target_iop', 'numerical', 'integerOnly'=>true, 'min' => 10, 'max' => 25, 'message' => 'Target IOP Values, use integers between 10 and 25.')

		);
	}

	/**
	 * @return array
	 * @see parent::sidedFields()
	 */
	public function sidedFields()
	{
		return array( 'target_iop' ,'clinic_internal_id' , 'photo_id' , 'oct_id' , 'hfa_id' , 'gonio_id','comments');
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'right_clinic_internal' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'right_clinic_internal_id'),
			'left_clinic_internal' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'left_clinic_internal_id'),
			'right_photo' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'right_photo_id'),
			'left_photo' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'left_photo_id'),
			'right_oct' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'right_oct_id'),
			'left_oct' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'left_oct_id'),
			'right_hfa' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'right_hfa_id'),
			'left_hfa' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'left_hfa_id'),
			'right_gonio' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'right_gonio_id'),
			'left_gonio' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OverallPeriod', 'left_gonio_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
			'right_target_iop' => 'Target IOP',
			'left_target_iop' => 'Target IOP',
			'right_comments' => 'Comments',
			'left_comments' => 'Comments',
			'right_clinic_internal_id' => 'Clinic Internal',
			'left_clinic_internal_id' => 'Clinic Internal',
			'right_photo_id' => 'Photo',
			'left_photo_id' => 'Photo',
			'right_oct_id' => 'OCT',
			'left_oct_id' => 'OCT',
			'right_hfa_id' => 'HFA',
			'left_hfa_id' => 'HFA',
			'right_gonio_id' => 'Gonio',
			'left_gonio_id' => 'Gonio',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);
		$criteria->compare('right_target_iop', $this->right_target_iop);
		$criteria->compare('left_target_iop', $this->left_target_iop);
		$criteria->compare('right_clinic_internal_id', $this->right_clinic_internal_id);
		$criteria->compare('left_clinic_internal_id', $this->left_clinic_internal_id);
		$criteria->compare('right_photo_id', $this->right_photo_id);
		$criteria->compare('left_photo_id', $this->left_photo_id);
		$criteria->compare('right_oct_id', $this->right_oct_id);
		$criteria->compare('left_oct_id', $this->left_oct_id);
		$criteria->compare('right_hfa_id', $this->right_hfa_id);
		$criteria->compare('left_hfa_id', $this->left_hfa_id);
		$criteria->compare('right_gonio_id', $this->right_gonio_id);
		$criteria->compare('left_gonio_id', $this->left_gonio_id);
		$criteria->compare('right_comments', $this->right_comments);
		$criteria->compare('left_comments', $this->left_comments);
		$criteria->compare('eye', $this->eye);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

	public function canCopy()
	{
		return true;
	}

	protected function afterSave()
	{

		return parent::afterSave();
	}
}
?>