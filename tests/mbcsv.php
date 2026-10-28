<?php
// Run: php tests/mbcsv.php  (needs Composer's autoload from a project that
// requires pear/pear-core-minimal; pass its vendor/autoload.php as $argv[1])
require $argv[1];
require __DIR__.'/../src/File/MBCSV.php';

function check($cond, $msg)
{
    if (!$cond) {
        fwrite(STDERR, "FAIL: $msg\n");
        exit(1);
    }
}

$file = tempnam(sys_get_temp_dir(), 'mbcsv-test-');
$rows = array(
    array('氏名', '備考', '点数'),
    array('山田 太郎', "引用符\"と,カンマ", '80'),
    array('佐藤 花子', 'ﾊﾝｶｸ①㈱', '95'),
);
$out = '';
foreach ($rows as $r) {
    $cells = array();
    foreach ($r as $c) {
        $cells[] = '"'.str_replace('"', '""', mb_convert_encoding($c, 'SJIS-win', 'UTF-8')).'"';
    }
    $out .= implode(',', $cells)."\r\n";
}
file_put_contents($file, $out);

$o = new File_MBCSV();
$o->setOutputEncoding('UTF-8');
check($o->open($file) === true, 'open');
$all = $o->fetchAll(1);
$o->close();
check(count($all) === 2, 'skip header: '.count($all));
check($all[0] === $rows[1], 'row 1 round trip');
check($all[1] === $rows[2], 'row 2 round trip (half-width kana, circled digit)');

$o = new File_MBCSV();
check(PEAR::isError($o->open('/nonexistent.csv')), 'missing file is a PEAR_Error');

unlink($file);
echo "PASS: SJIS-win round trip, header skip, missing file\n";
