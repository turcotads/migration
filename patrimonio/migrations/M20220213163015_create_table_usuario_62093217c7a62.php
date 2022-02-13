<?php
class M20220213163015_create_table_usuario_62093217c7a62 {
  public function up() {
    return "
create table usuario 
(
  id integer primary key,
  login text,
  senha text
);";
  }

  public function down() {
    return "drop table usuario;";
  }
}
?>