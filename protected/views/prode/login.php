<?php $this->pageTitle = 'Iniciar sesion - Prode'; ?>

<div class="prode-auth">
    <div class="prode-auth-card">
        <h2>Prode Veteranos</h2>
        <p class="prode-auth-sub">Iniciar sesion</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo CHtml::encode($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo CHtml::normalizeUrl(array('prode/login')); ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo CHtml::encode($email); ?>">
            </div>
            <div class="form-group">
                <label for="password">Contrasena</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Iniciar sesion</button>
        </form>

        <div class="prode-auth-foot">
            <a href="<?php echo CHtml::normalizeUrl(array('prode/register')); ?>">Crear cuenta nueva</a>
            ·
            <a href="<?php echo CHtml::normalizeUrl(array('prode/index')); ?>">Volver al prode</a>
        </div>
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
CSS
);
?>
