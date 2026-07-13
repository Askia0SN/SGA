<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;
use Illuminate\Support\Facades\DB;

$programme = Programme::with('typesDocuments')->find(1);
if (! $programme) {
    echo "NOT_FOUND\n";
    exit(0);
}
echo "PROGRAMME: {$programme->nom}\n";
echo "TYPES_COUNT: " . $programme->typesDocuments->count() . "\n";
foreach ($programme->typesDocuments as $type) {
    echo "{$type->id} {$type->nom} obligatoire=" . ($type->pivot->obligatoire ? '1' : '0') . "\n";
}
$docCount = DB::table('programme_type_document')->where('programme_id', 1)->count();
echo "DB_LINKED: {$docCount}\n";
