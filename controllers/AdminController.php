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

class AdminController extends ModuleAdminController
{
	public $defaultAction = "ViewNoTreatmentReasons";

	// No Treatment Reason views

	/**
	 * list the reasons that can be selected for not providing an injection treatment
	 */
	public function actionViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason()
	{
		$model_list = OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findAll(array('order' => 'display_order asc'));
		$this->jsVars['OphCiExamination_sort_url'] = $this->createUrl('sortNoTreatmentReasons');
		$this->jsVars['OphCiExamination_model_status_url'] = $this->createUrl('setNoTreatmentReasonStatus');

		Audit::add('admin','list',null,false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_InjectionManagementComplex_NoTreatmentReason'));

		$this->render('list',array(
				'model_list'=>$model_list,
				'title'=>'No Treatment Reasons',
				'model_class'=>'OphCiExamination_InjectionManagementComplex_NoTreatmentReason',
		));
	}

	/**
	 * create a new no treatment reason for injection
	 *
	 */
	public function actionCreateOphCiExamination_InjectionManagementComplex_NoTreatmentReason()
	{
		$model = new OphCiExamination_InjectionManagementComplex_NoTreatmentReason();

		if (isset($_POST['OphCiExamination_InjectionManagementComplex_NoTreatmentReason'])) {
			$model->attributes = $_POST['OphCiExamination_InjectionManagementComplex_NoTreatmentReason'];

			if ($bottom_drug = OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->find(array('order'=>'display_order desc'))) {
				$display_order = $bottom_drug->display_order+1;
			} else {
				$display_order = 1;
			}
			$model->display_order = $display_order;

			if ($model->save()) {
				Audit::add('admin','create',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'InjectionManagementComplex_NoTreatmentReason'));
				Yii::app()->user->setFlash('success', 'Injection Management No Treatment reason added');

				$this->redirect(array('ViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason'));
			}
		}

		$this->render('create', array(
				'model' => $model,
		));
	}

	/**
	 * update the no treatment reason with id $id
	 *
	 * @param integer $id
	 */
	public function actionUpdateOphCiExamination_InjectionManagementComplex_NoTreatmentReason($id)
	{
		$model = OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk((int) $id);

		if (isset($_POST['OphCiExamination_InjectionManagementComplex_NoTreatmentReason'])) {
			$model->attributes = $_POST['OphCiExamination_InjectionManagementComplex_NoTreatmentReason'];

			if ($model->save()) {
				Audit::add('admin','update',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'InjectionManagementComplex_NoTreatmentReason'));
				Yii::app()->user->setFlash('success', 'Injection Management No Treatment reason updated');

				$this->redirect(array('ViewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason'));
			}
		}

