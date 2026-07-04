# Recibos Caja Pagos Yii Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convertir el registro actual de `Ingresos` del proyecto Yii 1.x en un circuito de cobro con recibo correlativo, PDF imprimible/enviable, arqueo de caja y permisos por usuario.

**Architecture:** Extender el flujo Yii existente en `IngresosController`, `Ingresos`, vistas `protected/views/ingresos` y el módulo Cruge. No crear una app separada. Reusar `conceptos` para los tipos de cobro como arancel semanal, cuota societaria y multas; agregar trazabilidad de recibo, usuario, estado y cierre de caja sobre la tabla `ingresos`.

**Tech Stack:** Yii 1.x, Cruge RBAC, MySQL, Bootstrap widgets, `ext.yii-pdf.EYiiPdf` con mPDF, migraciones Yii en `protected/migrations`.

---

## Contexto Relevado

- `protected/models/Ingresos.php` ya representa pagos con `idEquipo`, `idConcepto`, `Fecha`, `Hora`, `Monto`, `Detalle` y `NFecha`.
- `protected/controllers/IngresosController.php` ya permite crear ingresos, listarlos, consultar por fecha y consultar por equipo/tipo.
- `protected/views/ingresos/_form.php` ya permite seleccionar equipo, fecha, concepto y monto.
- `protected/config/main.php` ya configura Cruge y `ePdf` con mPDF.
- `protected/modules/cruge/components/CrugeAccessControlFilter.php` no usa realmente `accessRules()` como fuente principal: exige permisos RBAC por operación, por ejemplo `action_ingresos_create`.
- `protected/controllers/TorneoController.php::actionSaldoCaja()` y `protected/views/torneo/saldocaja.php` ya tienen un arqueo básico, pero solo agrupan ingresos/egresos por concepto y fecha.
- No hay `AGENTS.md` en el proyecto.
- En esta máquina no hay `php` disponible en PATH, así que la validación local de migraciones y pruebas debe hacerse en el entorno donde corre el Yii o configurando PHP local.
- La carpeta `.git` existe pero `git status` no reconoce el repositorio; antes de ejecutar hay que revisar si `.git` está vacío/corrupto o si el deploy vino como copia FTP.

## Decisiones de Diseño

- Mantener `ingresos` como tabla principal de pagos para no romper consultas actuales.
- Agregar columnas de recibo y auditoría a `ingresos` en vez de crear una tabla paralela para pagos.
- Usar `conceptos` como catálogo configurable de tipo de cobro: `Arancel semanal`, `Cuota societaria`, `Multa`, etc.
- El número correlativo del recibo debe ser una columna propia (`NumeroRecibo`) y no depender de `idIngreso`, porque `idIngreso` es técnico y puede no representar la numeración contable que se quiere imprimir.
- Agregar anulación lógica para no borrar pagos con recibo emitido. La caja debe sumar recibos vigentes y mostrar anulados por separado.
- Registrar `idUsuario` del cobrador desde `Yii::app()->user->id`.
- Implementar PDF como acción Yii: `/ingresos/reciboPdf&id=123`, generada con mPDF.
- Agregar un arqueo específico en `IngresosController` o `CajaController`; por menor impacto, empezar con `IngresosController::actionArqueoCaja()`.

## Modelo de Datos Propuesto

### Tabla `ingresos`

Agregar:

```sql
ALTER TABLE ingresos
  ADD COLUMN NumeroRecibo INT NULL AFTER idIngreso,
  ADD COLUMN Estado VARCHAR(20) NOT NULL DEFAULT 'VIGENTE' AFTER NumeroRecibo,
  ADD COLUMN idUsuario INT NULL AFTER Estado,
  ADD COLUMN FechaAlta DATETIME NULL AFTER idUsuario,
  ADD COLUMN FechaAnulacion DATETIME NULL AFTER FechaAlta,
  ADD COLUMN MotivoAnulacion VARCHAR(250) NULL AFTER FechaAnulacion,
  ADD UNIQUE KEY ux_ingresos_numero_recibo (NumeroRecibo),
  ADD KEY ix_ingresos_fecha_estado (Fecha, Estado),
  ADD KEY ix_ingresos_usuario_fecha (idUsuario, Fecha);
```

Backfill recomendado:

```sql
UPDATE ingresos
SET NumeroRecibo = idIngreso,
    Estado = 'VIGENTE',
    FechaAlta = CONCAT(Fecha, ' ', COALESCE(Hora, '00:00:00'))
WHERE NumeroRecibo IS NULL;
```

