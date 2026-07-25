<?php
/* @var $this SiteController */
/* @var $torneo Torneo */
/* @var $partidos array */
/* @var $fechaSeleccionada int|null */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #0d1f18; }
        h1 { color: #063f2a; font-size: 20pt; margin: 0 0 4pt; }
        .subtitulo { color: #5d6d67; font-size: 10pt; margin: 0 0 18pt; }
        table { border-collapse: collapse; width: 100%; }
        th {
            background: #078a48;
            border: 1px solid #06753c;
            color: #fff;
            font-size: 10pt;
            padding: 8pt 6pt;
            text-align: left;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #d7e6de;
            font-size: 11pt;
            padding: 6pt 6pt;
            vertical-align: top;
        }
        tr.libre td { background: #fff5f5; color: #b04141; font-style: italic; }
        .nro { font-weight: bold; text-align: center; width: 60pt; }
        .vs { color: #078a48; font-weight: bold; text-align: center; width: 30pt; }
    </style>
</head>
<body>
    <h1>Fixture <?php echo CHtml::encode($torneo->Nombre); ?><?php echo $fechaSeleccionada !== null ? ' - Fecha ' . (int)$fechaSeleccionada : ''; ?></h1>
    <p class="subtitulo">Asociación de Fútbol &mdash; Generado el <?php echo date('d/m/Y H:i'); ?></p>

    <table>
        <thead>
            <tr>
                <th class="nro">Nº Fecha</th>
                <th>Local</th>
                <th class="vs">VS</th>
                <th>Visitante</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $fechaActual = 0;
            foreach ($partidos as $partido):
                $esLibre = ((int)$partido->Visitante === 0);
            ?>
                <tr<?php echo $esLibre ? ' class="libre"' : ''; ?>>
                    <td class="nro">
                        <?php if ((int)$partido->NFecha !== $fechaActual): ?>
                            <?php echo (int)$partido->NFecha; ?>
                            <?php $fechaActual = (int)$partido->NFecha; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $partido->local ? CHtml::encode($partido->local->Nombre) : '-'; ?></td>
                    <td class="vs">VS</td>
                    <td>
                        <?php if ($esLibre): ?>
                            <em>Libre</em>
                        <?php else: ?>
                            <?php echo $partido->visitante ? CHtml::encode($partido->visitante->Nombre) : '-'; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
