<?php
$pageTitle = "Editar Filme"; // Define o título da página.
include 'header.php'; // Inclui o cabeçalho.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.
require_once 'includes/funcoes.php'; // Inclui as funções de utilidade.

// Verifica se o usuário está logado. Se não, redireciona.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem_erro'] = "Acesso negado. Por favor, faça login para editar filmes.";
    header('Location: index.php');
    exit;
}

$mensagemErroForm = ''; // Variável para mensagens de erro do formulário.
$filme = null; // Variável para armazenar os dados do filme a ser editado.
$filme_id = isset($_GET['id']) ? $_GET['id'] : null; // ID do filme a ser editado, vindo da URL.
$usuario_id = $_SESSION['usuario_id']; // ID do usuário logado.

// Se um ID de filme foi fornecido, tenta buscar o filme.
if ($filme_id) {
    try {
        // Prepara e executa a consulta para buscar o filme pelo ID e ID do usuário.
        $stmt = $pdo->prepare("SELECT * FROM filmes WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$filme_id, $usuario_id]);
        $filme = $stmt->fetch();

        // Se o filme não for encontrado ou não pertencer ao usuário, redireciona.
        if (!$filme) {
            $_SESSION['mensagem_erro'] = "Filme não encontrado ou você não tem permissão para editá-lo.";
            header('Location: meus_itens.php');
            exit;
        }
    } catch (PDOException $e) {
        // Em caso de erro no banco de dados, armazena mensagem e redireciona.
        $_SESSION['mensagem_erro'] = "Erro ao buscar filme para edição: " . $e->getMessage();
        header('Location: meus_itens.php');
        exit;
    }
} else {
    // Se o ID do filme não foi fornecido, armazena mensagem e redireciona.
    $_SESSION['mensagem_erro'] = "ID do filme não fornecido para edição.";
    header('Location: meus_itens.php');
    exit;
}

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

    $caminho_imagem = $filme['url_imagem']; // Mantém a imagem existente por padrão.

    $erros = []; // Array para armazenar erros de validação.
    // Validação de campos obrigatórios e específicos.
    if (empty($titulo)) $erros[] = "O campo 'Título' é obrigatório.";
    if (empty($genero)) $erros[] = "O campo 'Gênero' é obrigatório.";
    
    $erroAno = validarAno($ano);
    if (!empty($erroAno)) $erros[] = $erroAno;
    
    $erroNota = validarNota($nota);
    if (!empty($erroNota)) $erros[] = $erroNota;

    // Processar upload de imagem se um novo arquivo foi enviado.
    if (isset($_FILES['upload_imagem']) && $_FILES['upload_imagem']['error'] == UPLOAD_ERR_OK) {
        $uploadResult = processarUploadImagem($_FILES['upload_imagem']);
        if (!empty($uploadResult['erro'])) {
            $erros[] = $uploadResult['erro'];
        } else {
            $caminho_imagem = $uploadResult['caminho']; // Usa o novo caminho da imagem.
        }
    } elseif (!empty($url_imagem_input)) {
        // Se uma nova URL foi fornecida e não houve upload.
        $erroUrl = validarURLImagem($url_imagem_input);
        if (!empty($erroUrl)) {
            $erros[] = $erroUrl;
        } else {
            $caminho_imagem = $url_imagem_input; // Usa a nova URL da imagem.
        }
    }

    // Verifica se há alguma imagem definida (URL ou upload).
    if (empty($caminho_imagem)) {
        $erros[] = 'É necessário fornecer uma URL ou fazer upload de uma imagem para a capa do filme.';
    }

    // Se houver erros, concatena-os para exibir.
    if (!empty($erros)) {
        $mensagemErroForm = implode('<br>', $erros);
    } else {
        try {
            // Prepara e executa a atualização do filme no banco de dados.
            $stmt = $pdo->prepare("UPDATE filmes SET titulo = ?, genero = ?, ano = ?, produtor = ?, distribuidora = ?, nota = ?, descricao = ?, url_imagem = ? WHERE id = ? AND usuario_id = ?");
            $stmt->execute([
                $titulo,
                $genero,
                $ano,
                $produtor,
                $distribuidora,
                ($nota !== false && $nota !== null) ? $nota : null,
                $descricao,
                $caminho_imagem,
                $filme_id,
                $usuario_id
            ]);

            $_SESSION['mensagem_sucesso'] = 'Filme "' . htmlspecialchars($titulo) . '" atualizado com sucesso!';
            header('Location: meus_itens.php'); // Redireciona para a página 'Meus Filmes'.
            exit;
        } catch (PDOException $e) {
            $mensagemErroForm = "Erro ao atualizar filme: " . $e->getMessage(); // Erro no banco de dados.
        }
    }
}
?>

