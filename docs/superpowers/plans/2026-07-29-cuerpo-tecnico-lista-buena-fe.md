# Cuerpo Técnico en Lista de Buena Fe Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Registrar técnico y ayudante de técnico por equipo y mostrar ambos rótulos y nombres en las exportaciones PDF y Excel de la lista de buena fe.

**Architecture:** Agregar dos columnas opcionales con valor vacío predeterminado a `equipos`, exponerlas mediante el ActiveRecord existente y capturarlas en el formulario clásico. Las plantillas PDF y Excel leerán los valores del objeto `$equipo`, escaparán los nombres y mantendrán los rótulos visibles aunque el dato esté vacío.

**Tech Stack:** Yii 1.1, PHP 5/7 compatible, MySQL/MariaDB, mPDF, HTML compatible con Excel, FTP TLS.

---

## Estructura de archivos

- Create: `scripts/test-equipo-cuerpo-tecnico.php`
  - Prueba de regresión ejecutable sin conexión a la base.
- Create: `protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php`
  - Migración Yii repetible para agregar o quitar las dos columnas.
- Modify: `protected/models/Equipos.php`
  - Reglas de asignación, longitud, búsqueda y etiquetas.
- Modify: `protected/views/equipos/_form.php`
  - Campos de edición con HTML y clases Bootstrap 3.
- Modify: `protected/views/equipos/pdf.php`
  - Datos del cuerpo técnico en los renglones de firmas.
- Modify: `protected/views/equipos/excel.php`
  - Bloque identificatorio del cuerpo técnico antes de los jugadores.

No se modifican el controlador, la consulta de jugadores ni las tablas de torneos.

### Task 1: Prueba de regresión inicialmente fallida

**Files:**
- Create: `scripts/test-equipo-cuerpo-tecnico.php`

- [ ] **Step 1: Crear la prueba**

```php
<?php

function failTest($message)
{
	fwrite(STDERR, $message . "\n");
	exit(1);
}

function readProjectFile($relativePath)
{
	$contents = file_get_contents(__DIR__ . '/../' . $relativePath);
	if($contents === false)
		failTest('No se pudo leer ' . $relativePath);
	return $contents;
}

function requireSnippet($contents, $snippet, $message)
{
	if(strpos($contents, $snippet) === false)
		failTest($message);
}

$migration = readProjectFile('protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php');
$model = readProjectFile('protected/models/Equipos.php');
$form = readProjectFile('protected/views/equipos/_form.php');
$pdf = readProjectFile('protected/views/equipos/pdf.php');
$excel = readProjectFile('protected/views/equipos/excel.php');

requireSnippet($migration, "columnExists('equipos', 'Tecnico')", 'La migracion debe controlar equipos.Tecnico.');
requireSnippet($migration, "columnExists('equipos', 'AyudanteTecnico')", 'La migracion debe controlar equipos.AyudanteTecnico.');
requireSnippet($migration, "VARCHAR(100) NOT NULL DEFAULT ''", 'Las columnas deben aceptar nombres de 100 caracteres y usar valor vacio.');

requireSnippet($model, "array('Tecnico, AyudanteTecnico', 'length', 'max'=>100)", 'El modelo debe validar ambos nombres.');
requireSnippet($model, "'Tecnico' => 'Técnico'", 'Falta la etiqueta Técnico.');
requireSnippet($model, "'AyudanteTecnico' => 'Ayudante de técnico'", 'Falta la etiqueta Ayudante de técnico.');

requireSnippet($form, "CHtml::activeTextField(\$model, 'Tecnico'", 'El formulario debe editar Técnico.');
requireSnippet($form, "CHtml::activeTextField(\$model, 'AyudanteTecnico'", 'El formulario debe editar Ayudante de técnico.');

requireSnippet($pdf, 'T&eacute;cnico:', 'El PDF debe imprimir siempre el rótulo Técnico.');
requireSnippet($pdf, 'Ayudante de t&eacute;cnico:', 'El PDF debe imprimir siempre el rótulo Ayudante de técnico.');
requireSnippet($pdf, 'CHtml::encode($tecnico)', 'El PDF debe escapar el nombre del técnico.');
requireSnippet($pdf, 'CHtml::encode($ayudanteTecnico)', 'El PDF debe escapar el nombre del ayudante.');

requireSnippet($excel, 'T&eacute;cnico', 'Excel debe imprimir siempre el rótulo Técnico.');
requireSnippet($excel, 'Ayudante de t&eacute;cnico', 'Excel debe imprimir siempre el rótulo Ayudante de técnico.');
requireSnippet($excel, 'CHtml::encode($tecnico)', 'Excel debe escapar el nombre del técnico.');
requireSnippet($excel, 'CHtml::encode($ayudanteTecnico)', 'Excel debe escapar el nombre del ayudante.');

echo "OK: cuerpo técnico disponible en equipo, PDF y Excel.\n";
```

