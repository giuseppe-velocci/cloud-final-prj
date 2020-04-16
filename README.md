# cloud-final-prj
Progetto finale del corso di Cloud Services (2019-2020) che prevede la realizzazione di un sito web per la gestione di un catalogo personale di foto.

This project will also make use of SimpleMVC Framework from Enrico Zimuel, which can be found here:

[SimpleMVC by E.Zimuel](https://github.com/ezimuel/SimpleMVC)

**Prerequisites**
- Php 7.3^
- Composer
- A mongodb (v4.2^) instance running on your local machine and php mongo extension enabled.


**Instructions**
- Activate the mongoDb extension for php from your php.ini file and restart the service

- Run the `composer install` command from your terminal pointing to the main folder of this app

- From */config* folder make a copy of *config.template.env* and rename it *config.env*. Then replace the palceholder values with those that suite your environment (please do not change the COOKIES_TO_ENCRYPT parameter).

- Then from your terminal run the command `php dbseed/index.php` to instance mock data and create the db user for the application (further details about this operation can be found in */dbseed/README.md*).

- Start a webserver with */public* folder as the root folder for the application.

