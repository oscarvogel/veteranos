<?php $this->pageTitle = 'Ranking por equipos - Prode'; ?>

<div class="prode-container">
    <div class="prode-header">
        <h1>🛡️ Ranking por equipos</h1>
        <p>Suma de los puntos de todos los participantes de cada equipo.
        Actualizado despues de cada fecha publicada.</p>
        <p style="margin-top:12px;">
            <a class="btn btn-default" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.4);"
                href="<?php echo CHtml::normalizeUrl(array('prode/ranking')); ?>">&larr; Ranking individual</a>
        </p>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">Todavia no hay equipos con participantes. Pediles a los usuarios que elijan equipo al registrarse.</div>
    <?php else: ?>
        <?php
        $top3 = array_slice($rows, 0, 3);
        $visual = array();
        if (isset($top3[1])) $visual[] = array($top3[1], 'plata', 2);
        if (isset($top3[0])) $visual[] = array($top3[0], 'oro', 1);
        if (isset($top3[2])) $visual[] = array($top3[2], 'bronce', 3);
        if (!empty($visual)):
        ?>
            <div class="prode-podio">
                <?php foreach ($visual as $v):
                    list($r, $clase, $pos) = $v;
                ?>
                    <a class="prode-podio-card prode-podio-<?php echo $clase; ?>"
                        href="<?php echo CHtml::normalizeUrl(array('prode/equipo', 'idEquipo' => (int)$r['idEquipo'])); ?>"
                        style="text-decoration:none;color:inherit;">
                        <div class="prode-podio-pos"><?php echo $pos; ?>º</div>
                        <div class="prode-podio-name"><?php echo CHtml::encode($r['nombre']); ?></div>
                        <div style="font-size:12px;opacity:.8;margin-bottom:4px;">
                            <?php echo (int)$r['miembros']; ?> participante<?php echo $r['miembros'] === 1 ? '' : 's'; ?>
                        </div>
                        <div class="prode-podio-puntos"><?php echo (int)$r['puntos']; ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <table class="table prode-table">
            <thead>
                <tr>
                    <th>Pos</th>
                    <th>Equipo</th>
                    <th style="text-align: center;">Participantes</th>
                    <th style="text-align: center;">Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php $miIdEquipo = ProdeSession::user() ? (int)ProdeSession::user()->idEquipo : 0; ?>
                <?php foreach ($rows as $i => $r):
                    $pos = $i + 1;
                    $esMiEquipo = $miIdEquipo > 0 && $miIdEquipo === (int)$r['idEquipo'];
                    $claseFila = $esMiEquipo ? 'prode-row-you' : '';
                ?>
                    <tr class="<?php echo $claseFila; ?>">
                        <td class="prode-pos">
                            <?php if ($pos === 1): ?>🥇
                            <?php elseif ($pos === 2): ?>🥈
                            <?php elseif ($pos === 3): ?>🥉
                            <?php else: echo $pos; endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo CHtml::normalizeUrl(array('prode/equipo', 'idEquipo' => (int)$r['idEquipo'])); ?>"
                                style="color:#063f2a;font-weight:600;">
                                <?php echo CHtml::encode($r['nombre']); ?>
                            </a>
                            <?php if ($esMiEquipo): ?>
                                <small style="color:#5d6d67">(mi equipo)</small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;"><?php echo (int)$r['miembros']; ?></td>
                        <td style="text-align: center;"><strong><?php echo (int)$r['puntos']; ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="prode-foot">
            <strong>Como se calcula:</strong> suma de los puntos de todos los participantes activos del equipo. Los que no eligieron equipo no aparecen aca.
        </p>
    <?php endif; ?>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-rankingeq-css', <<<CSS
.prode-container { max-width: 920px; margin: 24px auto; padding: 0 16px; }
.prode-header { background: linear-gradient(135deg, #078a48 0%, #063f2a 100%); color: #fff; border-radius: 12px; padding: 24px 32px; margin-bottom: 24px; }
.prode-header h1 { margin: 0 0 4px; font-size: 28px; font-weight: 800; }
.prode-header p { margin: 0; opacity: 0.9; }
.prode-podio { display: flex; gap: 12px; margin-bottom: 24px; }
.prode-podio-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; background: #fff; }
.prode-podio-card:hover { transform: translateY(-2px); transition: transform .15s; }
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
@media (max-width: 767px) {
    .prode-podio { flex-direction: column; }
}
CSS
);
?>
