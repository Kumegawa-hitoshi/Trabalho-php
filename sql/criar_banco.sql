-- Cria o banco de dados 'catalogo_filmes' se ele ainda não existir.
CREATE DATABASE IF NOT EXISTS catalogo_filmes;

-- Seleciona o banco de dados 'catalogo_filmes' para uso nas operações seguintes.
USE catalogo_filmes;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/06/2025 às 21:10
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `catalogo_filmes`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `filmes`
--

CREATE TABLE `filmes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `produtor` varchar(255) DEFAULT NULL,
  `distribuidora` varchar(255) DEFAULT NULL,
  `nota` decimal(2,1) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `url_imagem` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `filmes`
--

INSERT INTO `filmes` (`id`, `usuario_id`, `titulo`, `genero`, `ano`, `produtor`, `distribuidora`, `nota`, `descricao`, `url_imagem`, `data_criacao`) VALUES
(1, 1, 'O Castelo Animado', 'Animação, Aventura, Fantasia', 2004, 'Studio Ghibli, Tokuma Shoten, NTV', 'Buena Vista International', 8.2, 'Uma jovem é amaldiçoada com uma velhice precoce por uma bruxa ciumenta e encontra refúgio no castelo mágico e ambulante de um feiticeiro excêntrico.', 'https://image.tmdb.org/t/p/original/1hTfaEWktMJPxCk5nZNtK7F86C9.jpg', '2025-06-16 18:56:30'),
(2, 1, 'A Viagem de Chihiro', 'Animação, Aventura, Fantasia', 2001, 'Studio Ghibli, Tokuma Shoten, NTV', 'Toho', 8.6, 'Uma garota de dez anos chamada Chihiro, presa em um mundo misterioso de espíritos, deve trabalhar em uma casa de banhos para deuses, bruxas e monstros para encontrar uma maneira de libertar a si mesma e seus pais.', 'https://image.tmdb.org/t/p/original/hhoKhsyJ3hFaxEm5pMdZRiTu2lJ.jpg', '2025-06-16 18:56:30'),
(3, 1, 'My Hero Academia: Missão Mundial de Heróis', 'Animação, Ação, Aventura', 2021, 'Bones', 'Toho', 7.5, 'Quando uma organização misteriosa que prega a erradicação de superpoderes lança uma ameaça global, os heróis de todo o mundo, incluindo a Classe 1-A da UA, são enviados em uma missão crucial.', 'https://image.tmdb.org/t/p/original/7uVyf7or5KxQfO2h0OrV1ZZ99WK.jpg', '2025-06-16 18:56:30'),
(4, 1, 'Jujutsu Kaisen 0', 'Animação, Ação, Fantasia', 2021, 'MAPPA', 'Toho', 7.9, 'Yuta Okkotsu é um garoto que está sofrendo. Uma maldição o persegue desde a morte de seu amor de infância, Rika, que se tornou um espírito vingativo.', 'https://image.tmdb.org/t/p/original/4MAIb2ctURfs9YNIcEzk0IS4M5I.jpg', '2025-06-16 18:56:30'),
(5, 1, 'Naruto Shippuden o Filme: The Last', 'Animação, Ação, Romance', 2014, 'Studio Pierrot', 'Toho', 7.4, 'Dois anos após os eventos da Quarta Grande Guerra Ninja, uma nova ameaça surge na forma de Toneri Otsutsuki, que tenta destruir a Terra e sequestrar Hinata Hyuga.', 'https://image.tmdb.org/t/p/original/sKn9V7hvkxJqFWBhNLHStbpHUIN.jpg', '2025-06-16 18:56:30'),
(6, 1, 'Your Name', 'Animação, Drama, Fantasia, Romance', 2016, 'CoMix Wave Films', 'Toho', 8.5, 'Duas pessoas, um garoto do campo e uma garota da cidade, descobrem que estão trocando de corpo intermitentemente.', 'https://upload.wikimedia.org/wikipedia/pt/7/7f/Kimi-no-Na-wa-poster.jpg', '2025-06-16 18:56:30'),
(7, 1, 'A Voz do Silêncio', 'Animação, Drama, Romance', 2016, 'Kyoto Animation', 'Shochiku', 8.3, 'Um valentão de escola fundamental que atormentava uma colega surda é forçado a confrontar seu passado e faz um esforço para se redimir.', 'https://image.tmdb.org/t/p/original/haMUCn5bNgeKb2EkILvhYGoHXZX.jpg', '2025-06-16 18:56:30'),
(9, 1, 'Demon Slayer: Mugen Train - O Filme', 'Animação, Ação, Aventura, Fantasia', 2020, 'ufotable', 'Toho', 7.9, 'Tanjiro Kamado e seus amigos do Corpo de Extermínio de Demônios se juntam ao Pilar das Chamas, Kyojuro Rengoku, para investigar uma série de desaparecimentos misteriosos em um trem.', 'https://image.tmdb.org/t/p/original/jlGL6yoyDTAZeVzqTfD9aU1tFnr.jpg', '2025-06-16 18:56:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `login`, `senha`, `email`, `data_cadastro`) VALUES
(1, 'admin', '$2y$10$gnYpB1lwyGwY1RFZDNbGLunOi6CqZ6h3QLClSaWbrMnGM01Lzg8aK', 'admin@admin.com', '2025-06-16 18:53:27');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `filmes`
--
ALTER TABLE `filmes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `filmes`
--
ALTER TABLE `filmes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `filmes`
--
ALTER TABLE `filmes`
  ADD CONSTRAINT `filmes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
