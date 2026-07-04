<?php
namespace App\Model;

use App\Lib\Database;
use App\Lib\Response;

class UserModel
{
    private $db;
    private $table = 'empleado';
    private $response;
    
    public function __CONSTRUCT()
    {
        $this->db = Database::StartUp();
        $this->response = new Response();
    }
    
    public function GetAll()
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table");
			$stm->execute();
            
			$this->response->setResponse(true);
            $this->response->result = $stm->fetchAll();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}
    }
    
    public function Get($id)
    {
		try
		{
			$result = array();

			$stm = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
			$stm->execute(array($id));

			$this->response->setResponse(true);
            $this->response->result = $stm->fetch();
            
            return $this->response;
		}
		catch(Exception $e)
		{
			$this->response->setResponse(false, $e->getMessage());
            return $this->response;
		}  
    }
    
    public function InsertOrUpdate($data)
    {
		try 
		{
            if(isset($data['id']))
            {
                $sql = "UPDATE $this->table SET 
                            Nombre          = ?, 
                            Apellido        = ?,
                            Correo          = ?,
                            Sexo            = ?,
                            Sueldo          = ?,
                            Profesion_id    = ?,
                            FechaNacimiento = ?
                        WHERE id = ?";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['Nombre'], 
                            $data['Apellido'],
                            $data['Correo'],
                            $data['Sexo'],
                            $data['Sueldo'],
                            $data['Profesion_id'],
                            $data['FechaNacimiento'],
                            $data['id']
                        )
                    );
            }
            else
            {
                $sql = "INSERT INTO $this->table
                            (Nombre, Apellido, Correo, Sexo, Sueldo, Profesion_id, FechaNacimiento, FechaRegistro)
                            VALUES (?,?,?,?,?,?,?,?)";
                
                $this->db->prepare($sql)
                     ->execute(
                        array(
                            $data['Nombre'], 
                            $data['Apellido'],
                            $data['Correo'],
                            $data['Sexo'],
                            $data['Sueldo'],
                            $data['Profesion_id'],
                            $data['FechaNacimiento'],
                            date('Y-m-d')
                        )
                    ); 
            }
            
			$this->response->setResponse(true);
            return $this->response;
		}catch (Exception $e) 
		{
            $this->response->setResponse(false, $e->getMessage());
		}
    }
    
    public function Delete($id)
    {
		try 
		{
			$stm = $this->db
			            ->prepare("DELETE FROM $this->table WHERE id = ?");			          

			$stm->execute(array($id));
            
			$this->response->setResponse(true);
            return $this->response;
		} catch (Exception $e) 
		{
			$this->response->setResponse(false, $e->getMessage());
		}
    }
}