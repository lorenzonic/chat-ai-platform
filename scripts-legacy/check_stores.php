<?php

use Illuminate\Support\Facades\DB;

// Check stores table structure
$columns = DB::select('DESCRIBE stores');
echo "Stores table columns:\n";
foreach ($columns as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}
