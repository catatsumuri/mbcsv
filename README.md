# catatsumuri/mbcsv

Reads a Shift_JIS (`SJIS-win`) CSV file and returns the rows in the encoding
you choose. The file is converted to UTF-8 in a temporary file so that
`fgetcsv()` can parse multibyte fields safely.

Shift_JIS の CSV を読み、指定した文字コードで行を返す小さなクラスです。
`File_MBCSV` というクラス名とファイルパスは、元のアプリとの互換のために
そのまま残しています。

```php
require_once 'File/MBCSV.php'; // or rely on Composer's autoload

$csv = new File_MBCSV();
$csv->setOutputEncoding('UTF-8');
$result = $csv->open('/path/to/file.csv');
if (PEAR::isError($result)) {
    // e.g. "CSV File not exists."
}
$rows = $csv->fetchAll(1); // skip the header row
$csv->close();
```

Other setters: `setDelimiter()`, `setEnclosure()`, `setBufferSize()`,
`setTempFile()`. The input encoding defaults to `SJIS-win`
(property `_inputEncoding`); the output encoding defaults to the input
encoding when `setOutputEncoding()` is not called.

Errors are returned as `PEAR_Error` (`pear/pear-core-minimal`), not thrown.

## Install

```sh
composer require catatsumuri/mbcsv
```

Requires `ext-mbstring`. `composer.json` also registers `src/` in the include
path so that `require_once 'File/MBCSV.php'` keeps working.

## Notes

`setlocale(LC_ALL, 'ja_JP.UTF8')` runs when the file is loaded, as in the
original code.

## License

MIT
