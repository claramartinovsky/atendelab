-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/07/2026 às 02:48
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
-- Banco de dados: `atendelab`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('aberto','em_andamento','concluído') DEFAULT 'aberto',
  `data_atendimento` date NOT NULL DEFAULT current_timestamp(),
  `horario_atendimento` time NOT NULL,
  `observacao_final` text NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `descricao`, `status`, `data_atendimento`, `horario_atendimento`, `observacao_final`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 1, 1, 'O aluno compareceu à coordenação com dúvidas sobre quebra de pré-requisitos para cursar Fábrica de Software.', 'aberto', '2026-06-25', '14:30:00', '', '2026-07-01 22:48:28', '2026-07-01 22:48:28'),
(2, 2, 2, 1, 'Solicitação de monitoria para a estrutura MVC simples em PHP. Aluna relatou dificuldades no entendimento de rotas.', 'aberto', '2026-06-29', '15:15:00', '', '2026-07-01 22:48:28', '2026-07-01 22:48:28'),
(3, 3, 3, 1, 'Estudante informou instabilidade na conexão ao tentar commitar as alterações locais no GitHub através do laboratório 104.', 'concluído', '2026-06-29', '16:00:00', 'a', '2026-07-01 22:48:28', '2026-07-01 22:48:28'),
(4, 3, 2, 1, 'Apoio pedagógico.', 'em_andamento', '2026-06-16', '18:00:00', '', '2026-07-01 22:48:28', '2026-07-01 22:48:28'),
(5, 3, 2, 1, 'a', 'aberto', '2026-07-02', '21:19:00', '', '2026-07-02 00:18:44', '2026-07-02 00:18:44'),
(6, 3, 2, 1, 'aa', 'em_andamento', '2026-07-17', '16:28:00', '', '2026-07-02 00:28:14', '2026-07-02 00:28:14'),
(7, 2, 4, 1, 'aa', 'aberto', '2026-07-09', '16:29:00', '', '2026-07-02 00:30:06', '2026-07-02 00:30:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `curso` varchar(120) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `observacoes` text NOT NULL,
  `status` varchar(10) DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `documento`, `telefone`, `email`, `curso`, `periodo`, `observacoes`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Lucas Silva Costa', '111.222.333-44', '(47) 99988-1122', 'lucas.costa@univille.br', 'Engenharia de Software', '5', 'Bolsista integral', 'inativo', '2026-06-29 20:09:51', '2026-07-02 00:19:53'),
(2, 'Mariana Oliveira Souza', '222.333.444-55', '(47) 99122-3344', 'mariana.souza@univille.br', 'Odontologia', '7', 'Tempo integral', 'ativo', '2026-06-29 20:09:51', '2026-07-01 22:44:44'),
(3, 'Carlos Eduardo Santos', '333.444.555-66', '(47) 98877-6655', 'carlos.santos@gmail.com', 'Direito', '3', '', 'ativo', '2026-06-29 20:09:51', '2026-07-01 22:45:00'),
(4, 'Ana Julia Pereira', '444.555.666-77', '(47) 99233-4455', 'ana.pereira@univille.br', 'Biomedicina', '4', '', 'inativo', '2026-06-29 20:09:51', '2026-07-01 22:45:13'),
(6, 'Mariana Oliveira Souza', '222.333.444-55', '(47) 99122-3344', 'mariana.souza@univille.br', 'Design', '2', 'Bolsista integral', 'ativo', '2026-06-29 20:10:02', '2026-07-01 22:45:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Orientação Acadêmica', 'Ajustes de matrícula, análise de grade horária e validação de disciplinas.', 'ativo', '2026-07-01 22:40:28', '2026-07-01 22:40:28'),
(2, 'Apoio Pedagógico', 'Monitorias de programação, algoritmos e suporte a dúvidas de aulas práticas.', 'ativo', '2026-07-01 22:40:28', '2026-07-01 22:40:28'),
(3, 'Suporte Técnico', 'Problemas com o login do SmartClient, VS Code ou laboratórios da Fábrica.', 'ativo', '2026-07-01 22:40:28', '2026-07-01 22:40:28'),
(4, 'Secretaria', 'Emissão de atestados, requerimentos e protocolos gerais.', 'ativo', '2026-07-01 22:40:28', '2026-07-01 22:40:28'),
(5, 'Reserva de Laboratório', 'Agendamentos expirados ou salas inativas temporariamente.', 'inativo', '2026-07-01 22:40:28', '2026-07-01 22:40:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','aluno','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador Teste', 'admin@atendelab.com', '$2y$10$3NiVAmjzlGB.EF0LEEw9qOaCLUQCyGD1SIuW8MaEwlqCniVC3i5DW', 'admin', 'ativo', '2026-06-29 19:34:20', '2026-06-29 22:03:46');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimentos_pessoas` (`pessoa_id`),
  ADD KEY `fk_atendimentos_usuarios` (`usuario_id`),
  ADD KEY `fk_atendimentos_tipos` (`tipo_atendimento_id`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atendimentos_pessoas` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_atendimentos_tipos` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_atendimentos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
