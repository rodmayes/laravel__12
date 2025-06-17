<?php

namespace App\Imports;

use App\Models\LotteryResults as Lottery;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SheetLotteryImport implements ToCollection, WithBatchInserts, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows->slice(1) as $row) { // Saltar cabecera
            $row = $row->values();

            if ($row && $row->count() > 1) {
                $dateAt = gmdate('Y-m-d', ((int)$row[0] - 25569) * 86400);
                $numbers = json_encode(array_slice($row->toArray(), 1, 7));

                Lottery::updateOrCreate(
                    ['date_at' => $dateAt],      // Condición para encontrar existente
                    ['numbers' => $numbers]      // Campos a actualizar o crear
                );
            }
        }
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
