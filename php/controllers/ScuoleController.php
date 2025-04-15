<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ScuoleController
{
  public function index(Request $request, Response $response, $args){
    $db = Db::getInstance();
    $results = $db->select("scuole");

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }


  public function show(Request $request, Response $response, $args){
    $db = Db::getInstance();
    $results = $db->select("scuole", "id = " . $args['id'] . "");

     $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  
  public function create(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $body = json_decode($request->getBody()->getContents(), true);

    if (!isset($body['nome'], $body['indirizzo'])) {
        $response->getBody()->write(json_encode(["msg" => "Dati mancanti"]));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }

    $nome = $body["nome"];
    $indirizzo = $body["indirizzo"];
    $result = $mysqli_connection->query("INSERT INTO scuole(nome, indirizzo) VALUES('$nome', '$indirizzo')");

    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-Length", "0")->withStatus(201);

  }


  public function update(Request $request, Response $response, $args){
    $db = Db::getInstance();
    $body = json_decode($request->getBody()->getContents(), true);

    if (!isset($body['nome'], $body['indirizzo'])) {
        $response->getBody()->write(json_encode(["msg" => "Dati mancanti"]));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }

    $nome = $body["nome"];
    $indirizzo = $body["indirizzo"];
    $query = "UPDATE scuole SET nome = '$nome', indirizzo = '$indirizzo' WHERE id = " . $args['id'] . "";

    $result = $mysqli_connection->query("UPDATE alunni SET nome = '$nome', cognome = '$cognome' WHERE id = " . $args['id'] . "");

    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }


  public function destroy(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("DELETE FROM scuole WHERE id = " . $args['id'] . "");

    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-Length", "0")->withStatus(200);
  
  }

  public function search(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM scuole WHERE nome like '%" . $args['nome'] . "%'");
    if($result->num_rows > 0){
      $results = $result->fetch_all(MYSQLI_ASSOC);
      $response->getBody()->write(json_encode($results));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    }
    return $response->withHeader("Content-length", "0")->withStatus(404);
  }


  public function sort(Request $request, Response $response, $args){
    $db = Db::getInstance();
    $column = $args['column'];
    $order = $args['order'] ?? 'asc'; // default: asc (crescente)

    // Controllo che l'ordine sia solo 'asc' o 'desc'
    if (!in_array($order, ['asc', 'desc'])) {
        $response->getBody()->write(json_encode(["msg" => "Ordine non valido. Usa solo 'asc' o 'desc'."]));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }

    $valido = false;
    // Controllo che la colonna esista
    $results = $db->query("describe scuole");
    $columns = $results->fetch_all(MYSQLI_ASSOC);

    
    foreach ($columns as $col) {
        if ($col['Field'] === $column) {
            $valido = true;
            break;
        }
    }

    if (!$valido) {
        $response->getBody()->write(json_encode(["msg" => "Colonna non trovata"]));
        return $response->withHeader("Content-Type", "application/json")->withStatus(404);
    }

    //Query ordinata
    $query = "SELECT * FROM scuole ORDER BY $column $order";
    $result = $db->query($query);
    $scuole = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($scuole));
    return $response->withHeader("Content-Type", "application/json")->withStatus(200);
}

}
