<?php
$pageTitle = "Login"; // Define o título da página.
include 'header.php'; // Inclui o cabeçalho.

$mensagemErro = ''; // Variável para mensagens de erro.

// Credenciais fixas para teste (não é o login real com banco de dados).
$usuarioCorreto = 'admin';
$senhaCorreta = '123';

// Se já estiver logado (neste contexto, usando o login de teste), redireciona.
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: protegido.php'); // Redireciona para uma página protegida de exemplo.
    exit;
}

// Processa o formulário de login quando enviado.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuarioDigitado = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $senhaDigitada = isset($_POST['senha']) ? $_POST['senha'] : '';

    // Verifica as credenciais fixas.
    if ($usuarioDigitado === $usuarioCorreto && $senhaDigitada === $senhaCorreta) {
        $_SESSION['logado'] = true; // Define a sessão como logado.
        $_SESSION['usuario'] = $usuarioDigitado; // Armazena o usuário na sessão.
        header('Location: protegido.php'); // Redireciona para a página protegida.
        exit;
    } else {
        $mensagemErro = 'Usuário ou senha inválidos.'; // Mensagem de erro.
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="text-center mb-4">Login</h2>

        <?php
        // Exibe a mensagem de erro.
        if (!empty($mensagemErro)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($mensagemErro) . '</div>';
        }
        ?>

        <form method="post" action="login.php" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuário</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : 'admin'; ?>">
                <div class="form-text">Login de teste: admin</div>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required value="">
                 <div class="form-text">Senha de teste: 123</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</div>

<?php
include 'footer.php'; // Inclui o rodapé.
?>