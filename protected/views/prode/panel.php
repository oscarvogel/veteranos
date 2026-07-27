<?php
$this->pageTitle = 'Mi panel - Prode';
$user = ProdeSession::user();
$totalPuntos = $user->totalPuntos();
$totalExactos = $user->countExactos();
$tieneEquipo = (int)$user->idEquipo > 0 && $user->equipo !== null;
?>

<div class="prode-container">
    <div class="prode-welcome">
        <div>
            <h1>Hola, <?php echo CHtml::encode($user->nombre); ?> 👋</h1>
            <p>
                Tus puntos: <strong><?php echo (int)$totalPuntos; ?></strong>
                &nbsp;·&nbsp;
                Exactos: <strong><?php echo (int)$totalExactos; ?></strong>
                &nbsp;·&nbsp;
                Equipo: <strong><?php echo CHtml::encode($user->getEquipoLabel()); ?></strong>
            </p>
        </div>
        <div class="prode-welcome-cta">
            <a class="btn btn-primary" href="<?php echo CHtml::normalizeUrl(array('prode/ranking')); ?>">Ranking individual</a>
            <a class="btn btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/rankingEquipos')); ?>">Ranking por equipos</a>
            <a class="btn btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/logout')); ?>">Cerrar sesion</a>
        </div>
    </div>

    <div class="prode-card">
        <h2>Tu equipo</h2>
        <?php if ($tieneEquipo): ?>
            <p>Estas jugando para <strong><?php echo CHtml::encode($user->equipo->Nombre); ?></strong>.
            Los puntos que sumes en tus predicciones tambien suman al ranking de tu equipo.</p>
        <?php else: ?>
            <p>Aun no elegiste equipo. Si queres, podes sumarte a uno y competir en el ranking por equipos tambien.</p>
        <?php endif; ?>
        <form method="post" action="<?php echo CHtml::normalizeUrl(array('prode/cambiarEquipo')); ?>" class="form-inline">
            <div class="form-group" style="margin-right: 8px;">
                <label class="sr-only" for="idEquipo">Equipo</label>
                <select class="form-control" id="idEquipo" name="idEquipo">
                    <option value="0" <?php echo !$tieneEquipo ? 'selected' : ''; ?>>-- Sin equipo --</option>
                    <?php
                    $criteria = new CDbCriteria;
                    $criteria->order = 'Nombre ASC';
                    $equiposList = Equipos::model()->findAll($criteria);
                    foreach ($equiposList as $eq) {
                        $sel = ((int)$user->idEquipo === (int)$eq->idEquipo) ? 'selected' : '';
                        echo '<option value="' . (int)$eq->idEquipo . '" ' . $sel . '>' . CHtml::encode($eq->Nombre) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $tieneEquipo ? 'Cambiar' : 'Sumarme'; ?></button>
            <?php if ($tieneEquipo): ?>
                <a class="btn btn-default" style="margin-left:8px;"
                    href="<?php echo CHtml::normalizeUrl(array('prode/equipo', 'idEquipo' => (int)$user->idEquipo)); ?>">Ver mi equipo</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (Yii::app()->user->hasFlash('prode_ok')): ?>
        <div class="alert alert-success"><?php echo Yii::app()->user->getFlash('prode_ok'); ?></div>
    <?php endif; ?>

    <div class="prode-card">
        <h2>Hacer pronostico</h2>
        <p>Elegi una fecha de un torneo iniciado/activo y cargá tu pronostico.</p>

        <?php if (empty($torneos)): ?>
            <p class="alert alert-info">No hay torneos iniciados o activos en este momento.</p>
        <?php else: ?>
            <?php foreach ($torneos as $t): ?>
                <?php
                $idT = (int)$t->idTorneo;
                $fechas = isset($fechasPorTorneo[$idT]) ? $fechasPorTorneo[$idT] : array();
                ?>
                <div class="prode-torneo-block">
                    <h3><?php echo CHtml::encode($t->Nombre); ?></h3>
                    <?php if (empty($fechas)): ?>
                        <p class="text-muted">Aun no hay fechas con partidos cargados.</p>
                    <?php else: ?>
                        <div class="prode-fecha-buttons">
                            <?php foreach ($fechas as $n): ?>
                                <a class="btn btn-default prode-fecha-btn"
                                    href="<?php echo CHtml::normalizeUrl(array('prode/predict', 'idTorneo' => $idT, 'fecha' => (int)$n)); ?>">
                                    Fecha <?php echo (int)$n; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($historial)): ?>
        <div class="prode-card">
            <h2>Tu historial reciente</h2>
            <table class="table prode-table">
                <thead>
                    <tr>
                        <th>Partido</th>
                        <th style="text-align: center;">Tu pronostico</th>
                        <th style="text-align: center;">Resultado</th>
                        <th style="text-align: center;">Puntos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h):
                        $p = $h['partido'];
                        $pred = $h['pred'];
                        $local = $pred->partido && $pred->partido->local ? $pred->partido->local->Nombre : '?';
                        $visit = $pred->partido && $pred->partido->visitante ? $pred->partido->visitante->Nombre : '?';
                        $esLibre = (int)$p->Visitante === 0;
                    ?>
                        <tr>
                            <td>
                                <?php echo CHtml::encode($local); ?> vs <?php echo $esLibre ? '<em>Libre</em>' : CHtml::encode($visit); ?>
                                <br><small style="color:#5d6d67;">Fecha <?php echo (int)$p->NFecha; ?> · <?php echo CHtml::encode($p->Fecha); ?></small>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?>
                                    —
                                <?php else: ?>
                                    <strong><?php echo (int)$pred->golesLocal; ?> - <?php echo (int)$pred->golesVisitante; ?></strong>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?>—
                                <?php elseif ($p->GolLocal === null || $p->GolVisitante === null): ?>
                                    <span class="label label-default">Pendiente</span>
                                <?php else: ?>
                                    <strong><?php echo (int)$p->GolLocal; ?> - <?php echo (int)$p->GolVisitante; ?></strong>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?>—
                                <?php elseif ($pred->puntos === null): ?>
                                    <span class="label label-default">—</span>
                                <?php elseif ((int)$pred->puntos === ProdeUsuario::PUNTOS_EXACTO): ?>
                                    <span class="label label-success"><?php echo (int)ProdeUsuario::PUNTOS_EXACTO; ?> exacto</span>
                                <?php elseif ((int)$pred->puntos === ProdeUsuario::PUNTOS_SIGNO): ?>
                                    <span class="label label-warning"><?php echo (int)ProdeUsuario::PUNTOS_SIGNO; ?> signo</span>
                                <?php else: ?>
                                    <span class="label label-danger">0</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-panel-css', <<<CSS
