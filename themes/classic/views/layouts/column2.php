<?php
$hasSidebar = !(Yii::app()->user->isGuest or empty($this->menu));
$this->beginContent('//layouts/main');
?>
<div class="admin-layout <?php echo $hasSidebar ? 'has-sidebar' : 'no-sidebar'; ?>">
<div class="<?php echo $hasSidebar ? 'span-19 admin-main' : 'span-26 admin-main';?>">
	<div id="content" class="admin-content">
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<?php if($hasSidebar): ?>
<div class="span-5 last admin-sidebar">
	<div id="sidebar">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>Yii::t('app','Operations'),
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div><!-- sidebar -->
</div>
<?php endif; ?>
</div>
<?php $this->endContent(); ?>
