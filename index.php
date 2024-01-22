<?php
  $existe_pendencia = (bool)glob(__DIR__ . DIRECTORY_SEPARATOR . "avaliacoes/*.csv");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?php include_once(__DIR__ . "/./modulos/head.php") ?>
  <title>Gestão de Avaliações</title>
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
  ?>
  <div class="container">
    <h1>Gestão de Avaliações</h1>
    <form action="post/salvar_arquivos_csv.php" method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="envio-csv">Enviar Arquivos:</label>
        <input type="file" class="form-control" multiple name="envio-csv[]" id="envio-csv">
      </div>
      <div class="d-grid">
        <input type="submit" class="btn btn-success" value="Salvar Arquivos">
      </div>
    </form>
    <div class="row text-center mt-4">
      <div class="col">
        <a href="./salvar_informacoes_no_banco.php" class="btn btn-primary <?php if(!$existe_pendencia){echo "disabled";} ?>">Salvar Avaliações</a>
      </div>
      <div class="col">
        <a href="./opcoes_avaliacao.php" class="btn btn-primary">Opções Para as Avaliações</a>
      </div>
    </div>
  </div>

  <?php include_once(__DIR__ . "/./modulos/footer.php") ?>
</body>
</html>