CREATE DATABASE IF NOT EXISTS `bancolu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bancolu`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- criando tabela: clientes
CREATE TABLE clientes (
    id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
    cli_nome VARCHAR(120) NOT NULL,
    cli_sexo VARCHAR(25) NOT NULL,
    cli_cidade VARCHAR(120) NOT NULL,
    cli_idade INT(3) NOT NULL,
    cli_cpf VARCHAR(11) NOT NULL,
    cli_num VARCHAR(11) NOT NULL,
    cli_nasc DATETIME NOT NULL,
    cli_obs VARCHAR(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clientes` (`id`, `cli_nome`, `cli_idade`, `cli_sexo`, `cli_cidade`, `cli_cpf`, `cli_num`, `cli_nasc`)
 VALUES (1, 'Rafaela Morais', '18', 'Feminino', 'Sorocaba', '50334671825', '15997444383', '2006-01-10'),
 (2, 'Thiego França', '18', 'Masculino', 'Sorocaba', '50364381752', '15998009628', '2006-12-24');
 
 ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


-- criando tabela: procedimentos
 CREATE TABLE procedimentos ( 
     id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     p_nome VARCHAR(100) NOT NULL,
     p_descricao VARCHAR(200) NOT NULL,
     p_descricao2 VARCHAR(800) NOT NULL,
     p_duracao TIME NOT NULL,
     p_valor FLOAT(5,2) NOT NULL,
	 p_foto varchar(255) DEFAULT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
INSERT INTO `procedimentos` (`id`, `p_nome`, `p_descricao`, `p_descricao2`, `p_duracao`, `p_valor`, `p_foto`) 
 VALUES (1, 'Avaliação de crescimento de fios', 'Avalie o crescimento dos fios das suas sobrancelhas com um profissional.','Uma avaliação de crescimento de fios para sobrancelhas é um procedimento estético realizado por um profissional qualificado, que tem como objetivo analisar a saúde e o crescimento dos pelos das sobrancelhas. Durante a avaliação, o profissional examina a densidade, espessura, textura e padrão de crescimento dos fios, além de identificar possíveis problemas como falhas, afinamento ou crescimento irregular. Com base nessa análise, o profissional pode recomendar tratamentos específicos para estimular o crescimento saudável dos fios, como o uso de produtos tópicos, massagens ou procedimentos estéticos.', '00:30', '00,00','avali.jpg'),
 (2, 'Consultoria', 'Agende uma consulta personalizada para te avaliar e receber recomendações de cuidados e tratamentos estéticos faciais.','A consultoria é uma avaliação personalizada da pele do rosto, realizada por um profissional qualificado, com o objetivo de identificar as necessidades específicas da pele e recomendar tratamentos e cuidados adequados. Durante a consulta, são analisados aspectos como tipo de pele, presença de acne, manchas, sinais de envelhecimento, hidratação e outros fatores que influenciam a saúde e aparência da rosto. Com base nessa avaliação, o profissional pode sugerir tratamentos estéticos (como limpeza de pele, peelings, hidratações) e produtos específicos para melhorar a saúde e beleza da pele.', '00:30', '180,00', 'consul.jpg'),
 (3, 'Skin Care', 'Melhore sua autoestima com uma rotina personalizada de Skin Care, para tratar as o que mais te incomodam.','Skincare é um termo em inglês para "cuidados com a pele", que se refere a uma rotina diária de passos e produtos para manter a pele do rosto e corpo bonita e saudável. Essa rotina geralmente envolve limpeza, hidratação e proteção solar, com o objetivo de tratar a pele, prevenir problemas como envelhecimento precoce, manchas e oleosidade, além de melhorar a aparência geral da pele. Na Lunaris, oferecemos para você uma rotina de tratamentos única e personalizada para sua pele.', '01:00', '97,00', 'care.jpg'),
 (4, 'Limpeza de Pele', 'Transforme sua autoestima e sua pele com uma Limpeza de Pele personalizada.','A limpeza de pele profissional é um tratamento estético que remove impurezas, cravos e células mortas dos poros de forma profunda, sendo essencial para a saúde da pele. O procedimento é personalizado para cada tipo de pele e inclui etapas como higienização, esfoliação, vaporização para dilator os poros, extração de comedões (cravos), finalização com tônico e hidratantes, e LED terapia para otimizaros resultados.', '01:00', '90,00', 'limp.jpg'),
 (5, 'Despigmentação de Sobrancelhas', 'Remova ou clareie o pigmento indesejado aplicado nas suas sobrancelhas.','A despigmentação química de sobrancelhas é um procedimento estético que utiliza ácidos específicos para remover ou clarear pigmentos indesejados da pele, como de micropigmentações antigas ou mal sucedidas. O processo consiste na aplicação de um blend de ácidos com um dermógrafo, o qual penetra na pele e estimula a expulsão do pigmento, promovendo a renovação celular e a regeneração do tecido. No pacote com cinco sessões, o ideal, você ganha desconto. Avaliação dos fios está inclusa na despigmentação e é feita no mesmo dia que foi marcado o agendamento.', '01:30', '190,00', 'despig.jpeg'),
 (6, 'Micropigmentação de Sobrancelhas', 'Preencha suas sobrancelhas de forma natural com a nossa técnica de Microblanding ou Micropig. Shadow.','A micropigmentação de sobrancelhas é um procedimento estético que implanta pigmentos na camada superficial da pele, utilizando um aparelho chamado demógrafo, para desenhar e preencher as sobrancelhas, corrigir falhas e melhorar sua definição e densidade. Essa técnica imita a aparência de pelos naturais, oferecendo um resultado mais duradouro que a henna, mas menos permanente que uma tatuagem, com duração média de 6 meses a 1 ano, exigindo retoques para manter o resultado. Oferecemos duas principais técnicas: Microblanding (cria um desenho fio a fio, imitando a aparência leve e natural dos pelos) e Micropgmentação shadow (cria um efeito de preenchimento sombreado e suave nas sobrancelhas). Avaliação dos fios está inclusa na micropigmentação e é feita no mesmo dia que foi marcado o agendamento.', '03:00', '410,00', 'mic_S.jpeg'),
 (7, 'Tratamento dos Fios', 'Obtenha uma sessão personalizada de tratamento, para melhorar o crescimento dos fios de suas sobrancelhas.','"Tratamento dos Fios" para sobrancelhas é um procedimento estético personalizado que busca estimular o crescimento dos pelos, fortalecê-los e recuperar falhas na região. Ele é ideal para quem tem sobrancelhas ralas, falhadas ou danificadas pelo excesso de pinça ou por problemas de pele. O procedimento não é invasivo e tem como objetivo realçar a beleza natural das sobrancelhas, corrigindo pequenas imperfeições e promovendo um crescimento saudável. Avaliação dos fios é feita separadamente do tratamento e exige um agendamento próprio.', '01:30', '180,00', 'trat.jpeg');
 
 ALTER TABLE `procedimentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
 
  -- criando tabela: usuarios
CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `u_email` varchar(255) NOT NULL,
  `u_user` varchar(120) NOT NULL,
  `u_senha` varchar(120) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `auth_token` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `u_email`, `u_user`, `u_senha`, `foto`, `auth_token`) VALUES
(1, 'pedrofunceca@gmail.com', 'admin', '$2a$08$Cf1f11ePArKlBJomM0F6a.sXHUj16Ozct5isv1fBqP43RxrjasQQu', 'avatar1.png', '7dc90acdff5d1f63af5dd25bf38c4d1494ebfcd459c15138d9ba4cec28525898'),
(2, 'adm@example.com', 'adm', '$2a$08$Cf1f11ePArKlBJomM0F6a.BCzdVKJqfJTiox5MhpR.J1KjJ.KWCbO', 'avatar1.png', NULL),
(3, 'user3@example.com', 'fds', '$2a$08$Cf1f11ePArKlBJomM0F6a.kde0EnMOqlC3yy97YbmH4z5QiTVRlXK', 'avatar1.png', NULL),
(4, 'afonsodias3628@gmail.com', 'teste', '$2a$08$Cf1f11ePArKlBJomM0F6a.BCzdVKJqfJTiox5MhpR.J1KjJ.KWCbO', 'avatar4.png', NULL);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_email` (`u_email`);

ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


    -- criando tabela: password_resets
CREATE TABLE `password_resets` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `token` varchar(128) NOT NULL,
  `new_password_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `new_password_hash`, `expires_at`, `created_at`) VALUES
