<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Service;
use OpenSpout\Reader\XLSX\Reader;

class ServicesImport
{
    public function import(string $filePath): array
    {
        $reader = new Reader();
        $reader->open($filePath);

        $imported = 0;
        $errors = [];

        $categories = Category::pluck('id', 'name');

        foreach ($reader->getSheetIterator() as $sheet) {
            $rowIndex = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex++;
                if ($rowIndex === 1) continue;

                $cells = $row->toArray();
                $name = $cells[0] ?? null;

                if (empty($name)) continue;

                $description = $cells[1] ?? null;
                $sellingPrice = isset($cells[2]) ? floatval($cells[2]) : 0;
                $taxPercentage = isset($cells[3]) ? floatval($cells[3]) : 0;
                $categoryName = $cells[4] ?? null;
                $isActive = isset($cells[5]) ? (strtolower($cells[5]) === 'sí' || strtolower($cells[5]) === 'si' || strtolower($cells[5]) === 'yes') : true;

                $categoryId = $categoryName ? ($categories[$categoryName] ?? null) : null;

                try {
                    Service::create([
                        'name' => $name,
                        'description' => $description,
                        'selling_price' => $sellingPrice,
                        'tax_percentage' => $taxPercentage,
                        'category_id' => $categoryId,
                        'is_active' => $isActive,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Fila {$rowIndex}: {$e->getMessage()}";
                }
            }
        }

        $reader->close();

        return ['imported' => $imported, 'errors' => $errors];
    }
}