### Tabla opcional `caja_cierres`

Crear en una segunda etapa si se necesita cerrar caja formalmente:

```sql
CREATE TABLE caja_cierres (
  idCierre INT AUTO_INCREMENT PRIMARY KEY,
  DesdeFecha DATE NOT NULL,
  HastaFecha DATE NOT NULL,
  idUsuario INT NOT NULL,
  TotalIngresos DECIMAL(12,2) NOT NULL DEFAULT 0,
  TotalEgresos DECIMAL(12,2) NOT NULL DEFAULT 0,
  TotalNeto DECIMAL(12,2) NOT NULL DEFAULT 0,
  Observaciones VARCHAR(250) NULL,
  FechaAlta DATETIME NOT NULL,
  UNIQUE KEY ux_caja_cierre_usuario_rango (idUsuario, DesdeFecha, HastaFecha)
);
```

No crear `caja_cierres` en el primer commit salvo que el negocio necesite bloquear/modificar cobranzas después del cierre.

## Archivos a Modificar

- Modify: `protected/models/Ingresos.php`
  - Validaciones de monto, fecha, estado, usuario y número de recibo.
  - Relaciones con `CrugeUser`.
  - Métodos de numeración, búsqueda, arqueo y anulación.

- Modify: `protected/controllers/IngresosController.php`
  - Crear recibo con transacción.
  - Generar PDF.
  - Anular recibo.
  - Arqueo de caja.
  - Acciones nuevas: `reciboPdf`, `arqueoCaja`, `anular`.

- Modify: `protected/views/ingresos/_form.php`
  - Ajustar etiquetas: Equipo, Tipo de cobro, Fecha, Monto, Detalle.
  - Evitar que el usuario cargue manualmente `NumeroRecibo`.

- Modify: `protected/views/ingresos/admin.php`
  - Mostrar recibo, fecha, equipo, concepto, monto, cobrador y estado.
  - Agregar botón PDF.

- Modify: `protected/views/ingresos/view.php`
  - Mostrar datos completos del recibo.
  - Agregar enlace "Imprimir PDF".

- Create: `protected/views/ingresos/reciboPdf.php`
  - Plantilla limpia para PDF.

- Create: `protected/views/ingresos/arqueoCaja.php`
  - Filtros por fecha/cobrador.
  - Totales por concepto y listado de recibos.

- Create: `protected/migrations/m260702_000001_add_recibo_fields_to_ingresos.php`
  - Migración reversible para columnas e índices.

- Optional Create: `protected/migrations/m260702_000002_create_caja_cierres.php`
  - Solo si se decide registrar cierres formales.

- Modify: `themes/classic/views/layouts/main.php`
  - Agregar menú "Caja" visible solo para usuarios con permiso.

- Optional Create: `protected/commands/RecibosCommand.php`
  - Script de reparación/backfill si hay datos históricos inconsistentes.

## Task 1: Migración de Recibos

**Files:**
- Create: `protected/migrations/m260702_000001_add_recibo_fields_to_ingresos.php`

- [ ] **Step 1: Crear migración**

```php
<?php

class m260702_000001_add_recibo_fields_to_ingresos extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('ingresos', 'NumeroRecibo', 'INT NULL AFTER idIngreso');
        $this->addColumn('ingresos', 'Estado', "VARCHAR(20) NOT NULL DEFAULT 'VIGENTE' AFTER NumeroRecibo");
        $this->addColumn('ingresos', 'idUsuario', 'INT NULL AFTER Estado');
        $this->addColumn('ingresos', 'FechaAlta', 'DATETIME NULL AFTER idUsuario');
        $this->addColumn('ingresos', 'FechaAnulacion', 'DATETIME NULL AFTER FechaAlta');
        $this->addColumn('ingresos', 'MotivoAnulacion', 'VARCHAR(250) NULL AFTER FechaAnulacion');

        $this->execute("UPDATE ingresos
            SET NumeroRecibo = idIngreso,
                Estado = 'VIGENTE',
                FechaAlta = CONCAT(Fecha, ' ', COALESCE(Hora, '00:00:00'))
            WHERE NumeroRecibo IS NULL");

        $this->createIndex('ux_ingresos_numero_recibo', 'ingresos', 'NumeroRecibo', true);
        $this->createIndex('ix_ingresos_fecha_estado', 'ingresos', 'Fecha, Estado');
        $this->createIndex('ix_ingresos_usuario_fecha', 'ingresos', 'idUsuario, Fecha');
    }

    public function safeDown()
    {
        $this->dropIndex('ix_ingresos_usuario_fecha', 'ingresos');
        $this->dropIndex('ix_ingresos_fecha_estado', 'ingresos');
        $this->dropIndex('ux_ingresos_numero_recibo', 'ingresos');
        $this->dropColumn('ingresos', 'MotivoAnulacion');
        $this->dropColumn('ingresos', 'FechaAnulacion');
        $this->dropColumn('ingresos', 'FechaAlta');
        $this->dropColumn('ingresos', 'idUsuario');
        $this->dropColumn('ingresos', 'Estado');
        $this->dropColumn('ingresos', 'NumeroRecibo');
    }
}
```

