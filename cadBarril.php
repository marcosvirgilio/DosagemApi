<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'conexao.php';
$con->set_charset("utf8");

$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// Extração e validação dos novos campos
$deProduto     = $jsonParam['deProduto'] ?? '';
$vlLitrosTotal = floatval($jsonParam['vlLitrosTotal'] ?? 0);
$vlLitrosAtual = $vlLitrosTotal;

// Validação básica para evitar entradas vazias
if (empty($deProduto) || $vlLitrosTotal <= 0) {
    echo json_encode(['success' => false, 'message' => 'Descrição do produto e volume total são obrigatórios.']);
    exit;
}

// Preparar a query para a tabela 'barril'
$stmt = $con->prepare("
    INSERT INTO barril (deProduto, vlLitrosTotal, vlLitrosAtual)
    VALUES (?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

// "d" para campos decimais/double, "s" para string
$stmt->bind_param("sdd", $deProduto, $vlLitrosTotal, $vlLitrosAtual);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Barril registrado com sucesso!',
        'id' => $con->insert_id // Retorna o ID gerado
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro no registro: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>