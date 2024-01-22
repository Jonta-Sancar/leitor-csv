<?php
  require_once(__DIR__ . "/../auxiliares/constantes.php");
  include_once(__DIR__ ."/../Handlers/SQL_CRUD.php");

  use Handlers\SQL_CRUD;

  $db_handler = new SQL_CRUD(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if(!empty($_POST)){
    $av = $_POST['tabela'];
    unset($_POST['tabela']);
    
    if(!empty($av) && !empty($_POST)){
      foreach ($_POST as $key => $value) {
        $resultado = $db_handler->execUpdate("relaciona_perguntas_a_tabela_de_respostas", ["pontuacao_pergunta" => $value], [["tabela", $av], ["id", $key]]);

        if($resultado === false){
          break;
          header("Location: ../opcoes_avaliacao.php?av=". $av ."&msg=fail");
        }
      }

      if($resultado !== false){
        header("Location: ../opcoes_avaliacao.php?av=". $av ."&msg=success");
      }
    } else {
      header("Location: ../opcoes_avaliacao.php?av=". $av ."&msg=fail");
    }
  }