- [ ] **Step 2: Ejecutar migración en entorno con PHP**

Run:

```powershell
protected\yiic migrate --interactive=0
```

Expected:

```text
Migrated up successfully.
```

- [ ] **Step 3: Verificar datos históricos**

Run SQL:

```sql
SELECT COUNT(*) AS sin_numero FROM ingresos WHERE NumeroRecibo IS NULL;
SELECT NumeroRecibo, COUNT(*) FROM ingresos GROUP BY NumeroRecibo HAVING COUNT(*) > 1;
```

Expected:

```text
sin_numero = 0
sin duplicados
```

## Task 2: Endurecer Modelo `Ingresos`

**Files:**
- Modify: `protected/models/Ingresos.php`

- [ ] **Step 1: Agregar reglas**

Actualizar `rules()` para exigir monto y fecha, validar importe numérico y proteger campos calculados:

```php
return array(
    array('idEquipo, idConcepto, Fecha, Monto', 'required'),
    array('NFecha, NumeroRecibo, idUsuario', 'numerical', 'integerOnly'=>true),
    array('Monto', 'numerical', 'min'=>0.01),
    array('Estado', 'in', 'range'=>array('VIGENTE','ANULADO')),
    array('idEquipo, idConcepto', 'length', 'max'=>10),
    array('Detalle, MotivoAnulacion', 'length', 'max'=>250),
    array('Hora, FechaAlta, FechaAnulacion', 'safe'),
    array('idIngreso, idEquipo, NFecha, Fecha, Hora, Monto, idConcepto, Detalle, NumeroRecibo, Estado, idUsuario', 'safe', 'on'=>'search'),
);
```

- [ ] **Step 2: Agregar labels**

```php
'NumeroRecibo' => 'Nro. Recibo',
'Estado' => 'Estado',
'idUsuario' => 'Cobrador',
'FechaAlta' => 'Fecha de Alta',
'FechaAnulacion' => 'Fecha de Anulacion',
'MotivoAnulacion' => 'Motivo de Anulacion',
```

- [ ] **Step 3: Agregar relación de usuario**

```php
'Usuario' => array(self::BELONGS_TO, 'CrugeUser', 'idUsuario'),
```

- [ ] **Step 4: Agregar numeración correlativa**

```php
public static function siguienteNumeroRecibo()
{
    $max = Yii::app()->db->createCommand()
        ->select('MAX(NumeroRecibo)')
        ->from('ingresos')
        ->queryScalar();

    return ((int)$max) + 1;
}
```

- [ ] **Step 5: Agregar consulta para arqueo**

```php
public static function getArqueoCaja($desdeFecha, $hastaFecha, $idUsuario = null)
{
    $criteria = new CDbCriteria;
    $criteria->with = array('Equipos', 'Conceptos', 'Usuario');
    $criteria->condition = 't.Fecha BETWEEN :desde AND :hasta';
    $criteria->params = array(':desde'=>$desdeFecha, ':hasta'=>$hastaFecha);
    if($idUsuario !== null && $idUsuario !== '') {
        $criteria->addCondition('t.idUsuario = :idUsuario');
        $criteria->params[':idUsuario'] = $idUsuario;
    }
    $criteria->order = 't.Fecha ASC, t.NumeroRecibo ASC';

    return new CActiveDataProvider('Ingresos', array(
        'criteria'=>$criteria,
        'pagination'=>array('pageSize'=>100),
    ));
}
```

## Task 3: Registrar Pago con Transacción

**Files:**
- Modify: `protected/controllers/IngresosController.php`

- [ ] **Step 1: Modificar `actionCreate()`**

