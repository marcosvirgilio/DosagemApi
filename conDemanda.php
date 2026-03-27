<?php

ini_set('display_errors', 0); // Desativado para não corromper o JSON em caso de erro
header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';
$con->set_charset("utf8");

$sql = "SELECT idDemanda, dtCadastro, vlLitrosDemanda, idUsuario, idBarril 
        FROM Demanda 
        WHERE dtConclusao IS NULL 
        ORDER BY dtCadastro DESC";

$result = $con->query($sql);
$demandas = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Conversão de tipos para garantir integridade no JSON
        $row['idDemanda']       = (int)$row['idDemanda'];
        $row['vlLitrosDemanda'] = (float)$row['vlLitrosDemanda'];
        $row['idUsuario']       = (int)$row['idUsuario'];
        $row['idBarril']        = (int)$row['idBarril'];
        
        $demandas[] = $row;
    }
}

// Retorna apenas o array puro
echo json_encode($demandas);

$con->close();

?>