<?php
$pageTitle = "Catálogo de Filmes"; // Define o título da página.
include 'header.php'; // Inclui o cabeçalho.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.
require_once 'includes/funcoes.php'; // Inclui as funções de utilidade.

$filmesParaExibir = []; // Array para armazenar os filmes a serem exibidos.
$filtroAplicado = ''; // Variável para guardar o termo de busca.

try {
    $sql = "SELECT * FROM filmes"; // Consulta SQL base para buscar todos os filmes.
    $params = []; // Array para armazenar parâmetros da consulta.

    // Verifica se há um filtro de busca.
    if (isset($_GET['filtro']) && !empty(trim($_GET['filtro']))) {
        $filtro = '%' . trim($_GET['filtro']) . '%'; // Adiciona curingas para a busca.
        $filtroAplicado = trim($_GET['filtro']); // Armazena o termo filtrado.
        // Adiciona a condição WHERE para buscar por título ou gênero.
        $sql .= " WHERE titulo LIKE ? OR genero LIKE ?";
        $params = [$filtro, $filtro]; // Atribui os parâmetros para a busca.
    }

    $stmt = $pdo->prepare($sql); // Prepara a consulta SQL.
    $stmt->execute($params); // Executa a consulta com os parâmetros.
    $filmesParaExibir = $stmt->fetchAll(); // Busca todos os filmes resultantes.

} catch (PDOException $e) {
    // Em caso de erro no banco de dados, armazena uma mensagem de erro na sessão.
    $_SESSION['mensagem_erro'] = "Erro ao carregar filmes: " . $e->getMessage();
    $filmesParaExibir = []; // Garante que o array de filmes esteja vazio.
}
?>

<h2>Catálogo de Filmes</h2>

<?php if (!empty($filtroAplicado)): ?>
    <p class="text-muted">Exibindo resultados para: "<?php echo htmlspecialchars($filtroAplicado); ?>"</p>
<?php endif; ?>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php
    // Verifica se há filmes para exibir.
    if (count($filmesParaExibir) > 0) {
        foreach ($filmesParaExibir as $filme) {
            // Define a URL da imagem da capa do filme, usando um placeholder se não houver.
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
                  <a href="detalhes.php?id=<?php echo urlencode($filme['id']); ?>" class="btn btn-warning mt-auto">Saiba mais</a>
                </div>
              </div>
            </div>
            <?php
        }
    } else {
        // Mensagem exibida se nenhum filme for encontrado.
        echo '<p class="col-12 alert alert-warning">Nenhum filme encontrado' . (!empty($filtroAplicado) ? ' com o critério "' . htmlspecialchars($filtroAplicado) . '"' : '') . '.</p>';
    }
    ?>
</div>

<?php
include 'footer.php'; // Inclui o rodapé.
?>