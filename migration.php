<?php
error_reporting(E_ALL ^ E_NOTICE);

$sistema = $argv[1];
$comando = $argv[2];

if (empty($sistema) or empty($comando)) {
  echo 'parâmetros sistema/comando não definidos' . PHP_EOL;
}

$dirSistema = __DIR__ . DIRECTORY_SEPARATOR . $sistema;
$dirSistemaMigrations = $dirSistema . DIRECTORY_SEPARATOR . 'migrations';

if (!is_dir($dirSistema) or !is_dir($dirSistemaMigrations)) {
  echo 'diretório sistema/migrations não existe'. PHP_EOL;
}

$caminhoBanco = __DIR__ . DIRECTORY_SEPARATOR . 'banco.sqlite';
$pdo = new PDO('sqlite:' . $caminhoBanco);
$pdo->exec("
create table if not exists migration (
  nm_arquivo text
);
");

switch ($comando) {
  case "criar":
    $nmMigration = $argv[3];
    if (empty($nmMigration)) {
      echo 'nome da migration não informado' . PHP_EOL;
    }

    criarArquivoMigration($dirSistemaMigrations, $nmMigration);

    
      break;
  
  case "migrar":
    $functionMigration = $argv[3];
    $migrationEspecifica = $argv[4];

    if (!in_array($functionMigration, ['up', 'down'])) {
      die('comando de execução inválido');  
    }

    $isUP = $functionMigration === 'up' ? 1 : 0;
    //perigo quando for down sem especificar a migration porque remove todo o banco;
    // if (!$isUP and empty($migrationEspecifica)) {
    //   die('down apenas para uma migration específica');
    // }

    $migrationExecutadas = array();
    
    $scanned_directory = array_diff(scandir($dirSistemaMigrations, !$isUP), array('..', '.'));
    foreach ($scanned_directory as $nmArquivoMigration) {
      $sqlMigrationExecutada = "SELECT nm_arquivo FROM migration WHERE nm_arquivo = :nm_arquivo";
      $stm = $pdo->prepare($sqlMigrationExecutada);
      $stm->execute(['nm_arquivo' => $nmArquivoMigration]);
      $migrationExecutada = $stm->fetch()['nm_arquivo'];

      if ($isUP and !empty($migrationExecutada)) {
        echo 'já foi executada anteriormente: ' . $nmArquivoMigration . PHP_EOL;
        continue;
      }
      if (!$isUP and empty($migrationExecutada)) {
        echo 'não executada anteriormente: ' . $nmArquivoMigration . PHP_EOL;
        continue;
      }

      if (!empty($migrationEspecifica) and $migrationEspecifica !== substr($nmArquivoMigration, 0, -4)) {
        continue;
      }

      $migrationExecutadas[] = $nmArquivoMigration;

      $sqlInsertMigration = $isUP ? "insert into migration (nm_arquivo) values (:nm_arquivo);" : "delete from migration where nm_arquivo = :nm_arquivo;";
      $stm = $pdo->prepare($sqlInsertMigration);
      $stm->bindValue(':nm_arquivo', $nmArquivoMigration);
      $stm->execute();
      require_once $dirSistemaMigrations . DIRECTORY_SEPARATOR . $nmArquivoMigration ;
      $nmClasse = substr($nmArquivoMigration, 0, -4);
      $classe = new $nmClasse();

      $pdo->exec($classe->$functionMigration());
      echo 'function migration: ' . $classe->$functionMigration() . PHP_EOL;
    }

    if (empty($migrationExecutadas)) {
      echo 'nenhuma migration executada' . PHP_EOL;
    } else {
      echo 'migration executadas: ' . count($migrationExecutadas) . PHP_EOL;
    } 
      break;
}

function criarArquivoMigration($dirSistemaMigrations, $nmMigration) {
  $nmBaseNovoArquivoMigraton = 'M' . date('Ymd') . date('His') . '_' . $nmMigration . '_';
  $nmArquivoMigration = uniqid($nmBaseNovoArquivoMigraton) . '.php';
  $pathArquivoMigration = $dirSistemaMigrations . DIRECTORY_SEPARATOR . $nmArquivoMigration;

  if (file_exists($pathArquivoMigration)) {
    echo 'arquivo de migration já existe' . PHP_EOL;
  }

  $file = fopen($pathArquivoMigration, "w") or die("não foi possível criar o arquivo de migration");
  fwrite($file, '<?php
class '. substr($nmArquivoMigration, 0, -4) . ' {
  public function up() {
    return "create table x;";
  }

  public function down() {
    return "drop table x;";
  }
}
?>');
  fclose($file);
}

?>