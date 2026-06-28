<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\XLSX\Reader;

class ProductsImport
{
    public function import(string $filePath): array
    {
        $reader = new Reader();
        $reader->open($filePath);

        $imported = 0;
        $errors = [];

        $categories = Category::pluck('id', 'name');
        $branches = Branch::pluck('id', 'name');

        DB::beginTransaction();

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $rowIndex = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $rowIndex++;
                    if ($rowIndex === 1) continue;

                    $cells = $row->toArray();
                    $name = $cells[0] ?? null;

                    if (empty($name)) continue;

                    $sku = $cells[1] ?? null;
                    $barcode = $cells[2] ?? null;
                    $description = $cells[3] ?? null;
                    $categoryName = $cells[4] ?? null;
                    $branchName = $cells[5] ?? null;
                    $costPrice = isset($cells[6]) ? floatval($cells[6]) : null;
                    $sellingPrice = isset($cells[7]) ? floatval($cells[7]) : 0;
                    $taxPercentage = isset($cells[8]) ? floatval($cells[8]) : 0;
                    $stock = isset($cells[9]) ? intval($cells[9]) : 0;
                    $minStock = isset($cells[10]) ? intval($cells[10]) : 0;
                    $isActive = isset($cells[11]) ? (strtolower($cells[11]) === 'sí' || strtolower($cells[11]) === 'si' || strtolower($cells[11]) === 'yes') : true;
                    $availableForSale = isset($cells[12]) ? (strtolower($cells[12]) === 'sí' || strtolower($cells[12]) === 'si' || strtolower($cells[12]) === 'yes') : true;

                    $categoryId = $categoryName ? ($categories[$categoryName] ?? null) : null;
                    $branchId = $branchName ? ($branches[$branchName] ?? null) : null;

                    try {
                        Product::create([
                            'name' => $name,
                            'sku' => $sku,
                            'barcode' => $barcode,
                            'description' => $description,
                            'category_id' => $categoryId,
                            'branch_id' => $branchId,
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                            'tax_percentage' => $taxPercentage,
                            'stock' => $stock,
                            'min_stock' => $minStock,
                            'is_active' => $isActive,
                            'available_for_sale' => $availableForSale,
                        ]);
                        $imported++;
                    } catch (\Exception $e) {
                        $errors[] = "Fila {$rowIndex}: {$e->getMessage()}";
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = "Error general: {$e->getMessage()}";
        }

        $reader->close();

        return ['imported' => $imported, 'errors' => $errors];
    }
}
