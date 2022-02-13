<?php
class M20220213174942_create_table_tipo_bem_620944b6e5c80 {
  public function up() {
    return "
create table tipo_bem (
  id_tipo_bem integer primary key,
  ds_tipo_bem text
);";
  }

  public function down() {
    return "drop table tipo_bem;";
  }
}
?>