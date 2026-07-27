<?php $this->pageTitle = 'Admin Prode'; ?>

<div class="prode-container">
    <div class="prode-card">
        <h1>🔒 Admin Prode</h1>
        <p>Carg&aacute; los resultados reales de cada fecha y publicala para que sume puntos al ranking.</p>

        <p>
            <a class="btn btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/usuarios')); ?>">👥 Gestionar usuarios</a>
            &nbsp;
            <a class="btn btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/ranking')); ?>">🏆 Ver ranking</a>
        </p>

        <?php if (Yii::app()->user->hasFlash('prode_ok')): ?>
            <div class="alert alert-success"><?php echo Yii::app()->user->getFlash('prode_ok'); ?></div>
        <?php endif; ?>

        <?php if (empty($torneos)): ?>
            <p class="alert alert-info">No hay torneos iniciados o activos.</p>
        <?php else: ?>
            <?php foreach ($torneos as $t):
                $idT = (int)$t->idTorneo;
                $fechas = isset($fechasPorTorneo[$idT]) ? $fechasPorTorneo[$idT] : array();
                $publicadas = isset($publicadasPorTorneo[$idT]) ? $publicadasPorTorneo[$idT] : array();
            ?>
                <div class="prode-admin-torneo">
                    <h2><?php echo CHtml::encode($t->Nombre); ?></h2>
                    <?php if (empty($fechas)): ?>
                        <p class="text-muted">Sin fechas con partidos.</p>
                    <?php else: ?>
                        <table class="table prode-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th style="text-align: center;">Estado</th>
                                    <th style="text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fechas as $n): ?>
                                    <tr>
                                        <td>Fecha <?php echo (int)$n; ?></td>
                                        <td style="text-align: center;">
                                            <?php if (isset($publicadas[(int)$n])): ?>
                                                <span class="label label-success">Publicada</span>
                                                <br><small style="color:#5d6d67;"><?php echo CHtml::encode($publicadas[(int)$n]->publicadaEn); ?></small>
                                            <?php else: ?>
                                                <span class="label label-default">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-sm btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/loadResultados', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>">Cargar</a>
                                            <?php if (isset($publicadas[(int)$n])): ?>
                                                <button type="button" class="btn btn-sm btn-default"
                                                    data-toggle="modal" data-target="#prodeConfirmModal"
                                                    data-titulo="Re-calcular puntos"
                                                    data-texto="¿Querés re-calcular los puntos de esta fecha? Sirve si modificaste un resultado después de publicarla."
                                                    data-btn="Re-calcular" data-btn-class="btn-default"
                                                    data-href="<?php echo CHtml::normalizeUrl(array('prode/recalcular', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>">Re-calcular</button>
                                                <a class="btn btn-sm btn-success" href="<?php echo CHtml::normalizeUrl(array('prode/resultados', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>" target="_blank">Ver</a>
                                            <?php endif; ?>
                                            <?php if (!isset($publicadas[(int)$n])): ?>
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    data-toggle="modal" data-target="#prodeConfirmModal"
                                                    data-titulo="Publicar fecha <?php echo (int)$n; ?>"
                                                    data-texto="Después de publicarla, va a aparecer en el ranking y se calculan los puntos de todos los pronósticos. ¿Confirmás?"
                                                    data-btn="Publicar" data-btn-class="btn-warning"
                                                    data-href="<?php echo CHtml::normalizeUrl(array('prode/publicar', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>">Publicar</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-admin-css', <<<CSS
.prode-container { max-width: 1100px; margin: 24px auto; padding: 0 16px; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
.prode-card h1 { color: #063f2a; margin-top: 0; font-size: 24px; }
.prode-admin-torneo { border-top: 2px solid #edf3f0; padding-top: 16px; margin-top: 16px; }
.prode-admin-torneo h2 { color: #063f2a; font-size: 18px; margin: 0 0 12px; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
CSS
);
?>

<!-- Modal generico de confirmacion -->
<div class="modal fade" id="prodeConfirmModal" tabindex="-1" role="dialog" aria-labelledby="prodeConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #063f2a; color: #fff; border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="prodeConfirmModalLabel" style="font-weight:700;">Confirmar</h4>
            </div>
            <div class="modal-body" id="prodeConfirmModalBody" style="font-size:15px;color:#063f2a;">
                ¿Confirmás?
            </div>
            <div class="modal-footer" style="border-top: none;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a id="prodeConfirmModalBtn" href="#" class="btn btn-warning">Confirmar</a>
            </div>
        </div>
    </div>
</div>

<?php
Yii::app()->clientScript->registerScript('prode-confirm-modal', <<<JS
jQuery(function($){
    $('#prodeConfirmModal').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        var titulo = btn.data('titulo') || 'Confirmar';
        var texto = btn.data('texto') || '¿Confirmás?';
        var href = btn.data('href') || '#';
        var btnLabel = btn.data('btn') || 'Confirmar';
        var btnClass = btn.data('btn-class') || 'btn-warning';
        var \$modal = $(this);
        \$modal.find('.modal-title').text(titulo);
        \$modal.find('.modal-body').text(texto);
        var \$confirm = \$modal.find('#prodeConfirmModalBtn');
        \$confirm.text(btnLabel).attr('href', href);
        \$confirm.removeClass('btn-warning btn-default btn-success btn-danger').addClass(btnClass);
    });
});
JS
, CClientScript::POS_END);
?>
