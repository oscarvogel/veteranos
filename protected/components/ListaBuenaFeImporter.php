<?php

class ListaBuenaFeImporter
{
	public function importarArchivo($filePath, $originalName, $idEquipo)
	{
		$idEquipo = (int)$idEquipo;
		if($idEquipo <= 0)
			throw new CException('Debe seleccionar un equipo.');

		if(!is_file($filePath))
			throw new CException('Archivo no encontrado.');

		if(Equipos::model()->findByPk($idEquipo) === null)
			throw new CException('El equipo seleccionado no existe.');

		$filas = $this->leerArchivo($filePath, $originalName);
		if(count($filas) === 0)
			throw new CException('El archivo no contiene filas para importar.');

		$resumen = array(
			'total' => 0,
			'asignados' => 0,
			'no_encontrados' => 0,
			'dni_no_encontrados' => array(),
			'fechas_actualizadas' => 0,
			'fechas_invalidas' => array(),
		);

		$transaction = Yii::app()->db->beginTransaction();
		try
		{
			foreach($filas as $indice => $fila)
			{
				$resumen['total']++;
				$dni = $this->normalizarDni($this->valorCampo($fila, array('dni', 'documento', 'n_doc', 'nro_doc', 'nro_documento', 'numero_documento')));
				if($dni === '')
				{
					$resumen['no_encontrados']++;
					$resumen['dni_no_encontrados'][] = 'fila ' . ($indice + 2) . ' sin DNI';
					continue;
				}

				$jugador = $this->buscarJugadorPorDni($dni);
				if($jugador === false)
				{
					$resumen['no_encontrados']++;
					$resumen['dni_no_encontrados'][] = $dni;
					continue;
				}

				$fechaArchivo = $this->normalizarFecha($this->valorCampo($fila, array('fecha_nacimiento', 'fecha_nac', 'nacimiento', 'fecha')));
				if($fechaArchivo === false)
				{
					$resumen['fechas_invalidas'][] = $dni;
					$fechaArchivo = '';
				}

				$campos = array('idEquipo' => $idEquipo);
				if($this->fechaVacia($jugador['fecha_nacimiento']) && $fechaArchivo !== '')
				{
					$campos['fecha_nacimiento'] = $fechaArchivo;
					$campos['Clase'] = substr($fechaArchivo, 0, 4);
					$resumen['fechas_actualizadas']++;
				}

				Yii::app()->db->createCommand()->update(
					'jugador',
					$campos,
					'idJugador=:idJugador',
					array(':idJugador' => (int)$jugador['idJugador'])
				);
				$resumen['asignados']++;
			}

			$transaction->commit();
		}
		catch(Exception $e)
		{
			$transaction->rollback();
			throw $e;
		}

		return $resumen;
	}

	private function buscarJugadorPorDni($dni)
	{
		return Yii::app()->db->createCommand()
			->select('idJugador, DNI, idEquipo, fecha_nacimiento')
			->from('jugador')
			->where('DNI=:dni OR REPLACE(REPLACE(DNI, ".", ""), "-", "")=:dni', array(':dni' => $dni))
			->limit(1)
			->queryRow();
	}

	private function leerArchivo($filePath, $originalName)
	{
		$extension = strtolower(pathinfo($originalName ? $originalName : $filePath, PATHINFO_EXTENSION));
		if($extension === 'csv')
			return $this->leerCsv($filePath);

		if($extension === 'xlsx')
			return $this->leerXlsx($filePath);

		throw new CException('Formato no soportado. Use CSV o XLSX.');
	}

	private function leerCsv($filePath)
	{
		$handle = fopen($filePath, 'r');
		if(!$handle)
			throw new CException('No se pudo abrir el archivo CSV.');

		$encabezados = null;
		$filas = array();
		while(($data = fgetcsv($handle, 0, ',')) !== false)
		{
			if($encabezados === null)
			{
				$encabezados = $this->normalizarEncabezados($data);
				continue;
			}

			if(!$this->filaVacia($data))
				$filas[] = $this->combinarFila($encabezados, $data);
		}
		fclose($handle);

		return $filas;
	}