- [ ] **Step 2: Ejecutar la prueba y comprobar RED**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' 'scripts\test-equipo-cuerpo-tecnico.php'
```

Expected: FAIL con `No se pudo leer protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php`.

- [ ] **Step 3: Commit de la prueba fallida**

```powershell
git add -- scripts/test-equipo-cuerpo-tecnico.php
git commit -m "test(equipos): cubrir cuerpo tecnico en lista de buena fe"
```

### Task 2: Migración repetible

**Files:**
- Create: `protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php`

- [ ] **Step 1: Crear la migración**

```php
<?php

class m260729_000001_add_cuerpo_tecnico_to_equipos extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->columnExists('equipos', 'Tecnico'))
			$this->addColumn('equipos', 'Tecnico', "VARCHAR(100) NOT NULL DEFAULT '' AFTER DelegadoSuplente");

		if(!$this->columnExists('equipos', 'AyudanteTecnico'))
			$this->addColumn('equipos', 'AyudanteTecnico', "VARCHAR(100) NOT NULL DEFAULT '' AFTER Tecnico");
	}

	public function safeDown()
	{
		if($this->columnExists('equipos', 'AyudanteTecnico'))
			$this->dropColumn('equipos', 'AyudanteTecnico');

		if($this->columnExists('equipos', 'Tecnico'))
			$this->dropColumn('equipos', 'Tecnico');
	}

	private function columnExists($table, $column)
	{
		$tableSchema = $this->dbConnection->schema->getTable($table);
		return $tableSchema !== null && isset($tableSchema->columns[$column]);
	}
}
```

- [ ] **Step 2: Validar sintaxis**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' -l 'protected\migrations\m260729_000001_add_cuerpo_tecnico_to_equipos.php'
```

Expected: `No syntax errors detected`.

- [ ] **Step 3: Ejecutar la prueba y comprobar que ahora falla por el modelo**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' 'scripts\test-equipo-cuerpo-tecnico.php'
```

Expected: FAIL con `El modelo debe validar ambos nombres.`

- [ ] **Step 4: Commit de la migración**

```powershell
git add -- protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php
git commit -m "feat(equipos): agregar migracion de cuerpo tecnico"
```

### Task 3: Modelo y formulario

**Files:**
- Modify: `protected/models/Equipos.php:46-53`
- Modify: `protected/models/Equipos.php:80-90`
- Modify: `protected/views/equipos/_form.php:20-25`

- [ ] **Step 1: Ampliar las reglas del modelo**

Agregar después de la regla de 50 caracteres:

```php
array('Tecnico, AyudanteTecnico', 'length', 'max'=>100),
```

Agregar `Tecnico, AyudanteTecnico` a la regla `safe` del escenario `search`.

- [ ] **Step 2: Agregar las etiquetas del modelo**

Agregar a `attributeLabels()`:

```php
'Tecnico' => 'Técnico',
'AyudanteTecnico' => 'Ayudante de técnico',
```

- [ ] **Step 3: Agregar campos Bootstrap 3 al formulario**

Insertar después de `DelegadoSuplente`:

```php
	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Tecnico', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Tecnico', array('class'=>'form-control', 'maxlength'=>100)); ?>
			<?php echo CHtml::error($model, 'Tecnico', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'AyudanteTecnico', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'AyudanteTecnico', array('class'=>'form-control', 'maxlength'=>100)); ?>
			<?php echo CHtml::error($model, 'AyudanteTecnico', array('class'=>'help-block')); ?>
		</div>
	</div>
```

- [ ] **Step 4: Validar sintaxis**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' -l 'protected\models\Equipos.php'
& 'O:\veteranos\tools\php-7.4.33\php.exe' -l 'protected\views\equipos\_form.php'
```

Expected: ambos archivos informan `No syntax errors detected`.

