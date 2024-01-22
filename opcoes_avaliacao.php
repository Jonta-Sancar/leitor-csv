<?php
  require_once(__DIR__ . "/./auxiliares/funcoes_gerais.php");
  require_once(__DIR__ . "/./auxiliares/funcoes_interacao_com_banco.php");
  $av = filter_input(INPUT_GET, "av");

  $perguntas_av_sem_pontos = retornaPerguntasAvaliacaoSemPontos($av);
  $perguntas_av = retornaPerguntasAvaliacao($av);
  $quem_respondeu    = retornaQuemRespondeu($av);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Opções da Avaliação</title>
</head>
<body>
  <?php
    if (!empty($_GET['msg'])) {
      if($_GET['msg'] == "success"){
        ?>
          <div><strong>Tudo certo!</strong></div>
          <?php
      } else {
        ?>
        <div><strong>Algo deu errado.</strong></div>
        <?php
      }
    }

    if ($perguntas_av_sem_pontos){
      ?>
        <form action="/post/aplicar_pontos_as_questoes.php" method="post">
          <input type="hidden" name="tabela" value="<?= $av ?>">

          <?php
            foreach($perguntas_av as $pergunta_info){
              ?>
                <div class="form-group">
                  <input type="number" name="<?= $pergunta_info['identificador_pergunta'] ?>" id="<?= $pergunta_info['identificador_pergunta'] ?>" style="width:50px;">
                  <label for="<?= $pergunta_info['identificador_pergunta'] ?>"><?= $pergunta_info['texto_pergunta'] ?></label>
                </div>
              <?php
            }
          ?>

          <div class="form-group">
            <input type="submit" value="Salvar">
          </div>
        </form>
      <?php
    }
  ?>
</body>
</html>