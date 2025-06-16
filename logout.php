<?php
session_start(); // Inicia a sessão para poder manipulá-la.

$_SESSION = array(); // Limpa todas as variáveis de sessão.

session_destroy(); // Destrói a sessão atual.

header('Location: index.php'); // Redireciona o usuário para a página de login.
exit; // Garante que o script pare de executar após o redirecionamento.
?>