<h2>Editar Filme</h2>
<hr>

<?php
// Exibe mensagens de erro do formulário.
if (!empty($mensagemErroForm)) {
    echo '<div class="alert alert-danger">' . $mensagemErroForm . '</div>';
}
?>

<form method="post" action="editar_item.php?id=<?php echo urlencode($filme['id']); ?>" class="needs-validation" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
        <label for="titulo" class="form-label">Título*</label>
        <input type="text" class="form-control" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($filme['titulo']); ?>">
    </div>

    <div class="mb-3">
        <label for="genero" class="form-label">Gênero*</label>
        <input type="text" class="form-control" id="genero" name="genero" required value="<?php echo htmlspecialchars($filme['genero']); ?>">
        <div class="form-text">Separe múltiplos gêneros por vírgula.</div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="ano" class="form-label">Ano*</label>
            <input type="number" class="form-control" id="ano" name="ano" min="1888" max="<?php echo date('Y') + 5; ?>" required value="<?php echo htmlspecialchars($filme['ano']); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="nota" class="form-label">Nota (0 a 10)</label>
            <input type="number" step="0.1" min="0" max="10" class="form-control" id="nota" name="nota" value="<?php echo htmlspecialchars($filme['nota']); ?>">
        </div>
    </div>

    <div class="mb-3">
        <label for="produtor" class="form-label">Produtor</label>
        <input type="text" class="form-control" id="produtor" name="produtor" value="<?php echo htmlspecialchars($filme['produtor']); ?>">
    </div>

    <div class="mb-3">
        <label for="distribuidora" class="form-label">Distribuidora</label>
        <input type="text" class="form-control" id="distribuidora" name="distribuidora" value="<?php echo htmlspecialchars($filme['distribuidora']); ?>">
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea class="form-control" id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($filme['descricao']); ?></textarea>
    </div>

    <p class="mt-3 mb-1"><strong>Capa do Filme:</strong> Você pode adicionar a capa fornecendo uma URL ou enviando um arquivo do seu computador.</p>
    <p class="text-muted small mb-3">Obs: Se você fornecer ambos (URL e arquivo), o arquivo enviado terá prioridade.</p>

    <div class="mb-3">
        <label for="url_imagem" class="form-label">URL da Imagem (Opcional se enviar arquivo)</label>
        <input type="url" class="form-control" id="url_imagem" name="url_imagem" placeholder="https://exemplo.com/imagem.jpg" value="<?php echo htmlspecialchars($filme['url_imagem']); ?>">
    </div>

    <div class="mb-3">
        <label for="upload_imagem" class="form-label">Ou envie uma imagem do seu computador (Opcional se fornecer URL)</label>
        <input type="file" class="form-control" id="upload_imagem" name="upload_imagem" accept="image/jpeg, image/png">
        <div class="form-text">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB.</div>
    </div>
    
    <?php if (!empty($filme['url_imagem'])): ?>
        <div class="mb-3">
            <p>Capa atual:</p>
            <img src="<?php echo htmlspecialchars($filme['url_imagem']); ?>" alt="Capa atual do filme" style="max-width: 200px; height: auto; border: 1px solid #ccc;">
        </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-success">Salvar Alterações</button>
    <a href="meus_itens.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include 'footer.php'; ?>