<?php $this->pageTitle = 'Pronostico - ' . $torneo->Nombre . ' - Fecha ' . $fecha; ?>

<div class="prode-container">
    <div class="prode-card">
        <p><a href="<?php echo CHtml::normalizeUrl(array('prode/panel')); ?>">&larr; Volver a mi panel</a></p>
        <h1>Pronostico: <?php echo CHtml::encode($torneo->Nombre); ?> · Fecha <?php echo (int)$fecha; ?></h1>
        <p class="lead">Cargá tu pronostico. <strong><?php echo (int)ProdeUsuario::PUNTOS_EXACTO; ?> puntos</strong> si acert&aacute;s el resultado exacto, <strong><?php echo (int)ProdeUsuario::PUNTOS_SIGNO; ?> puntos</strong> si solo acert&aacute;s el signo (ganador/empate).</p>

        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo CHtml::encode($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($lock):
            $pp = $partidos[0];
            $ayer = date('Y-m-d', strtotime($pp->Fecha . ' -1 day'));
            $ayerFmt = date('d/m/Y', strtotime($ayer));
        ?>
            <div class="alert alert-warning">
                <strong>🔒 Fecha bloqueada.</strong> Es el d&iacute;a del partido (o ya pas&oacute;).
                No se puede modificar el pron&oacute;stico. Se pod&iacute;a editar hasta el <?php echo $ayerFmt; ?>.
            </div>
        <?php else:
            $pp = $partidos[0];
            $ayer = date('d/m/Y', strtotime($pp->Fecha . ' -1 day'));
            $diaPartido = date('d/m/Y', strtotime($pp->Fecha));
        ?>
            <div class="alert alert-info">
                <strong>⏰ Record&aacute;:</strong> pod&eacute;s editar tu pron&oacute;stico hasta el <?php echo $ayer; ?>
                (inclusive). El <?php echo $diaPartido; ?> ya est&aacute; bloqueado desde las 00:00.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo CHtml::normalizeUrl(array('prode/predict', 'idTorneo' => (int)$torneo->idTorneo, 'fecha' => (int)$fecha)); ?>">
            <table class="table prode-table">
                <thead>
                    <tr>
                        <th>Local</th>
                        <th style="width: 90px; text-align: center;">Goles</th>
                        <th style="width: 40px;"></th>
                        <th style="width: 90px; text-align: center;">Goles</th>
                        <th>Visitante</th>
                        <th style="width: 110px; text-align: center;">Ayuda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $p):
                        $idFix = (int)$p->idFixture;
                        $esLibre = (int)$p->Visitante === 0;
                        $local = $p->local ? $p->local->Nombre : '?';
                        $visit = $p->visitante ? $p->visitante->Nombre : '?';
                        $pred = isset($predPorFixture[$idFix]) ? $predPorFixture[$idFix] : null;
                        $gl = $pred ? (int)$pred->golesLocal : '';
                        $gv = $pred ? (int)$pred->golesVisitante : '';
                        $prob = isset($probabilidades[$idFix]) ? $probabilidades[$idFix] : null;
                    ?>
                        <tr<?php echo $esLibre ? ' class="prode-libre"' : ''; ?>>
                            <td><?php echo CHtml::encode($local); ?></td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?>
                                    <span style="color:#b04141;">—</span>
                                <?php else: ?>
                                    <input type="number" class="form-control prode-goles"
                                        name="predicciones[<?php echo $idFix; ?>][golesLocal]"
                                        min="0" max="20" value="<?php echo $gl; ?>"<?php echo $lock ? ' disabled' : ''; ?>>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-weight: 800; color: #5d6d67;">vs</td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?>
                                    <span style="color:#b04141;">—</span>
                                <?php else: ?>
                                    <input type="number" class="form-control prode-goles"
                                        name="predicciones[<?php echo $idFix; ?>][golesVisitante]"
                                        min="0" max="20" value="<?php echo $gv; ?>"<?php echo $lock ? ' disabled' : ''; ?>>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($esLibre): ?>
                                    <em>Libre</em>
                                <?php else: ?>
                                    <?php echo CHtml::encode($visit); ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($esLibre || $prob === null): ?>
                                    <span style="color:#94a3b8;font-size:12px;">—</span>
                                <?php else: ?>
                                    <button type="button" class="btn btn-xs btn-default prode-ayuda-btn"
                                        data-target="prode-ayuda-<?php echo $idFix; ?>"
                                        title="Ver probabilidades estimadas">
                                        📊 Ayuda
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!$esLibre && $prob !== null):
                            $max = max($prob['local'], $prob['empate'], $prob['visitante']);
                            $m = $prob['muestras'];
                            $notaHist = $m['historico'] > 0
                                ? "Hist&oacute;rico: {$m['historico']} partidos entre ellos (V{$m['hist_local']} / E{$m['hist_empate']} / V{$m['hist_visit']})."
                                : "Sin partidos previos entre estos dos equipos.";
                            $notaAct = "Rendimiento actual: local {$m['local_act']} partidos, visitante {$m['visit_act']} partidos.";
                        ?>
                            <tr class="prode-ayuda-row" id="prode-ayuda-<?php echo $idFix; ?>" style="display:none;">
                                <td colspan="6" style="background: #f8f9fa; padding: 12px 16px;">
                                    <div class="prode-ayuda-grid">
                                        <div class="prode-ayuda-cell<?php echo $prob['local'] === $max ? ' prode-ayuda-top' : ''; ?>">
                                            <div class="prode-ayuda-label">Local</div>
                                            <div class="prode-ayuda-pct"><?php echo (int)$prob['local']; ?>%</div>
                                        </div>
                                        <div class="prode-ayuda-cell<?php echo $prob['empate'] === $max ? ' prode-ayuda-top' : ''; ?>">
                                            <div class="prode-ayuda-label">Empate</div>
                                            <div class="prode-ayuda-pct"><?php echo (int)$prob['empate']; ?>%</div>
                                        </div>
                                        <div class="prode-ayuda-cell<?php echo $prob['visitante'] === $max ? ' prode-ayuda-top' : ''; ?>">
                                            <div class="prode-ayuda-label">Visitante</div>
                                            <div class="prode-ayuda-pct"><?php echo (int)$prob['visitante']; ?>%</div>
                                        </div>
                                    </div>
                                    <p style="font-size:11px;color:#5d6d67;margin:8px 0 0;">
                                        <em><?php echo $notaHist; ?> <?php echo $notaAct; ?> C&aacute;lculo: 60% hist&oacute;rico + 40% rendimiento actual (con 10% de ventaja por local&iacute;a).</em>
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!$lock): ?>
                <div style="text-align: right; margin-top: 16px;">
                    <button type="submit" class="btn btn-primary btn-lg">💾 Guardar pron&oacute;stico</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-predict-css', <<<CSS
