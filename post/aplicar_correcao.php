<?php
  require_once(__DIR__ . "/../auxiliares/constantes.php");
  include_once(__DIR__ ."/../Handlers/SQL_CRUD.php");

  use Handlers\SQL_CRUD;

  $db_handler = new SQL_CRUD(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if(!empty($_POST)){
    $av             = $_POST['tabela'];
    $quem_respondeu = $_POST['quem_respondeu'];
    unset($_POST['tabela']);
    unset($_POST['quem_respondeu']);
    
    if(!empty($av) && !empty($_POST)){
      foreach ($_POST as $key => $value) {
        $condicoes = [
          ['tabela', $av],
          ['email', $quem_respondeu],
          ["identificador_pergunta", $key]
        ];

        $resultado_select = $db_handler->execSelect('pontuacao_respostas', null, $condicoes)['RESULT'];
        $existe_registro = (bool)count($resultado_select);

        switch ($value) {
          case 'um':
              $valor_real = 1;
            break;
          case 'meio':
              $valor_real = 0.5;
            break;
          default:
              $valor_real = 0;
            break;
        }
        
        var_dump($existe_registro);
        echo '<br>';
        if($existe_registro){
          $resultado = $db_handler->execUpdate("pontuacao_respostas", ["pontuacao_pergunta" => $valor_real], $condicoes);
        } else {
          $dados_insert = [
            'tabela' => $av,
            'email' => $quem_respondeu,
            'identificador_pergunta' => $key,
            'pontuacao_pergunta' => $valor_real
          ];

          $resultado = $db_handler->execInsert("pontuacao_respostas", $dados_insert);
        }

        var_dump($resultado);
        echo '<br>';
        echo '<br>';


        if($resultado === false){
          break;
          // header("Location: ../opcoes_avaliacao.php?av=". $av ."&msg=fail");
        }
      }

      if($resultado !== false){
        // header("Location: ../opcoes_avaliacao.php?av=". $av ."&msg=success");
      }
    } else {
      // header("Location: ../opcoes_avaliacao.php?av=". $av ."&msg=fail");
    }
  }