CREATE DATABASE IF NOT EXISTS `bancolu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bancolu`;

-- criando tabela: clientes
CREATE TABLE clientes (
    id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
    cli_nome VARCHAR(120) NOT NULL,
    cli_cpf VARCHAR(15) NOT NULL,
    cli_num VARCHAR(15) NOT NULL,
    cli_nasc DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clientes` (`id`, `cli_nome`, `cli_cpf`, `cli_num`, `cli_nasc`)
 VALUES (1, 'Rafaela Morais', '503.346.718-25', '15997444383', '2006-01-10'),
 (2, 'Thiego França', '503.643.817-52', '15998009628', '2006-12-24');
 
 ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


-- criando tabela: procedimentos
 CREATE TABLE procedimentos ( 
     id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     p_nome VARCHAR(100) NOT NULL,
     p_descricao VARCHAR(200) NOT NULL,
     p_descricao2 VARCHAR(700) NOT NULL,
     p_duracao TIME NOT NULL,
     p_valor FLOAT(5,2) NOT NULL,
	 p_foto varchar(255) DEFAULT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
INSERT INTO `procedimentos` (`id`, `p_nome`, `p_descricao`, `p_descricao2`, `p_duracao`, `p_valor`, `p_foto`) 
 VALUES (1, 'HidraColor', 'Realce e destaque a aparência de seus lábios com HidraColor, hidratando, renovando e realçando a cor de seus lábios.','A "Hidra Color" é um procedimento estético para colorir temporariamente os lábios, que promove hidratação e um efeito natural semelhante ao de um lip tint, com a duração de 2 a 3 meses. É uma técnica de coloração temporária dos lábios através da aplicação de pigmentos com ácido hialurônico, que os hidrata e dá cor, proporcionando um aspecto mais saudável aos seus lábios. Diferencia-se da micropigmentação labial, sendo uma opção menos invasiva e mais rápida para quem tem receio do procedimento tradicional.', '01:30', '120,00', 'hid_C.jpeg'),
 (2, 'HidraLips', 'Renove e realce a aparência de seus lábios com nossa técnica de HidraLips, hidratando e dando brilho aos seus lábios.','A "Hidra Lips" é um procedimento estético que usa pequenas agulhas para aplicar ácido hialurónico e outros ativos nos lábios, conferindo-lhes hidratação profunda, revitalização, volume e tratamento de linhas finas. Tem como objetivo rejuvenescer, hidratar e dar brilho, volume e cor natural aos lábios com aspecto saudável, combatendo o ressecamento, descamação e linhas finas. É indicado para lábios secos, desidratados, com fissuras ou palidez.', '01:00', '120,00', 'hid_L.jpeg'),
 (3, 'Micropigmentação Labial', 'Defina o contorno de sua boca, realce sua cor natural e corrija assimetrias indesejadas.','A micropigmentação labial é um procedimento estético que insere pigmentos na camada superficial da pele dos lábios para realçar a cor, definir o contorno, corrigir assimetrias, disfarçar cicatrizes e criar um efeito de volume mais saudável e natural, semelhante a um batom semipermanente. Não é uma tatuagem, mas sim uma técnica semipermanente que utiliza agulhas finas e dura entre um e três anos, necessitando de retoques para manter o resultado. A micropigmentação não é um preenchimento e não confere volume físico, mas sim uma ilusão de volume.', '03:00', '400,00', 'mic_l.jpeg'),
 (4, 'Lash Lifting', 'realce seu olhar com naturalidade, com cílios mais alongados, curvados e volumosos.','O lash lifting é um tratamento cosmético que curva e alonga os cílios naturais, proporcionando um olhar mais definido e volumoso sem a necessidade de extensões ou cílios postiços, sendo menos invasivo. O procedimento utiliza moldes de silicone para levantar os cílios desde a raiz, além de outras etapas para fixar a nova curvatura, deixando os fios mais levantados e com o efeito do curvex por até 2 meses, a depender do ciclo de crescimento dos fios. Uma coloração pode ser aplicada para dar mais volume e cor aos cílios, intensificando o resultado.', '01:00', '115,00', 'lash.jpg'),
 (5, 'Skin Care', 'Melhore sua autoestima com uma rotina personalizada de Skin Care, para tratar as o que mais te incomodam.','Skincare é um termo em inglês para "cuidados com a pele", que se refere a uma rotina diária de passos e produtos para manter a pele do rosto e corpo bonita e saudável. Essa rotina geralmente envolve limpeza, hidratação e proteção solar, com o objetivo de tratar a pele, prevenir problemas como envelhecimento precoce, manchas e oleosidade, além de melhorar a aparência geral da pele. Na Lunaris, oferecemos para você uma rotina de tratamentos única e personalizada para sua pele.', '01:00', '97,00', 'care.jpg'),
 (6, 'Limpeza de Pele', 'Transforme sua autoestima e sua pele com uma Limpeza de Pele personalizada.','', '01:00', '90,00', 'limp.jpg'),
 (7, 'Brow Lamination', 'Realce a beleza de suas sobrancelhas, deixando-as mais alinhas e definidas com a nossa Brow Lumination personalizada.','O Brow Lamination é uma técnica que realinha e modela os pelos das sobrancelhas para cima, criando um efeito volumoso, preenchido e natural. Através do uso de produtos químicos específicos, os fios ficam com um aspecto laminado, fácil de pentear e com as falhas preenchidas, resultando num visual mais harmonioso e definido, realçando o desenho das sobrancelhas. O efeito dura em média de 1 a 2 meses, dependendo do tipo de pele e dos cuidados.', '01:30', '120,00', 'brow.jpg'),
 (8, 'Despigmentação de Sobrancelhas', 'Remova ou clareie o pigmento indesejado aplicado nas suas sobrancelhas.','A despigmentação química de sobrancelhas é um procedimento estético que utiliza ácidos específicos para remover ou clarear pigmentos indesejados da pele, como de micropigmentações antigas ou mal sucedidas. O processo consiste na aplicação de um blend de ácidos com um dermógrafo, o qual penetra na pele e estimula a expulsão do pigmento, promovendo a renovação celular e a regeneração do tecido. No pacote com cinco sessões, o ideal, você ganha desconto.', '01:10', '190,00', 'despig.jpeg'),
 (9, 'Aplicação de Henna', 'Preencha e defina suas sobrancelhas com a nossa Aplicação de Henna personalizada, corrigindo falhas indesejadas.','A aplicação de henna é um procedimento para tingir, preencher e definir sobrancelhas de forma temporária, utilizando um corante natural. Funciona como um verniz sobre os pelos, preenchendo falhas e proporcionando um desenho natural e marcante por cerca de 10 a 15 dias, desbotando gradualmente com o tempo. É uma alternativa natural a tintas químicas, que não contém amônia ou peróxido de hidrogênio, ajudando a realçar a expressão facial.', '01:00', '50,00', 'henna.jpeg'),
 (10, 'Micropigmentação de Sobrancelhas', 'Preencha suas sobrancelhas de forma natural com a nossa técnica de Microblanding ou Micropig. Shadow.','A micropigmentação de sobrancelhas é um procedimento estético que implanta pigmentos na camada superficial da pele, utilizando um aparelho chamado demógrafo, para desenhar e preencher as sobrancelhas, corrigir falhas e melhorar sua definição e densidade. Essa técnica imita a aparência de pelos naturais, oferecendo um resultado mais duradouro que a henna, mas menos permanente que uma tatuagem, com duração média de 6 meses a 1 ano, exigindo retoques para manter o resultado. Oferecemos duas principais técnicas: Microblanding (cria um desenho fio a fio, imitando a aparência leve e natural dos pelos) e Micropgmentação shadow (cria um efeito de preenchimento sombreado e suave nas sobrancelhas).', '03:00', '410,00', 'mic_S.jpeg'),
 (11, 'Tratamento dos Fios', 'Obtenha uma sessão personalizada de tratamento, para melhorar o crescimento dos fios de suas sobrancelhas.','"Tratamento dos Fios" para sobrancelhas é um procedimento estético personalizado que busca estimular o crescimento dos pelos, fortalecê-los e recuperar falhas na região. Ele é ideal para quem tem sobrancelhas ralas, falhadas ou danificadas pelo excesso de pinça ou por problemas de pele. O procedimento não é invasivo e tem como objetivo realçar a beleza natural das sobrancelhas, corrigindo pequenas imperfeições e promovendo um crescimento saudável.', '01:30', '180,00', 'trat.jpeg');
 
 ALTER TABLE `procedimentos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
 
  -- criando tabela: usuarios
CREATE TABLE usuarios (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  u_num varchar(15) NOT NULL,
  u_user varchar(120) NOT NULL,
  u_senha varchar(120) NOT NULL,
  foto varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `u_num`, `u_user`, `u_senha`, `foto`) VALUES
(1, '15998009628', 'admin', '$2a$08$Cf1f11ePArKlBJomM0F6a.UFZ6Sp2bbz/FEWdXSFF6hx71tGrjUc.', 'avatar1.png');


-- criando tabela: agendamento
 CREATE TABLE agendamento (
     id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     a_hora TIME NOT NULL,
     a_dia DATE NOT NULL,
     id_u INT(11) UNSIGNED NOT NULL,
     id_p INT(11) UNSIGNED NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
 ALTER TABLE `agendamento`ADD CONSTRAINT `fk_agendamento_id_u` FOREIGN KEY (id_u) REFERENCES `usuarios` (id);
 ALTER TABLE `agendamento` ADD CONSTRAINT `fk_agendamento_id_p` FOREIGN KEY (id_p) REFERENCES `procedimentos` (id);
 ALTER TABLE agendamento 
    MODIFY id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

-- (opcional mas recomendável)
-- adiciona timestamp de criação
ALTER TABLE agendamento 
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
 
 -- criando tabela: anamnese 1
 CREATE TABLE anamnese (
     id INT(11) UNSIGNED PRIMARY KEY NOT NULL,
     an_hipertensao VARCHAR(100) NOT NULL,
     an_diabetes VARCHAR(100) NOT NULL,
	 an_medic VARCHAR(200) NOT NULL,
     an_data DATETIME NOT NULL,
     id_cli INT(11) UNSIGNED NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
  ALTER TABLE `anamnese` ADD CONSTRAINT `fk_anamnese_id_cli` FOREIGN KEY (id_cli) REFERENCES `clientes` (id);

 ALTER TABLE `anamnese`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  INSERT INTO `anamnese` (`id`, `an_hipertensao`, `an_diabetes`, `an_medic`, `an_data`, `id_cli`) VALUES
 (1, 'Não', 'Não', 'Nenhum', '2023-11-01', 1),
 (2, 'Sim', 'Não', 'Metformina', '2023-11-05', 2);