.prode-container { max-width: 960px; margin: 24px auto; padding: 0 16px; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
.prode-card h1 { color: #063f2a; margin-top: 0; font-size: 22px; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
.prode-table tbody td { vertical-align: middle; }
.prode-libre { background: #fff5f5; color: #b04141; }
.prode-goles { display: inline-block; width: 70px; text-align: center; font-weight: 700; font-size: 18px; }
.prode-ayuda-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
.prode-ayuda-cell { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 8px; text-align: center; }
.prode-ayuda-top { background: #eaf5ef; border-color: #078a48; box-shadow: 0 1px 4px rgba(7,138,72,.2); }
.prode-ayuda-label { font-size: 11px; color: #5d6d67; text-transform: uppercase; letter-spacing: .04em; }
.prode-ayuda-top .prode-ayuda-label { color: #063f2a; font-weight: 700; }
.prode-ayuda-pct { font-size: 22px; font-weight: 800; color: #063f2a; margin-top: 2px; }
CSS
);
Yii::app()->clientScript->registerScript('prode-ayuda-toggle', <<<JS
jQuery(function($){
    $('.prode-ayuda-btn').on('click', function(){
        var target = $(this).data('target');
        var \$row = $('#' + target);
        if (\$row.length) {
            \$row.toggle();
        }
    });
});
JS
, CClientScript::POS_END);
?>
