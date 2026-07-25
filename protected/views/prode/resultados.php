<?php $this->pageTitle = 'Fecha ' . $fecha . ' - ' . $torneo->Nombre; ?>

<div class="prode-container">
    <div class="prode-card">
        <h1><?php echo CHtml::encode($torneo->Nombre); ?> · Fecha <?php echo (int)$fecha; ?></h1>
        <p class="lead">Resultados reales y pron&oacute;sticos de la fecha (publicada).</p>

        <table class="table prode-table">
            <thead>
                <tr>
                    <th>Partido</th>
                    <th style="text-align: center;">Real</th>
                    <th style="text-align: center;">Pron&oacute;sticos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidos as $p):
                    $esLibre = (int)$p->Visitante === 0;
                    $local = $p->local ? $p->local->Nombre : '?';
                    $visit = $p->visitante ? $p->visitante->Nombre : '?';
                    $preds = isset($predicciones[(int)$p->idFixture]) ? $predicciones[(int)$p->idFixture] : array();
                ?>
                    <tr<?php echo $esLibre ? ' class="prode-libre"' : ''; ?>>
                        <td>
                            <strong><?php echo CHtml::encode($local); ?></strong>
                            <?php echo $esLibre ? '' : ' vs ' . CHtml::encode($visit); ?>
                            <?php if ($esLibre): ?> <em>(Libre)</em><?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($esLibre): ?> —
                            <?php elseif ($p->GolLocal === null): ?> <span class="label label-default">S/D</span>
                            <?php else: ?> <strong><?php echo (int)$p->GolLocal; ?> - <?php echo (int)$p->GolVisitante; ?></strong>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($esLibre || empty($preds)): ?>
                                <span class="text-muted">—</span>
                            <?php else: ?>
                                <?php foreach ($preds as $pred):
                                    $u = $pred->usuario;
                                    $pts = $pred->puntos;
                                    $labelClass = $pts === null ? 'default' : ($pts >= 3 ? 'success' : ($pts >= 1 ? 'warning' : 'danger'));
                                    $labelTxt = $pts === null ? '—' : ($pts >= 3 ? '3' : ($pts >= 1 ? '1' : '0'));
                                ?>
                                    <span class="prode-pred-pill" title="<?php echo CHtml::encode($u ? $u->nombre : '?'); ?> · <?php echo (int)$pred->golesLocal; ?>-<?php echo (int)$pred->golesVisitante; ?> · <?php echo (int)$pts; ?> pts">
                                        <?php echo CHtml::encode($u ? $u->nombre : '?'); ?>
                                        <span class="label label-<?php echo $labelClass; ?>"><?php echo (int)$pred->golesLocal; ?>-<?php echo (int)$pred->golesVisitante; ?> (<?php echo $labelTxt; ?>)</span>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-resultados-css', <<<CSS
.prode-container { max-width: 960px; margin: 24px auto; padding: 0 16px; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
.prode-card h1 { color: #063f2a; margin-top: 0; font-size: 22px; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
.prode-libre { background: #fff5f5; }
.prode-pred-pill { display: inline-block; margin: 2px 4px 2px 0; padding: 2px 8px; background: #f8f9fa; border-radius: 4px; font-size: 12px; }
.prode-pred-pill .label { margin-left: 4px; font-size: 10px; }
CSS
);
?>
