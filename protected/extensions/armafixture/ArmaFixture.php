<?php
/**
 * Clase Fixture.
 * Genera una tabla de cruces para un torneo tipo Single Round Robin. o de
 * liguilla.
 *
 * @author      Marcelo Castro
 * @package     Fixture creado en el projecto opet
 * @copyright   2011 - ObjetivoPHP
 * @license     Gratuito (Free) http://www.opensource.org/licenses/gpl-license.html
 * @author      Marcelo Castro (ObjetivoPHP)
 * @link        objetivophp@******.*****
 * @link        http://objetivophp.com
 * @version     0.0.1 (19/02/2011 - 21/02/2011)
 */
class ArmaFixture
{
    /**
     * Cantidad de equipos participantes en el torneo.
     * @var integer
     */
    private $_cantidadEquipos   = 10;
 
    /**
     * Cantidad de equipos utilizados para armar el fixture.
     * Puede haber uno mas en caso de ser impar el numero de equipos.
     * @var integer
     */
    private $_equiposFixture;
 
    /**
     * Cantidad de fechas que contendra el torneo.
     * @var integer
     */
    public $_fechas;
 
    /**
     * Cantidad de partidos que se jugaran por fecha.
     * @var integer
     */
    public $_partidosXFechas;
 
    /**
     * Guarda la matriz del fixture, incluye el equipo ficticio en caso de que
     * sea necesario.
     * @var array
     */
    public $_fixture           = array();
 
    /**
     * Contiene los nombres de los equipos o en su defecto los numeros, que se
     * le otorgaron a cada uno.
     * @var array
     */
    private $_equipos           = array();
 
    /**
     * Contiene si se seudo-aleatoriza la tabla de cruces o no.
     * @var boolean
     */
    private $_aleatorio         = true;
 
    /**
     * Contiene que debe de ponerse cuando un cuadro queda libre, caso
     * de cantidad de cuadros impares.
     * @var string
     */
    private $_libre             = 'libre';
 
    /**
     * Metodo __construct.
     * @param   mixed     $equipos    Cantidad de equipos o arreglo con los nombres.
     * @return  void
     */
    public function __construct($equipos = null)
    {
        if (is_array($equipos)) {
            $this->_cantidadEquipos = count($equipos);
            $this->_equipos         = $equipos;
        } else {
            $this->_cantidadEquipos =  is_int($equipos)? $equipos : 10;
            for ($f = 0; $f <= $this->_cantidadEquipos; $f++) {
                $this->_equipos[$f] = $f+1;
            }
        }
 
        $this->_partidosXFechas = ceil($this->_cantidadEquipos /2 );
        $this->_equiposFixture  = $this->_cantidadEquipos +  $this->_cantidadEquipos % 2;
        $this->_fechas          = $this->_partidosXFechas * 2 - 1 ;
    }
 
    /**
     * Configura si se aleatoriza la tabla de cruces.
     * @param   boolean     $aleatorio
     * @return  void
     */
    public function setAleatorio($aleatorio = true)
    {
        $this->_aleatorio   = ($aleatorio)? true : false;
    }
 
    /**
     * Asigna el comentario para fecha libre.
     * @param   string  $cometario
     * @return  void
     */
    public function setFechaLibre($comentario)
    {
        $this->_libre = $comentario;
    }
 
    /**
     * Metodo tablaDeCruces.
     * Genera una matriz con los cruces correspondientes entre los equipos.
     * @return void
     */
    public function tablaDeCruces()
    {
        $fixture   = array();
        if ($this->_aleatorio) {
            shuffle($this->_equipos);
        }
 
        if ($this->_cantidadEquipos % 2) {
            $this->_equipos[$this->_equiposFixture-1] = $this->_libre;
        }
 
        // Lleno el indice A de cada elemento con numeros del numero 1
        // hasta llegar al maximo de fechas y vuelvo a comenzar en 1.
        // El ultimo numero puesto debe ser el maximo de fechas.-
        $datos      = $this->_partidosXFechas * $this->_fechas;
        for ($f = 1; $f <= $datos; $f++) {
 
            $col        = $f % $this->_partidosXFechas;
            $col        = ($col != 0)? $col : $this->_partidosXFechas;
            $fila       = ceil ($f / $this->_partidosXFechas);
            $auxiliar   = $f % $this->_fechas;
            if ($auxiliar == 0) {
                $auxiliar = (int) $this->_fechas;
            }
            $fixture[$fila][$col]['A'] = $this->_equipos[$auxiliar-1];
        }
 
        // Lleno el primer elemento de cada fila con el ultimo equipo
        // o el equipo ficticio, si la cantidad de equipo es impar.
        for ($f = 1; $f<= $this->_fechas; $f++) {
            $fixture[$f][1]['B']    = $this->_equipos[$this->_equiposFixture-1];
        }
 
        // Lleno el indice B de cada elemento empezando del maximo de fechas
        // hasta 1 y vuelvo a empezar salteo la primer columna que ya fue completada
        // en el ciclo anterior
        $indice = $this->_fechas;
        for ($f = 1; $f <= $this->_fechas; $f++) {
            for ($c = 2; $c <= $this->_partidosXFechas; $c++) {
                $fixture[$f][$c]['B']   = $this->_equipos[$indice - 1];
                if (--$indice == 0) {
                    $indice = $this->_fechas;
                }
            }
        }
        $this->_fixture     = $fixture;
    }
 
    /**
     * Retorna el arreglo de todos los cruces.
     * @return array 
     */
    public function getCruces()
    {
        return $this->_fixture;
    }
 
    /**
     * Metodo verCuadro.
     * Muestra la tabla de cruces en una tabla HTML, es solo a efectos demostrativos
     * @return  HTML
     */
    public function verCuadro()
    {
        echo "<table border=1>\n";
        for ($f = 1; $f <= $this->_fechas; $f++) {
            echo "<tr>\n";
            for ($c = 1; $c <= $this->_partidosXFechas; $c++) {
                echo "<td>";
                echo utf8_decode($this->_fixture[$f][$c]['A']);
                echo '</br>' . utf8_decode($this->_fixture[$f][$c]['B']);
                echo "</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>";
    }
}