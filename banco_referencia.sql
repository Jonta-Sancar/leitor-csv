DROP DATABASE IF EXISTS `respostas_av`;
CREATE DATABASE IF NOT EXISTS `respostas_av`;

USE `respostas_av`;

CREATE TABLE `relaciona_perguntas_a_tabela_de_respostas`(
	id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    tabela VARCHAR(200) NOT NULL,
    identificador_pergunta VARCHAR(50) NOT NULL,
    texto_pergunta VARCHAR(500) NOT NULL,
    pontuacao_pergunta FLOAT DEFAULT NULL
);