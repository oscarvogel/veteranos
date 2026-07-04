<?php
require __DIR__ . '/../app/app_loader.php';

function assertImportValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, $message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

function assertImportTrue($actual, $message) {
    assertImportValue(true, $actual, $message);
}

function writeMinimalXlsx($path, array $rows) {
    $shared = [];
    $sharedIndex = [];
    $sheetRows = [];
    foreach ($rows as $rowIndex => $row) {
        $cells = [];
        foreach ($row as $columnIndex => $value) {
            $key = (string)$value;
            if (!array_key_exists($key, $sharedIndex)) {
                $sharedIndex[$key] = count($shared);
                $shared[] = $key;
            }
            $column = chr(65 + $columnIndex);
            $cells[] = '<c r="' . $column . ($rowIndex + 1) . '" t="s"><v>' . $sharedIndex[$key] . '</v></c>';
        }
        $sheetRows[] = '<row r="' . ($rowIndex + 1) . '">' . implode('', $cells) . '</row>';
    }

    writeStoredZip($path, [
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Hoja1" sheetId="1" r:id="rId1"/></sheets></workbook>',
        'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>',
        'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . implode('', $sheetRows) . '</sheetData></worksheet>',
        'xl/sharedStrings.xml' => '<?xml version="1.0" encoding="UTF-8"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($shared) . '" uniqueCount="' . count($shared) . '">' . implode('', array_map(function ($value) {
        return '<si><t>' . htmlspecialchars($value, ENT_XML1) . '</t></si>';
    }, $shared)) . '</sst>',
    ]);
}

function writeStoredZip($path, array $entries) {
    $zip = '';
    $central = '';
    foreach ($entries as $name => $content) {
        $offset = strlen($zip);
        $crc = crc32($content);
        $size = strlen($content);
        $nameLength = strlen($name);
        $zip .= pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $size, $size, $nameLength, 0);
        $zip .= $name . $content;

        $central .= pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, 0, 0, $crc, $size, $size, $nameLength, 0, 0, 0, 0, 0, $offset);
        $central .= $name;
    }

    $zip .= $central;
    $zip .= pack('VvvvvVVv', 0x06054b50, 0, 0, count($entries), count($entries), strlen($central), strlen($zip) - strlen($central), 0);
    file_put_contents($path, $zip);
}

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$db->exec('CREATE TABLE equipos (idEquipo INTEGER PRIMARY KEY, Nombre TEXT)');
$db->exec('CREATE TABLE jugador (
    idJugador INTEGER PRIMARY KEY AUTOINCREMENT,
    Nombre TEXT,
    Clase TEXT,
    DNI TEXT,
    idEquipo INTEGER,
    fecha_nacimiento TEXT
)');
$db->exec("INSERT INTO equipos (idEquipo, Nombre) VALUES (7, 'Barrio Super')");
$db->exec("INSERT INTO jugador (Nombre, Clase, DNI, idEquipo, fecha_nacimiento) VALUES
    ('Gimenez Oscar Antonio', '', '29468408', NULL, NULL),
    ('Baumgratz Gerardo Walter', '1980', '28271579', 1, '1980-08-07'),
    ('Jugador Sin Fecha', '', '23385058', NULL, '')");

$csv = tempnam(sys_get_temp_dir(), 'lista_buena_fe_') . '.csv';
file_put_contents($csv, implode("\n", [
    'apellido_y_nombre,fecha_nacimiento,n_doc',
    'Gimenez Oscar Antonio,27/11/82,29468408',
    'Baumgratz Gerardo Walter,07/08/80,28271579',
    'No Existe,31/10/73,23558677',
    'Jugador Sin Fecha,07/06/72,23385058',
]));

$model = new \App\Model\JugadorModel($db);
$result = $model->ImportarListaBuenaFeArchivo($csv, 7, 'lista.csv');
unlink($csv);

assertImportTrue($result->response, 'import returns success');
assertImportValue(4, $result->result['total'], 'counts all data rows');
assertImportValue(3, $result->result['asignados'], 'assigns found players');
assertImportValue(1, $result->result['no_encontrados'], 'reports missing players');
assertImportValue(['23558677'], $result->result['dni_no_encontrados'], 'returns missing DNI list');
assertImportValue(2, $result->result['fechas_actualizadas'], 'updates only blank birth dates');

$rows = $db->query('SELECT DNI, idEquipo, fecha_nacimiento, Clase FROM jugador ORDER BY DNI')->fetchAll(PDO::FETCH_OBJ);
$byDni = [];
foreach ($rows as $row) {
    $byDni[$row->DNI] = $row;
}

assertImportValue(7, (int)$byDni['29468408']->idEquipo, 'assigns selected team to matching player');
assertImportValue('1982-11-27', $byDni['29468408']->fecha_nacimiento, 'fills blank two-digit-year birth date');
assertImportValue('1982', $byDni['29468408']->Clase, 'updates Clase from filled birth year');
assertImportValue('1980-08-07', $byDni['28271579']->fecha_nacimiento, 'keeps existing birth date');
assertImportValue('1972-06-07', $byDni['23385058']->fecha_nacimiento, 'fills empty-string birth date');

$db->exec("UPDATE jugador SET idEquipo = NULL, fecha_nacimiento = NULL, Clase = '' WHERE DNI = '29468408'");
$xlsx = tempnam(sys_get_temp_dir(), 'lista_buena_fe_') . '.xlsx';
writeMinimalXlsx($xlsx, [
    ['apellido_y_nombre', 'fecha_nacimiento', 'n_doc'],
    ['Gimenez Oscar Antonio', '27/11/82', '29468408'],
]);

$result = $model->ImportarListaBuenaFeArchivo($xlsx, 7, 'lista.xlsx');
unlink($xlsx);

if (!$result->response) {
    fwrite(STDERR, "xlsx import error: " . $result->message . "\n");
}
assertImportTrue($result->response, 'xlsx import returns success');
assertImportValue(1, $result->result['asignados'], 'xlsx assigns found player');
$jugadorXlsx = $db->query("SELECT idEquipo, fecha_nacimiento FROM jugador WHERE DNI = '29468408'")->fetch(PDO::FETCH_OBJ);
assertImportValue(7, (int)$jugadorXlsx->idEquipo, 'xlsx assigns selected team');
assertImportValue('1982-11-27', $jugadorXlsx->fecha_nacimiento, 'xlsx fills blank birth date');

fwrite(STDOUT, "jugador_import_lista_buena_fe_test OK\n");
