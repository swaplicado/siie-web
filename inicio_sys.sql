# Configurar nombres de bases de datos en App\Database\Config.php******************
# Configurar PHP max_execution_time=300 y memory_limit=512

DROP DATABASE siie_saporis;
DROP DATABASE siie_gs;
DROP DATABASE ssystem;

CREATE SCHEMA `ssystem` ;
CREATE SCHEMA `siie_saporis` ;
CREATE SCHEMA `siie_gs` ;


# Correr migraciones

# Realizar configuraciones
# Configurar partner_id en la tabla de configuración de cada empresa con el id correspondiente de la tabla erpu_partners
# Configurar nombre de la base de datos de importación en la tabla de configuración de cada empresa

/*
*	php artisan db:seed --class=UserSeeder
*	php artisan db:seed --class=PartnerSeeder
*
*/

UPDATE `ssystem`.`users` SET `username`='reader', `email`='reader@mail.com' WHERE `id`='3';
UPDATE `ssystem`.`users` SET `username`='author', `email`='author@mail.com' WHERE `id`='4';
UPDATE `ssystem`.`users` SET `username`='editor', `email`='editor@mail.com' WHERE `id`='5';
UPDATE `ssystem`.`users` SET `username`='manager', `email`='manager@mail.com', user_type_id = '2' WHERE `id`='6';
