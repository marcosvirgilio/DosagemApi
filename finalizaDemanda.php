<?php

ini_set('display_errors', 0); 
header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';
$con->set_charset("utf8");

// Obtém o ID do JSON
$jsonParam = json_decode(file_get_contents('php://input'), true);
$idDemanda = isset($jsonParam['idDemanda']) ? intval($jsonParam['idDemanda']) : 0;

if ($idDemanda <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da demanda inválido ou ausente.']);
    exit;
}

// Prepara o update para definir dtConclusao como agora
$stmt = $con->prepare("UPDATE Demanda SET dtConclusao = NOW() WHERE idDemanda = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação: ' . $con->error]);
    exit;
}

$stmt->bind_param("i", $idDemanda);

if ($stmt->execute()) {
    // Verifica se alguma linha foi de fato alterada (se o ID existia)
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Demanda concluída com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Demanda não encontrada ou já concluída.'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>