(1, 1, '56f4b1aa4fda1b7af095787374bd88166cc9ffbde31baca73159e01d1c307a59', '$2y$10$rX9Ci5XQv4WPhk1oxhsj3OtnFR9qxXnKXlHwI93JzYpGEl3yfvTOG', '2025-10-24 19:04:37', '2025-10-24 16:04:37');

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `password_resets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;


-- criando tabela: agendamento
 CREATE TABLE agendamento (
     id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     a_hora TIME NOT NULL,
     a_dia DATE NOT NULL,
     id_u INT(11) UNSIGNED NOT NULL,
     id_p INT(11) UNSIGNED NOT NULL,
     created_at timestamp NOT NULL DEFAULT current_timestamp()
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
 ALTER TABLE `agendamento`ADD CONSTRAINT `fk_agendamento_id_u` FOREIGN KEY (id_u) REFERENCES `usuarios` (id);
 ALTER TABLE `agendamento` ADD CONSTRAINT `fk_agendamento_id_p` FOREIGN KEY (id_p) REFERENCES `procedimentos` (id);
 ALTER TABLE agendamento 
    MODIFY id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `agendamento` (`id`, `a_hora`, `a_dia`, `id_u`, `id_p`, `created_at`) VALUES
(1, '10:00:00', '2025-10-30', 4, 7, '2025-10-24 16:34:14'),
(2, '11:30:00', '2025-10-30', 4, 1, '2025-10-24 16:34:14'),
(3, '09:00:00', '2025-10-31', 4, 5, '2025-10-24 16:36:48');
 
 -- criando tabela: anamnese 1
 CREATE TABLE anamnese (
     id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     an_hipertensao VARCHAR(100) NOT NULL,
     an_cancer VARCHAR(100) NOT NULL,
     an_fumante VARCHAR(100) NOT NULL,
     an_alergia VARCHAR(100) NOT NULL,
     an_gravidez VARCHAR(100) NOT NULL,
     an_herpes VARCHAR(100) NOT NULL,
     an_queloide VARCHAR(100) NOT NULL,
     an_hepatite VARCHAR(100) NOT NULL,
     an_cardiopata VARCHAR(100) NOT NULL,
     an_anemia VARCHAR(100) NOT NULL,
     an_depressao VARCHAR(100) NOT NULL,
     an_glaucoma VARCHAR(100) NOT NULL,
     an_hiv VARCHAR(100) NOT NULL,
     an_pele VARCHAR(100) NOT NULL,
     an_acne VARCHAR(100) NOT NULL,
     an_outro VARCHAR(100) NOT NULL,
     an_diabetes VARCHAR(100) NOT NULL,
	 an_medic VARCHAR(200) NOT NULL,
     an_data DATETIME NOT NULL,
     id_cli INT(11) UNSIGNED NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
  ALTER TABLE `anamnese` ADD CONSTRAINT `fk_anamnese_id_cli` FOREIGN KEY (id_cli) REFERENCES `clientes` (id);

 ALTER TABLE `anamnese`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  INSERT INTO `anamnese` (`id`, `an_hipertensao`, `an_cancer`, `an_fumante`, `an_alergia`, `an_gravidez`, `an_herpes`, `an_queloide`, `an_hepatite`, `an_cardiopata`, `an_anemia`, `an_depressao`, `an_glaucoma`, `an_hiv`, `an_pele`, `an_acne`, `an_outro`, `an_diabetes`, `an_medic`, `an_data`, `id_cli`) VALUES
 (1, 'Não', 'Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não', 'Nenhum', '2023-11-01', 1),
 (2, 'Sim', 'Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não','Não', 'Metformina', '2023-11-05', 2);

-- tabela para requests de alteração de senha (token seguro)
CREATE TABLE IF NOT EXISTS password_resets (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    token VARCHAR(128) NOT NULL,
    new_password_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (token),
    INDEX (user_id),
    CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;