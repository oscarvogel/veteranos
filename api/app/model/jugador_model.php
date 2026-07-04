<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;
use PDO;
use Exception;

class JugadorModel
{
    private $db;
    private $table = 'jugador';

    public function __construct(PDO $db = null)
    {
        $this->db = $db ?: Database::StartUp();
    }

    public function GetAll()
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT j.*, e.Nombre AS equipo
                 FROM {$this->table} j
                 LEFT JOIN equipos e ON j.idEquipo = e.idEquipo
                 ORDER BY j.Nombre ASC"
            );
            $stm->execute();
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function GetByEquipo($idEquipo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE idEquipo = ? ORDER BY Nombre ASC"
            );
            $stm->execute([(int)$idEquipo]);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Get($id)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT j.*, e.Nombre AS equipo
                 FROM {$this->table} j
                 LEFT JOIN equipos e ON j.idEquipo = e.idEquipo
                 WHERE j.idJugador = ?"
            );
            $stm->execute([(int)$id]);
            $r->result = $stm->fetch(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    /** Historia del jugador en todos los torneos */
    public function GetHistoria($idJugador)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT hj.*, e.Nombre AS equipo, t.Nombre AS torneo
                 FROM historicojugador hj
                 JOIN equipos e ON hj.idEquipo = e.idEquipo
                 JOIN torneo t ON hj.idTorneo = t.idTorneo
                 WHERE hj.idJugador = ?
                 ORDER BY t.idTorneo DESC"
            );
            $stm->execute([(int)$idJugador]);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Save($data)
    {
        $r = new Response();
        try {
            if (empty($data['idEquipo'])) {
                $r->SetResponse(false, 'idEquipo es requerido');
                return $r;
            }
            $fields = [
                'Nombre'       => htmlspecialchars(strip_tags($data['Nombre'] ?? '')),
                'Clase'        => htmlspecialchars(strip_tags($data['Clase'] ?? '')),
                'DNI'          => htmlspecialchars(strip_tags($data['DNI'] ?? '')),
                'idEquipo'     => (int)$data['idEquipo'],
                'Observacion'  => $data['Observacion'] ?? '',
                'certificado'  => isset($data['certificado']) ? (int)$data['certificado'] : 0,
                'firma_lista'  => isset($data['firma_lista']) ? (int)$data['firma_lista'] : 0,
                'fotocopia_dni'=> isset($data['fotocopia_dni']) ? (int)$data['fotocopia_dni'] : 0,
                'dec_jurada'   => isset($data['dec_jurada']) ? (int)$data['dec_jurada'] : 0,
            ];

            if (!empty($data['idJugador'])) {
                $stm = $this->db->prepare(
                    "UPDATE {$this->table} SET
                        Nombre=?, Clase=?, DNI=?, idEquipo=?, Observacion=?,
                        certificado=?, firma_lista=?, fotocopia_dni=?, dec_jurada=?
                     WHERE idJugador=?"
                );
                $stm->execute([...array_values($fields), (int)$data['idJugador']]);
                $r->result = ['idJugador' => (int)$data['idJugador']];
            } else {
                $stm = $this->db->prepare(
                    "INSERT INTO {$this->table}
                        (Nombre, Clase, DNI, idEquipo, Observacion, certificado, firma_lista, fotocopia_dni, dec_jurada)
                     VALUES (?,?,?,?,?,?,?,?,?)"
                );
                $stm->execute(array_values($fields));
                $r->result = ['idJugador' => $this->db->lastInsertId()];
            }
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Liberar($id)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("UPDATE {$this->table} SET idEquipo = NULL WHERE idJugador = ?");
            $stm->execute([(int)$id]);
            $r->result = ['liberado' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Asignar($idJugador, $idEquipo)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("UPDATE {$this->table} SET idEquipo = ? WHERE idJugador = ?");
            $stm->execute([(int)$idEquipo, (int)$idJugador]);
            $r->result = ['asignado' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Delete($id)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare("DELETE FROM {$this->table} WHERE idJugador = ?");
            $stm->execute([(int)$id]);
            $r->result = ['deleted' => $stm->rowCount() > 0];
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function Autocomplete($q)
    {
        $r = new Response();
        try {
            $stm = $this->db->prepare(
                "SELECT j.idJugador AS id, CONCAT(j.Nombre, ' (', COALESCE(e.Nombre,'sin equipo'), ')') AS label
                 FROM {$this->table} j
                 LEFT JOIN equipos e ON j.idEquipo = e.idEquipo
                 WHERE j.Nombre LIKE ? ORDER BY j.Nombre ASC LIMIT 10"
            );
            $stm->execute(["%{$q}%"]);
            $r->result = $stm->fetchAll(PDO::FETCH_OBJ);
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    public function ImportarListaBuenaFeArchivo($filePath, $idEquipo, $originalName = '')
    {
        $r = new Response();
        $idEquipo = (int)$idEquipo;

        try {
            if ($idEquipo <= 0) {
                $r->SetResponse(false, 'Debe seleccionar un equipo.');
                return $r;
            }

            if (!is_file($filePath)) {
                $r->SetResponse(false, 'Archivo no encontrado.');
                return $r;
            }

            if (!$this->ExisteEquipo($idEquipo)) {
                $r->SetResponse(false, 'El equipo seleccionado no existe.');
                return $r;
            }

            $rows = $this->LeerArchivoListaBuenaFe($filePath, $originalName);
            if (count($rows) === 0) {
                $r->SetResponse(false, 'El archivo no contiene filas para importar.');
                return $r;
            }

            $resumen = [
                'total' => 0,
                'asignados' => 0,
                'no_encontrados' => 0,
                'dni_no_encontrados' => [],
                'fechas_actualizadas' => 0,
                'fechas_invalidas' => [],
            ];

            $this->db->beginTransaction();
            foreach ($rows as $index => $row) {
                $resumen['total']++;
                $dni = $this->NormalizarDni($this->ValorCampo($row, ['dni', 'documento', 'n_doc', 'nro_doc', 'nro_documento', 'numero_documento']));
                if ($dni === '') {
                    $resumen['no_encontrados']++;
                    $resumen['dni_no_encontrados'][] = 'fila ' . ($index + 2) . ' sin DNI';
                    continue;
                }

                $jugador = $this->BuscarJugadorPorDni($dni);
                if (!$jugador) {
                    $resumen['no_encontrados']++;
                    $resumen['dni_no_encontrados'][] = $dni;
                    continue;
                }

                $fechaArchivo = $this->NormalizarFecha($this->ValorCampo($row, ['fecha_nacimiento', 'fecha_nac', 'nacimiento', 'fecha']));
                if ($fechaArchivo === false) {
                    $resumen['fechas_invalidas'][] = $dni;
                    $fechaArchivo = '';
                }

                $fechaActual = trim((string)($jugador->fecha_nacimiento ?? ''));
                if ($fechaActual === '' && $fechaArchivo !== '') {
                    $stm = $this->db->prepare("UPDATE {$this->table} SET idEquipo = ?, fecha_nacimiento = ?, Clase = ? WHERE idJugador = ?");
                    $stm->execute([$idEquipo, $fechaArchivo, substr($fechaArchivo, 0, 4), (int)$jugador->idJugador]);
                    $resumen['fechas_actualizadas']++;
                } else {
                    $stm = $this->db->prepare("UPDATE {$this->table} SET idEquipo = ? WHERE idJugador = ?");
                    $stm->execute([$idEquipo, (int)$jugador->idJugador]);
                }
                $resumen['asignados']++;
            }
            $this->db->commit();

            $r->result = $resumen;
            $r->SetResponse(true, '');
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $r->SetResponse(false, $e->getMessage());
        }
        return $r;
    }

    private function ExisteEquipo($idEquipo)
    {
        $stm = $this->db->prepare('SELECT COUNT(*) FROM equipos WHERE idEquipo = ?');
        $stm->execute([(int)$idEquipo]);
        return (int)$stm->fetchColumn() > 0;
    }

    private function BuscarJugadorPorDni($dni)
    {
        $stm = $this->db->prepare(
            "SELECT idJugador, DNI, idEquipo, fecha_nacimiento
             FROM {$this->table}
             WHERE DNI = ?
                OR REPLACE(REPLACE(DNI, '.', ''), '-', '') = ?
             LIMIT 1"
        );
        $stm->execute([$dni, $dni]);
        return $stm->fetch(PDO::FETCH_OBJ);
    }

    private function LeerArchivoListaBuenaFe($filePath, $originalName)
    {
        $name = $originalName ?: $filePath;
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            return $this->LeerCsvListaBuenaFe($filePath);
        }

        if ($ext === 'xlsx') {
            return $this->LeerXlsxListaBuenaFe($filePath);
        }

        throw new Exception('Formato no soportado. Use CSV o XLSX.');
    }

    private function LeerCsvListaBuenaFe($filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception('No se pudo abrir el archivo CSV.');
        }

        $headers = null;
        $rows = [];
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($headers === null) {
                $headers = $this->NormalizarEncabezados($data);
                continue;
            }

            if ($this->FilaVacia($data)) {
                continue;
            }

            $rows[] = $this->CombinarFila($headers, $data);
        }
        fclose($handle);
        return $rows;
    }

    private function LeerXlsxListaBuenaFe($filePath)
    {
        $sharedStrings = $this->LeerSharedStringsXlsx($this->LeerZipEntry($filePath, 'xl/sharedStrings.xml'));
        $sheetXml = $this->LeerZipEntry($filePath, 'xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            throw new Exception('El XLSX no contiene la primera hoja.');
        }

        $sheet = simplexml_load_string($sheetXml);
        if (!$sheet) {
            throw new Exception('No se pudo leer la primera hoja del XLSX.');
        }

        $headers = null;
        $rows = [];
        foreach ($sheet->sheetData->row as $rowXml) {
            $row = [];
            foreach ($rowXml->c as $cell) {
                $ref = (string)$cell['r'];
                $column = $this->ColumnaXlsxAIndice(preg_replace('/\d+/', '', $ref));
                $value = isset($cell->v) ? (string)$cell->v : '';
                if ((string)$cell['t'] === 's') {
                    $value = $sharedStrings[(int)$value] ?? '';
                } elseif ((string)$cell['t'] === 'inlineStr' && isset($cell->is->t)) {
                    $value = (string)$cell->is->t;
                }
                $row[$column] = $value;
            }

            if (empty($row)) {
                continue;
            }

            $max = max(array_keys($row));
            $data = [];
            for ($i = 0; $i <= $max; $i++) {
                $data[] = $row[$i] ?? '';
            }

            if ($headers === null) {
                $headers = $this->NormalizarEncabezados($data);
                continue;
            }

            if (!$this->FilaVacia($data)) {
                $rows[] = $this->CombinarFila($headers, $data);
            }
        }

        return $rows;
    }

    private function LeerZipEntry($filePath, $entry)
    {
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                throw new Exception('No se pudo abrir el archivo XLSX.');
            }
            $content = $zip->getFromName($entry);
            $zip->close();
            return $content;
        }

        return $this->LeerZipEntryManual($filePath, $entry);
    }

    private function LeerZipEntryManual($filePath, $entry)
    {
        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new Exception('No se pudo abrir el archivo XLSX.');
        }

        $eocdOffset = strrpos($data, "PK\x05\x06");
        if ($eocdOffset === false) {
            throw new Exception('El archivo XLSX no tiene un directorio ZIP valido.');
        }

        $eocd = unpack('Vsig/vdisk/vcentralDisk/ventriesDisk/ventries/VcentralSize/VcentralOffset/vcommentLength', substr($data, $eocdOffset, 22));
        $offset = (int)$eocd['centralOffset'];
        $entries = (int)$eocd['entries'];

        for ($i = 0; $i < $entries; $i++) {
            $header = substr($data, $offset, 46);
            if (strlen($header) < 46) {
                return false;
            }

            $central = unpack(
                'Vsig/vmade/vversion/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength/vcommentLength/vdisk/vinternal/Vexternal/VlocalOffset',
                $header
            );
            if ((int)$central['sig'] !== 0x02014b50) {
                return false;
            }

            $name = substr($data, $offset + 46, (int)$central['nameLength']);
            if ($name === $entry) {
                return $this->LeerZipEntryDesdeLocalHeader($data, $central);
            }

            $offset += 46 + (int)$central['nameLength'] + (int)$central['extraLength'] + (int)$central['commentLength'];
        }

        return false;
    }

    private function LeerZipEntryDesdeLocalHeader($data, array $central)
    {
        $localOffset = (int)$central['localOffset'];
        $localHeader = substr($data, $localOffset, 30);
        if (strlen($localHeader) < 30) {
            return false;
        }

        $local = unpack('Vsig/vversion/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength', $localHeader);
        if ((int)$local['sig'] !== 0x04034b50) {
            return false;
        }

        $contentOffset = $localOffset + 30 + (int)$local['nameLength'] + (int)$local['extraLength'];
        $compressed = substr($data, $contentOffset, (int)$central['compressed']);
        $method = (int)$central['method'];

        if ($method === 0) {
            return $compressed;
        }

        if ($method === 8) {
            $inflated = gzinflate($compressed);
            if ($inflated === false) {
                throw new Exception('No se pudo descomprimir una hoja del XLSX.');
            }
            return $inflated;
        }

        throw new Exception('El XLSX usa un metodo de compresion no soportado.');
    }

    private function LeerSharedStringsXlsx($xml)
    {
        if ($xml === false || $xml === '') {
            return [];
        }

        $shared = simplexml_load_string($xml);
        if (!$shared) {
            return [];
        }

        $values = [];
        foreach ($shared->si as $si) {
            if (isset($si->t)) {
                $values[] = (string)$si->t;
                continue;
            }

            $text = '';
            foreach ($si->r as $run) {
                $text .= (string)$run->t;
            }
            $values[] = $text;
        }
        return $values;
    }

    private function ColumnaXlsxAIndice($column)
    {
        $column = strtoupper($column);
        $index = 0;
        for ($i = 0; $i < strlen($column); $i++) {
            $index = $index * 26 + (ord($column[$i]) - 64);
        }
        return $index - 1;
    }

    private function NormalizarEncabezados(array $headers)
    {
        $normalized = [];
        foreach ($headers as $header) {
            $normalized[] = $this->NormalizarClave($header);
        }
        return $normalized;
    }

    private function CombinarFila(array $headers, array $data)
    {
        $row = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }
            $row[$header] = trim((string)($data[$index] ?? ''));
        }
        return $row;
    }

    private function FilaVacia(array $data)
    {
        foreach ($data as $value) {
            if (trim((string)$value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function ValorCampo(array $row, array $aliases)
    {
        foreach ($aliases as $alias) {
            $key = $this->NormalizarClave($alias);
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }
        }
        return '';
    }

    private function NormalizarClave($value)
    {
        $value = strtolower(trim((string)$value));
        $value = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', ' '],
            ['a', 'e', 'i', 'o', 'u', 'n', '_'],
            $value
        );
        return preg_replace('/[^a-z0-9_]/', '', $value);
    }

    private function NormalizarDni($dni)
    {
        return preg_replace('/\D+/', '', (string)$dni);
    }

    private function NormalizarFecha($fecha)
    {
        $fecha = trim((string)$fecha);
        if ($fecha === '') {
            return '';
        }

        if (is_numeric($fecha) && (float)$fecha > 25569) {
            return gmdate('Y-m-d', ((int)$fecha - 25569) * 86400);
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $fecha, $m)) {
            $year = (int)$m[1];
            $month = (int)$m[2];
            $day = (int)$m[3];
        } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2}|\d{4})$/', $fecha, $m)) {
            $day = (int)$m[1];
            $month = (int)$m[2];
            $year = (int)$m[3];
            if ($year < 100) {
                $year += $year >= 30 ? 1900 : 2000;
            }
        } else {
            return false;
        }

        if (!checkdate($month, $day, $year)) {
            return false;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
