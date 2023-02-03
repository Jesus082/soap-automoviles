<?php
/*
 * Servicio Web en PHP por Jose Hernández
 * https://josehernandez.es/2011/01/18/servicio-web-php.html
 * https://web.archive.org/web/20201026070426/https://josehernandez.es/2011/01/18/servicio-web-php.html
 */

class GestionAutomoviles
{
    private $con;
    private $IsAuthenticated;
    public function __construct()
    {
        $this->con = (is_null($this->con)) ? self::ConectarMarcas() : $this->con;
        $this->IsAuthenticated = false;
    }
    public function ConectarMarcas()
    {
        try {
            $user = "root"; // usuario con el que se va conectar con MySQL
            $pass = ""; // contraseña del usuario
            $dbname = "coches"; // nombre de la base de datos
            $host = "localhost"; // nombre o IP del host

            $db = new PDO("mysql:host=$host; dbname=$dbname", $user, $pass); //conectar con MySQL y SELECCIONAR LA Base de Datos
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Manejo de errores con PDOException
            echo "<p>Se ha conectado a la BD $dbname.</p>\n";
            return $db;
        } catch (PDOException $e) { // Si hubieran errores de conexión, se captura un objeto de tipo PDOException
            print "<p>Error: No se pudo conectar con la BD $dbname.</p>\n";
            print "<p>Error: " . $e->getMessage() . "</p>\n"; // mensaje de excepción

            exit(); // terminar si no hay conexión $db
        }
    }

    public function authenticate($header_params)
    {
        if ($header_params->username == 'ies' && $header_params->password == 'daw') {
            $this->IsAuthenticated = true;
            return true;
        } else {
            throw new SoapFault('Wrong user/pass combination', 401);
        }
    }

    public function ObtenerMarcasURL()
    {
        if (is_null($this->con)) {
            return "ERROR";
        }
        if (!$this->IsAuthenticated) {
            return "Not Authenticated";
        }

        $sql = "SELECT marca, url FROM marcas";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $marcaUrl = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = null;
        return $marcaUrl;
    }


    public function ObtenerModelos($marca)
    {

        $marca = intVal($marca);
        $modelos = array();

        if ($marca !== 0) {
            $con = $this->ConectarMarcas();
            $con->query("SET CHARACTER SET utf8");

            if ($con) {
                $result = $con->query('select id, modelo from modelos ' .
                    'where marca = ' . $marca);

                while ($row = $result->fetch(PDO::FETCH_ASSOC))
                    $modelos[$row['id']] = $row['modelo'];
            }
        }

        return $modelos;
    }

    public function ObtenerModelosPorMarca($marca)
    {
        if (is_null($this->con)) {
            return "ERROR";
        }
        if (!$this->IsAuthenticated) {
            return "Not Authenticated";
        }

        $sql = "SELECT modelo FROM `modelos` WHERE marca = (SELECT id FROM `marcas` WHERE marca = :marca)";
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':marca', $marca[0], PDO::PARAM_STR);
        $stmt->execute();
        $modelos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = null;
        return $modelos;
    }
}
/* 
$con = $this->ConectarMarcas();
// using prepared statements
$sql = "SELECT modelo FROM `modelos` where marca=(SELECT id FROM `marcas` where marca='".$marca[0]."')";
$stmt = $this->$con->prepare($sql);
$stmt->execute();
$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$stmt = null;
return $res;
}
}
*/