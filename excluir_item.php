<?php
session_start(); // Inicia a sessão.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.

// Verifica se o usuário está logado e tem ID de usuário na sessão.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem_erro'] = "Acesso negado. Por favor, faça login para excluir filmes.";
    header('Location: index.php'); // Redireciona para o login.
    exit;
}

$filme_id = isset($_GET['id']) ? $_GET['id'] : null; // Pega o ID do filme da URL.
$usuario_id = $_SESSION['usuario_id']; // Pega o ID do usuário logado.

// Se um ID de filme foi fornecido.
if ($filme_id) {
    try {
        // Primeiro, verifica se o filme pertence ao usuário logado.
        $stmtCheck = $pdo->prepare("SELECT id, url_imagem FROM filmes WHERE id = ? AND usuario_id = ?");
        $stmtCheck->execute([$filme_id, $usuario_id]);
        $filmeParaExcluir = $stmtCheck->fetch();

        // Se o filme for encontrado e pertencer ao usuário.
        if ($filmeParaExcluir) {
            // Se a imagem for um arquivo local (não uma URL), tenta excluí-la.
            if (!filter_var($filmeParaExcluir['url_imagem'], FILTER_VALIDATE_URL) && file_exists($filmeParaExcluir['url_imagem'])) {
                unlink($filmeParaExcluir['url_imagem']);
            }

            // Deleta o filme do banco de dados.
            $stmtDelete = $pdo->prepare("DELETE FROM filmes WHERE id = ? AND usuario_id = ?");
            $stmtDelete->execute([$filme_id, $usuario_id]);

            $_SESSION['mensagem_sucesso'] = "Filme excluído com sucesso!"; // Mensagem de sucesso.
        } else {
            $_SESSION['mensagem_erro'] = "Filme não encontrado ou você não tem permissão para excluí-lo."; // Erro de permissão ou não encontrado.
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao excluir filme: " . $e->getMessage(); // Erro no banco de dados.
    }
} else {
    $_SESSION['mensagem_erro'] = "ID do filme não fornecido para exclusão."; // Erro se ID não for fornecido.
}

header('Location: meus_itens.php'); // Redireciona de volta para a página 'Meus Filmes'.
exit;
?>