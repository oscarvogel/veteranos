<?php
/* @var $this FixtureController */
/* @var $listaTorneos array */
/* @var $form array */
/* @var $previewRows array */
/* @var $resultado array|null */
/* @var $error string|null */

$this->breadcrumbs = array(
    'Fixture' => array('fixture/admin'),
    'Reprogramar fecha',
);

$nombresTorneosSeleccionados = '';
if (!empty($resultado)) {
    $nombresTorneosSeleccionados = array();
    foreach ($resultado['torneos'] as $tid) {
        $nombresTorneosSeleccionados[] = isset($listaTorneos[$tid]) ? $listaTorneos[$tid]['nombre'] : '#' . $tid;
    }
    $nombresTorneosSeleccionados = implode(', ', $nombresTorneosSeleccionados);
}

$cantPreview = !empty($previewRows) ? count($previewRows) : 0;
?>
<style>
.reprog-wrap { max-width: 900px; margin: 0 auto; }
.reprog-wrap-preview { max-width: 1100px; margin: 0 auto; }
.reprog-intro { max-width: 800px; margin: 0 auto 20px; text-align: left; }
.reprog-code { color: #555; background: #f5f5f5; padding: 1px 6px; border-radius: 3px; font-family: Menlo,Monaco,Consolas,"Courier New",monospace; font-size: 90%; }
.reprog-list label { font-weight: normal; }
.reprog-actions { margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e5e5; }
.reprog-torneo-estado { display: inline-block; min-width: 22px; padding: 1px 6px; font-size: 11px; font-weight: bold; color: #fff; background: #5cb85c; border-radius: 3px; vertical-align: middle; text-align: center; }
.reprog-modal-num { font-size: 32px; font-weight: bold; color: #5cb85c; line-height: 1; }
</style>

<h2 class="text-center">Reprogramar fechas del fixture</h2>
<p class="text-muted reprog-intro">
    Suma N dias a la columna <span class="reprog-code">Fecha</span> de los partidos <strong>pendientes</strong>
    (donde <span class="reprog-code">PuntosLocal = 0</span> y <span class="reprog-code">PuntosVisitante = 0</span>)
    de los torneos en estado <strong>Iniciado</strong> o <strong>Activo</strong>, a partir de la fecha indicada.
    Los partidos ya jugados (con puntos cargados) no se modifican.
</p>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger reprog-wrap">
        <strong>Error:</strong> <?php echo CHtml::encode($error); ?>
    </div>
<?php endif; ?>

<?php if (!empty($resultado)): ?>
    <div class="alert alert-success reprog-wrap">
        <h4 style="margin-top:0;">Reprogramacion aplicada</h4>
        <p>Se movieron <strong><?php echo (int)$resultado['cantidad']; ?></strong> partido(s) pendientes,
        sumando <strong><?php echo (int)$resultado['dias']; ?></strong> dia(s) a partir del
        <strong><?php echo CHtml::encode($resultado['fechaDesde']); ?></strong>,
        torneo(s) objetivo: <strong><?php echo CHtml::encode($nombresTorneosSeleccionados); ?></strong>.</p>
    </div>
<?php endif; ?>

<?php if (empty($previewRows) && empty($resultado)): ?>
    <div class="panel panel-default reprog-wrap">
        <div class="panel-heading">
            <strong>1. Elegi los torneos y los parametros</strong>
        </div>
        <div class="panel-body">
            <form method="post" action="<?php echo CHtml::encode(Yii::app()->createUrl('/fixture/CorrerFechas')); ?>">
                <div class="form-group">
                    <label style="font-weight: bold; margin-bottom: 10px;">Torneos a reprogramar</label>
                    <div class="reprog-list">
                        <?php if (empty($listaTorneos)): ?>
                            <p class="text-warning">No hay torneos en estado Iniciado o Activo.</p>
                        <?php else: ?>
                            <?php foreach ($listaTorneos as $id => $info): ?>
                                <label class="checkbox" style="display:block; margin-bottom: 8px; padding-left: 0;">
                                    <input type="checkbox" name="torneos[]" value="<?php echo (int)$id; ?>"
                                        style="margin-right: 8px; vertical-align: middle;"
                                        <?php echo in_array((int)$id, (array)$form['torneos'], true) ? 'checked' : ''; ?>>
                                    <span class="reprog-torneo-estado" title="Estado del torneo"><?php echo CHtml::encode($info['estado']); ?></span>
                                    <span style="vertical-align: middle; margin-left: 4px;"><?php echo CHtml::encode($info['nombre']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fechaDesde" style="font-weight: bold;">Fecha desde</label>
                            <input type="date" id="fechaDesde" name="fechaDesde" class="form-control"
                                   value="<?php echo CHtml::encode($form['fechaDesde']); ?>" required>
                            <span class="help-block" style="margin-bottom: 0;">Formato: YYYY-MM-DD. Solo se afectan fechas iguales o posteriores.</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dias" style="font-weight: bold;">Dias a sumar</label>
                            <input type="number" id="dias" name="dias" class="form-control"
                                   min="1" max="60" value="<?php echo (int)$form['dias']; ?>" required>
                            <span class="help-block" style="margin-bottom: 0;">Entre 1 y 60.</span>
                        </div>
                    </div>
                </div>

                <div class="reprog-actions">
                    <button type="submit" name="paso" value="preview" class="btn btn-primary" <?php echo empty($listaTorneos) ? 'disabled' : ''; ?>>
                        <i class="icon-eye-open icon-white"></i> Vista previa
                    </button>
                    <a href="<?php echo CHtml::encode(Yii::app()->createUrl('/fixture/admin')); ?>" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($previewRows) && empty($resultado)): ?>
    <div class="panel panel-warning reprog-wrap-preview">
        <div class="panel-heading">
            <strong>2. Confirmá los cambios antes de aplicar</strong>
        </div>
        <div class="panel-body">
            <form id="reprogForm" method="post" action="<?php echo CHtml::encode(Yii::app()->createUrl('/fixture/CorrerFechas')); ?>">
                <?php foreach ($form['torneos'] as $tid): ?>
                    <input type="hidden" name="torneos[]" value="<?php echo (int)$tid; ?>">
                <?php endforeach; ?>
                <input type="hidden" name="fechaDesde" value="<?php echo CHtml::encode($form['fechaDesde']); ?>">
                <input type="hidden" name="dias"       value="<?php echo (int)$form['dias']; ?>">
                <input type="hidden" name="paso"       value="apply">

                <p style="margin-top: 0;">
                    <strong><?php echo $cantPreview; ?></strong> partido(s) pendiente(s) seran reprogramados.
                    Solo se muestran los partidos sin puntos cargados
                    (<span class="reprog-code">PuntosLocal = 0</span> y <span class="reprog-code">PuntosVisitante = 0</span>).
                    Verificá las fechas nuevas antes de confirmar.
                </p>

                <table class="table table-striped table-bordered table-condensed" style="background: #fff;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th>Torneo</th>
                            <th style="width: 70px;">NFecha</th>
                            <th>Fecha actual</th>
                            <th>Fecha nueva</th>
                            <th>Hora</th>
                            <th>Local</th>
                            <th>Visitante</th>
                            <th>Cancha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grupoTorneo = null;
                        foreach ($previewRows as $r):
                            $esNuevoGrupo = ($grupoTorneo !== $r['Torneo']);
                            $grupoTorneo = $r['Torneo'];
                        ?>
                            <tr>
                                <td>
                                    <?php if ($esNuevoGrupo): ?>
                                        <strong><?php echo CHtml::encode($r['Torneo']); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo (int)$r['NFecha']; ?></td>
                                <td><?php echo CHtml::encode($r['FechaActual']); ?></td>
                                <td><strong style="color: #5cb85c;"><?php echo CHtml::encode($r['FechaNueva']); ?></strong></td>
                                <td><?php echo CHtml::encode($r['Hora']); ?></td>
                                <td><?php echo CHtml::encode($r['Local']); ?></td>
                                <td><?php echo CHtml::encode($r['Visitante']); ?></td>
                                <td><?php echo CHtml::encode($r['Cancha']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="reprog-actions">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#confirmModal">
                        <i class="icon-ok icon-white"></i> Confirmar y aplicar
                    </button>
                    <a href="<?php echo CHtml::encode(Yii::app()->createUrl('/fixture/CorrerFechas')); ?>" class="btn btn-default">
                        <i class="icon-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmacion Bootstrap 3 -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #fcf8e3; border-bottom: 2px solid #faebcc;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="confirmModalLabel" style="color: #8a6d3b;">
                        <i class="icon-warning-sign" style="color: #8a6d3b;"></i> Confirmar reprogramacion
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="text-align: center; margin-bottom: 20px;">
                        <div class="col-md-12">
                            <div class="reprog-modal-num"><?php echo $cantPreview; ?></div>
                            <div style="margin-top: 6px; color: #777; font-size: 13px;">partido(s) pendiente(s)</div>
                        </div>
                    </div>
                    <p>Vas a <strong>modificar la base de datos</strong>:
                       agregar <strong>+<?php echo (int)$form['dias']; ?> dia(s)</strong> a la columna <span class="reprog-code">Fecha</span>
                       de los <strong><?php echo $cantPreview; ?></strong> partido(s) listados arriba,
                       a partir del <strong><?php echo CHtml::encode($form['fechaDesde']); ?></strong>.</p>
                    <p style="margin-bottom: 0;">Esta operacion <strong>no se puede deshacer</strong> sin un dump previo de la base de datos.</p>
                </div>
                <div class="modal-footer" style="background: #f5f5f5;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="icon-remove"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btnAplicarModal">
                        <i class="icon-ok icon-white"></i> Si, aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var btn = document.getElementById('btnAplicarModal');
        if (btn) {
            btn.addEventListener('click', function() {
                document.getElementById('reprogForm').submit();
            });
        }
    })();
    </script>
<?php endif; ?>

<?php if (!empty($resultado)): ?>
    <div class="reprog-wrap">
        <a href="<?php echo CHtml::encode(Yii::app()->createUrl('/fixture/CorrerFechas')); ?>" class="btn btn-primary">
            <i class="icon-repeat icon-white"></i> Reprogramar otra fecha
        </a>
        <a href="<?php echo CHtml::encode(Yii::app()->createUrl('/fixture/admin')); ?>" class="btn btn-default">
            Ir al admin de Fixture
        </a>
    </div>
<?php endif; ?>
