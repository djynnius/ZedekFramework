The config files are located within the /zedek/config/ folder. The config files take the form of json. They are in fact JSON files.

==General Config==

The config file takes the form below

    {
        "version": "2.0"
    }

new config values may be added using the ZConfig object:

    $config = new ZConfig();
    $config->set("theme", "new_theme");

The ZConfig object has 2 methods: set and get. set takes 2 arguments - key and value pair while the get takes only one argument which is the key and returns the value.

    echo $config->get("theme");

will return "new_theme".


==Database Config==

This is a json file that shuld be edited directly. Unlike the general config it is not manipulated with the ZConfig object. The settings are:

    {
        "engine": "sqlite",
        "host": "null",
        "user": "null",
        "pass": "null",
        "db": "/media/ubuntu/zedek/database/zedek.db", 
        "prefix": "ztest"
    }

 #engine represents which Relational Database Management System in use and may be sqlite, mysql or any other option allowed by PDO.

#host represents the host and does not have to be set for sqlite

#user represents database username and does not have to be set for sqlite

#pass represents password and does not have to be set for sqlite 

#db represents database name. In the case of sqlite it is the path to the sqlite database

#prefix represents any chosen prefix for database table names - akin to a namespace

    {
        "engine": "mysql",
        "host": "localhost",
        "user": "root",
        "pass": "gaj676_af2)",
        "db": "newsite", 
        "prefix": "ztest"
    }
