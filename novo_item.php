<?php
$pageTitle = "Adicionar Novo Filme"; // Define o título da página.
include 'header.php'; // Inclui o cabeçalho.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.
require_once 'includes/funcoes.php'; // Inclui as funções de utilidade.

// Verifica se o usuário está logado. Se não, redireciona.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem_erro'] = "Acesso negado. Por favor, faça login para adicionar filmes.";
    header('Location: index.php');
    exit;
}

$mensagemErroForm = ''; // Variável para armazenar mensagens de erro do formulário.
$usuario_id = $_SESSION['usuario_id']; // ID do usuário logado.

// Processa o formulário quando enviado.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta e sanitiza os dados do formulário.
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
    $ano = isset($_POST['ano']) ? $_POST['ano'] : null;
    $produtor = isset($_POST['produtor']) ? trim($_POST['produtor']) : '';
    $distribuidora = isset($_POST['distribuidora']) ? trim($_POST['distribuidora']) : '';
    $nota = isset($_POST['nota']) ? $_POST['nota'] : null;
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $url_imagem_input = isset($_POST['url_imagem']) ? trim($_POST['url_imagem']) : '';

    $caminho_imagem = ''; // Variável para o caminho da imagem final.

    $erros = []; // Array para armazenar erros de validação.
    // Validação de campos obrigatórios e específicos.
    if (empty($titulo)) $erros[] = "O campo 'Título' é obrigatório.";
    if (empty($genero)) $erros[] = "O campo 'Gênero' é obrigatório.";
    
    $erroAno = validarAno($ano);
    if (!empty($erroAno)) $erros[] = $erroAno;
    
    $erroNota = validarNota($nota);
    if (!empty($erroNota)) $erros[] = $erroNota;

    // Processa o upload da imagem.
    $uploadResult = processarUploadImagem($_FILES['upload_imagem']);
    if (!empty($uploadResult['erro'])) {
        $erros[] = $uploadResult['erro'];
    } else {
        $caminho_imagem = $uploadResult['caminho'];
    }

    // Se não houve upload de imagem, tenta usar a URL fornecida.
    if (empty($caminho_imagem) && !empty($url_imagem_input)) {
        $erroUrl = validarURLImagem($url_imagem_input);
        if (!empty($erroUrl)) {
            $erros[] = $erroUrl;
        } else {
            $caminho_imagem = $url_imagem_input;
        }
    } elseif (empty($caminho_imagem) && empty($url_imagem_input)) {
        // Se nem upload nem URL foram fornecidos, adiciona erro.
        $erros[] = 'É necessário fornecer uma URL ou fazer upload de uma imagem para a capa do filme.';
    }

    // Se houver erros, concatena-os para exibir.
    if (!empty($erros)) {
        $mensagemErroForm = implode('<br>', $erros);
    } else {
        try {
            // Prepara e executa a inserção do novo filme no banco de dados.
            $stmt = $pdo->prepare("INSERT INTO filmes (usuario_id, titulo, genero, ano, produtor, distribuidora, nota, descricao, url_imagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $usuario_id,
                $titulo,
                $genero,
                $ano,
                $produtor,
                $distribuidora,
                ($nota !== false && $nota !== null) ? $nota : null, // Garante que null seja inserido se a nota for inválida ou vazia.
                $descricao,
                $caminho_imagem
            ]);

            $_SESSION['mensagem_sucesso'] = 'Filme "' . htmlspecialchars($titulo) . '" adicionado com sucesso!';
            header('Location: dashboard.php'); // Redireciona para o dashboard.
            exit;
        } catch (PDOException $e) {
            $mensagemErroForm = "Erro ao adicionar filme: " . $e->getMessage(); // Erro no banco de dados.
        }
    }
}
?>

<h2>Área restrita</h2>
<p>Bem-vindo(a), <strong><?php echo htmlspecialchars($_SESSION['usuario_login']); ?></strong>! Utilize o formulário abaixo para adicionar um novo filme ao catálogo.</p>
<hr>

<h3>Adicionar Novo Filme</h3>

<?php
// Exibe mensagens de erro do formulário.
if (!empty($mensagemErroForm)) {
    echo '<div class="alert alert-danger">' . $mensagemErroForm . '</div>';
}
?>

<form method="post" action="novo_item.php" class="needs-validation" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
        <label for="titulo" class="form-label">Título*</label>
        <input type="text" class="form-control" id="titulo" name="titulo" required value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
    </div>

    <div class="mb-3">
        <label for="genero" class="form-label">Gênero*</label>
        <input type="text" class="form-control" id="genero" name="genero" required value="<?php echo isset($_POST['genero']) ? htmlspecialchars($_POST['genero']) : ''; ?>">
        <div class="form-text">Separe múltiplos gêneros por vírgula.</div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="ano" class="form-label">Ano*</label>
            <input type="number" class="form-control" id="ano" name="ano" min="1888" max="<?php echo date('Y') + 5; ?>" required value="<?php echo isset($_POST['ano']) ? htmlspecialchars($_POST['ano']) : ''; ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="nota" class="form-label">Nota (0 a 10)</label>
            <input type="number" step="0.1" min="0" max="10" class="form-control" id="nota" name="nota" value="<?php echo isset($_POST['nota']) ? htmlspecialchars($_POST['nota']) : ''; ?>">
        </div>
    </div>

    <div class="mb-3">
        <label for="produtor" class="form-label">Produtor</label>
        <input type="text" class="form-control" id="produtor" name="produtor" value="<?php echo isset($_POST['produtor']) ? htmlspecialchars($_POST['produtor']) : ''; ?>">
    </div>

    <div class="mb-3">
        <label for="distribuidora" class="form-label">Distribuidora</label>
        <input type="text" class="form-control" id="distribuidora" name="distribuidora" value="<?php echo isset($_POST['distribuidora']) ? htmlspecialchars($_POST['distribuidora']) : ''; ?>">
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea class="form-control" id="descricao" name="descricao" rows="4"><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
    </div>

    <p class="mt-3 mb-1"><strong>Capa do Filme:</strong> Você pode adicionar a capa fornecendo uma URL ou enviando um arquivo do seu computador.</p>
    <p class="text-muted small mb-3">Obs: Se você fornecer ambos (URL e arquivo), o arquivo enviado terá prioridade.</p>

    <div class="mb-3">
        <label for="url_imagem" class="form-label">URL da Imagem (Opcional se enviar arquivo)</label>
        <input type="url" class="form-control" id="url_imagem" name="url_imagem" placeholder="https://exemplo.com/imagem.jpg" value="<?php echo isset($_POST['url_imagem']) ? htmlspecialchars($_POST['url_imagem']) : ''; ?>">
    </div>

    <div class="mb-3">
        <label for="upload_imagem" class="form-label">Ou envie uma imagem do seu computador (Opcional se fornecer URL)</label>
        <input type="file" class="form-control" id="upload_imagem" name="upload_imagem" accept="image/jpeg, image/png">
        <div class="form-text">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB.</div>
    </div>

    <button type="submit" class="btn btn-success">Adicionar Filme</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancelar e Ver Catálogo</a>
</form>

<?php include 'footer.php'; ?>