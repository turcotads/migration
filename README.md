# migration
Ideia inicial sobre migration

## Utilizar
Para criar uma migration, na pasta raiz execute:
* php migration.php patrimonio criar {NOME_DA_MIGRATION}
Exemplo:
```shell
php migration.php patrimonio criar create_table_x
```
Para rodar a migração up|down, na pasta raiz execute:
* php migration.php patrimonio migrar {up|down}
Exemplo:
```shell
php migration.php patrimonio migrar up
```