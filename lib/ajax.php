<?php
session_start();
require_once(__DIR__ . "/Controller/Medicine.php");
require_once(__DIR__ . "/functions.php");
require_once(__DIR__ . "/../config/config.php");

$medicine = new \MyApp\Medicine();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $res = $medicine->post();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    } catch (Exception $e) {
        header($_SERVER['SERVER_PROTOCOL'] . '500 Interval Server Error', true, 500);
        echo $e->getMessage();
        exit;
    }
}