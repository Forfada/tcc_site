CREATE DATABASE IF NOT EXISTS `bancolu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bancolu`;

-- criando tabela: clientes
CREATE TABLE clientes (
    id_cli INT(11) UNSIGNED PRIMARY KEY NOT NULL,
    cli_nome VARCHAR(120) NOT NULL,
    cli_cpf VARCHAR(15) NOT NULL,
    cli_num VARCHAR(15) NOT NULL,
    cli_nasc DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clientes` (`id_cli`, `cli_nome`, `cli_cpf`, `cli_num`, `cli_nasc`)
 VALUES (1, 'Rafaela Morais', '503.346.718-25', '15997444383', '2006-01-10'),
 (2, 'Thiego França', '503.643.817-52', '15998009628', '2006-12-24');
 
 ALTER TABLE `clientes`
  MODIFY `id_cli` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


-- criando tabela: procedimentos
 CREATE TABLE procedimentos ( 
     id_p INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     p_nome VARCHAR(100) NOT NULL,
     p_descricao VARCHAR(200) NOT NULL,
     p_duracao VARCHAR(100) NOT NULL,
     p_valor FLOAT(5,2) NOT NULL,
	 p_foto varchar(255) DEFAULT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
INSERT INTO `procedimentos` (`id_p`, `p_nome`, `p_descricao`, `p_duracao`, `p_valor`, `p_foto`) 
 VALUES (1, 'HidraColor', 'Realce e destaque a aparência de seus lábios com HidraColor, hidratando, renovando e realçando a cor de seus lábios.', '1h30m', '120,00', 'hid_C.jpeg'),
 (2, 'HidraLips', 'Renove e realce a aparência de seus lábios com nossa técnica de HidraLips, hidratando e dando brilho aos seus lábios.', '1h', '120,00', 'hid_L.jpeg'),
 (3, 'Micropigmentação Labial', 'Defina o contorno de sua boca, realce sua cor natural e corrija assimetrias indesejadas.', '3h', '400,00', 'mic_l.jpeg'),
 (4, 'Lash Lifting', 'realce seu olhar com naturalidade, com cílios mais alongados, curvados e volumosos.', '1h', '115,00', 'lash.jpg'),
 (5, 'Skin Care', 'Melhore sua autoestima com uma rotina personalizada de Skin Care, para tratar as o que mais te incomodam.', '1h', '97,00', 'care.jpg'),
 (6, 'Limpeza de Pele', 'Transforme sua autoestima e sua pele com uma Limpeza de Pele personalizada.', '1h', '90,00', 'limp.jpg'),
 (7, 'Brow Lamination', 'Realce a beleza de suas sobrancelhas, deixando-as mais alinhas e definidas com a nossa Brow Lumination personalizada.', '1h30m', '120,00', 'brow.jpg'),
 (8, 'Despigmentação de Sobrancelhas', 'Remova ou clareie o pigmento indesejado aplicado nas suas sobrancelhas. No pacote com cinco sessões, o ideal, você ganha desconto.', '1h10m', '190,00', 'despig.jpeg'),
 (9, 'Aplicação de Henna', 'Preencha e defina suas sobrancelhas com a nossa Aplicação de Henna personalizada, corrigindo falhas indesejadas.', '1h', '50,00', 'henna.jpeg'),
 (10, 'Micropigmentação de Sobrancelhas', 'Preencha suas sobrancelhas de forma natural com a nossa técnica de Microblanding ou Micropig. Shadow.', '3h', '410,00', 'mic_S.jpeg'),
 (11, 'Tratamento dos Fios', 'Obtenha uma sessão personalizada de tratamento, para melhorar o crescimento dos fios de suas sobrancelhas.', '1h30m', '180,00', 'trat.jpeg');
 
 ALTER TABLE `procedimentos`
  MODIFY `id_p` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
  
  
  -- criando tabela: esteticista
 CREATE TABLE esteticista (
     id_est INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     est_nome VARCHAR(120) NOT NULL,
     est_num VARCHAR(15) NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
 INSERT INTO `esteticista` (`id_est`, `est_nome`, `est_num`) 
 VALUES (1, 'Larissa Moraes', '13982114071');
 
 ALTER TABLE `esteticista`
  MODIFY `id_est` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
 
 
  -- criando tabela: usuarios
CREATE TABLE usuarios (
  id_u INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  u_num varchar(15) NOT NULL,
  u_user varchar(120) NOT NULL,
  u_senha varchar(120) NOT NULL,
  foto varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id_u`, `u_num`, `u_user`, `u_senha`, `foto`) VALUES
(1, '15998009628', 'admin', '$2a$08$Cf1f11ePArKlBJomM0F6a.UFZ6Sp2bbz/FEWdXSFF6hx71tGrjUc.', 'avatar1.jpg');


-- criando tabela: agendamento
 CREATE TABLE agendamento (
     id_ag INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     a_hora DATETIME NOT NULL,
     a_dia DATETIME NOT NULL,
     id_cli INT(11) UNSIGNED NOT NULL,
     id_p INT(11) UNSIGNED NOT NULL,
     id_est INT(11) UNSIGNED NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
 ALTER TABLE `agendamento`ADD CONSTRAINT `fk_agendamento_id_cli` FOREIGN KEY (id_cli) REFERENCES `clientes` (id_cli);
 ALTER TABLE `agendamento` ADD CONSTRAINT `fk_agendamento_id_p` FOREIGN KEY (id_p) REFERENCES `procedimentos` (id_p);
 ALTER TABLE `agendamento` ADD CONSTRAINT `fk_agendamento_id_est` FOREIGN KEY (id_est) REFERENCES `esteticista` (id_est);
 
 
 --criando tabela: anamnese
 CREATE TABLE anamnese (
     id_an INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     an_hipertensao VARCHAR(100) NOT NULL,
     an_diabetes VARCHAR(100) NOT NULL,
	 an_medic VARCHAR(200) NOT NULL,
     id_cli INT(11) UNSIGNED NOT NULL,
     id_est INT(11) UNSIGNED NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
  ALTER TABLE `anamnese` ADD CONSTRAINT `fk_anamnese_id_cli` FOREIGN KEY (id_cli) REFERENCES `clientes` (id_cli);
  ALTER TABLE `anamnese` ADD CONSTRAINT `fk_anamnese_id_est` FOREIGN KEY (id_est) REFERENCES `esteticista` (id_est);
 
 