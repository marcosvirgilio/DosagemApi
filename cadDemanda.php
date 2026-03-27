<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'conexao.php';
$con->set_charset("utf8");

// Obtém o corpo da requisição
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

/**
 * Extração dos campos NOT NULL conforme o CREATE TABLE:
 * vlLitrosDemanda (decimal), idUsuario (int), idBarril (int)
 */
$vlLitrosDemanda = isset($jsonParam['vlLitrosDemanda']) ? floatval($jsonParam['vlLitrosDemanda']) : 0;
$idUsuario       = isset($jsonParam['idUsuario']) ? intval($jsonParam['idUsuario']) : 0;
$idBarril        = isset($jsonParam['idBarril']) ? intval($jsonParam['idBarril']) : 0;

// Validação simples de campos obrigatórios
if ($vlLitrosDemanda <= 0 || $idUsuario <= 0 || $idBarril <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Campos obrigatórios faltando ou inválidos (vlLitrosDemanda, idUsuario, idBarril).'
    ]);
    exit;
}

// Preparar a query para a tabela 'Demanda'
// dtCadastro é preenchido com NOW() para capturar data e hora atual
$stmt = $con->prepare("
    INSERT INTO Demanda (dtCadastro, vlLitrosDemanda, idUsuario, idBarril)
    VALUES (NOW(), ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

/**
 * Tipos de bind: 
 * d = double/decimal
 * i = integer
 */
$stmt->bind_param("dii", $vlLitrosDemanda, $idUsuario, $idBarril);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Demanda registrada com sucesso!',
        'idDemanda' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao inserir no banco: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>