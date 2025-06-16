<?php
$pageTitle = "Login"; // Define o título da página para o cabeçalho.
include 'header.php'; // Inclui o cabeçalho da página.
require_once 'includes/conexao.php'; // Inclui a conexão com o banco de dados.

$mensagemErro = ''; // Variável para armazenar mensagens de erro de login.

// Redireciona para o dashboard se o usuário já estiver logado.
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Processa o formulário de login quando enviado.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuarioDigitado = isset($_POST['login']) ? trim($_POST['login']) : '';
    $senhaDigitada = isset($_POST['senha']) ? $_POST['senha'] : '';

    // Validação básica se os campos estão vazios.
    if (empty($usuarioDigitado) || empty($senhaDigitada)) {
        $mensagemErro = 'Por favor, preencha todos os campos.';
    } else {
        try {
            // Prepara e executa a consulta para buscar o usuário pelo login.
            $stmt = $pdo->prepare("SELECT id, login, senha FROM usuarios WHERE login = ?");
            $stmt->execute([$usuarioDigitado]);
            $usuario = $stmt->fetch();

            // Verifica se o usuário existe e se a senha está correta (usando password_verify para senhas hash).
            if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {
                $_SESSION['logado'] = true; // Define a sessão como logado.
                $_SESSION['usuario_id'] = $usuario['id']; // Armazena o ID do usuário na sessão.
                $_SESSION['usuario_login'] = $usuario['login']; // Armazena o login do usuário na sessão.
                $_SESSION['mensagem_sucesso'] = 'Login realizado com sucesso!'; // Mensagem de sucesso.
                header('Location: dashboard.php'); // Redireciona para o dashboard.
                exit;
            } else {
                $mensagemErro = 'Usuário ou senha inválidos.'; // Mensagem de erro de credenciais.
            }
        } catch (PDOException $e) {
            $mensagemErro = "Erro ao tentar fazer login: " . $e->getMessage(); // Erro no banco de dados.
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="text-center mb-4">Login</h2>

        <?php
        // Exibe a mensagem de erro, se houver.
        if (!empty($mensagemErro)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($mensagemErro) . '</div>';
        }
        ?>

        <form method="post" action="index.php" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" class="form-control" id="login" name="login" required value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
            <p class="text-center mt-3">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
        </form>
    </div>
</div>

<?php
include 'footer.php'; // Inclui o rodapé da página.
?>