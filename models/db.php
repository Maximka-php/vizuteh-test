<?php

require_once '../config-db.php';

class DB
{
    function get_db()
    {
        return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

}