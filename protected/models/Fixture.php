<?php

/**
 * This is the model class for table "fixture".
 *
 * The followings are the available columns in table 'fixture':
 * @property string $idFixture
 * @property string $idTorneo
 * @property string $NFecha
 * @property string $Fecha
 * @property string $Local
 * @property string $Visitante
 * @property string $GolLocal
 * @property string $GolVisitante
 *
 * The followings are the available model relations:
 * @property Equipos $local
 * @property Equipos $visitante
 * @property Torneo $idTorneo0
 */
class Fixture extends CActiveRecord
{
	
	public $CambiaFecha;
	private $datos = array();
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Fixture the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'fixture';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Local, Visitante', 'required'),
			array('idTorneo, Local, Visitante', 'length', 'max'=>10),
			array('NFecha, GolLocal, GolVisitante', 'length', 'max'=>2),
			array('Fecha, PuntosLocal, PuntosVisitante, idCancha, idArbitro, idLinea1, idLinea2, Hora, PostTemporada, SumaPuntos, interzonal', 'safe'),
			array('Archivo', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idFixture, idTorneo, NFecha, Fecha, Local, Visitante, GolLocal, GolVisitante, 
				PuntosLocal, PuntosVisitante, idCancha, idArbitro, idLinea1, idLinea2, Hora, PostTemporada,
				SumaPuntos, Archivo, interzonal', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'local' => array(self::BELONGS_TO, 'Equipos', 'Local'),
			'visitante' => array(self::BELONGS_TO, 'Equipos', 'Visitante'),
			'Torneo' => array(self::BELONGS_TO, 'Torneo', 'idTorneo'),
 			'Goles' => array(self::HAS_MANY, 'Goles', 'idFixture'),
            'Tarjetas' => array(self::HAS_MANY, 'Tarjetas', 'idFixture'),
            'Linea1' => array(self::BELONGS_TO, 'Arbitros', 'idLinea1'),
            'Cancha' => array(self::BELONGS_TO, 'Canchas', 'idCancha'),
            'Arbitro' => array(self::BELONGS_TO, 'Arbitros', 'idArbitro'),
            'Linea2' => array(self::BELONGS_TO, 'Arbitros', 'idLinea2'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idFixture' => 'Id Fixture',
			'idTorneo' => 'Id Torneo',
			'NFecha' => 'Nº de Fecha',
			'Fecha' => 'Fecha',
			'Local' => 'Local',
			'Visitante' => 'Visitante',
			'GolLocal' => 'Gol Local',
			'GolVisitante' => 'Gol Visitante',
			'CambiaFecha' => 'Nueva Fecha',
			'Hora' => 'Hora',
			'PosTemporada' => 'Post Temporada',
			'SumaPuntos' => 'Suma Puntos',
			'Semanas' => 'Cantidad de Semanas',
			'interzonal' => 'Interzonal'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idFixture',$this->idFixture,true);
		$criteria->compare('idTorneo',$this->idTorneo,true);
		$criteria->compare('NFecha',$this->NFecha,true);
		$criteria->compare('Fecha',$this->Fecha,true);
		$criteria->compare('Local',$this->Local,true);
		$criteria->compare('Visitante',$this->Visitante,true);
		$criteria->compare('GolLocal',$this->GolLocal,true);
		$criteria->compare('GolVisitante',$this->GolVisitante,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function asignaPuntosPorResultado(){
		if($this->GolLocal > $this->GolVisitante){
			$this->PuntosLocal = 3;
			$this->PuntosVisitante = 0;
		}elseif($this->GolVisitante > $this->GolLocal){
			$this->PuntosLocal = 0;
			$this->PuntosVisitante = 3;
		}else{
			$this->PuntosLocal = 1;
			$this->PuntosVisitante = 1;
		}
	}

	public function TablaPosiciones($id = 0, $idCategoria = 1){
		$torneo = Torneo::buscarPorClave($id);
		$sql = "select equipos.* from equipos inner join equipostorneo on equipos.idEquipo = equipostorneo.idEquipo where equipostorneo.idTorneo = " . $id; 
		$model = Equipos::model()->findAllBySql($sql);
		//print_r($model);
		
		foreach ($model as $equipo) {
			$this->datos[$equipo->idEquipo]['Nombre'] = $equipo->Nombre;
			$this->datos[$equipo->idEquipo]['Puntos'] = 0;
			$this->datos[$equipo->idEquipo]['GolFavor'] = 0;
			$this->datos[$equipo->idEquipo]['GolContra'] = 0;
			$this->datos[$equipo->idEquipo]['Partidos'] = 0;
			$this->datos[$equipo->idEquipo]['Ganados'] = 0;
			$this->datos[$equipo->idEquipo]['Empatados'] = 0;
			$this->datos[$equipo->idEquipo]['Perdidos'] = 0;
			$this->getPuntos($equipo->idEquipo, $id);
			$this->datos[$equipo->idEquipo]['Diferencia'] = $this->datos[$equipo->idEquipo]['GolFavor'] - $this->datos[$equipo->idEquipo]['GolContra'];
		}
		$this->datos = $this->ordenar_array($this->datos,'Puntos', SORT_DESC, 'Diferencia', SORT_DESC, 'GolFavor', SORT_DESC);
		return $this->datos;
	}

	public function getPuntos($idEquipo, $idTorneo){

		$torneos = Paramsist::getTorneosRelacionados($idTorneo);
		$criteria=new CDbCriteria;
		$criteria->condition = "(Local=:idEquipo or Visitante=:idEquipo) and idTorneo in (" . $torneos . ") and PostTemporada=0";
		$criteria->params=array(':idEquipo'=>$idEquipo);
		$Partidos=Fixture::model()->findAll($criteria); // $params no es necesario
		foreach ($Partidos as $Partido) {
			if($Partido->PuntosLocal != 0 or $Partido->PuntosVisitante != 0)
				$this->datos[$idEquipo]['Partidos'] ++;
			if($Partido->SumaPuntos){
				if($Partido->Local == $idEquipo){
					$this->datos[$idEquipo]['Puntos'] += $Partido->PuntosLocal;
					$this->datos[$idEquipo]['GolFavor'] += $Partido->GolLocal;
					$this->datos[$idEquipo]['GolContra'] += $Partido->GolVisitante;
					if($Partido->PuntosLocal == 3){
						$this->datos[$idEquipo]['Ganados'] ++;
					}elseif($Partido->PuntosLocal == 0 and $Partido->PuntosVisitante == 3){
						$this->datos[$idEquipo]['Perdidos'] ++;
					}elseif($Partido->PuntosLocal == 1){
						$this->datos[$idEquipo]['Empatados'] ++;
					}
				}else{
					$this->datos[$idEquipo]['Puntos'] += $Partido->PuntosVisitante;
					$this->datos[$idEquipo]['GolFavor'] += $Partido->GolVisitante;
					$this->datos[$idEquipo]['GolContra'] += $Partido->GolLocal;
					if($Partido->PuntosVisitante == 3){
						$this->datos[$idEquipo]['Ganados'] ++;
					}elseif($Partido->PuntosVisitante == 0 and $Partido->PuntosLocal == 3){
						$this->datos[$idEquipo]['Perdidos'] ++;
					}elseif($Partido->PuntosVisitante == 1){
						$this->datos[$idEquipo]['Empatados'] ++;
					}
				}
			}
		}
		
	}
	
	
	public function ordenar_array() {
		$n_parametros = func_num_args();
		if($n_parametros < 3 || $n_parametros % 2 != 1)
			return false;

		$arg_list = func_get_args();
		if(!(is_array($arg_list[0]) && is_array(current($arg_list[0]))))
			return false;

		for($i = 1; $i < $n_parametros; $i++){
			if($i % 2 != 0){
				if(!array_key_exists($arg_list[$i], current($arg_list[0])))
					return false;
			}else{
				if($arg_list[$i] != SORT_ASC && $arg_list[$i] != SORT_DESC)
					return false;
			}
		}

		$array_salida = $arg_list[0];
		$criterios = array();
		for($i = 1; $i < $n_parametros; $i += 2)
			$criterios[] = array('campo'=>$arg_list[$i], 'orden'=>$arg_list[$i + 1]);

		usort($array_salida, function($a, $b) use ($criterios){
			foreach($criterios as $criterio){
				$campo = $criterio['campo'];
				if($a[$campo] == $b[$campo])
					continue;
				$resultado = ($a[$campo] < $b[$campo]) ? -1 : 1;
				return $criterio['orden'] == SORT_ASC ? $resultado : -$resultado;
			}
			return 0;
		});

		return $array_salida;
	}

	public static function ConsultaFecha($idTorneo = 1, $Fecha = "0000-00-00"){
		$criteria = new CDbCriteria;
		$criteria->condition = "idTorneo = :Torneo and Fecha = :Fecha";
		$criteria->params = array("Torneo" => $idTorneo, "Fecha" => $Fecha);
		$criteria->order = "visitante desc";
		return Fixture::model()->findAll($criteria);
	}
	
	
	public static function Guardar($idTorneo, $fixture, $cruces, $fecha){
		$criteria = new CDbCriteria;
		$criteria->condition = "idTorneo = " . $idTorneo ;
		Fixture::model()->deleteAll($criteria);
		for ($f = 1; $f <= $fixture->_fechas; $f++) {
	        for ($c = 1; $c <= $fixture->_partidosXFechas; $c++) {
	            $local = explode("-", $fixture->_fixture[$f][$c]['A']);
				$visitante = explode("-", $fixture->_fixture[$f][$c]['B']);
	            $registro = new Fixture;
	            $registro->idTorneo 	= $idTorneo;
				$registro->NFecha		= $f;
				$registro->Local 		= $local[0];
				$registro->Visitante	= ($visitante[0]) == 'libre' ? 0 : $visitante[0];
				$registro->Fecha		= $fecha;
				$registro->save();
	        }
			$nuevafecha = strtotime ( '+7 day' , strtotime ( $fecha ) ) ;
			$fecha = date ( 'Y-m-j' , $nuevafecha );
    	}
		
	}
	
	
	public static function ConsultaFixture($idTorneo){
		$criteria = new CDbCriteria;
		$criteria->condition = "idTorneo = " . $idTorneo;
		$criteria->order = "NFecha,visitante desc";
		return Fixture::model()->findAll($criteria);
		
	}
	
	
	public static function ConsultaAsignaciones($Fecha){
		$criteria = new CDbCriteria;
		$criteria->join = "inner join torneo on t.idTorneo = torneo.idTorneo inner join equipos on t.Local = equipos.idEquipo";
		$criteria->condition = "t.Fecha = :Fecha and torneo.Estado='I'";
		$criteria->params = array('Fecha'=>$Fecha);
		$criteria->order = "t.idCancha desc, t.hora, equipos.idCategoria";

		return Fixture::model()->findAll($criteria);
	}  
	
	
	public static function CambiaFecha($FechaActual, $NFecha, $NuevaFecha, $idTorneo){
		Fixture::model()->updateAll(array("Fecha" => $NuevaFecha), 
				"Fecha = :Fecha and NFecha = :NFecha and idTorneo = :idTorneo", 
				array("Fecha" => $FechaActual, "NFecha" => $NFecha, "idTorneo" => $idTorneo));
	}

	
	public static function verCanchasEquipo($idEquipo, $idTorneo){
		$criteria = new CDbCriteria;
		//$criteria->condition = "(Local = :Equipo or Visitante = :Equipo) and idTorneo = :idTorneo";
		//$criteria->params = array('Equipo'=>$idEquipo, 'idTorneo' => $idTorneo);
        $criteria->condition = "(Local = :Equipo or Visitante = :Equipo)";
        $criteria->params = array('Equipo'=>$idEquipo);
        $criteria->limit = 30;
		$criteria->order = "Fecha desc";
		
		return Fixture::model()->findAll($criteria);
	}
	
	public static function CopiaFixture($idTorneoOrigen, $idTorneoDestino, $DesdeFecha, $HastaFecha){
		/*$criteria = new CDbCriteria;
		$criteria->condition = "idTorneo = :idTorneoDestino";
		$criteria->params = array("idTorneoDestino"=>$idTorneoDestino);
		Fixture::model()->deleteAll($criteria);
		*/
		$criteria = new CDbCriteria;
		$criteria->condition = 'idTorneo = :idTorneoOrigen and NFecha between :DesdeFecha and :HastaFecha';
		$criteria->params = array('idTorneoOrigen'=>$idTorneoOrigen, 'DesdeFecha'=>$DesdeFecha, 'HastaFecha'=>$HastaFecha);
		$criteria->order = "NFecha";
		$datos = Fixture::model()->findAll($criteria);
		
		$f = 0;
		foreach($datos as $dato){
			if($f != $dato->NFecha + $HastaFecha){
				$f = $dato->NFecha + $HastaFecha;
				$nuevafecha = strtotime ( '+ ' . $HastaFecha * 7 . ' day' , strtotime ( $dato->Fecha ) ) ;
				$fecha = date ( 'Y-m-j' , $nuevafecha );
			}
			$registro = new Fixture;
			$registro->idTorneo 	= $idTorneoDestino;
			$registro->NFecha		= $f;
			$registro->Local 		= ($dato->Visitante == 0 ? $dato->Local : $dato->Visitante);
			$registro->Visitante	= ($dato->Visitante == 0 ? 0 : $dato->Local);
			$registro->Fecha		= $fecha;
			$registro->Hora			= '00:00:00';
			$registro->save();
		}
	}

	public static function AdelantaFecha($NFecha, $idTorneo){
		/*$laotrasemana        = new DateTime($DesdeFecha);
		$laotrasemana->add(new DateInterval('P7D'));
		Fixture::model()->updateAll(array("Fecha" => "ADDDATE(fecha,7)" ),
				"Fecha >= :DesdeFecha and idTorneo = :idTorneo",
				array("DesdeFecha" => $DesdeFecha, "idTorne" => $idTorneo));*/
		$sql = "update fixture set fecha = adddate(fecha, 7) where NFecha >= " . $NFecha . " and idTorneo = " . $idTorneo;
		$command=Yii::app()->db->createCommand($sql)->execute();
	}

	/**
	 * Reprogama fechas del fixture sumando N dias a la columna Fecha.
	 * Solo afecta partidos PENDIENTES (PuntosLocal=0 AND PuntosVisitante=0)
	 * de torneos en estado I o A.
	 *
	 * @param array  $torneosIds array de idTorneo a incluir (vacio = ninguno)
	 * @param string $fechaDesde Fecha calendario (Y-m-d) a partir de la cual se reprograma
	 * @param int    $dias       Cantidad de dias a sumar (1..60)
	 * @param bool   $aplicar    false = solo preview, true = ejecuta el UPDATE en transaccion
	 * @param array  $previewRows OUT: array con las filas afectadas y la fecha nueva calculada
	 * @return int Cantidad de filas afectadas (o que se van a afectar en preview)
	 */
	public static function CorrerFechas($torneosIds, $fechaDesde, $dias, $aplicar = false, &$previewRows = array())
	{
		$previewRows = array();

		$dias = (int)$dias;
		if ($dias < 1 || $dias > 60) {
			throw new CHttpException(400, 'La cantidad de dias debe estar entre 1 y 60.');
		}
		if (empty($fechaDesde) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde)) {
			throw new CHttpException(400, 'Fecha desde invalida.');
		}
		if (!is_array($torneosIds) || count($torneosIds) === 0) {
			throw new CHttpException(400, 'Tenes que seleccionar al menos un torneo.');
		}

		$torneosIds = array_map('intval', $torneosIds);
		$torneosIds = array_filter($torneosIds, function($id) { return $id > 0; });
		if (count($torneosIds) === 0) {
			throw new CHttpException(400, 'Tenes que seleccionar al menos un torneo valido.');
		}

		$placeholders = implode(',', array_fill(0, count($torneosIds), '?'));

		$sql = "SELECT f.idFixture, f.idTorneo, t.Nombre AS Torneo,
                       f.NFecha, DATE_FORMAT(f.Fecha,'%Y-%m-%d') AS Fecha, f.Hora,
                       el.Nombre AS LocalNombre, ev.Nombre AS VisitanteNombre,
                       c.Nombre AS Cancha
                FROM fixture f
                INNER JOIN torneo t ON t.idTorneo = f.idTorneo
                LEFT JOIN equipos el ON el.idEquipo = f.Local
                LEFT JOIN equipos ev ON ev.idEquipo = f.Visitante
                LEFT JOIN canchas c ON c.idCancha = f.idCancha
                WHERE t.Estado IN ('I','A')
                  AND f.idTorneo IN ($placeholders)
                  AND f.Fecha >= ?
                  AND f.PuntosLocal = 0
                  AND f.PuntosVisitante = 0";

		$params = array_merge($torneosIds, array($fechaDesde));

		$sql .= " ORDER BY t.Nombre, f.Fecha, f.NFecha, f.idFixture";

		$rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);

		foreach ($rows as $r) {
			$previewRows[] = array(
				'idFixture'   => (int)$r['idFixture'],
				'idTorneo'    => (int)$r['idTorneo'],
				'Torneo'      => $r['Torneo'],
				'NFecha'      => $r['NFecha'],
				'FechaActual' => $r['Fecha'],
				'FechaNueva'  => date('Y-m-d', strtotime($r['Fecha'] . ' +' . $dias . ' days')),
				'Hora'        => $r['Hora'],
				'Local'       => $r['LocalNombre'],
				'Visitante'   => $r['VisitanteNombre'],
				'Cancha'      => $r['Cancha'],
			);
		}

		if ($aplicar && count($previewRows) > 0) {
			$ids = array_map('intval', array_column($previewRows, 'idFixture'));
			$condition = 'idFixture IN (' . implode(',', $ids) . ')';

			$transaction = Yii::app()->db->beginTransaction();
			try {
				Yii::app()->db->createCommand()->update(
					'fixture',
					array('Fecha' => new CDbExpression('DATE_ADD(Fecha, INTERVAL ' . $dias . ' DAY)')),
					$condition
				);
				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollback();
				throw $e;
			}
		}

		return count($previewRows);
	}
}
