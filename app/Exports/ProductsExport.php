<?php

namespace App\Exports;

use App\Models\Product;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ProductsExport
{
    public function export(string $path = null)
    {
        $products = Product::with(['category', 'branch'])->orderBy('name')->get();

        $writer = new Writer();
        $filePath = $path ?? storage_path('app/exports/productos_' . date('Y-m-d_His') . '.xlsx');

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer->openToFile($filePath);

        $headerStyle = new Style(fontBold: true);

        $writer->addRow(Row::fromValuesWithStyle([
            'Nombre', 'SKU', 'Código de Barras', 'Descripción',
            'Categoría', 'Sucursal', 'Precio Costo', 'Precio Venta',
            'IVA (%)', 'Stock', 'Stock Mínimo', 'Activo',
            'Disponible para Venta',
        ], $headerStyle));

        foreach ($products as $product) {
            $writer->addRow(Row::fromValues([
                $product->name,
                $product->sku,
                $product->barcode,
                $product->description,
                $product->category?->name ?? '',
                $product->branch?->name ?? '',
                $product->cost_price ?? 0,
                $product->selling_price,
                $product->tax_percentage,
                $product->stock ?? 0,
                $product->min_stock ?? 0,
                $product->is_active ? 'Sí' : 'No',
                $product->available_for_sale ? 'Sí' : 'No',
            ]));
        }

        $writer->close();

        return $filePath;
    }
}
