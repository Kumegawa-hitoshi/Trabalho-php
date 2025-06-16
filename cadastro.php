<?php
$pageTitle = "Cadastro de Usuário"; // Define o título da página.
include 'header.php'; // Inclui o cabeçalho.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.
require_once 'includes/funcoes.php'; // Inclui as funções de utilidade.

$mensagemErro = ''; // Variável para mensagens de erro.

// Processa o formulário de cadastro quando enviado.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';

    // Validações dos campos do formulário.
    if (empty($login) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $mensagemErro = 'Por favor, preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagemErro = 'Formato de e-mail inválido.';
    } elseif ($senha !== $confirmar_senha) {
        $mensagemErro = 'A senha e a confirmação de senha não coincidem.';
    } elseif (strlen($senha) < 6) {
        $mensagemErro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        // Verifica se o login ou e-mail já existem no banco de dados.
        if (verificarUsuarioExistente($login, $email, $pdo)) {
            $mensagemErro = 'Este login ou e-mail já está em uso. Por favor, escolha outro.';
        } else {
            // Cria um hash da senha para segurança.
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                // Prepara e executa a inserção do novo usuário no banco de dados.
                $stmt = $pdo->prepare("INSERT INTO usuarios (login, senha, email) VALUES (?, ?, ?)");
                $stmt->execute([$login, $senhaHash, $email]);

                $_SESSION['mensagem_sucesso'] = 'Cadastro realizado com sucesso! Agora você pode fazer login.';
                header('Location: index.php'); // Redireciona para a página de login.
                exit;
            } catch (PDOException $e) {
                $mensagemErro = "Erro ao cadastrar usuário: " . $e->getMessage(); // Erro no banco de dados.
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4">Cadastro de Usuário</h2>

        <?php
        // Exibe a mensagem de erro, se houver.
        if (!empty($mensagemErro)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($mensagemErro) . '</div>';
        }
        ?>

        <form method="post" action="cadastro.php" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" class="form-control" id="login" name="login" required value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="mb-3">
                <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
            <p class="text-center mt-3">Já tem uma conta? <a href="index.php">Faça login</a></p>
        </form>
    </div>
</div>

<?php
include 'footer.php'; // Inclui o rodapé.
?>