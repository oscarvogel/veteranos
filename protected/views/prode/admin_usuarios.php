<?php $this->pageTitle = 'Usuarios - Admin Prode'; ?>

<div class="prode-container">
    <div class="prode-card">
        <p><a href="<?php echo CHtml::normalizeUrl(array('prode/admin')); ?>">&larr; Volver al admin</a></p>
        <h1>👥 Usuarios del prode</h1>

        <table class="table prode-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Equipo</th>
                    <th style="text-align: center;">Puntos</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Admin</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo CHtml::encode($u->nombre); ?></td>
                        <td><?php echo CHtml::encode($u->email); ?></td>
                        <td>
                            <?php if ($u->equipo): ?>
                                <a href="<?php echo CHtml::normalizeUrl(array('prode/equipo', 'idEquipo' => (int)$u->idEquipo)); ?>"
                                    style="color:#063f2a;"><?php echo CHtml::encode($u->equipo->Nombre); ?></a>
                            <?php else: ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;"><strong><?php echo (int)$u->totalPuntos(); ?></strong></td>
                        <td style="text-align: center;">
                            <?php if ($u->activo): ?>
                                <span class="label label-success">Activo</span>
                            <?php else: ?>
                                <span class="label label-danger">Bloqueado</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($u->esAdmin): ?>
                                <span class="label label-warning">Admin</span>
                            <?php else: ?> — <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ((int)$u->idProdeUsuario !== (int)$user->idProdeUsuario): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="idProdeUsuario" value="<?php echo (int)$u->idProdeUsuario; ?>">
                                    <input type="hidden" name="op" value="toggleActivo">
                                    <button type="submit" class="btn btn-xs <?php echo $u->activo ? 'btn-danger' : 'btn-success'; ?>">
                                        <?php echo $u->activo ? 'Bloquear' : 'Activar'; ?>
                                    </button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="idProdeUsuario" value="<?php echo (int)$u->idProdeUsuario; ?>">
                                    <input type="hidden" name="op" value="toggleAdmin">
                                    <button type="submit" class="btn btn-xs btn-default">
                                        <?php echo $u->esAdmin ? 'Quitar admin' : 'Hacer admin'; ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <small style="color:#5d6d67;">(vos)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-usuarios-css', <<<CSS
.prode-container { max-width: 1100px; margin: 24px auto; padding: 0 16px; }
.prode-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
.prode-card h1 { color: #063f2a; margin-top: 0; font-size: 24px; }
.prode-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.prode-table thead th { background: #f8f9fa; color: #5d6d67; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
CSS
);
?>
