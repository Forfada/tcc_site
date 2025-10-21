-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/10/2025 às 15:53
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
-- Banco de dados: `bancolu`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento`
--

CREATE TABLE `agendamento` (
  `id` int(11) UNSIGNED NOT NULL,
  `a_hora` time NOT NULL,
  `a_dia` date NOT NULL,
  `id_u` int(11) UNSIGNED NOT NULL,
  `id_p` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `anamnese`
--

CREATE TABLE `anamnese` (
  `id` int(11) UNSIGNED NOT NULL,
  `an_hipertensao` varchar(100) NOT NULL,
  `an_cancer` varchar(100) NOT NULL,
  `an_diabetes` varchar(100) NOT NULL,
  `an_medic` varchar(200) NOT NULL,
  `an_data` datetime NOT NULL,
  `id_cli` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `anamnese`
--

INSERT INTO `anamnese` (`id`, `an_hipertensao`, `an_cancer`, `an_diabetes`, `an_medic`, `an_data`, `id_cli`) VALUES
(1, 'Não', '', 'Não', 'Nenhum', '2023-11-01 00:00:00', 1),
(2, 'Sim', '', 'Não', 'Metformina', '2023-11-05 00:00:00', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) UNSIGNED NOT NULL,
  `cli_nome` varchar(120) NOT NULL,
  `cli_idade` int(3) NOT NULL,
  `cli_cpf` varchar(11) NOT NULL,
  `cli_num` varchar(11) NOT NULL,
  `cli_nasc` datetime NOT NULL,
  `cli_obs` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `cli_nome`, `cli_idade`, `cli_cpf`, `cli_num`, `cli_nasc`, `cli_obs`) VALUES
(1, 'Rafaela Morais', 18, '50334671825', '15997444383', '2006-01-10 00:00:00', ''),
(2, 'Thiego França', 18, '50364381752', '15998009628', '2006-12-24 00:00:00', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `procedimentos`
--

CREATE TABLE `procedimentos` (
  `id` int(11) UNSIGNED NOT NULL,
  `p_nome` varchar(100) NOT NULL,
  `p_descricao` varchar(200) NOT NULL,
  `p_descricao2` varchar(800) NOT NULL,
  `p_duracao` time NOT NULL,
  `p_valor` float(5,2) NOT NULL,
  `p_foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `procedimentos`
--

