<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('packing_dispatches')) {
            return;
        }

        $stream = fopen('php://temp', 'w+');
        if ($stream === false) {
            throw new RuntimeException('Cannot create a packing backup; table was not removed.');
        }
        try {
            DB::table('packing_dispatches')->orderBy('id')->chunkById(100, function ($rows) use ($stream) {
                foreach ($rows as $row) {
                    $line = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
                    if (fwrite($stream, $line) !== strlen($line)) {
                        throw new RuntimeException('Cannot write packing backup; table was not removed.');
                    }
                }
            });
            rewind($stream);
            $path = 'packing-backups/removed-packing-dispatches-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.jsonl';
            if (!Storage::disk('local')->put($path, $stream)) {
                throw new RuntimeException('Cannot save packing backup; table was not removed.');
            }
        } finally {
            fclose($stream);
        }

        Schema::drop('packing_dispatches');
    }

    public function down(): void
    {
        throw new RuntimeException('Packing history removal cannot be rolled back automatically. Original rows are archived in storage/app/private/packing-backups.');
    }
};