- [ ] **Step 5: Ejecutar la prueba y comprobar que ahora falla por el PDF**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' 'scripts\test-equipo-cuerpo-tecnico.php'
```

Expected: FAIL con `El PDF debe imprimir siempre el rótulo Técnico.`

- [ ] **Step 6: Commit de modelo y formulario**

```powershell
git add -- protected/models/Equipos.php protected/views/equipos/_form.php
git commit -m "feat(equipos): permitir cargar cuerpo tecnico"
```

### Task 4: PDF y Excel

**Files:**
- Modify: `protected/views/equipos/pdf.php:13-15`
- Modify: `protected/views/equipos/pdf.php:315-322`
- Modify: `protected/views/equipos/excel.php:15-20`

- [ ] **Step 1: Preparar valores seguros en el PDF**

Agregar junto a los delegados:

```php
$tecnico = isset($equipo->Tecnico) ? $equipo->Tecnico : '';
$ayudanteTecnico = isset($equipo->AyudanteTecnico) ? $equipo->AyudanteTecnico : '';
```

- [ ] **Step 2: Reemplazar los renglones del cuerpo técnico en el PDF**

```php
    <tr>
        <td>T&eacute;cnico: <?php echo CHtml::encode($tecnico); ?></td>
        <td class="right">Firma:........................................</td>
    </tr>
    <tr>
        <td>Ayudante de t&eacute;cnico: <?php echo CHtml::encode($ayudanteTecnico); ?></td>
        <td class="right">Firma:........................................</td>
    </tr>
```

Los rótulos se renderizan siempre; un valor vacío produce una cadena vacía después de `:`.

- [ ] **Step 3: Preparar valores seguros en Excel**

Dentro del bloque `if(isset($jugadores))`, antes del encabezado:

```php
	$tecnico = isset($equipo->Tecnico) ? $equipo->Tecnico : '';
	$ayudanteTecnico = isset($equipo->AyudanteTecnico) ? $equipo->AyudanteTecnico : '';
```

- [ ] **Step 4: Agregar el bloque del cuerpo técnico a Excel**

Insertar entre el `<h5>` y la tabla de jugadores:

```php
	<table border="1">
		<tr>
			<th>T&eacute;cnico</th>
			<td><?php echo CHtml::encode($tecnico); ?></td>
		</tr>
		<tr>
			<th>Ayudante de t&eacute;cnico</th>
			<td><?php echo CHtml::encode($ayudanteTecnico); ?></td>
		</tr>
	</table>
```

- [ ] **Step 5: Validar sintaxis y comprobar GREEN**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' -l 'protected\views\equipos\pdf.php'
& 'O:\veteranos\tools\php-7.4.33\php.exe' -l 'protected\views\equipos\excel.php'
& 'O:\veteranos\tools\php-7.4.33\php.exe' 'scripts\test-equipo-cuerpo-tecnico.php'
```

Expected: ambos archivos sin errores y `OK: cuerpo técnico disponible en equipo, PDF y Excel.`

- [ ] **Step 6: Ejecutar regresiones existentes**

Run:

```powershell
& 'O:\veteranos\tools\php-7.4.33\php.exe' 'scripts\test-lista-buena-fe-pdf.php'
& 'O:\veteranos\tools\php-7.4.33\php.exe' 'scripts\test-lista-buena-fe-excel.php'
```

Expected:

```text
OK: Lista de buena fe genera PDF dinamico con encabezado y categorias.
OK lista buena fe Excel exporta fecha de nacimiento
```

- [ ] **Step 7: Commit de las salidas**

```powershell
git add -- protected/views/equipos/pdf.php protected/views/equipos/excel.php
git commit -m "feat(equipos): imprimir cuerpo tecnico en lista de buena fe"
```

### Task 5: Verificación local completa

**Files:**
- Verify: todos los archivos modificados

- [ ] **Step 1: Ejecutar sintaxis y pruebas juntas**

```powershell
$php = 'O:\veteranos\tools\php-7.4.33\php.exe'
& $php -l 'protected\migrations\m260729_000001_add_cuerpo_tecnico_to_equipos.php'
& $php -l 'protected\models\Equipos.php'
& $php -l 'protected\views\equipos\_form.php'
& $php -l 'protected\views\equipos\pdf.php'
& $php -l 'protected\views\equipos\excel.php'
& $php 'scripts\test-equipo-cuerpo-tecnico.php'
& $php 'scripts\test-lista-buena-fe-pdf.php'
& $php 'scripts\test-lista-buena-fe-excel.php'
if($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
```

Expected: cinco validaciones de sintaxis y tres pruebas terminan correctamente.

- [ ] **Step 2: Revisar alcance y espacio en blanco**

```powershell
git diff HEAD~4 --check
git status -sb
git diff HEAD~4 -- protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php protected/models/Equipos.php protected/views/equipos/_form.php protected/views/equipos/pdf.php protected/views/equipos/excel.php scripts/test-equipo-cuerpo-tecnico.php
```

