<?php
require_once '../models/calculator.php';

if ($_POST['action'] == 'calculation' && $_POST['amount'] && $_POST['amount']
    && $_POST['percent'] && $_POST['time']) {

    $a = new Calculator();
    $a->make();
}