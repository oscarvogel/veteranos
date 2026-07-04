<?php
$this->breadcrumbs=array(
	'Recibos'=>array('admin'),'Registrar pago',
);

$this->menu=array(
	array('label'=>'Administrar Recibos', 'url'=>array('admin')),
	array('label'=>'Arqueo de Caja', 'url'=>array('arqueoCaja')),
	array('label'=>'Resumen mensual', 'url'=>array('resumenMensual')),
	array('label'=>'Conceptos de cobro', 'url'=>array('/conceptos/admin')),
);
?>

<?php
Yii::app()->clientScript->registerCss('ingresos-create-form', '
.caja-page {
	max-width: 900px;
	margin: 0 auto;
}
.caja-page-header {
	margin-bottom: 18px;
}
.caja-page-header h1 {
	margin-bottom: 6px;
}
.caja-page-header p {
	color: #64748b;
	margin: 0;
}
.caja-payment-card {
	background: #fff;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
	overflow: hidden;
}
.caja-payment-card__header {
	background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
	color: #fff;
	padding: 18px 22px;
}
.caja-payment-card__header h2 {
	color: #fff;
	font-size: 18px;
	margin: 0 0 4px;
}
.caja-payment-card__header p {
	color: #cbd5e1;
	margin: 0;
}
.caja-payment-card__body {
	padding: 24px 22px 22px;
}
.caja-form-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 18px 20px;
}
.caja-field-full {
	grid-column: 1 / -1;
}
.caja-form .control-group,
.caja-form .form-row {
	margin: 0;
}
.caja-form label {
	color: #1e293b;
	display: block;
	font-size: 13px;
	font-weight: 700;
	margin: 0 0 7px;
}
.caja-form .required {
	color: #dc2626;
}
.caja-form .form-control,
.caja-form select,
.caja-form input[type="text"],
.caja-form input[type="date"],
.caja-form textarea {
	background: #f8fafc;
	border: 1px solid #cbd5e1;
	border-radius: 8px;
	box-shadow: none;
	color: #0f172a;
	font-size: 15px;
	height: 44px;
	line-height: 1.4;
	margin: 0;
	padding: 9px 12px;
	width: 100%;
}
.caja-form textarea {
	height: auto;
	min-height: 88px;
	resize: vertical;
}
.caja-form .monto-input {
	max-width: 220px;
}
.caja-form .form-control:focus,
.caja-form select:focus,
.caja-form input[type="text"]:focus,
.caja-form input[type="date"]:focus,
.caja-form textarea:focus {
	background: #fff;
	border-color: #2563eb;
	box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
	outline: none;
}
.caja-form .errorMessage {
	color: #dc2626;
	font-size: 12px;
	margin-top: 5px;
}
.caja-form .errorSummary {
	background: #fef2f2;
	border: 1px solid #fecaca;
	border-radius: 8px;
	color: #991b1b;
	margin-bottom: 18px;
	padding: 12px 14px;
}
.caja-actions {
	align-items: center;
	border-top: 1px solid #e2e8f0;
	display: flex;
	gap: 10px;
	justify-content: flex-end;
	margin-top: 22px;
	padding-top: 18px;
}
.caja-actions .btn {
	border-radius: 7px;
	font-weight: 700;
	min-height: 40px;
	padding: 9px 16px;
}
@media (max-width: 767px) {
	.caja-page {
		max-width: none;
	}
	.caja-form-grid {
		grid-template-columns: 1fr;
	}
	.caja-actions {
		align-items: stretch;
		flex-direction: column-reverse;
	}
	.caja-actions .btn {
		width: 100%;
	}
}
');
?>

<div class="caja-page">
	<div class="caja-page-header">
		<h1>Registrar pago</h1>
		<p>Seleccioná el equipo, concepto y monto para emitir el recibo de caja.</p>
	</div>

	<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
</div>
