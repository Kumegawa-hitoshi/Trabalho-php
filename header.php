<?php
// header.php

// Inicia a sessão PHP se ainda não estiver iniciada.
// Essencial para gerenciar o status de login e mensagens.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Catálogo de Filmes'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        /* Estilos básicos para o layout da página, garantindo o rodapé e navbar fixos. */
        body {
            padding-top: 70px; /* Espaço para a navbar fixa */
            padding-bottom: 60px; /* Espaço para o footer */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container.flex-grow-1 {
          flex-grow: 1; /* Permite que o conteúdo principal ocupe o espaço restante. */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Catálogo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" aria-current="page" href="dashboard.php">Catálogo</a>
        </li>
        <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'novo_item.php') ? 'active' : ''; ?>" href="novo_item.php">Adicionar Filme</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'meus_itens.php') ? 'active' : ''; ?>" href="meus_itens.php">Meus Filmes</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cadastro.php') ? 'active' : ''; ?>" href="cadastro.php">Cadastro</a>
            </li>
        <?php endif; ?>
      </ul>
      <div class="d-flex flex-column flex-lg-row navbar-right-items ms-lg-auto">

          <form class="d-flex me-lg-2 my-2 my-lg-0" method="get" action="dashboard.php">
              <input class="form-control me-2" type="search" placeholder="Buscar filme ou gênero" name="filtro" aria-label="Buscar" value="<?php echo isset($_GET['filtro']) ? htmlspecialchars($_GET['filtro']) : ''; ?>">
              <button class="btn btn-warning" type="submit">Buscar</button>
          </form>

          <ul class="navbar-nav mb-2 mb-lg-0 mt-2 mt-lg-0">
              <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                  <li class="nav-item">
                      <a class="btn btn-outline-danger" href="logout.php">
                          Logout (<?php echo isset($_SESSION['usuario_login']) ? htmlspecialchars($_SESSION['usuario_login']) : 'Usuário'; ?>)
                      </a>
                  </li>
              <?php endif; ?>
          </ul>
      </div>
    </div>
  </div>
</nav>

<div class="container mt-4 flex-grow-1">
    <?php
    // Exibe mensagens de sucesso armazenadas na sessão.
    if (isset($_SESSION['mensagem_sucesso'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['mensagem_sucesso']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['mensagem_sucesso']); // Limpa a mensagem após exibir.
    }
    // Exibe mensagens de erro armazenadas na sessão.
    if (isset($_SESSION['mensagem_erro'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['mensagem_erro']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['mensagem_erro']); // Limpa a mensagem após exibir.
    }
    ?>