INSERT INTO `procedimentos` (`id`, `p_nome`, `p_descricao`, `p_descricao2`, `p_duracao`, `p_valor`, `p_foto`) VALUES
(1, 'Avaliação de crescimento de fios', 'Avalie o crescimento dos fios das suas sobrancelhas com um profissional.', 'Uma avaliação de crescimento de fios para sobrancelhas é um procedimento estético realizado por um profissional qualificado, que tem como objetivo analisar a saúde e o crescimento dos pelos das sobrancelhas. Durante a avaliação, o profissional examina a densidade, espessura, textura e padrão de crescimento dos fios, além de identificar possíveis problemas como falhas, afinamento ou crescimento irregular. Com base nessa análise, o profissional pode recomendar tratamentos específicos para estimular o crescimento saudável dos fios, como o uso de produtos tópicos, massagens ou procedimentos estéticos.', '00:30:00', 0.00, 'noimg.jpg'),
(2, 'Consultoria', 'Agende uma consulta personalizada para te avaliar e receber recomendações de cuidados e tratamentos estéticos faciais.', 'A consultoria é uma avaliação personalizada da pele do rosto, realizada por um profissional qualificado, com o objetivo de identificar as necessidades específicas da pele e recomendar tratamentos e cuidados adequados. Durante a consulta, são analisados aspectos como tipo de pele, presença de acne, manchas, sinais de envelhecimento, hidratação e outros fatores que influenciam a saúde e aparência da rosto. Com base nessa avaliação, o profissional pode sugerir tratamentos estéticos (como limpeza de pele, peelings, hidratações) e produtos específicos para melhorar a saúde e beleza da pele.', '00:30:00', 180.00, 'noimg.jpg'),
(3, 'Skin Care', 'Melhore sua autoestima com uma rotina personalizada de Skin Care, para tratar as o que mais te incomodam.', 'Skincare é um termo em inglês para \"cuidados com a pele\", que se refere a uma rotina diária de passos e produtos para manter a pele do rosto e corpo bonita e saudável. Essa rotina geralmente envolve limpeza, hidratação e proteção solar, com o objetivo de tratar a pele, prevenir problemas como envelhecimento precoce, manchas e oleosidade, além de melhorar a aparência geral da pele. Na Lunaris, oferecemos para você uma rotina de tratamentos única e personalizada para sua pele.', '01:00:00', 97.00, 'care.jpg'),
(4, 'Limpeza de Pele', 'Transforme sua autoestima e sua pele com uma Limpeza de Pele personalizada.', 'A limpeza de pele profissional é um tratamento estético que remove impurezas, cravos e células mortas dos poros de forma profunda, sendo essencial para a saúde da pele. O procedimento é personalizado para cada tipo de pele e inclui etapas como higienização, esfoliação, vaporização para dilator os poros, extração de comedões (cravos), finalização com tônico e hidratantes, e LED terapia para otimizaros resultados.', '01:00:00', 90.00, 'limp.jpg'),
(5, 'Despigmentação de Sobrancelhas', 'Remova ou clareie o pigmento indesejado aplicado nas suas sobrancelhas.', 'A despigmentação química de sobrancelhas é um procedimento estético que utiliza ácidos específicos para remover ou clarear pigmentos indesejados da pele, como de micropigmentações antigas ou mal sucedidas. O processo consiste na aplicação de um blend de ácidos com um dermógrafo, o qual penetra na pele e estimula a expulsão do pigmento, promovendo a renovação celular e a regeneração do tecido. No pacote com cinco sessões, o ideal, você ganha desconto. Avaliação dos fios está inclusa na despigmentação e é feita no mesmo dia que foi marcado o agendamento.', '01:30:00', 190.00, 'despig.jpeg'),
(6, 'Micropigmentação de Sobrancelhas', 'Preencha suas sobrancelhas de forma natural com a nossa técnica de Microblanding ou Micropig. Shadow.', 'A micropigmentação de sobrancelhas é um procedimento estético que implanta pigmentos na camada superficial da pele, utilizando um aparelho chamado demógrafo, para desenhar e preencher as sobrancelhas, corrigir falhas e melhorar sua definição e densidade. Essa técnica imita a aparência de pelos naturais, oferecendo um resultado mais duradouro que a henna, mas menos permanente que uma tatuagem, com duração média de 6 meses a 1 ano, exigindo retoques para manter o resultado. Oferecemos duas principais técnicas: Microblanding (cria um desenho fio a fio, imitando a aparência leve e natural dos pelos) e Micropgmentação shadow (cria um efeito de preenchimento sombreado e suave nas sobrancelhas). Avaliação dos fios está inclusa na micropigmentação e é feita no mesmo dia que foi marcado o agendamen', '03:00:00', 410.00, 'mic_S.jpeg'),
(7, 'Tratamento dos Fios', 'Obtenha uma sessão personalizada de tratamento, para melhorar o crescimento dos fios de suas sobrancelhas.', '\"Tratamento dos Fios\" para sobrancelhas é um procedimento estético personalizado que busca estimular o crescimento dos pelos, fortalecê-los e recuperar falhas na região. Ele é ideal para quem tem sobrancelhas ralas, falhadas ou danificadas pelo excesso de pinça ou por problemas de pele. O procedimento não é invasivo e tem como objetivo realçar a beleza natural das sobrancelhas, corrigindo pequenas imperfeições e promovendo um crescimento saudável. Avaliação dos fios é feita separadamente do tratamento e exige um agendamento próprio.', '01:30:00', 180.00, 'trat.jpeg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `u_num` varchar(11) NOT NULL,
  `u_user` varchar(120) NOT NULL,
  `u_senha` varchar(120) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `u_num`, `u_user`, `u_senha`, `foto`) VALUES
(1, '15998009628', 'admin', '$2a$08$Cf1f11ePArKlBJomM0F6a.kde0EnMOqlC3yy97YbmH4z5QiTVRlXK', 'avatar1.png'),
(2, '15998009620', 'admin', '$2a$08$Cf1f11ePArKlBJomM0F6a.BCzdVKJqfJTiox5MhpR.J1KjJ.KWCbO', 'avatar1.png'),
(3, '15998009623', 'zd', '$2a$08$Cf1f11ePArKlBJomM0F6a.BCzdVKJqfJTiox5MhpR.J1KjJ.KWCbO', 'avatar4.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamento`
--
ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_agendamento_id_u` (`id_u`),
  ADD KEY `fk_agendamento_id_p` (`id_p`);

--
-- Índices de tabela `anamnese`
--
ALTER TABLE `anamnese`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_anamnese_id_cli` (`id_cli`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamento`
--
ALTER TABLE `agendamento`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `anamnese`
--
ALTER TABLE `anamnese`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamento`
--
ALTER TABLE `agendamento`
  ADD CONSTRAINT `fk_agendamento_id_p` FOREIGN KEY (`id_p`) REFERENCES `procedimentos` (`id`),
  ADD CONSTRAINT `fk_agendamento_id_u` FOREIGN KEY (`id_u`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `anamnese`
--
ALTER TABLE `anamnese`
  ADD CONSTRAINT `fk_anamnese_id_cli` FOREIGN KEY (`id_cli`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
