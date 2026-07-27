<?php $this->pageTitle = 'Prode Veteranos'; ?>

<div class="prode-landing">
    <div class="prode-hero">
        <h1>Prode Veteranos</h1>
        <p class="lead">Hace tu pronostico de cada fecha y sumá puntos. <strong>Sin plata, solo por diversion.</strong></p>
        <div class="prode-hero-cta">
            <a class="btn btn-primary btn-lg" href="<?php echo CHtml::normalizeUrl(array('prode/register')); ?>">Crear cuenta</a>
            <a class="btn btn-default btn-lg" href="<?php echo CHtml::normalizeUrl(array('prode/login')); ?>">Ya tengo cuenta</a>
        </div>
        <p class="prode-hero-rs">
            <a href="<?php echo CHtml::normalizeUrl(array('prode/ranking')); ?>">Ranking individual &rarr;</a>
            &nbsp;·&nbsp;
            <a href="<?php echo CHtml::normalizeUrl(array('prode/rankingEquipos')); ?>">Ranking por equipos &rarr;</a>
        </p>
    </div>

    <div class="prode-features">
        <div class="prode-feature">
            <h3>🎯 Pronostica</h3>
            <p>Cargá el resultado exacto de cada partido. Si acertás el score, suman <?php echo (int)ProdeUsuario::PUNTOS_EXACTO; ?> puntos. Si solo el signo, <?php echo (int)ProdeUsuario::PUNTOS_SIGNO; ?> puntos.</p>
        </div>
        <div class="prode-feature">
            <h3>🏆 Sumá puntos</h3>
            <p>Las fechas se publican despues de cargar los resultados reales. El ranking se actualiza solo.</p>
        </div>
        <div class="prode-feature">
            <h3>👥 Sin drama</h3>
            <p>No hay plata ni premios. Es para jugar con amigos y ponerle un poco de sal a la fecha.</p>
        </div>
    </div>

    <p class="prode-landing-foot">Para participar, hace una cuenta con tu email. No se mezcla con el resto del sitio, es totalmente independiente.</p>
</div>

<?php
Yii::app()->clientScript->registerCss('prode-landing', <<<CSS
.prode-landing { max-width: 880px; margin: 0 auto; padding: 16px; }
.prode-hero {
    background: linear-gradient(135deg, #078a48 0%, #063f2a 100%);
    color: #fff;
    border-radius: 16px;
    padding: 48px 40px;
    text-align: center;
    margin-bottom: 32px;
}
.prode-hero h1 { font-size: 40px; font-weight: 800; margin: 0 0 12px; }
.prode-hero .lead { font-size: 18px; margin: 0 0 24px; opacity: 0.95; }
.prode-hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.prode-hero-cta .btn { padding: 14px 28px; font-size: 16px; }
.prode-hero-cta .btn-primary { background: #fff; color: #063f2a; border-color: #fff; }
.prode-hero-cta .btn-primary:hover { background: #eaf5ef; color: #063f2a; }
.prode-hero-cta .btn-default { background: transparent; color: #fff; border-color: #fff; }
.prode-hero-cta .btn-default:hover { background: rgba(255,255,255,0.1); color: #fff; }
.prode-hero-rs { margin-top: 24px; opacity: 0.9; }
.prode-hero-rs a { color: #fff; text-decoration: underline; }
.prode-features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
.prode-feature { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; text-align: center; }
.prode-feature h3 { color: #063f2a; margin: 0 0 8px; font-size: 18px; }
.prode-feature p { color: #5d6d67; margin: 0; font-size: 14px; }
.prode-landing-foot { color: #5d6d67; font-size: 13px; text-align: center; margin-top: 16px; }
@media (max-width: 767px) {
    .prode-hero h1 { font-size: 28px; }
    .prode-features { grid-template-columns: 1fr; }
}
CSS
);
?>
