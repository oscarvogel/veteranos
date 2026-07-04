<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	\$model->{$nameColumn},
);\n";
?>

$this->menu=array(
	array('label'=>'<?php echo Yii::t('app','List');?> <?php echo $this->modelClass; ?>', 'url'=>array('index')),
	array('label'=>'<?php echo Yii::t('app','Create');?> <?php echo $this->modelClass; ?>', 'url'=>array('create')),
	array('label'=>'<?php echo Yii::t('app','Update');?> <?php echo $this->modelClass; ?>', 'url'=>array('update', 'id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>'<?php echo Yii::t('app','Delete');?> <?php echo $this->modelClass; ?>', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>),'confirm'=>'<?php echo Yii::t('app','Are you sure you want to delete this item?');?>')),
	array('label'=>'<?php echo Yii::t('app','Manage');?> <?php echo $this->modelClass; ?>', 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('app','View');?> <?php echo $this->modelClass." #<?php echo \$model->{$this->tableSchema->primaryKey}; ?>"; ?></h1>

<?php echo "<?php"; ?> $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
<?php
foreach($this->tableSchema->columns as $column)
	echo "\t\t'".$column->name."',\n";
?>
	),
)); ?>
