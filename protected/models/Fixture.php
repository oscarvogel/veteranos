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
  		 
  	$n_parametros = func_num_args(); // Obenemos el número de parámetros 
  	if ($n_parametros<3 || $n_parametros%2!=1) { // Si tenemos el número de parametro mal... 
    	return false; 
  	} else { // Hasta aquí todo correcto...veamos si los parámetros tienen lo que debe ser... 
    	$arg_list = func_get_args(); 
 
    if (!(is_array($arg_list[0]) && is_array(current($arg_list[0])))) { 
      return false; // Si el primero no es un array...MALO! 
    } 
    for ($i = 1; $i<$n_parametros; $i++) { // Miramos que el resto de parámetros tb estén bien... 
      if ($i%2!=0) {// Parámetro impar...tiene que ser un campo del array... 
        if (!array_key_exists($arg_list[$i], current($arg_list[0]))) { 
          return false; 
        } 
      } else { // Par, no falla...si no es SORT_ASC o SORT_DESC...a la calle! 
        if ($arg_list[$i]!=SORT_ASC && $arg_list[$i]!=SORT_DESC) { 
          return false; 
        } 
      } 
    } 
    $array_salida = $arg_list[0]; 
 
    // Una vez los parámetros se que están bien, procederé a ordenar... 
    $a_evaluar = "foreach (\$array_salida as \$fila){\n"; 
    for ($i=1; $i<$n_parametros; $i+=2) { // Ahora por cada columna... 
      $a_evaluar .= "  \$campo[$2][] = \$fila['$arg_list[$i]'];\n"; 
    } 
    $a_evaluar .= "}\n"; 
    $a_evaluar .= "array_multisort(\n"; 
    for ($i=1; $i<$n_parametros; $i+=2) { // Ahora por cada elemento... 
      $a_evaluar .= "  \$campo[$2], SORT_REGULAR, \$arg_list[".($i+1)."],\n"; 
    } 
    $a_evaluar .= "  \$array_salida);"; 
    // La verdad es que es más complicado de lo que creía en principio... :) 
 
    eval($a_evaluar); 
    return $array_salida; 
  } 

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
}