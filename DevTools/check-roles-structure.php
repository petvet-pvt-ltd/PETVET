<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();
$cols = $pdo->query('DESCRIBE roles')->fetchAll();
foreach($cols as $c) {
    echo "{$c['Field']} ({$c['Type']})\n";
}
?>
