<?php
session_start();

if (!isset($_POST['challenge_id']) || !isset($_POST['pow_solution'])) {
    die('no proof of work bro.');
}
//if (!isset($_POST('challid'))) 
//{
//    die("fuck off");
//}


$challid = $_POST['challenge_id'];
$solution = $_POST['pow_solution'];

if (!preg_match('/^[a-f0-9]{32}$/', $challid)) {
    die('boi wgaht teh fuck');
}

if (!isset($_SESSION['challenges'][$challid])) {
    die('not found or expired');
}

$challenge = $_SESSION['challenges'][$challid];

if ($challenge['used']) {
    die('ncie fucking try pal');
}

if (time() - $challenge['timestamp'] > 300) {
    unset($_SESSION['challenges'][$challid]);
    die('expired');
}

$secret = $challenge['secret'];
$pubchal = hash('sha256', $secret);
$hash = hash('sha256', $pubchal . $solution);
$difficulty = 4;
$target = str_repeat('0', $difficulty);
// $f = fopen("wtflol", "wb");
// fprintf($f, "%d", $target);
// fclose(f);
// 
if (!str_starts_with($hash, $target)) {
    die('invalid compute');
}

$_SESSION['challenges'][$challid]['used'] = true;

$postName = $_POST["name"] ?? '';
$postText = $_POST["post"] ?? '';

if (empty(trim($postName)) || empty(trim($postText))) {
    header("Location: .");
    exit();
}

function trunc($text, $maxWords = 100) {
    $words = explode(' ', $text);
    if (count($words) > $maxWords) {
        $words = array_slice($words, 0, $maxWords);
        return implode(' ', $words) . '...';
    }
    return $text;
}

// iiwiw dont question me this is made in a day
$postName = htmlspecialchars(trunc(trim($postName)));
$postText = htmlspecialchars(trunc(trim($postText)));

$postnum = file_get_contents("incrementer");
if (!$postnum) {$postnum = 1;}


//*CHANGEME if needed 
$template = <<<HTML
<div class="post-container depth-0" data-post-id="<_POSTNUM_>">
    <div class="p-4 border transition-colors mb-4">
        <div class="flex items-baseline justify-between mb-2">
            <div class="flex items-baseline gap-3">
                <span class="font-medium"><_POSTNAME_></span>
                <span class="text-xs">No. <_POSTNUM_></span>
            </div>
            <time class="text-xs"><_POSTDATE_></time>
        </div>
        <p class="text-sm break-words" style="<_POSTSTYLE_>"><_POSTTEXT_></p>
    </div>
</div>
HTML;

// fallback
if (!file_exists("posts.html")) {
    file_put_contents("posts.html", $template);
}

$postHTML = str_replace("<_POSTNAME_>", $postName, $template);
$postHTML = str_replace("<_POSTTEXT_>", $postText, $postHTML);
$postHTML = str_replace("<_POSTNUM_>", $postnum, $postHTML);
$postHTML = str_replace("<_POSTDATE_>", date("Y/m/d g:i:s"), $postHTML);

if(preg_match("/^&gt;/", $postText)) {
    $postHTML = str_replace("style=\"<_POSTSTYLE_>\"", "style=\"color: #22c55e;\"", $postHTML);
} else {
    $postHTML = str_replace("style=\"<_POSTSTYLE_>\"", "", $postHTML);
}

file_put_contents("posts.html", $postHTML . file_get_contents("posts.html"));
file_put_contents("incrementer", $postnum+1);

header("Location: .");
?>
