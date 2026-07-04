<?php
/**
 * @var ReportesController $this
 * @var CActiveDataProvider $dataProvider
 * @var Equipos $equipoModel
 * @var Torneo $torneoModel (asumiendo un modelo Torneo)
 */

$this->pageTitle = 'Tarjetas Amarillas: ' . CHtml::encode($equipoModel->Nombre) . ' en ' . CHtml::encode($torneoModel->Nombre); // Asumiendo que los modelos tienen un atributo 'nombre'

$this->breadcrumbs = array(
    'Reportes' => array('index'), // O tu ruta base de reportes
    'Tarjetas Amarillas por Equipo y Torneo',
);
?>

<h1>Tarjetas Amarillas</h1>
<h2>Equipo: <?php echo CHtml::encode($equipoModel->Nombre); ?></h2>
<h3>Torneo: <?php echo CHtml::encode($torneoModel->Nombre); ?></h3>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'tarjetas-amarillas-grid',
    'dataProvider' => $dataProvider,
    'filter' => null, // Puedes configurar un modelo de filtro si lo deseas
    'columns' => array(
        array(
            'header' => 'Jugador',
            // Asumiendo que el modelo Jugador tiene un atributo 'nombreCompleto' o similar
            // y que la relación en Tarjeta se llama 'Jugador'
            'value' => '$data->Jugador ? CHtml::encode($data->Jugador->Nombre) : "N/D"',
        ),
        array(
            'header' => 'Cant. Amarillas',
            'htmlOptions' => array('style' => 'text-align:center; width:80px;'),
            'value' => '$data->total_amarillas',
        ),
        array(
            'header' => 'Última Fecha',
            'htmlOptions' => array('style' => 'text-align:center; width:100px;'),
            'value' => '$data->ultima_fecha',
            'type' => 'date',
        ),
        // Podrías añadir más columnas si es necesario, como 'Roja', 'DesdeFecha', 'HastaFecha'
        // 'Roja',
        // array(
        //     'name' => 'DesdeFecha',
        //     'type' => 'date', // Formatear como fecha si es un timestamp o YYYY-MM-DD
        // ),
        // array(
        //     'name' => 'HastaFecha',
        //     'type' => 'date',
        // ),
    ),
)); ?>