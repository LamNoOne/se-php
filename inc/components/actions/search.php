<?php

require_once dirname(dirname(__DIR__)) . "/init.php";
require_once dirname(dirname(__DIR__)) . "/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        $conn = require_once dirname(dirname(__DIR__)) . "/db.php";
        $search = $_POST['search'];
        $queryProduct = [
            'fields' => '*',
            'filters' => [
                createFilter('name', "%$search%", "LIKE")
            ],
            'limit' => isset($_POST['limit']) ? $_POST['limit'] : 20,
            'offset' => 0,
        ];
    
        $searchResponse = Product::getAllProductsByCondition($conn, $queryProduct)['data'];
        return throwStatusMessage($searchResponse);
    }
}