		$this->render('create', array(
				'model' => $model,
		));
	}

	/*
	 * sorts the no treatment reasons into the provided order (NOTE does not support a paginated list of reasons)
	*/
	public function actionSortNoTreatmentReasons()
	{
		if (!empty($_POST['order'])) {
			foreach ($_POST['order'] as $i => $id) {
				if ($drug = OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk($id)) {
					$drug->display_order = $i+1;
					if (!$drug->save()) {
						throw new Exception("Unable to save drug: ".print_r($drug->getErrors(),true));
					}
				}
			}
		}
	}

	/**
	 * Update the enabled status of the given reason
	 */
	public function actionSetNoTreatmentReasonStatus()
	{
		if ($model = OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk((int) @$_POST['id'])) {
			if (!array_key_exists('enabled', $_POST)) {
				throw new Exception('cannot determine status for reason');
			}

			if ($_POST['enabled']) {
				$model->enabled = true;
			} else {
				$model->enabled = false;
			}
			if (!$model->save()) {
				throw new Exception("Unable to set reason status: " . print_r($model->getErrors(), true));
			}

			Audit::add('admin','set-reason-status',serialize($_POST),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_InjectionManagementComplex_NoTreatmentReason'));

		} else {
			throw new Exception('Cannot find reason with id' . @$_POST['id']);
		}
	}

	// Disorder Questions

	/**
	 * list the questions set for the given disorder id
	 */
	public function actionViewOphCiExamination_InjectionManagementComplex_Question()
	{
		$this->jsVars['OphCiExamination_sort_url'] = $this->createUrl('sortQuestions');
		$this->jsVars['OphCiExamination_model_status_url'] = $this->createUrl('setQuestionStatus');

		$model_list = array();
		$disorder_id = null;
		if (isset($_GET['disorder_id'])) {
			$disorder_id = (int) $_GET['disorder_id'];
			$criteria = new CDbCriteria;
			$criteria->order = "display_order asc";
			$criteria->condition = "disorder_id = :disorder_id";
			$criteria->params = array(':disorder_id' => (int) $_GET['disorder_id']);

			$model_list = OphCiExamination_InjectionManagementComplex_Question::model()->findAll($criteria);

			$this->jsVars['OphCiExamination_sort_url'] = $this->createUrl('sortQuestions');
		}

		Audit::add('admin','list-for-disorder',serialize($_GET),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_InjectionManagementComplex_Question'));

		$this->render('list_diagnosis_questions',array(
				'disorder_id'=>$disorder_id,
				'model_list'=>$model_list,
				'title'=>'Disorder Questions',
				'model_class'=>'OphCiExamination_InjectionManagementComplex_Question',
		));

	}

	/**
	 * create a question for the given disorder id
	 */
	public function actionCreateOphCiExamination_InjectionManagementComplex_Question()
	{
		$model = new OphCiExamination_InjectionManagementComplex_Question();

		if (isset($_POST['OphCiExamination_InjectionManagementComplex_Question'])) {
			// process submission
			$model->attributes = $_POST['OphCiExamination_InjectionManagementComplex_Question'];

			if ($model->disorder_id) {
				// not a valid question otherwise
				$criteria = new CDbCriteria;
				$criteria->order = "display_order desc";
				$criteria->condition = "disorder_id = :disorder_id";
				$criteria->limit	= 1;
				$criteria->params = array(':disorder_id' => $model->disorder_id);

				if ($bottom = OphCiExamination_InjectionManagementComplex_Question::model()->find($criteria) ) {
					$display_order = $bottom->display_order+1;
				} else {
					$display_order = 1;
				}
				$model->display_order = $display_order;

				if ($model->save()) {
					Audit::add('admin','create',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'InjectionManagementComplex_Question'));
					Yii::app()->user->setFlash('success', 'Injection Management Disorder Question added');

					$this->redirect(array('ViewOphCiExamination_InjectionManagementComplex_Question', 'disorder_id' => $model->disorder_id));
				}
			}
		} elseif (isset($_GET['disorder_id'])) {
			// allow the ability to pre-select which disorder is being set for a question
			$model->disorder_id = $_GET['disorder_id'];
		}

		$this->render('create', array(
				'model' => $model,
		));

	}

	/**
	 * update the question for the specified id
	 *
	 * @param integer $id
	 */
	public function actionUpdateOphCiExamination_InjectionManagementComplex_Question($id)
	{
		$model = OphCiExamination_InjectionManagementComplex_Question::model()->findByPk((int) $id);
		if (isset($_POST['OphCiExamination_InjectionManagementComplex_Question'])) {
			// process submission
			$model->attributes = $_POST['OphCiExamination_InjectionManagementComplex_Question'];

			if ($model->save()) {
				Audit::add('admin','update',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'InjectionManagementComplex_Question'));
				Yii::app()->user->setFlash('success', 'Injection Management Disorder Question updated');

				$this->redirect(array('ViewOphCiExamination_InjectionManagementComplex_Question', 'disorder_id' => $model->disorder_id));
			}
		}

		$this->render('update', array(
				'model' => $model
		));
	}

	/**
	 * sorts questions into the given order
	*/
	public function actionSortQuestions()
	{
		if (!empty($_POST['order'])) {
			foreach ($_POST['order'] as $i => $id) {
				if ($question = OphCiExamination_InjectionManagementComplex_Question::model()->findByPk($id)) {
					$question->display_order = $i+1;
					if (!$question->save()) {
						throw new Exception("Unable to save question: ".print_r($question->getErrors(),true));
					}
				}
			}
		}
	}

	/**
	 * Update the enabled status of the given question
	 */
	public function actionSetQuestionStatus()
	{
		if ($model = OphCiExamination_InjectionManagementComplex_Question::model()->findByPk((int) @$_POST['id'])) {
			if (!array_key_exists('enabled', $_POST)) {
				throw new Exception('cannot determine status for question');
			}

			if ($_POST['enabled']) {
				$model->enabled = true;
			} else {
				$model->enabled = false;
			}
			if (!$model->save()) {
				throw new Exception("Unable to set question status: " . print_r($model->getErrors(), true));
			}

			Audit::add('admin','set-question-status',serialize($_POST),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_InjectionManagementComplex_Question'));
		} else {
			throw new Exception('Cannot find question with id' . @$_POST['id']);
		}
	}

	public function actionViewWorkflows()
	{
		Audit::add('admin','list',null,false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_Workflow'));

		$this->render('list_OphCiExamination_Workflow', array(
				'model_class' => 'OphCiExamination_Workflow',
				'model_list' => OphCiExamination_Workflow::model()->findAll(array('order'=>'name asc')),
				'title' => 'Workflows',
		));
	}

	public function actionAddWorkflow()
	{
		$model = new OphCiExamination_Workflow();
		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
		Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

		if (isset($_POST['OphCiExamination_Workflow'])) {
			$model->attributes = $_POST['OphCiExamination_Workflow'];

			if ($model->save()) {
				Audit::add('admin','create',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_Workflow'));
				Yii::app()->user->setFlash('success', 'Workflow added');

				$this->redirect(array('viewWorkflowRules'));
			}
		}

		$this->render('update', array(
						'model' => $model,
						'title' => 'Add workflow',
						'cancel_uri' => '/OphCiExamination/admin/viewWorkflows',
				));
	}


	public function actionEditWorkflow($id)
	{
		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
		Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

		$model = OphCiExamination_Workflow::model()->findByPk((int) $id);

		if (isset($_POST['OphCiExamination_ElementSet'])) {
			$model->attributes = $_POST['OphCiExamination_ElementSet'];

			if ($model->save()) {
				Audit::add('admin','update',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_ElementSet'));
				Yii::app()->user->setFlash('success', 'Workflow updated');

				$this->redirect(array('viewWorkflows'));
			}
		}

		$this->render('update', array(
				'model' => $model,
				'title' => 'Edit workflow',
				'cancel_uri' => '/OphCiExamination/admin/viewWorkflows',
				'related_view' => 'update_Workflow_ElementSets',
		));
	}

	public function actionEditWorkflowStep()
	{
		if (!$step = OphCiExamination_ElementSet::model()->findByPk(@$_GET['step_id'])) {
			throw new Exception("ElementSetItem not found: ".@$_GET['step_id']);
		}

		$element_type_ids = array();

		foreach ($step->items as $item) {
			$element_type_ids[] = $item->element_type_id;
		}

		$et_exam = EventType::model()->find('class_name=?',array('OphCiExamination'));

		$criteria = new CDbCriteria;
		$criteria->addCondition('event_type_id = :event_type_id');
		$criteria->addNotInCondition('id',$element_type_ids);
		$criteria->params[':event_type_id'] = $et_exam->id;
		$criteria->order = 'name asc';

		$this->renderPartial('_update_Workflow_ElementSetItem',array(
			'step' => $step,
			'element_types' => ElementType::model()->findAll($criteria),
		));
	}

	public function actionReorderWorkflowSteps()
	{
		foreach ($_POST as $id => $position) {
			if ($id != 'YII_CSRF_TOKEN') {
				if (!$step = OphCiExamination_ElementSet::model()->findByPk($id)) {
					throw new Exception("Unable to find workflow step: $id");
				}
				$step->position = $position;

				if (!$step->save()) {
					throw new Exception("Unable to save workflow step: ".print_r($step->getErrors(),true));
				}
			}
		}

		echo "1";
	}

	public function actionAddElementTypeToWorkflowStep()
	{
		$et_exam = EventType::model()->find('class_name=?',array('OphCiExamination'));

		if (!$element_type = ElementType::model()->find('event_type_id = ? and id = ?',array($et_exam->id,@$_POST['element_type_id']))) {
			throw new Exception("Unknown examination element type: ".@$_POST['element_type_id']);
		}

		if (!$step = OphCiExamination_ElementSet::model()->findByPk(@$_POST['step_id'])) {
			throw new Exception("Unknown element set: ".@$_POST['step_id']);
		}

		if (!OphCiExamination_ElementSetItem::model()->find('set_id=? and element_type_id=?',array($step->id,$element_type->id))) {
			$item = new OphCiExamination_ElementSetItem;
			$item->set_id = $step->id;
			$item->element_type_id = $element_type->id;

			if (!$item->save()) {
				throw new Exception("Unable to save element set item: ".print_r($item->getErrors(),true));
			}
		}

		echo "1";
	}

	public function actionRemoveElementTypeFromWorkflowStep()
	{
		if (!$item = OphCiExamination_ElementSetItem::model()->find('set_id=? and id=?',array(@$_POST['step_id'],@$_POST['element_type_item_id']))) {
			throw new Exception("Element set item not found: ".@$_POST['element_type_item_id']." in set ".@$_POST['step_id']);
		}

		if (!$item->delete()) {
			throw new Exception("Unable to delete element set item: ".print_r($item->getErrors(),true));
		}

		echo "1";
	}

	public function actionAddworkflowStep()
	{
		if (!$workflow = OphCiExamination_Workflow::model()->findByPk(@$_POST['workflow_id'])) {
			throw new Exception("Workflow not found: ".@$_POST['workflow_id']);
		}

		if ($current_last = OphCiExamination_ElementSet::model()->find(array(
			'condition' => 'workflow_id = :workflow_id',
			'params' => array(
				':workflow_id' => $workflow->id,
			),
			'order' => 'position desc',
		))) {
			$current_last_position = $current_last->position;
		} else {
			$current_last_position = 0;
		}

		$set = new OphCiExamination_ElementSet;
		$set->workflow_id = $workflow->id;
		$set->position = $current_last_position + 1;
		$set->name = 'Step '.$set->position;

		if (!$set->save()) {
			throw new Exception("Unable to save element set: ".print_r($set->getErrors(),true));
		}

		echo json_encode(array(
			'id' => $set->id,
			'position' => $set->position,
			'name' => $set->name,
		));
	}

	public function actionRemoveWorkflowStep()
	{
		if (!$step = OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?',array(@$_POST['workflow_id'],@$_POST['element_set_id']))) {
			throw new Exception("Unknown element set ".@$_POST['element_set_id']." for workflow ".@$_POST['workflow_id']);
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('set_id = :set_id');
		$criteria->params[':set_id'] = $step->id;

		OphCiExamination_ElementSetItem::model()->deleteAll($criteria);

		if (!$step->delete()) {
			throw new Exception("Unable to remove element set: ".print_r($step->getErrors(),true));
		}

		echo "1";
	}

	public function actionSaveWorkflowStepName()
	{
		if (!$step = OphCiExamination_ElementSet::model()->find('workflow_id=? and id=?',array(@$_POST['workflow_id'],@$_POST['element_set_id']))) {
			throw new Exception("Unknown element set ".@$_POST['element_set_id']." for workflow ".@$_POST['workflow_id']);
		}

		$step->name = @$_POST['step_name'];

		if (!$step->save()) {
			throw new Exception("Unable to save element set: ".print_r($step->getErrors(),true));
		}

		echo "1";
	}

	public function actionViewWorkflowRules()
	{
		Audit::add('admin','list',null,false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_Workflow_Rule'));

		$this->render('list_OphCiExamination_Workflow_Rules', array(
				'model_class' => 'OphCiExamination_Workflow_Rule',
				'model_list' => OphCiExamination_Workflow_Rule::model()->findAll(array('order'=>'id asc')),
				'title' => 'Workflow rules',
		));
	}

	public function actionEditWorkflowRule($id)
	{
		if (!$model = OphCiExamination_Workflow_Rule::model()->findByPk($id)) {
			throw new Exception("Workflow rule not found: $id");
		}

		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
		Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

		if (isset($_POST['OphCiExamination_Workflow_Rule'])) {
			$model->attributes = $_POST['OphCiExamination_Workflow_Rule'];

			if ($model->save()) {
				Audit::add('admin','update',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_Workflow_Rule'));
				Yii::app()->user->setFlash('success', 'Workflow rule updated');

				$this->redirect(array('viewWorkflowRules'));
			}
		}

		$this->render('update', array(
				'model' => $model,
				'title' => 'Edit workflow rule',
				'cancel_uri' => '/OphCiExamination/admin/viewWorkflowRules',
		));
	}

	public function actionAddWorkflowRule()
	{
		$model = new OphCiExamination_Workflow_Rule;

		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
		Yii::app()->clientScript->registerCssFile($assetPath.'/css/components/admin.css');

		if (isset($_POST['OphCiExamination_Workflow_Rule'])) {
			$model->attributes = $_POST['OphCiExamination_Workflow_Rule'];

			if ($model->save()) {
				Audit::add('admin','create',serialize($model->attributes),false,array('module'=>'OphCiExamination','model'=>'OphCiExamination_Workflow_Rule'));
				Yii::app()->user->setFlash('success', 'Workflow rule updated');

				$this->redirect(array('viewWorkflowRules'));
			}
		}

		$this->render('update', array(
				'model' => $model,
				'title' => 'Add workflow rule',
				'cancel_uri' => '/OphCiExamination/admin/viewWorkflowRules',
		));
	}

	public function actionDeleteWorkflowRules()
	{
		if (is_array(@$_POST['workflowrules'])) {
			foreach ($_POST['workflowrules'] as $rule_id) {
				if ($rule = OphCiExamination_Workflow_Rule::model()->findByPk($rule_id)) {
					if (!$rule->delete()) {
						throw new Exception("Unable to delete workflow rule: ".print_r($rule->getErrors(),true));
					}
				}
			}
		}

		echo "1";
	}
}
