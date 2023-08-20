# maxihabana
Tienda Virtual MaxiHabana


Requerimientos de Instalación
- PHP 7.0 o superior
- MySQL 5.0 o superior

Mi Recomendación
- PHP 7.4 o superior
- MySQL 5.7 o superior O MariaDB 10.4 o superior.
- Activar el módulo mod_rewrite de Apache.

Guía de instalación
- Copiar toda la carpeta del proyecto a la raíz del servidor web.
- Importar el backup de la base de datos. Usando el siguiente comando: mysql -u [user] -h [ip_servidor] -v -p [nombte_base_datos] < [ruta_al_backup]
- Ejemplo: mysql -u app -h 127.0.0.1 -v -p maxihabana < maxihabana.backup
- Cambiar los valores de configuración en el archivo wp-config.php.
- Los valores a cambiar son: DB_NAME, DB_USER, DB_PASSWORD, DB_HOST. Los cuales estarán correspondencia con los del servidor de base de datos.