<?php
/*
 * @author Naser Kholghi <kholghi67@gmail.com>
 * 
 * usage
 * <?php  
 * echo CHtml::textField('datepicker');
 * $this->widget('application.widgets.JalaliDatePicker.JalaliDatePicker',array('textField'=>'datepicker',	
 *	'options'=>array(
 *		'changeMonth'=>'true',
 *		'changeYear'=>'true',
 *		'showButtonPanel'=>'true',
 *	)
 * ));?>
 */

class JalaliDatePicker extends CWidget
{
	/*
	 * model instance to use in CActiveForm
	 */
	public  $model;
	/*
	 * textFiled name
	 */
	public  $textField;
	public  $id;
	/*
	 * options of datePicker
	 * 
	 */
	public  $options = array();
	
	public function run()
	{
		$this->id = (is_object($this->model)) ? get_class($this->model).'_'.$this->textField : $this->textField;
		$this->render('calendar');
	}
	
	private function publishAssets()
	{
		$path = Yii::getPathOfAlias('ext.widgets.JalaliDatePicker.assets');
		return  Yii::app()->assetManager->publish($path);
	}
}