<?php
class M20220213185822_alter_table_usuario_620954ce7cd2b {
public function up() {
  return "ALTER TABLE usuario RENAME COLUMN senha TO password;";
}

public function down() {
  return "ALTER TABLE usuario RENAME COLUMN password TO senha;";
}
}
?>