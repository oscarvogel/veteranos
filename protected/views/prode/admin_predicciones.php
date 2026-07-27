<?php $this->pageTitle = 'Predicciones - Prode Admin'; ?>

<div class="prode-container">
    <div class="prode-card">
        <p><a href="<?php echo CHtml::normalizeUrl(array('prode/admin')); ?>">&larr; Volver al admin</a></p>
        <h1>👀 Predicciones de los usuarios</h1>
        <p>Eleg&iacute; un torneo y una fecha para ver qu&eacute; pronostic&oacute; cada usuario en cada partido.</p>

        <form method="get" action="<?php echo Yii::app()->createUrl('/prode/predicciones'); ?>" class="form-inline" style="margin-bottom: 20px;">
            <div class="form-group" style="margin-right: 8px;">
                <label for="idTorneo" style="margin-right:6px;">Torneo:</label>
                <select class="form-control" id="idTorneo" name="idTorneo" onchange="this.form.submit()">
                    <option value="">-- Eleg&iacute; uno --</option>
                    <?php foreach ($torneos as $t): ?>
                        <option value="<?php echo (int)$t->idTorneo; ?>"
                            <?php echo ((int)$t->idTorneo === (int)$idTorneoSel) ? 'selected' : ''; ?>>
                            <?php echo CHtml::encode($t->Nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-right: 8px;">
                <label for="fecha" style="margin-right:6px;">Fecha:</label>
                <select class="form-control" id="fecha" name="fecha" onchange="this.form.submit()">
                    <option value="">-- Eleg&iacute; una --</option>
                    <?php if ($idTorneoSel !== null && !empty($fechasPorTorneo[$idTorneoSel])): ?>
                        <?php foreach ($fechasPorTorneo[$idTorneoSel] as $n): ?>
                            <option value="<?php echo (int)$n; ?>"
                                <?php echo ((int)$n === (int)$fechaSel) ? 'selected' : ''; ?>>
                                Fecha <?php echo (int)$n; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <noscript><button type="submit" class="btn btn-primary">Ver</button></noscript>
        </form>

        <?php if (empty($partidos)): ?>
            <div class="alert alert-info">Eleg&iacute; un torneo y una fecha para ver las predicciones.</div>
        <?php else:
            // Calcular cuantos usuarios ya cargaron al menos 1 prediccion
            $cargaron = 0;
            foreach ($usuarios as $u) {
                if (isset($predPorUsuarioYFixture[(int)$u->idProdeUsuario]) && !empty($predPorUsuarioYFixture[(int)$u->idProdeUsuario])) {
                    $cargaron++;
                }
            }
            $totalU = count($usuarios);
            $totalP = count($partidos);
        ?>
            <div class="prode-pred-resumen">
                <p>
                    <strong><?php echo $cargaron; ?>/<?php echo $totalU; ?></strong> usuarios cargaron al menos un pron&oacute;stico
                    &nbsp;·&nbsp;
                    <strong><?php echo $totalP; ?></strong> partido<?php echo $totalP === 1 ? '' : 's'; ?> en la fecha
                    <?php if ($publicada): ?>
                        &nbsp;·&nbsp; <span class="label label-success">Publicada</span>
                    <?php else: ?>
                        &nbsp;·&nbsp; <span class="label label-default">Pendiente</span>
                    <?php endif; ?>
                </p>
            </div>

            <div style="overflow-x: auto;">
                <table class="table prode-table prode-pred-tabla">
                    <thead>
                        <tr>
                            <th class="prode-pred-th-user">Usuario</th>
                            <?php foreach ($partidos as $p):
                                $esLibre = (int)$p->Visitante === 0;
                                $localN = $p->local ? $p->local->Nombre : '?';
                                $visitN = $p->visitante ? $p->visitante->Nombre : '?';
                            ?>
                                <th class="prode-pred-th-fix" title="<?php echo CHtml::encode($localN . ' vs ' . $visitN); ?>">
                                    <?php if ($esLibre): ?>
                                        <span class="prode-pred-libre">Libre</span>
                                    <?php else: ?>
                                        <span class="prode-pred-l"><?php echo CHtml::encode($localN); ?></span>
                                        <span class="prode-pred-vs">vs</span>
                                        <span class="prode-pred-v"><?php echo CHtml::encode($visitN); ?></span>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u):
                            $eqLabel = $u->getEquipoLabel();
                        ?>
                            <tr>
                                <td class="prode-pred-td-user">
                                    <strong><?php echo CHtml::encode($u->nombre); ?></strong>
                                    <br><small style="color:#5d6d67;"><?php echo CHtml::encode($eqLabel); ?></small>
                                </td>
                                <?php foreach ($partidos as $p):
                                    $idFix = (int)$p->idFixture;
                                    $esLibre = (int)$p->Visitante === 0;
                                    $pred = isset($predPorUsuarioYFixture[(int)$u->idProdeUsuario][$idFix])
                                        ? $predPorUsuarioYFixture[(int)$u->idProdeUsuario][$idFix]
                                        : null;
                                    $puntos = $pred ? (int)$pred->puntos : null;
                                    $clase = 'prode-pred-cell';
                                    if ($esLibre) $clase .= ' prode-pred-libre-cell';
                                    elseif ($pred === null) $clase .= ' prode-pred-empty';
                                    elseif ($puntos === ProdeUsuario::PUNTOS_EXACTO) $clase .= ' prode-pred-exacto';
                                    elseif ($puntos === ProdeUsuario::PUNTOS_SIGNO) $clase .= ' prode-pred-signo';
                                    elseif ($puntos === 0) $clase .= ' prode-pred-fallo';
                                ?>
                                    <td class="<?php echo $clase; ?>">
                                        <?php if ($esLibre): ?>
                                            <span style="color:#b04141;">—</span>
                                        <?php elseif ($pred === null): ?>
                                            <span class="prode-pred-no">—</span>
                                        <?php else: ?>
                                            <strong><?php echo (int)$pred->golesLocal; ?> - <?php echo (int)$pred->golesVisitante; ?></strong>
                                            <?php if ($publicada && $puntos !== null): ?>
                                                <br><small class="prode-pred-pts"><?php echo $puntos; ?>p</small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="prode-foot">
                <strong>Leyenda:</strong>
                <span class="prode-pred-cell prode-pred-exacto" style="display:inline-block; padding: 2px 6px; margin: 0 4px;">3-1</span> <?php echo (int)ProdeUsuario::PUNTOS_EXACTO; ?> puntos (exacto)
                &nbsp;·&nbsp;
                <span class="prode-pred-cell prode-pred-signo" style="display:inline-block; padding: 2px 6px; margin: 0 4px;">2-1</span> <?php echo (int)ProdeUsuario::PUNTOS_SIGNO; ?> puntos (signo)
                &nbsp;·&nbsp;
                <span class="prode-pred-cell prode-pred-fallo" style="display:inline-block; padding: 2px 6px; margin: 0 4px;">1-3</span> 0 puntos (fallo)
                &nbsp;·&nbsp;
                <span class="prode-pred-cell prode-pred-empty" style="display:inline-block; padding: 2px 6px; margin: 0 4px;">—</span> sin cargar
            </p>
        <?php endif; ?>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-pred-css', <<<CSS
