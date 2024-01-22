<?php
  require_once(__DIR__ . "/./constantes.php");
  include_once(__DIR__ ."/../Handlers/SQL_CRUD.php");

  use Handlers\SQL_CRUD;

  $db_handler = new SQL_CRUD(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if(!empty($_POST)){

    $av = '';
    $INFO = [];
    foreach ($_POST as $key => $value) {
      $dado = filter_input(INPUT_POST, $key);

      if($key == "tabela"){
        $av = $dado;
      } else {
        $INFO[ $key ] = $dado;
      }
    }
    
    if(!empty($av) && !empty($INFO)){
      foreach ($INFO as $key => $value) {
        $resultado = $db_handler->execUpdate;
      }
    } else {
      header("Location: ../opcoes_avaliacao.php?msg=fail");
    }
  }