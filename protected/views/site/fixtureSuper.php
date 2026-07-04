<?php
/* @var $this SiteController */

$this->breadcrumbs=array(
	'Fixture Super Veteranos',
);

$baseUrl=Yii::app()->baseUrl.'/media/fixture-super-veteranos';
$fixtures=array();
for($i=1;$i<=17;$i++)
{
	$padded=str_pad($i,2,'0',STR_PAD_LEFT);
	$fixtures[]=array(
		'number'=>$i,
		'url'=>$baseUrl.'/fecha_'.$padded.'.png',
		'download'=>'fixture_super_veteranos_fecha_'.$padded.'.png',
	);
}

$cs=Yii::app()->clientScript;
$cs->registerCss('fixture-super-veteranos', <<<CSS
.fixture-super-page {
	background: #f4f8f6;
	border: 1px solid #d7e6de;
	border-radius: 10px;
	margin-bottom: 30px;
	padding: 18px;
}
.fixture-super-header {
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-webkit-box-align: end;
	-ms-flex-align: end;
	align-items: flex-end;
	-webkit-box-pack: justify;
	-ms-flex-pack: justify;
	justify-content: space-between;
	gap: 15px;
	margin-bottom: 18px;
}
.fixture-super-title h1 {
	color: #063f2a;
	font-size: 30px;
	font-weight: 800;
	margin: 0 0 4px;
}
.fixture-super-title p {
	color: #5d6d67;
	font-size: 14px;
	margin: 0;
}
.fixture-download-btn {
	background: #078a48;
	border: 0;
	border-radius: 6px;
	color: #fff;
	display: inline-block;
	font-weight: 700;
	padding: 11px 16px;
	text-decoration: none;
	white-space: nowrap;
}
.fixture-download-btn:hover,
.fixture-download-btn:focus {
	background: #056b39;
	color: #fff;
	text-decoration: none;
}
.fixture-super-grid {
	display: grid;
	grid-template-columns: 230px minmax(0, 1fr);
	gap: 16px;
}
.fixture-date-panel,
.fixture-image-panel {
	background: #fff;
	border: 1px solid #d7e6de;
	border-radius: 8px;
	box-shadow: 0 1px 2px rgba(4, 42, 26, .07);
	overflow: hidden;
}
.fixture-date-panel-header {
	border-bottom: 1px solid #edf3f0;
	padding: 14px;
}
.fixture-date-panel-header label {
	color: #5d6d67;
	display: block;
	font-size: 12px;
	font-weight: 700;
	letter-spacing: .05em;
	margin-bottom: 8px;
	text-transform: uppercase;
}
.fixture-date-select {
	border: 1px solid #cbdcd3;
	border-radius: 6px;
	font-size: 14px;
	height: 38px;
	width: 100%;
}
.fixture-date-list {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 8px;
	padding: 12px;
}
.fixture-date-btn {
	background: #fff;
	border: 1px solid #cbdcd3;
	border-radius: 6px;
	color: #23443a;
	font-size: 14px;
	font-weight: 700;
	min-height: 38px;
	transition: all .15s ease;
}
.fixture-date-btn:hover {
	background: #eaf5ef;
	border-color: #078a48;
}
.fixture-date-btn.active {
	background: #078a48;
	border-color: #078a48;
	color: #fff;
}
.fixture-image-header {
	border-bottom: 1px solid #edf3f0;
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-webkit-box-align: center;
	-ms-flex-align: center;
	align-items: center;
	-webkit-box-pack: justify;
	-ms-flex-pack: justify;
	justify-content: space-between;
	gap: 12px;
	padding: 14px;
}
.fixture-image-kicker {
	color: #078a48;
	font-size: 12px;
	font-weight: 800;
	letter-spacing: .06em;
	text-transform: uppercase;
}
.fixture-image-title {
	color: #0d1f18;
	font-size: 20px;
	font-weight: 800;
	margin: 2px 0 0;
}
.fixture-nav {
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	gap: 8px;
}
.fixture-nav button {
	background: #fff;
	border: 1px solid #cbdcd3;
	border-radius: 6px;
	color: #23443a;
	font-size: 13px;
	font-weight: 700;
	min-width: 92px;
	padding: 8px 10px;
}
.fixture-nav button:disabled {
	color: #9aa7a2;
	cursor: not-allowed;
	opacity: .55;
}
.fixture-image-wrap {
	background: #eaf3ee;
	padding: 12px;
}
.fixture-image-wrap img {
	background: #fff;
	border-radius: 6px;
	box-shadow: 0 1px 4px rgba(4, 42, 26, .12);
	display: block;
	height: auto;
	margin: 0 auto;
	max-width: 100%;
}
@media (max-width: 767px) {
	.fixture-super-page {
		border-radius: 0;
		margin-left: -15px;
		margin-right: -15px;
		padding: 14px;
	}
	.fixture-super-header,
	.fixture-image-header {
		display: block;
	}
	.fixture-download-btn {
		margin-top: 12px;
		text-align: center;
		width: 100%;
	}
	.fixture-super-grid {
		grid-template-columns: 1fr;
	}
	.fixture-date-list {
		grid-template-columns: repeat(4, 1fr);
	}
	.fixture-nav {
		margin-top: 12px;
	}
	.fixture-nav button {
		width: 50%;
	}
}
CSS
);