El guardado debe asignar número, hora, usuario y fecha de alta dentro de una transacción:

```php
public function actionCreate()
{
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    $model = new Ingresos;
    $model->Fecha = date("Y-m-d");
    $model->Estado = 'VIGENTE';

    $this->performAjaxValidation($model);

    if(isset($_POST['Ingresos']))
    {
        $model->attributes = $_POST['Ingresos'];
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $model->Hora = date("H:i:s");
            $model->FechaAlta = date("Y-m-d H:i:s");
            $model->idUsuario = Yii::app()->user->id;
            $model->Estado = 'VIGENTE';
            $model->NumeroRecibo = Ingresos::siguienteNumeroRecibo();

            if(!$model->save()) {
                throw new CException('No se pudo registrar el pago.');
            }

            $transaction->commit();
            $this->redirect(array('view','id'=>$model->idIngreso));
        } catch(Exception $e) {
            $transaction->rollback();
            $model->addError('idIngreso', $e->getMessage());
        }
    }

    $this->render('create', array('model'=>$model));
}
```

- [ ] **Step 2: Mantener email como mejora posterior**

El email actual de `actionCreate()` se puede conservar, pero conviene moverlo a un método privado después de que el recibo se haya guardado. Para WhatsApp, lo importante es tener PDF descargable; el envío puede ser manual compartiendo el archivo.

## Task 4: PDF de Recibo

**Files:**
- Modify: `protected/controllers/IngresosController.php`
- Create: `protected/views/ingresos/reciboPdf.php`

- [ ] **Step 1: Agregar acción `actionReciboPdf($id)`**

```php
public function actionReciboPdf($id)
{
    $model = $this->loadModel($id);
    $html = $this->renderPartial('reciboPdf', array('model'=>$model), true);

    $pdf = Yii::app()->ePdf->mpdf('', 'A5');
    $pdf->WriteHTML($html);
    $filename = 'recibo-' . str_pad($model->NumeroRecibo, 8, '0', STR_PAD_LEFT) . '.pdf';
    $pdf->Output($filename, 'I');
}
```

- [ ] **Step 2: Crear plantilla PDF**

```php
<?php
$numero = str_pad($model->NumeroRecibo, 8, '0', STR_PAD_LEFT);
?>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12pt; }
        .titulo { font-size: 18pt; font-weight: bold; text-align: center; }
        .numero { font-size: 14pt; text-align: right; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .monto { font-size: 16pt; font-weight: bold; }
        .estado-anulado { color: #900; font-weight: bold; }
    </style>
</head>
<body>
    <div class="titulo"><?php echo CHtml::encode(Yii::app()->params['Sistema']); ?></div>
    <div class="numero">Recibo Nro. <?php echo $numero; ?></div>
    <?php if($model->Estado === 'ANULADO'): ?>
        <p class="estado-anulado">RECIBO ANULADO</p>
    <?php endif; ?>
    <table>
        <tr><td>Fecha</td><td><?php echo CHtml::encode($model->Fecha); ?> <?php echo CHtml::encode($model->Hora); ?></td></tr>
        <tr><td>Equipo</td><td><?php echo CHtml::encode($model->Equipos->Nombre); ?></td></tr>
        <tr><td>Concepto</td><td><?php echo CHtml::encode($model->Conceptos->Nombre); ?></td></tr>
        <tr><td>Detalle</td><td><?php echo CHtml::encode($model->Detalle); ?></td></tr>
        <tr><td>Monto</td><td class="monto">$ <?php echo number_format((float)$model->Monto, 2, ',', '.'); ?></td></tr>
        <tr><td>Cobrador</td><td><?php echo $model->Usuario ? CHtml::encode($model->Usuario->username) : ''; ?></td></tr>
    </table>
</body>
</html>
```

## Task 5: Administración y Botones

**Files:**
- Modify: `protected/views/ingresos/admin.php`
- Modify: `protected/views/ingresos/view.php`

- [ ] **Step 1: Agregar columnas al grid**

```php
'NumeroRecibo',
'Fecha',
array(
    'name'=>'idEquipo',
    'value'=>'$data->Equipos->Nombre',
    'filter'=>Equipos::getListEquipo(),
),
array(
    'name'=>'idConcepto',
    'value'=>'$data->Conceptos->Nombre',
    'filter'=>Conceptos::getListConceptos(),
),
'Monto',
'Estado',
```

- [ ] **Step 2: Agregar botón PDF**

