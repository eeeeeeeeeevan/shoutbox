<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD']!=='POST' || $_POST['action'] !== 'get_challenge') {
    http_response_code(400);
    die(json_encode(['error'=>'fuck']));
}
//
//$time = time(NULL);
//if ()
// set legacycall to this 
if (!isset($_SESSION['legacycall'])) {
    $_SESSION['legacycall'] = bin2hex(random_bytes(32));
}


if (!isset($_SESSION['challenges'])) { // how
    $_SESSION['challenges'] = [];
}

$_SESSION['challenges'] = array_filter($_SESSION['challenges'], function($challenge) {
    // shit impl because time() but whatever
    return time()-$challenge['timestamp'] < 300; 
});

$chalid = bin2hex(random_bytes(16));
$sekrit = bin2hex(random_bytes(32));
/* $difficulty = rand(3,4); */
$difficulty = 4;
$_SESSION['challenges'][$chalid] = [
    'secret' => $sekrit,
    'timestamp' => time(),
    'used' => false
];

// {id: ..., challenge: ..., difficulty: ...}
echo json_encode([ 'id'=>$chalid, 'challenge'=>hash('sha256', $sekrit),  'difficulty'=>$difficulty ]);
?>
