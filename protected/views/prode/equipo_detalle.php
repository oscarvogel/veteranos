<?php
$this->pageTitle = $equipo->Nombre . ' - Prode';
$esMiEquipo = ProdeSession::user() !== null && (int)ProdeSession::user()->idEquipo === (int)$equipo->idEquipo;
$puntosEquipo = (int)ProdeUsuario::totalPuntosEquipo($equipo->idEquipo);
$cantMiembros = (int)ProdeUsuario::countMiembrosEquipo($equipo->idEquipo);
?>

<div class="prode-container">
    <div class="prode-header">
        <h1>🛡️ <?php echo CHtml::encode($equipo->Nombre); ?></h1>
        <p>
            <?php echo $cantMiembros; ?> participante<?php echo $cantMiembros === 1 ? '' : 's'; ?>
            &nbsp;·&nbsp;
            <strong><?php echo $puntosEquipo; ?> puntos</strong> en total
            <?php if ($esMiEquipo): ?>
                &nbsp;·&nbsp; <em>(es tu equipo)</em>
            <?php endif; ?>
        </p>
        <p style="margin-top:12px;">
            <a class="btn btn-default" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.4);"
                href="<?php echo CHtml::normalizeUrl(array('prode/rankingEquipos')); ?>">&larr; Ranking por equipos</a>
            <a class="btn btn-default" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.4);margin-left:8px;"
                href="<?php echo CHtml::normalizeUrl(array('prode/ranking')); ?>">Ranking individual</a>
        </p>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">Este equipo todavia no tiene participantes registrados en el prode.</div>
    <?php else: ?>
        <div class="prode-card">
            <h2>Participantes</h2>
            <p>Ordenados por puntos totales. El desempate es por resultados exactos y despues nombre alfabetico.</p>

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
                            </td>
                            <td style="text-align: center;"><strong><?php echo (int)$r['puntos']; ?></strong></td>
                            <td style="text-align: center;"><?php echo (int)$r['exactos']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-equipo-css', <<<CSS
.prode-container { max-width: 920px; margin: 24px auto; padding: 0 16px; }
.prode-header { background: linear-gradient(135deg, #078a48 0%, #063f2a 100%); color: #fff; border-radius: 12px; padding: 24px 32px; margin-bottom: 24px; }
.prode-header h1 { margin: 0 0 4px; font-size: 28px; font-weight: 800; }
.prode-header p { margin: 0; opacity: 0.9; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
.prode-card h2 { color: #063f2a; margin-top: 0; font-size: 20px; font-weight: 800; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
.prode-row-you { background: #eaf5ef; font-weight: 600; }
.prode-pos { font-weight: 800; color: #063f2a; width: 60px; font-size: 18px; }
CSS
);
?>
