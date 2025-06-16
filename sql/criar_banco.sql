-- Cria o banco de dados 'catalogo_filmes' se ele ainda não existir.
CREATE DATABASE IF NOT EXISTS catalogo_filmes;

-- Seleciona o banco de dados 'catalogo_filmes' para uso.
USE catalogo_filmes;

-- Cria a tabela 'usuarios' se ela ainda não existir.
-- Esta tabela armazena informações de autenticação dos usuários.
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY, -- ID único para cada usuário.
    login VARCHAR(50) NOT NULL UNIQUE, -- Nome de usuário para login, deve ser único.
    senha VARCHAR(255) NOT NULL,       -- Senha hash do usuário.
    email VARCHAR(100) UNIQUE,         -- Endereço de e-mail do usuário, deve ser único.
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Data e hora do registro.
);

-- Cria a tabela 'filmes' se ela ainda não existir.
-- Esta tabela armazena os detalhes dos filmes.
CREATE TABLE IF NOT EXISTS filmes (
    id INT AUTO_INCREMENT PRIMARY KEY, -- ID único para cada filme.
    usuario_id INT NOT NULL,           -- Chave estrangeira para o ID do usuário que adicionou o filme.
    titulo VARCHAR(255) NOT NULL,      -- Título do filme (obrigatório).
    genero VARCHAR(255),               -- Gênero(s) do filme.
    ano INT,                           -- Ano de lançamento.
    produtor VARCHAR(255),             -- Produtor.
    distribuidora VARCHAR(255),        -- Distribuidora.
    nota DECIMAL(2,1),                 -- Nota do filme.
    descricao TEXT,                    -- Descrição ou sinopse.
    url_imagem VARCHAR(255),           -- URL da capa ou caminho local.
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de adição.
    -- Define a chave estrangeira e a ação CASCADE para exclusão.
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);