.prode-container { max-width: 960px; margin: 24px auto; padding: 0 16px; }
.prode-welcome { background: linear-gradient(135deg, #078a48 0%, #063f2a 100%); color: #fff; border-radius: 12px; padding: 24px 32px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
.prode-welcome h1 { margin: 0 0 4px; font-size: 24px; font-weight: 800; }
.prode-welcome p { margin: 0; opacity: 0.95; }
.prode-welcome-cta { display: flex; gap: 8px; }
.prode-welcome-cta .btn-primary { background: #fff; color: #063f2a; border-color: #fff; }
.prode-welcome-cta .btn-primary:hover { background: #eaf5ef; color: #063f2a; }
.prode-welcome-cta .btn-default { background: transparent; color: #fff; border-color: #fff; }
.prode-welcome-cta .btn-default:hover { background: rgba(255,255,255,0.1); color: #fff; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
.prode-card h2 { color: #063f2a; margin-top: 0; font-size: 20px; font-weight: 800; }
.prode-torneo-block { border-top: 1px solid #edf3f0; padding: 16px 0; }
.prode-torneo-block h3 { color: #063f2a; font-size: 16px; margin: 0 0 12px; }
.prode-fecha-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
.prode-fecha-btn { min-width: 100px; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
CSS
);
?>
