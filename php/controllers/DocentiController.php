<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DocentiController
{

  // ricerca singolo docente
  private function docenteExists($id) {
    $db = Db::getInstance();
    $result = $db->query("SELECT id FROM docenti WHERE id = $id");
    return $result && $result->num_rows > 0;
  }

  //mostra tutti i docenti di una scuola
  public function index(Request $request, Response $response, $args){
    $db = Db::getInstance();
    $scuola_id = $args['scuola_id'];
    $result = $db->query("SELECT * FROM docenti WHERE scuola_id = $scuola_id");

    if ($result->num_rows > 0) {
      $docenti = $result->fetch_all(MYSQLI_ASSOC);
      $response->getBody()->write(json_encode($docenti));
      return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    }
    $response->getBody()->write(json_encode(["msg" => "Scuola non trovata"]));
    return $response->withHeader("Content-Type", "application/json")->withStatus(404);
  }

  //singolo docente tramite id
  public function show(Request $request, Response $response, $args) {
    $db = Db::getInstance();
    $idDocente = $args['idDocente'];
    $scuola_id = $args['idScuola'];

    $result = $db->query("SELECT * FROM docenti WHERE id = $idDocente AND scuola_id = $scuola_id");

    if ($result->num_rows > 0) {
      $docenti = $result->fetch_assoc();
      $response->getBody()->write(json_encode($docenti));
      return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    }

    $response->getBody()->write(json_encode(["msg" => "Docente non trovato"]));
        return $response->withHeader("Content-Type", "application/json")->withStatus(404);
  }

  //creazione docente
  public function create(Request $request, Response $response, $args){
    
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $scuola = $args["id_scuola"];
    $body = json_decode($request->getBody()->getContents(), true);

    if (!isset($body['nome'], $body['cognome'])) {
      $response->getBody()->write(json_encode(["msg" => "Dati mancanti"]));
      return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }

    $nome = $body["nome"];
    $cognome = $body["cognome"];
    
    
    $result = $mysqli_connection->query("INSERT INTO docenti(nome, cognome, scuola_id) VALUES('$nome', '$cognome', '$scuola')");

    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-Length", "0")->withStatus(201);

  }

  //aggiornare docenti
  public function update(Request $request, Response $response, $args) {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $scuola_id = $args['id'];
    $idDocenti = $args['idDoc'];
    $body = json_decode($request->getBody()->getContents(), true);

    if (!isset($body['nome'], $body['cognome'])) {
      $response->getBody()->write(json_encode(["msg" => "Dati mancanti"]));
      return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }

    $nome = $body['nome'];
    $cognome = $body['cognome'];

    $result = $mysqli_connection->query("UPDATE docenti SET nome = '$nome', cognome = '$cognome' WHERE id = '$idDocenti' AND scuola_id = '$scuola_id'");

    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-Length", "0")->withStatus(201);
  }

  //elimina docenti
  public function destroy(Request $request, Response $response, $args){
    $id = $args['idDocen'];
    if (!$this->docenteExists($id)) {
        $response->getBody()->write(json_encode(["msg" => "Docente non trovato"]));
        return $response->withHeader("Content-Type", "application/json")->withStatus(404);
    }
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("DELETE FROM docenti WHERE id = '$id'");

    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-Length", "0")->withStatus(200);
  
  }

  //ordina per docenti
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
    $results = $db->query("describe docenti");
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
    $idS = $args['idScuol'];
    //Query ordinata
    $query = "SELECT * FROM docenti WHERE scuola_id = '$idS' ORDER BY $column $order";
    $result = $db->query($query);
    $docenti = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($docenti));
    return $response->withHeader("Content-Type", "application/json")->withStatus(200);
}
}
