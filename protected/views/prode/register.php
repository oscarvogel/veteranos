<?php $this->pageTitle = 'Crear cuenta - Prode'; ?>

<div class="prode-auth">
    <div class="prode-auth-card">
        <h2>Prode Veteranos</h2>
        <p class="prode-auth-sub">Crear cuenta nueva</p>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?php echo CHtml::errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo CHtml::normalizeUrl(array('prode/register')); ?>">
            <div class="form-group">
                <label for="nombre">Nombre y apellido</label>
                <input type="text" class="form-control" id="nombre" name="ProdeUsuario[nombre]" required maxlength="100"
                    value="<?php echo CHtml::encode($model->nombre); ?>">
                <p class="form-help">Como va a aparecer en el ranking.</p>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="ProdeUsuario[email]" required maxlength="150"
                    value="<?php echo CHtml::encode($model->email); ?>">
            </div>
            <div class="form-group">
                <label for="password">Contrasena</label>
                <input type="password" class="form-control" id="password" name="ProdeUsuario[password]" required minlength="6">
                <p class="form-help">Minimo 6 caracteres.</p>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Crear cuenta</button>
        </form>

        <div class="prode-auth-foot">
            <a href="<?php echo CHtml::normalizeUrl(array('prode/login')); ?>">Ya tengo cuenta</a>
            ·
            <a href="<?php echo CHtml::normalizeUrl(array('prode/index')); ?>">Volver al prode</a>
        </div>

        <p class="prode-legal">
            <strong>Importante:</strong> El prode es solo para diversion, sin dinero ni premios. Los datos se guardan solo para identificar tu prediccion y tu posicion en el ranking. Podes pedir la baja escribiendonos.
        </p>
    </div>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-auth-css', <<<CSS
.prode-auth { max-width: 480px; margin: 24px auto; padding: 0 16px; }
.prode-auth-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    padding: 32px;
}
.prode-auth-card h2 { color: #063f2a; margin: 0 0 4px; font-weight: 800; }
.prode-auth-sub { color: #5d6d67; margin-top: 0; margin-bottom: 20px; }
.prode-auth-foot { text-align: center; margin-top: 20px; color: #5d6d67; font-size: 13px; }
.prode-auth-foot a { color: #078a48; }
.prode-legal { background: #f8f9fa; border-radius: 8px; padding: 12px; font-size: 12px; color: #5d6d67; margin-top: 20px; }
.form-help { color: #5d6d67; font-size: 12px; margin: 4px 0 0; }
CSS
);
?>
