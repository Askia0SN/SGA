<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$typesCount = DB::table('types_documents')->count();
$linksCount = DB::table('programme_type_document')->count();
$progLinks = DB::table('programme_type_document')->where('programme_id', 1)->count();

echo "TYPE_DOCS: $typesCount\n";
echo "PROGRAMME_LINKS: $linksCount\n";
echo "PROGRAMME_1_LINKS: $progLinks\n";
