<?php
/* @var $this SiteController */
/* @var $torneo Torneo */
/* @var $torneosDisponibles array */
/* @var $partidos array|null */
/* @var $fechasDisponibles array */
/* @var $fechaSeleccionada int|null */
/* @var $idTorneo int */

$this->breadcrumbs = array(
    'Fixture' => array('site/fixture'),
    CHtml::encode($torneo->Nombre) . ($fechaSeleccionada !== null ? ' - Fecha ' . $fechaSeleccionada : ''),
);

$baseUrl = Yii::app()->getBaseUrl(true);
$urlCompartida = $baseUrl . '/index.php?r=site/fixture&idTorneo=' . (int)$idTorneo
    . ($fechaSeleccionada !== null ? '&fecha=' . (int)$fechaSeleccionada : '');
$urlPdf = $baseUrl . '/index.php?r=site/fixturePdf&idTorneo=' . (int)$idTorneo
    . ($fechaSeleccionada !== null ? '&fecha=' . (int)$fechaSeleccionada : '');

$textoWa = 'Fixture ' . $torneo->Nombre
    . ($fechaSeleccionada !== null ? ' - Fecha ' . $fechaSeleccionada : '') . "\n"
    . $urlCompartida
    . ($fechaSeleccionada !== null ? "\n" . $urlPdf : '');
$waHref = 'https://wa.me/?text=' . rawurlencode($textoWa);
?>

<form id="fixturePublicForm" class="well form-horizontal" method="get" action="<?php echo CHtml::encode($baseUrl . '/index.php?r=site/fixture'); ?>">
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label for="Torneo_idTorneo">Torneo</label>
                <select id="Torneo_idTorneo" name="idTorneo" class="form-control">
                    <?php foreach ($torneosDisponibles as $id => $nombre): ?>
                        <option value="<?php echo (int)$id; ?>"<?php echo (int)$id === (int)$idTorneo ? ' selected' : ''; ?>>
                            <?php echo CHtml::encode($nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="Torneo_fecha">Fecha</label>
                <select id="Torneo_fecha" name="fecha" class="form-control">
                    <option value="">Todas las fechas</option>
                    <?php foreach ($fechasDisponibles as $n): ?>
                        <option value="<?php echo (int)$n; ?>"<?php echo $fechaSeleccionada === (int)$n ? ' selected' : ''; ?>>
                            Fecha <?php echo (int)$n; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group form-actions" style="margin-top: 8px; border-top: 1px solid #e5e5e5; padding-top: 15px;">
        <button type="submit" class="btn btn-primary">
            <i class="icon-list"></i> Consulta Fixture
        </button>
        <a href="<?php echo CHtml::encode($urlPdf); ?>" class="btn btn-success" target="_blank" rel="noopener">
            <i class="icon-file"></i> Exportar a PDF
        </a>
        <a href="<?php echo CHtml::encode($waHref); ?>" class="btn btn-whatsapp" target="_blank" rel="noopener">
            <span class="wa-icon" aria-hidden="true">&#128172;</span>
            Compartir por WhatsApp
        </a>
    </div>
</form>


<?php if (!empty($partidos)): ?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 90px;">Nº Fecha</th>
                <th>Local</th>
                <th style="width: 50px; text-align: center;">VS</th>
                <th>Visitante</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $fechaActual = 0;
            foreach ($partidos as $partido):
                $esLibre = ((int)$partido->Visitante === 0);
            ?>
                <tr class="<?php echo $esLibre ? 'libre' : ''; ?>">
                    <td>
                        <?php if ((int)$partido->NFecha !== $fechaActual): ?>
                            <strong><?php echo (int)$partido->NFecha; ?></strong>
                            <?php $fechaActual = (int)$partido->NFecha; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $partido->local ? CHtml::encode($partido->local->Nombre) : '-'; ?></td>
                    <td style="text-align: center;">
                        <span class="label label-<?php echo $esLibre ? 'inverse' : 'success'; ?>">VS</span>
                    </td>
                    <td>
                        <?php if ($esLibre): ?>
                            <em>Libre</em>
                        <?php else: ?>
                            <?php echo $partido->visitante ? CHtml::encode($partido->visitante->Nombre) : '-'; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
$cs = Yii::app()->clientScript;
$cs->registerCss('fixture-public', <<<CSS
#fixturePublicForm .form-group { margin-bottom: 12px; }
#fixturePublicForm label {
    color: #5d6d67;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: .05em;
    text-transform: uppercase;
    margin-bottom: 6px;
    display: block;
}
#fixturePublicForm .form-actions { margin-left: 0; margin-right: 0; }
#fixturePublicForm .form-actions .btn { margin-right: 6px; }
.btn-whatsapp {
    background-color: #25D366 !important;
    border-color: #1ebe57 !important;
    color: #fff !important;
}
.btn-whatsapp:hover,
.btn-whatsapp:focus {
    background-color: #1ebe57 !important;
    color: #fff !important;
}
.btn-whatsapp .wa-icon { margin-right: 4px; font-size: 14px; }
table.table tr.libre td { background: #fff5f5; color: #b04141; font-style: italic; }
table.table tr.libre td .label-inverse { background: #b04141; }
CSS
);

$cs->registerScript('fixture-public-reset-fecha', <<<JS
(function() {
    var selTorneo = document.getElementById('Torneo_idTorneo');
    var selFecha = document.getElementById('Torneo_fecha');
    if (selTorneo && selFecha) {
        selTorneo.addEventListener('change', function() {
            selFecha.value = '';
        });
    }
})();
JS
);
?>
