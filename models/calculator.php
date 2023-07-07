<?php

require_once '../models/db.php';

class Calculator
{
    private $amount;
    private $percent;
    private $time;
    private $date = '01.07.2023';

    function __construct()
    {
        $this->amount = $_POST['amount'];
        $this->percent = $_POST['percent'];
        $this->time = $_POST['time'];
    }

    public function make()
    {
        $this->percent = $this->percent / 100 / 12;
        // определяем ежемесячный платеж
        $month_payment = ($this->percent / (1 - pow(1 + $this->percent, -1 * $this->time)))
            * $this->amount;
        $payment_paid = 0; // оплаченная сумма долга
        $array_payment = []; // массив ежемесячных платежей
        $date = $this->date;
        // заполняем массив ежемесячных платежей
        for ($i = 1; $i <= $this->time; $i++) {
            $month_payment_percent = ($this->amount - $payment_paid) * $this->percent; // по процентам
            $month_payment_main = $month_payment - $month_payment_percent; // по долгу
            $payment_paid += $month_payment_main; // оплаченная сумма по долгу с нарастающим итогом
            $balance = $this->amount - $payment_paid; // остаток по долгу
            $array_payment[$i]['percent'] = round($month_payment_percent, 2);
            $array_payment[$i]['main'] = round($month_payment_main, 2);
            $array_payment[$i]['balance'] = abs(round($balance, 2));
            $array_payment[$i]['date'] = $date;
            // добавляем месяц к дате платежа
            $date = strtotime('+1 MONTH', strtotime($date));
            $date = date('d.m.Y', $date);
        }
        $month_payment = round($month_payment, 2);

        // выводим таблицу платежей (возможно не очень красиво :) )
        echo '<div class="payment-month">Платеж в месяц составит: ' . $month_payment . ' </div>
<table border="2"> <h3>График платежей</h3>
  <thead>
    <tr>
      <th>Номер платежа</th>
      <th>Дата платежа</th>
      <th>Платеж по процентам</th>
      <th>Платеж по долгу</th>
      <th>Остаток по долгу</th>
    </tr>
    </thead>
    <tbody>';
        $i = 1;
        foreach ($array_payment as $month) {
            echo '<tr>';
            echo "<td>{$i}</td>";
            echo "<td>{$month['date']}</td>";
            echo "<td>{$month['percent']}</td>";
            echo "<td>{$month['main']}</td>";
            echo "<td>{$month['balance']}</td>";
            echo '<tr>';
            $i++;
        }
        echo '</tbody>
</table>';
        // записываем в бд результат расчета
        // в зависимости от скорости запроса и нагрузки на сервер можно реализовать выгрузку
        //         результата из БД вместо нового расчета
        $this->insert_data($array_payment);
    }

    private function insert_data($array)
    {
        $a = new DB();
        $db = $a->get_db();
        $token = $this->date . $this->amount . $this->percent . $this->time;
        $data = serialize($array);
        $request = "INSERT INTO `Calculations` (`token`,`data_calc`) VALUES ('{$token}','{$data}')";
        $db->query($request);
    }
}