```php
array(
    'class'=>'bootstrap.widgets.TbButtonColumn',
    'template'=>'{view} {update} {pdf} {delete}',
    'buttons'=>array(
        'pdf'=>array(
            'label'=>'PDF',
            'url'=>'Yii::app()->createUrl("ingresos/reciboPdf", array("id"=>$data->idIngreso))',
            'options'=>array('target'=>'_blank'),
        ),
    ),
),
```

## Task 6: Arqueo de Caja

**Files:**
- Modify: `protected/controllers/IngresosController.php`
- Create: `protected/views/ingresos/arqueoCaja.php`

- [ ] **Step 1: Agregar acción**

```php
public function actionArqueoCaja()
{
    $desde = isset($_GET['desde']) ? $_GET['desde'] : date('Y-m-d');
    $hasta = isset($_GET['hasta']) ? $_GET['hasta'] : date('Y-m-d');
    $idUsuario = isset($_GET['idUsuario']) ? $_GET['idUsuario'] : null;

    $dataProvider = Ingresos::getArqueoCaja($desde, $hasta, $idUsuario);

    $totalVigente = Yii::app()->db->createCommand()
        ->select('COALESCE(SUM(Monto), 0)')
        ->from('ingresos')
        ->where('Fecha BETWEEN :desde AND :hasta AND Estado = :estado', array(
            ':desde'=>$desde,
            ':hasta'=>$hasta,
            ':estado'=>'VIGENTE',
        ))
        ->queryScalar();

    $this->render('arqueoCaja', array(
        'desde'=>$desde,
        'hasta'=>$hasta,
        'idUsuario'=>$idUsuario,
        'dataProvider'=>$dataProvider,
        'totalVigente'=>$totalVigente,
    ));
}
```

- [ ] **Step 2: Crear vista de arqueo**

La vista debe mostrar filtros, total cobrado y grilla con recibos:

```php
<h1>Arqueo de Caja</h1>

<form method="get" class="well form-inline">
    <input type="hidden" name="r" value="ingresos/arqueoCaja">
    <label>Desde</label>
    <input type="date" name="desde" value="<?php echo CHtml::encode($desde); ?>">
    <label>Hasta</label>
    <input type="date" name="hasta" value="<?php echo CHtml::encode($hasta); ?>">
    <button type="submit" class="btn btn-primary">Consultar</button>
</form>

<h3>Total vigente: $ <?php echo number_format((float)$totalVigente, 2, ',', '.'); ?></h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'arqueo-caja-grid',
    'dataProvider'=>$dataProvider,
    'columns'=>array(
        'NumeroRecibo',
        'Fecha',
        array('name'=>'Equipo', 'value'=>'$data->Equipos->Nombre'),
        array('name'=>'Concepto', 'value'=>'$data->Conceptos->Nombre'),
        'Monto',
        'Estado',
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{view} {pdf}',
            'buttons'=>array(
                'pdf'=>array(
                    'label'=>'PDF',
                    'url'=>'Yii::app()->createUrl("ingresos/reciboPdf", array("id"=>$data->idIngreso))',
                    'options'=>array('target'=>'_blank'),
                ),
            ),
        ),
    ),
)); ?>
```

## Task 7: Permisos Cruge

**Files:**
- Modify: `protected/controllers/IngresosController.php`
- Modify: `themes/classic/views/layouts/main.php`

- [ ] **Step 1: Crear operaciones RBAC en Cruge**

Desde "Administrar Usuarios" en Cruge, crear estas operaciones:

```text
action_ingresos_create
action_ingresos_admin
action_ingresos_view
action_ingresos_update
action_ingresos_reciboPdf
action_ingresos_arqueoCaja
action_ingresos_anular
```

- [ ] **Step 2: Crear rol**

Crear rol:

```text
cajero
```

Asignar al rol `cajero`:

```text
action_ingresos_create
action_ingresos_admin
action_ingresos_view
action_ingresos_reciboPdf
action_ingresos_arqueoCaja
```

Reservar para administrador:

```text
action_ingresos_update
action_ingresos_anular
```

- [ ] **Step 3: Agregar menú Caja**

En `themes/classic/views/layouts/main.php`, dentro del bloque de usuario logueado:

