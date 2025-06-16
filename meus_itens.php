<?php
$pageTitle = "Meus Filmes"; // Define o título da página.
include 'header.php'; // Inclui o cabeçalho.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.
require_once 'includes/funcoes.php'; // Inclui as funções de utilidade.

// Verifica se o usuário está logado. Se não, redireciona.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem_erro'] = "Acesso negado. Por favor, faça login para ver seus filmes.";
    header('Location: index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id']; // ID do usuário logado.
$meusFilmes = []; // Array para armazenar os filmes do usuário.
$filtroAplicado = ''; // Variável para o termo de busca.

try {
    // Consulta SQL para buscar filmes pelo ID do usuário.
    $sql = "SELECT * FROM filmes WHERE usuario_id = ?";
    $params = [$usuario_id]; // Parâmetro do ID do usuário.

    // Adiciona filtro se houver termo de busca.
    if (isset($_GET['filtro']) && !empty(trim($_GET['filtro']))) {
        $filtro = '%' . trim($_GET['filtro']) . '%';
        $filtroAplicado = trim($_GET['filtro']);
        $sql .= " AND (titulo LIKE ? OR genero LIKE ?)"; // Condição AND para o filtro.
        $params[] = $filtro; // Adiciona parâmetros para o filtro.
        $params[] = $filtro;
    }

    $stmt = $pdo->prepare($sql); // Prepara a consulta.
    $stmt->execute($params); // Executa com os parâmetros.
    $meusFilmes = $stmt->fetchAll(); // Busca os filmes.

} catch (PDOException $e) {
    // Em caso de erro, armazena mensagem e define array vazio.
    $_SESSION['mensagem_erro'] = "Erro ao carregar seus filmes: " . $e->getMessage();
    $meusFilmes = [];
}
?>

<h2>Meus Filmes</h2>
<p>Aqui estão os filmes que você adicionou ao catálogo.</p>

<?php if (!empty($filtroAplicado)): ?>
    <p class="text-muted">Exibindo resultados para: "<?php echo htmlspecialchars($filtroAplicado); ?>"</p>
<?php endif; ?>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php
    if (count($meusFilmes) > 0) {
        foreach ($meusFilmes as $filme) {
            // Define a URL da imagem da capa, com um placeholder se estiver vazia.
            $imagemUrl = (!empty($filme['url_imagem'])) ? htmlspecialchars($filme['url_imagem']) : 'https://via.placeholder.com/500x750.png?text=Sem+Imagem';
            ?>
            <div class="col">
              <div class="card h-100 shadow-sm">
                <img src="<?php echo $imagemUrl; ?>" class="card-img-top" alt="Capa do filme <?php echo htmlspecialchars($filme['titulo']); ?>">
                <div class="card-body">
                  <div>
                      <h5 class="card-title"><?php echo htmlspecialchars($filme['titulo']); ?></h5>
                      <?php if (isset($filme['genero'])): ?>
                          <p class="card-text mb-1"><small class="text-muted">Gênero: <?php echo htmlspecialchars($filme['genero']); ?></small></p>
                      <?php endif; ?>
                       <?php if (isset($filme['ano'])): ?>
                          <p class="card-text mb-2"><small class="text-muted">Ano: <?php echo htmlspecialchars($filme['ano']); ?></small></p>
                      <?php endif; ?>
                  </div>
                  <div class="d-flex justify-content-between">
                      <a href="detalhes.php?id=<?php echo urlencode($filme['id']); ?>" class="btn btn-warning btn-sm">Saiba mais</a>
                      <a href="editar_item.php?id=<?php echo urlencode($filme['id']); ?>" class="btn btn-info btn-sm">Editar</a>
                      <a href="excluir_item.php?id=<?php echo urlencode($filme['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este filme?');">Excluir</a>
                  </div>
                </div>
              </div>
            </div>
            <?php
        }
    } else {
        // Mensagem se nenhum filme for adicionado pelo usuário.
        echo '<p class="col-12 alert alert-info">Você ainda não adicionou nenhum filme' . (!empty($filtroAplicado) ? ' com o critério "' . htmlspecialchars($filtroAplicado) . '"' : '') . '.</p>';
        echo '<p class="col-12"><a href="novo_item.php" class="btn btn-success">Adicionar meu primeiro filme!</a></p>';
    }
    ?>
</div>

<?php
include 'footer.php'; // Inclui o rodapé.
?>