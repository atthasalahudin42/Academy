<?php
$c = new mysqli('localhost', 'root', 'root123', 'academy');
if ($c->connect_error) {
    echo 'Connection failed: ' . $c->connect_error;
} else {
    echo 'Connection successful';
    $r = $c->query('SHOW VARIABLES LIKE "innodb_force_recovery"');
    if ($r) {
        $v = $r->fetch_assoc();
        echo ' | innodb_force_recovery = ' . $v['Value'];
    } else {
        echo ' | Could not fetch innodb_force_recovery';
    }
}
