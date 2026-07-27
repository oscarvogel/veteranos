<?php $this->pageTitle = 'Ranking - Prode'; ?>

<div class="prode-container">
    <div class="prode-header">
        <h1>🏆 Ranking Prode</h1>
        <p>Todos los participantes ordenados por puntos. Actualizado despues de cada fecha publicada.</p>
        <p style="margin-top:12px;">
            <a class="btn btn-default" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.4);"
                href="<?php echo CHtml::normalizeUrl(array('prode/rankingEquipos')); ?>">Ver ranking por equipos &rarr;</a>
        </p>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">Aun no hay participantes. <a href="<?php echo CHtml::normalizeUrl(array('prode/register')); ?>">Se el primero</a>.</div>
    <?php else: ?>
        <div class="prode-podio">
            <?php
            $top3 = array_slice($rows, 0, 3);
            $ordenarTop = array(1 => 'oro', 2 => 'plata', 3 => 'bronce');
            // Reordenar visualmente: plata, oro, bronce
            $visual = array();
            if (isset($top3[1])) $visual[] = array($top3[1], 'plata', 2);
            if (isset($top3[0])) $visual[] = array($top3[0], 'oro', 1);
            if (isset($top3[2])) $visual[] = array($top3[2], 'bronce', 3);
            foreach ($visual as $v):
                list($r, $clase, $pos) = $v;
                $u = $r['usuario'];
                $eqLabel = $u->getEquipoLabel();
                $eqClass = $u->equipo ? 'prode-eq' : 'prode-eq prode-eq-none';
            ?>
                <div class="prode-podio-card prode-podio-<?php echo $clase; ?>">
                    <div class="prode-podio-pos"><?php echo $pos; ?>º</div>
                    <div class="prode-podio-name"><?php echo CHtml::encode($u->nombre); ?></div>
                    <div class="<?php echo $eqClass; ?>"><?php echo CHtml::encode($eqLabel); ?></div>
                    <div class="prode-podio-puntos"><?php echo (int)$r['puntos']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <table class="table prode-table">
            <thead>
                <tr>
                    <th>Pos</th>
                    <th>Participante</th>
                    <th style="text-align: center;">Puntos</th>
                    <th style="text-align: center;">Exactos</th>
                </tr>
            </thead>
            <tbody>
                <?php $yoId = ProdeSession::getId(); ?>
                <?php foreach ($rows as $i => $r):
                    $pos = $i + 1;
                    $u = $r['usuario'];
                    $esYo = $yoId !== null && (int)$u->idProdeUsuario === $yoId;
                    $claseFila = $esYo ? 'prode-row-you' : '';
                    $eqLabel = $u->getEquipoLabel();
                    $eqClass = $u->equipo ? 'prode-eq' : 'prode-eq prode-eq-none';
                ?>
                    <tr class="<?php echo $claseFila; ?>">
                        <td class="prode-pos">
                            <?php if ($pos === 1): ?>🥇
                            <?php elseif ($pos === 2): ?>🥈
                            <?php elseif ($pos === 3): ?>🥉
                            <?php else: echo $pos; endif; ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($u->nombre); ?>
                            <?php if ($esYo): ?> <small style="color:#5d6d67">(vos)</small><?php endif; ?>
                            <?php if ($u->equipo): ?>
                                <a class="<?php echo $eqClass; ?>" style="text-decoration:none;"
                                    href="<?php echo CHtml::normalizeUrl(array('prode/equipo', 'idEquipo' => (int)$u->idEquipo)); ?>"
                                    title="Ver equipo"><?php echo CHtml::encode($eqLabel); ?></a>
                            <?php else: ?>
                                <span class="<?php echo $eqClass; ?>"><?php echo CHtml::encode($eqLabel); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;"><strong><?php echo (int)$r['puntos']; ?></strong></td>
                        <td style="text-align: center;"><?php echo (int)$r['exactos']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="prode-foot">
            <strong>Desempate:</strong> 1º Puntos totales, 2º Cantidad de resultados exactos, 3º Nombre alfabetico.
        </p>
    <?php endif; ?>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-ranking-css', <<<CSS
.prode-container { max-width: 920px; margin: 24px auto; padding: 0 16px; }
.prode-header { background: linear-gradient(135deg, #078a48 0%, #063f2a 100%); color: #fff; border-radius: 12px; padding: 24px 32px; margin-bottom: 24px; }
.prode-header h1 { margin: 0 0 4px; font-size: 28px; font-weight: 800; }
.prode-header p { margin: 0; opacity: 0.9; }
.prode-podio { display: flex; gap: 12px; margin-bottom: 24px; }
.prode-podio-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; background: #fff; }
.prode-podio-oro { background: linear-gradient(180deg, #fef3c7 0%, #fcd34d 100%); border-color: #f59e0b; }
.prode-podio-plata { background: linear-gradient(180deg, #f1f5f9 0%, #cbd5e1 100%); border-color: #94a3b8; }
.prode-podio-bronce { background: linear-gradient(180deg, #fed7aa 0%, #fb923c 100%); border-color: #ea580c; }
.prode-podio-pos { font-size: 32px; font-weight: 800; }
.prode-podio-name { font-weight: 700; margin: 4px 0; }
.prode-podio-puntos { font-size: 24px; font-weight: 800; color: #063f2a; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
.prode-row-you { background: #eaf5ef; font-weight: 600; }
.prode-pos { font-weight: 800; color: #063f2a; width: 60px; font-size: 18px; }
.prode-foot { color: #5d6d67; font-size: 13px; margin-top: 16px; }
.prode-eq { display: inline-block; margin-left: 6px; padding: 2px 8px; background: #eaf5ef; color: #063f2a; border-radius: 10px; font-size: 11px; font-weight: 600; }
.prode-eq-none { background: #f1f5f9; color: #5d6d67; font-weight: 500; }
@media (max-width: 767px) {
    .prode-podio { flex-direction: column; }
}
CSS
);
?>
