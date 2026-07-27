<?php $this->pageTitle = 'Cargar resultados - Fecha ' . $fecha; ?>

<div class="prode-container">
    <div class="prode-card">
        <p><a href="<?php echo CHtml::normalizeUrl(array('prode/admin')); ?>">&larr; Volver al admin</a></p>
        <h1>Cargar resultados · <?php echo CHtml::encode($torneo->Nombre); ?> · Fecha <?php echo (int)$fecha; ?></h1>
        <p class="lead">Ingres&aacute; los goles reales de cada partido. Despu&eacute;s pod&eacute;s publicar la fecha desde el panel admin.</p>

        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo CHtml::encode($mensaje); ?></div>
        <?php endif; ?>

        <?php if (!empty($lock)):
            $pp = !empty($partidos) ? $partidos[0] : null;
            $ayer = $pp ? date('d/m/Y', strtotime($pp->Fecha . ' -1 day')) : '';
        ?>
            <div class="alert alert-warning">
                <strong>🔒 Edici&oacute;n bloqueada.</strong> Es el d&iacute;a del partido (o ya pas&oacute;).
                Se pod&iacute;a cargar hasta el <?php echo $ayer; ?>. Los resultados que ves son los que quedaron cargados.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo CHtml::normalizeUrl(array('prode/loadResultados', 'idTorneo' => (int)$torneo->idTorneo, 'fecha' => (int)$fecha)); ?>">
            <table class="table prode-table">
                <thead>
                    <tr>
                        <th>Partido</th>
                        <th style="width: 90px; text-align: center;">Local</th>
                        <th style="width: 90px; text-align: center;">Visitante</th>
                        <th>Pron&oacute;sticos m&aacute;s comunes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $p):
                        $esLibre = (int)$p->Visitante === 0;
                        $local = $p->local ? $p->local->Nombre : '?';
                        $visit = $p->visitante ? $p->visitante->Nombre : '?';
                    ?>
                        <tr<?php echo $esLibre ? ' class="prode-libre"' : ''; ?>>
                            <td>
                                <?php if ($esLibre): ?>
                                    <strong><?php echo CHtml::encode($local); ?></strong> <em>(Libre)</em>
                                <?php else: ?>
                                    <strong><?php echo CHtml::encode($local); ?></strong> vs <?php echo CHtml::encode($visit); ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?> —
                                <?php else: ?>
                                    <input type="number" class="form-control prode-goles"
                                        name="resultados[<?php echo (int)$p->idFixture; ?>][golesLocal]"
                                        min="0" max="30"
                                        value="<?php echo $p->GolLocal !== null ? (int)$p->GolLocal : ''; ?>"<?php echo !empty($lock) ? ' disabled' : ''; ?>>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($esLibre): ?> —
                                <?php else: ?>
                                    <input type="number" class="form-control prode-goles"
                                        name="resultados[<?php echo (int)$p->idFixture; ?>][golesVisitante]"
                                        min="0" max="30"
                                        value="<?php echo $p->GolVisitante !== null ? (int)$p->GolVisitante : ''; ?>"<?php echo !empty($lock) ? ' disabled' : ''; ?>>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small style="color: #5d6d67;">(ver pron&oacute;sticos en el ranking una vez publicada)</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($lock)): ?>
                <div style="text-align: right; margin-top: 16px;">
                    <button type="submit" class="btn btn-primary btn-lg">💾 Guardar borrador</button>
                    <a class="btn btn-warning btn-lg" href="<?php echo CHtml::normalizeUrl(array('prode/publicar', 'idTorneo' => (int)$torneo->idTorneo, 'fecha' => (int)$fecha)); ?>" onclick="return confirm('Publicar esta fecha? Despues se va a ver en el ranking.');">📢 Publicar fecha</a>
                </div>
            <?php else: ?>
                <div style="text-align: right; margin-top: 16px;">
                    <a class="btn btn-default" href="<?php echo CHtml::normalizeUrl(array('prode/admin')); ?>">Volver al admin</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-load-css', <<<CSS
.prode-container { max-width: 1100px; margin: 24px auto; padding: 0 16px; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
.prode-card h1 { color: #063f2a; margin-top: 0; font-size: 22px; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
.prode-libre { background: #fff5f5; }
.prode-goles { display: inline-block; width: 80px; text-align: center; font-weight: 700; }
CSS
);
?>
