# portal-videos

## 1. Instalaciones previas

Primero es necesario instalar el servidor web Apache 2 con PHP 5 y SQLite 3. 

`sudo apt-get install apache2 php5 libapache2-mod-php5 php5-sqlite sqlite3`

Aparte hay que instalar las librerías curl y php5-mcrypt, necesaria para poder encriptar la información. 

`sudo apt-get install php5-curl php5-mcrypt`

Para configurar php5-mcrypt, hay que realizar lo siguiente:

`sudo php5enmod mcrypt
sudo service apache2 restart`

Para la instalación de la librería ffmpeg, hay que seguir los siguientes pasos:

`sudo apt-add-repository ppa:mc3man/trusty-media`

NOTA: si no permite realizar la acción de añadir un repositorio, instalar lo siguiente:
`sudo apt-get install python-software-properties`

`sudo apt-get update
sudo apt-get dist-upgrade
sudo apt-get install ffmpeg`

Por último es necesario instalar git para tener acceso al control de versiones.

`sudo apt-get install git`

## 2. Configuración de PHP

Una vez instalado todo lo necesario, necesitaremos modificar el archivo php.ini para poder aumentar el tamaño de archivos que se permiten enviar por POST en un formulario. Esto es necesario para poder subir desde la herramienta archivos de vídeo, ya que el tamaño por defecto es de 2MB.

Para ello, desde el terminal abrimos el archivo php.ini y modificamos las líneas donde aparecen post_max_size y upload_max_filesize por lo que se indica:

`sudo gedit /etc/php5/apache2/php.ini`

`post_max_size = 100M`

`upload_max_filesize = 100M`

Una vez hecho, hay que reiniciar el servidor web.

`sudo service apache2 restart`

## 3. Instalación de la herramienta

Una vez instalado todo, lo siguiente es proceder con la instalación de la herramienta. Para ello, lo primero que hay que hacer es posicionarse en la carpeta del servidor web desde la que se publica su contenido.

`cd /var/www/html/`

Dentro de esta carpeta, iniciar git:

`git init`

Después, clonamos el repositorio de git de la herramienta:

`git clone https://github.com/laurabgpfc/portal-videos/`

Con esto tendremos una copia de la herramienta en la ubicación desde la que hayamos ejecutado esta instrucción.

Si la carpeta donde lo hemos instalado no es el root del servidor web, necesitamos modificar el archivo “config.php”, concretamente la variable “_PORTALROOT”, añadiendo la ubicación correspondiente:

Valor original
```php
define('_PORTALROOT', 'http://'.$_SERVER['HTTP_HOST'].'/portal-videos/');
```

Valor correcto (si el root es, por ejemplo, /lblazquez/)
```php
define('_PORTALROOT', 'http://'.$_SERVER['HTTP_HOST'].'/lblazquez/portal-videos/');
```