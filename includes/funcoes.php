<?php
// Inclui o arquivo de conexão com o banco de dados.
require_once 'conexao.php';

/**
 * Busca um filme no banco de dados pelo ID.
 * @param int $id ID do filme.
 * @param PDO $pdo Objeto PDO de conexão.
 * @return array|false Dados do filme ou false se não encontrado.
 */
function encontrarFilmePorId($id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM filmes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Verifica se login ou e-mail já existem.
 * @param string $login Login a verificar.
 * @param string $email E-mail a verificar.
 * @param PDO $pdo Objeto PDO de conexão.
 * @return bool True se existir, false caso contrário.
 */
function verificarUsuarioExistente($login, $email, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE login = ? OR email = ?");
    $stmt->execute([$login, $email]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Valida se um campo obrigatório está preenchido.
 * @param string $campo Valor do campo.
 * @param string $nomeCampo Nome para a mensagem de erro.
 * @return string Mensagem de erro ou vazio.
 */
function validarCampoObrigatorio($campo, $nomeCampo) {
    if (empty(trim($campo))) {
        return "O campo '$nomeCampo' é obrigatório.";
    }
    return "";
}

/**
 * Valida o ano do filme.
 * @param int $ano Ano a validar.
 * @return string Mensagem de erro ou vazio.
 */
function validarAno($ano) {
    $anoAtual = date("Y");
    if (!filter_var($ano, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1895, "max_range" => $anoAtual + 5]])) {
        return "O ano deve ser um número válido entre 1895 e " . ($anoAtual + 5) . ".";
    }
    return "";
}

/**
 * Valida a nota do filme (0 a 10).
 * @param float $nota Nota a validar.
 * @return string Mensagem de erro ou vazio.
 */
function validarNota($nota) {
    if (!empty($nota) && !filter_var($nota, FILTER_VALIDATE_FLOAT, ["options" => ["min_range" => 0, "max_range" => 10]])) {
        return "A nota deve ser um número entre 0 e 10.";
    }
    return "";
}

/**
 * Valida uma URL de imagem.
 * @param string $url URL a validar.
 * @return string Mensagem de erro ou vazio.
 */
function validarURLImagem($url) {
    if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
        return "A URL da imagem é inválida.";
    }
    return "";
}

/**
 * Processa o upload de um arquivo de imagem.
 * @param array $file Array $_FILES do arquivo.
 * @return array Contém o caminho da imagem e/ou mensagem de erro.
 */
function processarUploadImagem($file) {
    $mensagemErro = '';
    $caminho_imagem = '';

    if ($file['error'] == UPLOAD_ERR_OK) {
        $arquivoTmp = $file['tmp_name'];
        $nomeOriginal = basename($file['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
        $tamanhoMaximo = 2 * 1024 * 1024; // 2MB

        if (in_array($extensao, $extensoesPermitidas) && $file['size'] <= $tamanhoMaximo) {
            $novoNomeArquivo = uniqid('img_') . '.' . $extensao;
            $caminhoDestino = 'imagens/' . $novoNomeArquivo;

            // Cria o diretório 'imagens' se não existir.
            if (!is_dir('imagens')) {
                mkdir('imagens', 0755, true);
            }

            if (move_uploaded_file($arquivoTmp, $caminhoDestino)) {
                $caminho_imagem = $caminhoDestino;
            } else {
                $mensagemErro = 'Erro ao mover o arquivo para o diretório de destino.';
            }
        } else {
            $mensagemErro = 'A imagem deve ser JPG ou PNG e ter até 2MB.';
        }
    }
    return ['caminho' => $caminho_imagem, 'erro' => $mensagemErro];
}
?>