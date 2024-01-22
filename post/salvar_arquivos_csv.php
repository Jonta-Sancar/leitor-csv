<?php
  require_once(__DIR__ . "/../auxiliares/constantes.php");
  include_once(__DIR__ ."/../Handlers/SQL_CRUD.php");

  use Handlers\SQL_CRUD;

  $db_handler = new SQL_CRUD(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if(!empty($_FILES)) {
    $arquivos = $_FILES['envio-csv'];
    $quantos_arquivos = count($arquivos['name']);
    
    $resultado_upload = false;
    for($i=0; $i < $quantos_arquivos; $i++){
      $nome = $arquivos['name'][$i];
      $nome_temporario = $arquivos['tmp_name'][$i];

      $extensao = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
      if($extensao == "csv"){
        $caminho_envio = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "avaliacoes";
        
        if(!is_dir($caminho_envio)){
          mkdir($caminho_envio);
        }

        $o_arquivo_existe = is_file($caminho_envio . DIRECTORY_SEPARATOR . $nome);
        if(!$o_arquivo_existe){
          $resultado_upload = move_uploaded_file($nome_temporario, $caminho_envio . DIRECTORY_SEPARATOR . $nome);
        } else {
          break;
        }
      } else {
        header("Location: ../index.php?av=". $av ."&msg=fail");
      }
    }

    if($resultado_upload && !$o_arquivo_existe){
      header("Location: ../index.php?av=". $av ."&msg=success");
    } else if($o_arquivo_existe){
      header("Location: ../index.php?av=". $av ."&msg=o arquivo já existe");
    } else {
      header("Location: ../index.php?av=". $av ."&msg=fail");
    }
  }