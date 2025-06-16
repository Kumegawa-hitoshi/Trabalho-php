<?php
session_start(); // Inicia a sessão para acesso às mensagens.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.
require_once 'includes/funcoes.php'; // Inclui as funções de utilidade.

$filme = null; // Variável para armazenar os detalhes do filme.

// Verifica se um ID de filme foi passado via URL.
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idFilme = $_GET['id'];

    try {
        // Busca os detalhes do filme pelo ID usando a função.
        $filme = encontrarFilmePorId($idFilme, $pdo);
    } catch (PDOException $e) {
        // Em caso de erro, armazena mensagem e redireciona.
        $_SESSION['mensagem_erro'] = "Erro ao buscar detalhes do filme: " . $e->getMessage();
        header('Location: dashboard.php');
        exit;
    }
}

include 'header.php'; // Inclui o cabeçalho.

// Se o filme foi encontrado, exibe seus detalhes.
if ($filme) {
?>
    <h2><?php echo htmlspecialchars($filme['titulo']); ?> (<?php echo isset($filme['ano']) ? htmlspecialchars($filme['ano']) : 'N/A'; ?>)</h2>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <?php
                // Define a URL da imagem da capa, com um placeholder se estiver vazia.
                $imagemUrl = (isset($filme['url_imagem']) && !empty($filme['url_imagem'])) ? htmlspecialchars($filme['url_imagem']) : 'https://via.placeholder.com/300x450.png?text=Sem+Imagem';
            ?>
            <img src="<?php echo $imagemUrl; ?>" class="img-fluid rounded" alt="Capa do filme <?php echo htmlspecialchars($filme['titulo']); ?>">
        </div>
        <div class="col-md-8">
            <p><strong>Gênero:</strong> <?php echo isset($filme['genero']) ? htmlspecialchars($filme['genero']) : 'Não informado'; ?></p>
            <p><strong>Produtor:</strong> <?php echo isset($filme['produtor']) ? htmlspecialchars($filme['produtor']) : 'Não informado'; ?></p>
            <p><strong>Distribuidora:</strong> <?php echo isset($filme['distribuidora']) ? htmlspecialchars($filme['distribuidora']) : 'Não informado'; ?></p>
            <p><strong>Nota:</strong> <?php echo isset($filme['nota']) ? htmlspecialchars(number_format($filme['nota'], 1)) : 'N/A'; ?> / 10</p>
            <p><strong>Descrição:</strong></p>
            <p><?php echo isset($filme['descricao']) ? nl2br(htmlspecialchars($filme['descricao'])) : 'Descrição não disponível.'; ?></p>

            <a href="dashboard.php" class="btn btn-secondary mt-3">Voltar ao Catálogo</a>
        </div>
    </div>

<?php
} else {
    // Mensagem exibida se o filme não for encontrado.
    echo '<div class="alert alert-danger" role="alert">';
    echo 'Filme não encontrado ou ID inválido.';
    echo '</div>';
    echo '<a href="dashboard.php" class="btn btn-secondary">Voltar ao Catálogo</a>';
}

include 'footer.php'; // Inclui o rodapé.
?>