# cloud-final-prj
Final project for the Cloud Service class (2019-2020): a personal photos management website.

This project will also make use of SimpleMVC Framework by Enrico Zimuel, which can be found here:

[SimpleMVC by E.Zimuel](https://github.com/ezimuel/SimpleMVC)

**Prerequisites: Software**
- Php 7.3^
- Composer
- Curl enabled for php
- A mongodb (v4.2^) instance running on your local machine and php mongo extension enabled.

**Prerequisites: Azure**
- An existing Blob subscription with a container (with enough permissions to get connection details)
- An exisiting Computer Vision subscription (with enough permissions to get connection details)


**Instructions**
- Activate the mongoDb extension for php from your php.ini file and restart the service

- Enable curl for php (if needed install the required packages on your machine)

- Run the `composer install` command from your terminal pointing to the main folder of this app

- From */config* folder make a copy of *config.template.env* and rename it *config.env*. Then replace the palceholder values with those that suite your environment (please do not change the COOKIES_TO_ENCRYPT parameter).

- Then from your terminal run the command `php dbseed/index.php` to create the db user for the application (further details about this operation can be found in */dbseed/README.md*).

- Start a webserver with */public* folder as the root folder for the application.