	private function leerXlsx($filePath)
	{
		$sharedStrings = $this->leerSharedStringsXlsx($this->leerZipEntry($filePath, 'xl/sharedStrings.xml'));
		$sheetXml = $this->leerZipEntry($filePath, 'xl/worksheets/sheet1.xml');
		if($sheetXml === false)
			throw new CException('El XLSX no contiene la primera hoja.');

		$sheet = simplexml_load_string($sheetXml);
		if(!$sheet)
			throw new CException('No se pudo leer la primera hoja del XLSX.');

		$encabezados = null;
		$filas = array();
		foreach($sheet->sheetData->row as $rowXml)
		{
			$filaPorColumna = array();
			foreach($rowXml->c as $cell)
			{
				$ref = (string)$cell['r'];
				$columna = $this->columnaXlsxAIndice(preg_replace('/\d+/', '', $ref));
				$value = isset($cell->v) ? (string)$cell->v : '';
				if((string)$cell['t'] === 's')
					$value = isset($sharedStrings[(int)$value]) ? $sharedStrings[(int)$value] : '';
				elseif((string)$cell['t'] === 'inlineStr' && isset($cell->is->t))
					$value = (string)$cell->is->t;

				$filaPorColumna[$columna] = $value;
			}

			if(empty($filaPorColumna))
				continue;

			$max = max(array_keys($filaPorColumna));
			$data = array();
			for($i = 0; $i <= $max; $i++)
				$data[] = isset($filaPorColumna[$i]) ? $filaPorColumna[$i] : '';

			if($encabezados === null)
			{
				$encabezados = $this->normalizarEncabezados($data);
				continue;
			}

			if(!$this->filaVacia($data))
				$filas[] = $this->combinarFila($encabezados, $data);
		}

		return $filas;
	}

	private function leerZipEntry($filePath, $entry)
	{
		if(class_exists('ZipArchive'))
		{
			$zip = new ZipArchive();
			if($zip->open($filePath) !== true)
				throw new CException('No se pudo abrir el archivo XLSX.');
			$content = $zip->getFromName($entry);
			$zip->close();
			return $content;
		}

		return $this->leerZipEntryManual($filePath, $entry);
	}

	private function leerZipEntryManual($filePath, $entry)
	{
		$data = file_get_contents($filePath);
		if($data === false)
			throw new CException('No se pudo abrir el archivo XLSX.');

		$eocdOffset = strrpos($data, "PK\x05\x06");
		if($eocdOffset === false)
			throw new CException('El archivo XLSX no tiene un directorio ZIP valido.');

		$eocd = unpack('Vsig/vdisk/vcentralDisk/ventriesDisk/ventries/VcentralSize/VcentralOffset/vcommentLength', substr($data, $eocdOffset, 22));
		$offset = (int)$eocd['centralOffset'];
		$entries = (int)$eocd['entries'];

		for($i = 0; $i < $entries; $i++)
		{
			$header = substr($data, $offset, 46);
			if(strlen($header) < 46)
				return false;

			$central = unpack('Vsig/vmade/vversion/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength/vcommentLength/vdisk/vinternal/Vexternal/VlocalOffset', $header);
			if((int)$central['sig'] !== 0x02014b50)
				return false;

			$name = substr($data, $offset + 46, (int)$central['nameLength']);
			if($name === $entry)
				return $this->leerZipEntryDesdeLocalHeader($data, $central);

			$offset += 46 + (int)$central['nameLength'] + (int)$central['extraLength'] + (int)$central['commentLength'];
		}

		return false;
	}

