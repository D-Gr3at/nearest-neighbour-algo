<?php

use Database\Database;

class Lga{


    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnectionInstance();
    }

    public function getLgas($data)
    {
        $id = $data['id'];
        $query = 'SELECT * FROM lga WHERE state = '.$id;
        $result = mysqli_query($this->conn, $query);
        $lgaList = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $query = 'SELECT postal_code FROM state WHERE id = '.$id;
        $r = mysqli_query($this->conn, $query);
        $postalCode = mysqli_fetch_assoc($r);
        if (!$result){
            echo json_encode(array('data' => [], 'responseCode' => 400));
        }else{
            echo json_encode(array(
                'data' => $lgaList,
                'responseCode' => 200,
                'postalCode' => $postalCode['postal_code']));
        }
    }

}