```php
<?php if(Yii::app()->user->checkAccess('action_ingresos_arqueoCaja') || Yii::app()->user->checkAccess('action_ingresos_create')): ?>
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Caja <span class="caret"></span></a>
    <ul class="dropdown-menu">
        <?php if(Yii::app()->user->checkAccess('action_ingresos_create')): ?>
            <li><a href="<?php echo Yii::app()->createUrl('/ingresos/create')?>">Registrar pago</a></li>
        <?php endif; ?>
        <?php if(Yii::app()->user->checkAccess('action_ingresos_arqueoCaja')): ?>
            <li><a href="<?php echo Yii::app()->createUrl('/ingresos/arqueoCaja')?>">Arqueo de caja</a></li>
        <?php endif; ?>
        <?php if(Yii::app()->user->checkAccess('action_ingresos_admin')): ?>
            <li><a href="<?php echo Yii::app()->createUrl('/ingresos/admin')?>">Recibos</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>
```

## Task 8: Anulación de Recibos

**Files:**
- Modify: `protected/controllers/IngresosController.php`
- Modify: `protected/models/Ingresos.php`

- [ ] **Step 1: Agregar método en modelo**

```php
public function anular($motivo)
{
    if($this->Estado === 'ANULADO') {
        $this->addError('Estado', 'El recibo ya esta anulado.');
        return false;
    }

    $this->Estado = 'ANULADO';
    $this->MotivoAnulacion = $motivo;
    $this->FechaAnulacion = date('Y-m-d H:i:s');

    return $this->save(false, array('Estado', 'MotivoAnulacion', 'FechaAnulacion'));
}
```

- [ ] **Step 2: Agregar acción**

```php
public function actionAnular($id)
{
    $model = $this->loadModel($id);
    if(isset($_POST['motivo'])) {
        if($model->anular($_POST['motivo'])) {
            $this->redirect(array('view','id'=>$model->idIngreso));
        }
    }
    $this->render('anular', array('model'=>$model));
}
```

## Task 9: Pruebas Manuales de Aceptación

**Files:**
- No code changes.

- [ ] **Step 1: Registrar pago de arancel semanal**

Ir a:

```text
/index.php?r=ingresos/create
```

Expected:

```text
Se guarda el pago, se asigna NumeroRecibo correlativo, Estado=VIGENTE, idUsuario del cobrador.
```

- [ ] **Step 2: Abrir recibo PDF**

Ir a:

```text
/index.php?r=ingresos/reciboPdf&id=<idIngreso>
```

Expected:

```text
El navegador muestra o descarga un PDF A5 con número de recibo, equipo, concepto, monto, fecha y cobrador.
```

- [ ] **Step 3: Arqueo por fecha**

Ir a:

```text
/index.php?r=ingresos/arqueoCaja&desde=2026-07-02&hasta=2026-07-02
```

Expected:

```text
La pantalla lista los recibos del día y el total coincide con SUM(Monto) de recibos VIGENTE.
```

- [ ] **Step 4: Permisos**

Ingresar con usuario sin rol `cajero`:

```text
/index.php?r=ingresos/create
```

Expected:

```text
Acceso denegado por Cruge.
```

Ingresar con usuario con rol `cajero`:

```text
/index.php?r=ingresos/create
```

Expected:

```text
Puede registrar pagos y generar PDF.
```

## Riesgos y Controles

- La numeración correlativa con `MAX(NumeroRecibo)+1` puede fallar si dos usuarios cobran al mismo tiempo. Si habrá concurrencia real, reemplazar por tabla `recibo_secuencia` bloqueada con transacción o usar `AUTO_INCREMENT` en tabla separada.
- Si se permite borrar ingresos, se pierde trazabilidad. Deshabilitar borrado o permitir solo anulación.
- El campo `Monto` hoy está tratado como string por el modelo. Validarlo como número y revisar tipo real en MySQL antes de migrar.
- Los conceptos deben estar cargados y normalizados antes de usar caja: `Arancel semanal`, `Cuota societaria`, `Multa`.
- El envío por WhatsApp no necesita integración API al principio: basta con generar PDF liviano y descargarlo/compartirlo.
- La configuración actual contiene credenciales MySQL en `protected/config/main.php`; no tocar en esta tarea, pero conviene planificar separación por ambiente.

## Orden de Implementación Recomendado

1. Migración de columnas de recibo.
2. Modelo `Ingresos` con validaciones, usuario, estado y numeración.
3. `actionCreate()` transaccional.
4. PDF de recibo.
5. Grilla/admin con botón PDF.
6. Arqueo específico de caja.
7. Permisos Cruge y menú Caja.
8. Anulación lógica.
9. Validación manual completa en entorno real.

