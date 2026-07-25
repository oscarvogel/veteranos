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
                                                <a class="btn btn-sm btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/recalcular', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>" onclick="return confirm('Recalcular puntos de esta fecha?');">Re-calcular</a>
                                                <a class="btn btn-sm btn-success" href="<?php echo CHtml::normalizeUrl(array('prode/resultados', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>" target="_blank">Ver</a>
                                            <?php endif; ?>
                                            <?php if (!isset($publicadas[(int)$n])): ?>
                                                <a class="btn btn-sm btn-warning" href="<?php echo CHtml::normalizeUrl(array('prode/publicar', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>" onclick="return confirm('Publicar esta fecha? Despues se va a ver en el ranking.');">Publicar</a>
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