	private function leerZipEntryDesdeLocalHeader($data, $central)
	{
		$localOffset = (int)$central['localOffset'];
		$localHeader = substr($data, $localOffset, 30);
		if(strlen($localHeader) < 30)
			return false;

		$local = unpack('Vsig/vversion/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength', $localHeader);
		if((int)$local['sig'] !== 0x04034b50)
			return false;

		$contentOffset = $localOffset + 30 + (int)$local['nameLength'] + (int)$local['extraLength'];
		$compressed = substr($data, $contentOffset, (int)$central['compressed']);
		$method = (int)$central['method'];

		if($method === 0)
			return $compressed;

		if($method === 8)
		{
			$inflated = gzinflate($compressed);
			if($inflated === false)
				throw new CException('No se pudo descomprimir una hoja del XLSX.');
			return $inflated;
		}

		throw new CException('El XLSX usa un metodo de compresion no soportado.');
	}

	private function leerSharedStringsXlsx($xml)
	{
		if($xml === false || $xml === '')
			return array();

		$shared = simplexml_load_string($xml);
		if(!$shared)
			return array();

		$values = array();
		foreach($shared->si as $si)
		{
			if(isset($si->t))
			{
				$values[] = (string)$si->t;
				continue;
			}

			$text = '';
			foreach($si->r as $run)
				$text .= (string)$run->t;
			$values[] = $text;
		}

		return $values;
	}

	private function columnaXlsxAIndice($column)
	{
		$column = strtoupper($column);
		$index = 0;
		for($i = 0; $i < strlen($column); $i++)
			$index = $index * 26 + (ord($column[$i]) - 64);
		return $index - 1;
	}

	private function normalizarEncabezados($headers)
	{
		$normalized = array();
		foreach($headers as $header)
			$normalized[] = $this->normalizarClave($header);
		return $normalized;
	}

	private function combinarFila($headers, $data)
	{
		$row = array();
		foreach($headers as $index => $header)
		{
			if($header === '')
				continue;
			$row[$header] = trim((string)(isset($data[$index]) ? $data[$index] : ''));
		}
		return $row;
	}

	private function filaVacia($data)
	{
		foreach($data as $value)
		{
			if(trim((string)$value) !== '')
				return false;
		}
		return true;
	}

	private function valorCampo($row, $aliases)
	{
		foreach($aliases as $alias)
		{
			$key = $this->normalizarClave($alias);
			if(array_key_exists($key, $row))
				return $row[$key];
		}
		return '';
	}

	private function normalizarClave($value)
	{
		$value = strtolower(trim((string)$value));
		$value = str_replace(
			array('á', 'é', 'í', 'ó', 'ú', 'ñ', ' '),
			array('a', 'e', 'i', 'o', 'u', 'n', '_'),
			$value
		);
		return preg_replace('/[^a-z0-9_]/', '', $value);
	}

	private function normalizarDni($dni)
	{
		return preg_replace('/\D+/', '', (string)$dni);
	}

	private function normalizarFecha($fecha)
	{
		$fecha = trim((string)$fecha);
		if($fecha === '')
			return '';

		if(is_numeric($fecha) && (float)$fecha > 25569)
			return gmdate('Y-m-d', ((int)$fecha - 25569) * 86400);

		if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $fecha, $m))
		{
			$year = (int)$m[1];
			$month = (int)$m[2];
			$day = (int)$m[3];
		}
		elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2}|\d{4})$/', $fecha, $m))
		{
			$day = (int)$m[1];
			$month = (int)$m[2];
			$year = (int)$m[3];
			if($year < 100)
				$year += $year >= 30 ? 1900 : 2000;
		}
		else
			return false;

		if(!checkdate($month, $day, $year))
			return false;

		return sprintf('%04d-%02d-%02d', $year, $month, $day);
	}

	private function fechaVacia($fecha)
	{
		$fecha = trim((string)$fecha);
		return $fecha === '' || $fecha === '0000-00-00';
	}
}
