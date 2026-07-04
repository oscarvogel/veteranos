<?php

/**
 * Modelo de documentos del legajo digital de un jugador.
 */
class JugadorDocumento extends CActiveRecord
{
	const TIPO_DNI = 'dni';
	const TIPO_CERTIFICADO_SALUD = 'certificado_salud';
	const TIPO_FIRMA_LISTA = 'firma_lista';
	const TIPO_DECLARACION_JURADA = 'declaracion_jurada';
	const TIPO_ADICIONAL = 'adicional';
	const MAX_FILE_SIZE = 10485760;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'jugador_documento';
	}

	public function rules()
	{
		return array(
			array('idJugador, tipo, archivo_original, archivo_guardado, mime_type, extension, tamano_bytes', 'required'),
			array('idJugador, idUsuario, tamano_bytes', 'numerical', 'integerOnly'=>true),
			array('tipo', 'in', 'range'=>array_keys(self::getTipos())),
			array('archivo_original, archivo_guardado', 'length', 'max'=>255),
			array('mime_type', 'length', 'max'=>100),
			array('extension', 'length', 'max'=>10),
			array('titulo', 'length', 'max'=>120),
			array('observacion', 'length', 'max'=>500),
			array('created_at, updated_at', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'Jugador' => array(self::BELONGS_TO, 'Jugador', 'idJugador'),
			'Usuario' => array(self::BELONGS_TO, 'CrugeUser', 'idUsuario'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'idDocumento' => 'Id Documento',
			'idJugador' => 'Jugador',
			'tipo' => 'Tipo',
			'titulo' => 'Titulo',
			'archivo_original' => 'Archivo original',
			'archivo_guardado' => 'Archivo guardado',
			'mime_type' => 'Tipo de archivo',
			'extension' => 'Extension',
			'tamano_bytes' => 'Tamanio',
			'observacion' => 'Observacion',
			'idUsuario' => 'Usuario',
			'created_at' => 'Fecha de carga',
			'updated_at' => 'Ultima modificacion',
		);
	}

	protected function beforeSave()
	{
		$now = date('Y-m-d H:i:s');
		if($this->isNewRecord)
			$this->created_at = $now;
		$this->updated_at = $now;

		return parent::beforeSave();
	}

	protected function afterSave()
	{
		parent::afterSave();
		self::sincronizarCamposLegacy($this->idJugador);
	}

	protected function afterDelete()
	{
		$this->eliminarArchivoFisico();
		self::sincronizarCamposLegacy($this->idJugador);
		parent::afterDelete();
	}

	public static function getTipos()
	{
		return array(
			self::TIPO_DNI => 'DNI',
			self::TIPO_CERTIFICADO_SALUD => 'Certificado buena salud',
			self::TIPO_FIRMA_LISTA => 'Lista firmada',
			self::TIPO_DECLARACION_JURADA => 'Declaracion jurada',
			self::TIPO_ADICIONAL => 'Adicional',
		);
	}

	public function getTipoLabel()
	{
		$tipos = self::getTipos();
		return isset($tipos[$this->tipo]) ? $tipos[$this->tipo] : $this->tipo;
	}

	public static function getExtensionesPermitidas()
	{
		return array('pdf', 'jpg', 'jpeg', 'png');
	}

	public static function getMimesPermitidos()
	{
		return array(
			'application/pdf',
			'image/jpeg',
			'image/png',
		);
	}

	public static function esExtensionPermitida($extension)
	{
		return in_array(strtolower((string)$extension), self::getExtensionesPermitidas(), true);
	}

	public static function esMimePermitido($mime)
	{
		$mime = strtolower(trim((string)$mime));
		if(strpos($mime, ';') !== false)
			$mime = trim(substr($mime, 0, strpos($mime, ';')));

		return in_array($mime, self::getMimesPermitidos(), true);
	}

	public static function getCampoLegacyPorTipo($tipo)
	{
		$map = array(
			self::TIPO_DNI => 'fotocopia_dni',
			self::TIPO_CERTIFICADO_SALUD => 'certificado',
			self::TIPO_FIRMA_LISTA => 'firma_lista',
			self::TIPO_DECLARACION_JURADA => 'dec_jurada',
		);

		return isset($map[$tipo]) ? $map[$tipo] : null;
	}

	public static function getBaseStoragePath()
	{
		return Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'legajos' . DIRECTORY_SEPARATOR . 'jugadores';
	}

	public static function getJugadorStoragePath($idJugador)
	{
		return self::getBaseStoragePath() . DIRECTORY_SEPARATOR . (int)$idJugador;
	}

	public static function asegurarDirectorioJugador($idJugador)
	{
		$path = self::getJugadorStoragePath($idJugador);
		if(!is_dir($path) && !mkdir($path, 0775, true))
			throw new CException('No se pudo crear la carpeta privada del legajo.');

		return $path;
	}

	public static function generarNombreGuardado($idJugador, $tipo, $archivoOriginal)
	{
		$extension = strtolower(pathinfo($archivoOriginal, PATHINFO_EXTENSION));
		$random = function_exists('openssl_random_pseudo_bytes')
			? bin2hex(openssl_random_pseudo_bytes(4))
			: substr(md5(uniqid('', true)), 0, 8);

		return 'jugador-' . (int)$idJugador . '-' . preg_replace('/[^a-z0-9_]+/', '-', strtolower($tipo)) . '-' . date('YmdHis') . '-' . $random . '.' . $extension;
	}

	public function getAbsolutePath()
	{
		return self::getJugadorStoragePath($this->idJugador) . DIRECTORY_SEPARATOR . $this->archivo_guardado;
	}

	public function getTamanoLegible()
	{
		$bytes = (int)$this->tamano_bytes;
		if($bytes >= 1048576)
			return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
		if($bytes >= 1024)
			return number_format($bytes / 1024, 1, ',', '.') . ' KB';

		return $bytes . ' B';
	}

	public static function detectarMime($path, $fallback = '')
	{
		if(function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if($finfo) {
				$mime = finfo_file($finfo, $path);
				finfo_close($finfo);
				if($mime !== false && $mime !== '')
					return strtolower($mime);
			}
		}

		return strtolower((string)$fallback);
	}

	public function eliminarArchivoFisico()
	{
		$path = $this->getAbsolutePath();
		if(is_file($path))
			@unlink($path);
	}

	public static function sincronizarCamposLegacy($idJugador)
	{
		$jugador = Jugador::model()->findByPk($idJugador);
		if($jugador === null)
			return;

		foreach(array(
			self::TIPO_DNI,
			self::TIPO_CERTIFICADO_SALUD,
			self::TIPO_FIRMA_LISTA,
			self::TIPO_DECLARACION_JURADA,
		) as $tipo) {
			$campo = self::getCampoLegacyPorTipo($tipo);
			$count = (int)Yii::app()->db->createCommand(
				'SELECT COUNT(*) FROM jugador_documento WHERE idJugador = :idJugador AND tipo = :tipo'
			)->queryScalar(array(':idJugador'=>$idJugador, ':tipo'=>$tipo));
			$jugador->$campo = $count > 0 ? 1 : 0;
		}

		$jugador->save(false, array('certificado', 'firma_lista', 'fotocopia_dni', 'dec_jurada'));
	}
}
