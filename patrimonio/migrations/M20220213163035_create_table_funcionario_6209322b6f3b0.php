<?php
class M20220213163035_create_table_funcionario_6209322b6f3b0 {
  public function up() {
    return "
create table funcionario 
(
  id integer primary key,
  nome text
);";
  }

  public function down() {
    return "drop table funcionario;";
  }
}
?>