$cs->registerScript('fixture-super-veteranos', <<<JS
(function($) {
	var currentFecha = 1;
	var totalFechas = 17;

	function selectFecha(fecha) {
		var button = $('.fixture-date-btn[data-fecha="' + fecha + '"]');
		if (!button.length) {
			return;
		}

		currentFecha = fecha;
		$('.fixture-date-btn').removeClass('active');
		button.addClass('active');
		$('#fixtureDateSelect').val(fecha);
		$('#fixtureImage')
			.attr('src', button.data('url'))
			.attr('alt', 'Fixture Super Veteranos - fecha ' + fecha);
		$('#fixtureDownload')
			.attr('href', button.data('url'))
			.attr('download', button.data('download'))
			.text('Descargar fecha ' + fecha);
		$('#fixtureImageTitle').text('Fecha ' + fecha + ' de 17');
		$('.fixture-prev').prop('disabled', fecha <= 1);
		$('.fixture-next').prop('disabled', fecha >= totalFechas);
	}

	$('#fixtureDateSelect').on('change', function() {
		selectFecha(parseInt($(this).val(), 10));
	});

	$('.fixture-date-btn').on('click', function() {
		selectFecha(parseInt($(this).data('fecha'), 10));
	});

	$('.fixture-prev').on('click', function() {
		selectFecha(currentFecha - 1);
	});

	$('.fixture-next').on('click', function() {
		selectFecha(currentFecha + 1);
	});

	selectFecha(1);
})(jQuery);
JS
);
?>

<div class="fixture-super-page">
	<div class="fixture-super-header">
		<div class="fixture-super-title">
			<h1>Fixture Super Veteranos</h1>
			<p>Seleccione una fecha para ver el fixture y descargar la imagen oficial.</p>
		</div>
		<a id="fixtureDownload" class="fixture-download-btn" href="<?php echo CHtml::encode($fixtures[0]['url']); ?>" download="<?php echo CHtml::encode($fixtures[0]['download']); ?>">Descargar fecha 1</a>
	</div>

	<div class="fixture-super-grid">
		<div class="fixture-date-panel">
			<div class="fixture-date-panel-header">
				<label for="fixtureDateSelect">Fecha</label>
				<select id="fixtureDateSelect" class="fixture-date-select">
					<?php foreach($fixtures as $fixture): ?>
						<option value="<?php echo (int)$fixture['number']; ?>">Fecha <?php echo (int)$fixture['number']; ?> de 17</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="fixture-date-list">
				<?php foreach($fixtures as $fixture): ?>
					<button
						type="button"
						class="fixture-date-btn<?php echo $fixture['number']===1 ? ' active' : ''; ?>"
						data-fecha="<?php echo (int)$fixture['number']; ?>"
						data-url="<?php echo CHtml::encode($fixture['url']); ?>"
						data-download="<?php echo CHtml::encode($fixture['download']); ?>"
					><?php echo (int)$fixture['number']; ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="fixture-image-panel">
			<div class="fixture-image-header">
				<div>
					<div class="fixture-image-kicker">Super Veteranos</div>
					<div id="fixtureImageTitle" class="fixture-image-title">Fecha 1 de 17</div>
				</div>
				<div class="fixture-nav">
					<button type="button" class="fixture-prev" disabled>Anterior</button>
					<button type="button" class="fixture-next">Siguiente</button>
				</div>
			</div>
			<div class="fixture-image-wrap">
				<img id="fixtureImage" src="<?php echo CHtml::encode($fixtures[0]['url']); ?>" alt="Fixture Super Veteranos - fecha 1" width="1080" height="1080">
			</div>
		</div>
	</div>
</div>
