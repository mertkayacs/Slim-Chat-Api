Created by : Mert KAYA.

Last Change Date : 22-December-2021 00:40

In order to run the project : 
    
    -Be sure to have PHP,SLIM and SQLITE installed in your computer.
    
    -Check php.ini file for slim,sqlite and pdo settings, uncomment them by deleting ";" . 
    
    -Make sure you have created the database tables correctly. SQL queries for this is given in the end of this README.
    
    -Run on php virtual server by the following command on slim-chat-app folder.
    php -S localhost:8000

    -Make sure to install and run composer by following command.
    composer require slim/slim:3.*
    composer update

    -Be sure to follow parameter rules for the receiving and sending message apis.

    -If you have any more questions watch the demo video which is sent to you with this project in a .zip file



Q/A : 
    Q1 : Why isn't there any Auth for Api.
    A1 : This was not asked in the pdf file. Bearer token can be added.

    Q2 : Why isn't there user register, login pages .
    A2 : This was not asked in the pdf file but can be added easily into database.
    A2 cont': If password added it must be stored as hashed in database :D 

    Q3 : Why isn't there .html .phtml pages for views .
    A3 : Simply it was not asked in pdf :) 
    



To create users table run following SQL query.

    CREATE TABLE "users" (
        "userid"	INTEGER,
        "username"	TEXT,
        PRIMARY KEY("userid","username")
    );



To create messages table run following SQL query.

    CREATE TABLE "messages" (
        "messageid"	INTEGER,
        "senderUserid"	INTEGER,
        "receiverUserid"	INTEGER,
        "message"	TEXT,
        PRIMARY KEY("messageid")
    );