.prode-container { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
.prode-card h1 { color: #063f2a; margin-top: 0; font-size: 22px; }
.prode-pred-resumen { background: #f8f9fa; border: 1px solid #edf3f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; }
.prode-pred-resumen p { margin: 0; color: #063f2a; }
.prode-pred-tabla { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; font-size: 13px; }
.prode-pred-tabla thead th { background: #f8f9fa; color: #5d6d67; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; border-bottom: 2px solid #e2e8f0; vertical-align: middle; }
.prode-pred-tabla tbody td { vertical-align: middle; }
.prode-pred-th-user { min-width: 180px; }
.prode-pred-th-fix { min-width: 110px; max-width: 140px; }
.prode-pred-th-fix .prode-pred-l { font-weight: 700; color: #063f2a; }
.prode-pred-th-fix .prode-pred-v { font-weight: 700; color: #5d6d67; }
.prode-pred-th-fix .prode-pred-vs { color: #94a3b8; margin: 0 4px; font-size: 10px; }
.prode-pred-libre { color: #b04141; font-style: italic; }
.prode-pred-td-user { background: #fafbfc; }
.prode-pred-cell { text-align: center; padding: 8px 4px; }
.prode-pred-empty { background: #fafbfc; }
.prode-pred-no { color: #cbd5e1; font-weight: 700; }
.prode-pred-exacto { background: #d1fae5; color: #063f2a; }
.prode-pred-signo { background: #fef3c7; color: #92400e; }
.prode-pred-fallo { background: #fee2e2; color: #b91c1c; }
.prode-pred-libre-cell { background: #fff5f5; }
.prode-pred-pts { color: #5d6d67; font-size: 10px; }
.prode-foot { color: #5d6d67; font-size: 12px; margin-top: 16px; }
@media (max-width: 767px) {
    .prode-pred-tabla { font-size: 11px; }
    .prode-pred-th-user { min-width: 120px; }
    .prode-pred-th-fix { min-width: 80px; }
}
CSS
);
?>
