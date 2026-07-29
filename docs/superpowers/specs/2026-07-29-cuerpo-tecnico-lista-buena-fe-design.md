# Cuerpo técnico en la lista de buena fe

## Objetivo

Permitir registrar el técnico y el ayudante de técnico de cada equipo desde el formulario clásico de Yii y mostrar ambos datos en las salidas PDF y Excel de la lista de buena fe.

## Alcance

- Agregar dos atributos persistentes a la tabla `equipos`:
  - `Tecnico VARCHAR(100) NOT NULL DEFAULT ''`
  - `AyudanteTecnico VARCHAR(100) NOT NULL DEFAULT ''`
- Incorporar los campos `Técnico` y `Ayudante de técnico` al formulario usado por las acciones `equipos/create` y `equipos/update`.
- Actualizar el modelo `Equipos` para permitir asignación masiva, validar una longitud máxima de 100 caracteres y exponer las etiquetas en español.
- Mostrar los dos datos en el PDF de la lista de buena fe.
- Mostrar los dos datos en la exportación Excel de la lista de buena fe.
- Mantener siempre visibles los rótulos correspondientes. Cuando el nombre no esté cargado, el espacio del nombre debe quedar vacío.

## Diseño de datos y migración

La información pertenece al equipo, por lo que se almacenará directamente en `equipos`. No se creará una tabla separada porque el requerimiento contempla exactamente un técnico y un ayudante por equipo.

Se agregará un script SQL versionado y repetible. El script consultará `information_schema.COLUMNS` y ejecutará cada `ALTER TABLE` solamente cuando la columna todavía no exista. Esto permitirá aplicarlo sin sobrescribir información ni fallar si una columna ya fue creada.

Los valores existentes quedarán como cadena vacía mediante `NOT NULL DEFAULT ''`.

## Formulario y modelo

El formulario clásico `protected/views/equipos/_form.php` incluirá dos campos de texto próximos a los datos de delegados:

- `Técnico`
- `Ayudante de técnico`

El modelo `protected/models/Equipos.php` incluirá ambos atributos en la regla de longitud de 100 caracteres, en el escenario de búsqueda seguro y en `attributeLabels()`. Yii ActiveRecord detectará las columnas desde el esquema de la tabla y las acciones actuales de alta y modificación seguirán guardando mediante asignación masiva.

## Salidas de lista de buena fe

### PDF

En `protected/views/equipos/pdf.php` se conservarán los renglones y rótulos ya existentes. La salida será:

- `Técnico: <nombre>`
- `Ayudante de técnico: <nombre>`

Si el valor está vacío, se imprimirá solamente `Técnico:` o `Ayudante de técnico:`. Las firmas permanecerán en sus celdas actuales.

### Excel

En `protected/views/equipos/excel.php` se agregará un bloque identificatorio antes de la tabla de jugadores:

- `Técnico: <nombre>`
- `Ayudante de técnico: <nombre>`

Los valores se escaparán antes de renderizarse. Si están vacíos, se mantendrá el rótulo y la celda del nombre quedará vacía.

## Compatibilidad y errores

- No se modifica la selección de jugadores ni la lógica de torneos.
- Los equipos existentes continúan funcionando gracias a los valores predeterminados vacíos.
- La aplicación no se desplegará antes de aplicar la migración, para evitar errores de esquema al guardar o leer los atributos nuevos.
- El cambio mantiene compatibilidad con PHP 5/7 y Yii 1.1; no utilizará sintaxis exclusiva de PHP 8.

## Pruebas y verificación

Se crearán pruebas de regresión ejecutables con PHP para comprobar:

- que el modelo admite y etiqueta ambos atributos;
- que el formulario presenta ambos campos;
- que PDF y Excel muestran siempre los rótulos;
- que ambas salidas utilizan los valores del equipo y los escapan;
- que el script de migración contiene las dos columnas y controles de existencia.

Después se ejecutará `php -l` sobre cada PHP modificado y las pruebas específicas de lista de buena fe.

Para producción se seguirá este orden:

1. Respaldar la estructura y los datos afectados.
2. Ejecutar la migración SQL.
3. Subir por FTP TLS solamente los archivos PHP modificados.
4. Verificar por MD5 cada archivo subido.
5. Cargar temporalmente valores de prueba controlados o usar un equipo que ya tenga los datos.
6. Descargar una lista PDF y una exportación Excel reales.
7. Confirmar que ambas muestran los rótulos y los nombres esperados, y que los rótulos siguen presentes cuando un valor está vacío.

## Fuera de alcance

- Historial de técnicos por torneo o temporada.
- Más de un técnico o ayudante por equipo.
- Cambios en la interfaz nueva o en endpoints de API.
- Rediseño del formulario legacy o del documento completo de lista de buena fe.
