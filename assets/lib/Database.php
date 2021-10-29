<?php

class Database{

    private $username;
    private $password;
    private $hostName;
    private $databaseName;

    public function getConnectionInstance(){
        return new mysqli($this->hostName, $this->username, $this->password, $this->databaseName);
    }


}