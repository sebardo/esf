<?php

pake_desc('Envia recordatorio de pago para pagos fraccionados');
pake_task('send-reminder-second-payment');

function run_send_reminder_second_payment($task, $args)
{
    $date = null;
    if (isset($args[0])) {
        $date = $args[0];
    }

    $host = "http://www.englishsummerfun.com";
    // $host = "http://esf.localhost/summer_fun_dev.php";

    if ($date) {
        echo file_get_contents("$host/payment/tpv/reminder/9$03sPsD21zSdesP/$date");
    }
    else {
        echo file_get_contents("$host/payment/tpv/reminder/9$03sPsD21zSdesP");
    }
}