CREATE DATABASE IF NOT EXISTS `respostas_av`;

USE `respostas_av`;

CREATE TABLE `relaciona_perguntas_a_tabela_de_respostas`(
	id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    tabela VARCHAR(200) NOT NULL,
    identificador_pergunta VARCHAR(50) NOT NULL,
    texto_pergunta VARCHAR(500) NOT NULL,
    pontuacao_pergunta FLOAT DEFAULT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `pontuacao_respostas`(
	id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    tabela VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    identificador_pergunta VARCHAR(50) NOT NULL,
    pontuacao_pergunta FLOAT DEFAULT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);