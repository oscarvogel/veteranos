<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'creaFixtureForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>
<div class="row">
	<?php echo $form->dropDownListRow($torneo,'idTorneo',Torneo::getListTorneo('A')); ?>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Crea Fixture')); ?>
</div>

<?php
if(isset($mensaje)){
	echo '<h2>' . $mensaje . '</h2>';
}
if(isset($cruces)){?>
	<table class="table">
    <?php for ($f = 1; $f <= $fixture->_fechas; $f++) {
        echo "<tr>\n";
		echo "<td>Fecha Nº " . $f . "</td>";
        for ($c = 1; $c <= $fixture->_partidosXFechas; $c++) {
            echo "<td>";
            echo utf8_decode($fixture->_fixture[$f][$c]['A']);
            echo '</br>' . utf8_decode($fixture->_fixture[$f][$c]['B']);
            echo "</td>\n";
        }
        echo "</tr>\n";
    }?>
    </table>
<?php }
$this->endWidget();
?>
