<?php

// Configurações de erro para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';
$con->set_charset("utf8");

// Query para buscar barris com volume atual maior que zero
$query = "SELECT idBarril, deProduto, vlLitrosTotal, vlLitrosAtual FROM Barril WHERE vlLitrosAtual > 0";

$result = $con->query($query);

if ($result) {
    $barris = [];

    // Percorre os resultados e monta o array
    while ($row = $result->fetch_assoc()) {
        // Garantindo que valores numéricos sejam tipados corretamente
        $row['idBarril'] = (int)$row['idBarril'];
        $row['vlLitrosTotal'] = (float)$row['vlLitrosTotal'];
        $row['vlLitrosAtual'] = (float)$row['vlLitrosAtual'];
        
        $barris[] = $row;
    }

    // Retorna o JSON array (mesmo que vazio, se não houver registros)
    echo json_encode($barris, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    // Caso ocorra erro na query
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao consultar banco de dados: ' . $con->error
    ]);
}

$con->close();

?>