Expected: ningún error de `diff --check`; solamente aparecen los archivos previstos.

### Task 6: Migración y despliegue de producción

**Files:**
- Deploy: `protected/models/Equipos.php`
- Deploy: `protected/views/equipos/_form.php`
- Deploy: `protected/views/equipos/pdf.php`
- Deploy: `protected/views/equipos/excel.php`
- Use once and remove: migrador web temporal protegido con token

- [ ] **Step 1: Obtener evidencia previa**

Verificar mediante un migrador web temporal de solo lectura que `equipos.Tecnico` y `equipos.AyudanteTecnico` no existen todavía. El migrador debe cargar `protected/config/main.php`, consultar `Yii::app()->db->schema->getTable('equipos')` y no imprimir credenciales.

Expected: ambas columnas informadas como ausentes.

- [ ] **Step 2: Ejecutar la migración antes de subir la aplicación**

El mismo migrador temporal, protegido por un token aleatorio de 32 bytes, debe ejecutar:

```sql
ALTER TABLE equipos
	ADD COLUMN Tecnico VARCHAR(100) NOT NULL DEFAULT '' AFTER DelegadoSuplente,
	ADD COLUMN AyudanteTecnico VARCHAR(100) NOT NULL DEFAULT '' AFTER Tecnico
```

Debe consultar cada columna antes de agregarla para que la operación sea repetible.

Expected: el migrador confirma que ambas columnas existen y que el equipo `idEquipo=21` conserva sus datos anteriores.

- [ ] **Step 3: Eliminar inmediatamente el migrador temporal**

Eliminar el archivo remoto por la misma conexión FTP TLS y comprobar que su URL responde `404`.

- [ ] **Step 4: Subir solamente los cuatro archivos PHP funcionales**

Run:

```powershell
& 'C:\Program Files\Python313\python.exe' 'scripts\deploy_ftp.py' `
	'protected/models/Equipos.php' `
	'protected/views/equipos/_form.php' `
	'protected/views/equipos/pdf.php' `
	'protected/views/equipos/excel.php'
```

Si esa instalación de Python no existe, usar `py -3 scripts\deploy_ftp.py` con los mismos cuatro argumentos.

Expected: conexión FTP TLS exitosa, mismo tamaño local/remoto y MD5 local informado para cada archivo.

- [ ] **Step 5: Probar guardado real del equipo 21**

Abrir `http://veteranos.ar/index.php?r=equipos/update&id=21`, confirmar que aparecen ambos campos y guardar valores controlados. Volver a abrir la pantalla y verificar que persisten. Si los nombres reales todavía no fueron informados, dejar ambos valores vacíos después de la prueba.

- [ ] **Step 6: Verificar Excel real**

Consultar primero el torneo vigente del equipo 21:

```sql
SELECT et.idTorneo
FROM equipostorneo et
INNER JOIN torneo t ON t.idTorneo = et.idTorneo
WHERE et.idEquipo = 21
  AND t.Estado IN ('I', 'A')
ORDER BY t.Inicio DESC, et.idTorneo DESC
LIMIT 1
```

Hacer POST a `http://veteranos.ar/index.php?r=equipos/ListaBuenaFe` usando el `idTorneo` devuelto por esa consulta:

```text
Equipostorneo[idTorneo]=resultado de la consulta anterior
Equipostorneo[idEquipo]=21
btnExcel=Enviar a Excel
```

Decodificar el cuerpo HTML/XLS y verificar:

- existe el rótulo `Técnico`;
- existe el rótulo `Ayudante de técnico`;
- los valores coinciden con el equipo 21 o quedan vacíos;
- continúa la columna `Fecha de nacimiento`;
- no reaparece la columna `Clase`.

- [ ] **Step 7: Verificar PDF real**

Hacer POST a la misma ruta usando `btnLista=Lista`, guardar el PDF y comprobar visualmente que:

- ambos rótulos aparecen en el pie;
- los nombres coinciden con el equipo 21 o el espacio queda vacío;
- las firmas y el resto del pie no se superponen;
- la tabla de jugadores y la fecha de nacimiento se mantienen.

- [ ] **Step 8: Registrar evidencia final**

Guardar para el informe final:

- salida de pruebas locales;
- nombres y MD5 de los cuatro archivos subidos;
- confirmación de columnas de producción;
- estado HTTP y tamaño de las salidas PDF/Excel;
- resultado visual de los dos rótulos con nombre vacío y con nombre cargado.

No conservar ni informar credenciales, token del migrador o datos sensibles.
