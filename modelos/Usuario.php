<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Usuario
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$clave,$imagen,$permisos)
	{
		// ⭐ El login ahora es automáticamente la parte antes del @ del email
		$login = explode('@', $email)[0];
		
		$sql="INSERT INTO usuario (nombre,tipo_documento,num_documento,direccion,telefono,email,cargo,login,clave,imagen,condicion)
		VALUES ('$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$cargo','$login','$clave','$imagen','1')";
		
		$idusuarionew=ejecutarConsulta_retornarID($sql);

		$num_elementos=0;
		$sw=true;

		while ($num_elementos < count($permisos))
		{
			$sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idusuarionew', '$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos=$num_elementos + 1;
		}

		return $sw;
	}

	//Implementamos un método para editar registros
	public function editar($idusuario,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$clave,$imagen,$permisos)
	{
		// ⭐ Actualizar login automáticamente desde el email
		$login = explode('@', $email)[0];
		
		$sql="UPDATE usuario SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',cargo='$cargo',login='$login',clave='$clave',imagen='$imagen' WHERE idusuario='$idusuario'";
		ejecutarConsulta($sql);

		//Eliminamos todos los permisos asignados para volverlos a registrar
		$sqldel="DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
		ejecutarConsulta($sqldel);

		$num_elementos=0;
		$sw=true;

		while ($num_elementos < count($permisos))
		{
			$sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idusuario', '$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos=$num_elementos + 1;
		}

		return $sw;
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='0' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='1' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idusuario)
	{
		$sql="SELECT * FROM usuario WHERE idusuario='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM usuario ORDER BY idusuario DESC";
		return ejecutarConsulta($sql);		
	}
	
	//Implementar un método para listar los permisos marcados
	public function listarmarcados($idusuario)
	{
		$sql="SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Función para verificar el acceso al sistema
	// ⭐ IMPORTANTE: Ahora permite login con EMAIL o con LOGIN (usuario)
	public function verificar($login,$clave)
    {
    	$sql="SELECT idusuario,nombre,tipo_documento,num_documento,telefono,email,cargo,imagen,login 
    	      FROM usuario 
    	      WHERE (login='$login' OR email='$login') 
    	      AND clave='$clave' 
    	      AND condicion='1'"; 
    	return ejecutarConsulta($sql);  
    }
    
    //Función para verificar si el email ya existe (para evitar duplicados)
    public function verificarEmailExiste($email, $idusuario = 0)
    {
        if ($idusuario > 0) {
            // Al editar, excluir el usuario actual
            $sql = "SELECT idusuario FROM usuario WHERE email='$email' AND idusuario != '$idusuario' LIMIT 1";
        } else {
            // Al crear, verificar que no exista
            $sql = "SELECT idusuario FROM usuario WHERE email='$email' LIMIT 1";
        }
        $result = ejecutarConsultaSimpleFila($sql);
        return ($result !== false); // true si existe, false si no existe
    }
    
    //Función para verificar si el documento ya existe (para evitar duplicados)
    public function verificarDocumentoExiste($tipo_documento, $num_documento, $idusuario = 0)
    {
        if ($idusuario > 0) {
            // Al editar, excluir el usuario actual
            $sql = "SELECT idusuario FROM usuario WHERE tipo_documento='$tipo_documento' AND num_documento='$num_documento' AND idusuario != '$idusuario' LIMIT 1";
        } else {
            // Al crear, verificar que no exista
            $sql = "SELECT idusuario FROM usuario WHERE tipo_documento='$tipo_documento' AND num_documento='$num_documento' LIMIT 1";
        }
        $result = ejecutarConsultaSimpleFila($sql);
        return ($result !== false); // true si existe, false si no existe
    }
}

?>