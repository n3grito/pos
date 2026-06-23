<?php

namespace App\Exports;

use App\Models\Service;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ServicesExport
{
    public function export(string $path = null)
    {
        $services = Service::with('category')->orderBy('name')->get();

        $writer = new Writer();
        $filePath = $path ?? storage_path('app/exports/servicios_' . date('Y-m-d_His') . '.xlsx');

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer->openToFile($filePath);

        $headerStyle = new Style(fontBold: true);

        $writer->addRow(Row::fromValuesWithStyle([
            'Nombre', 'Descripción', 'Precio Venta', 'IVA (%)', 'Categoría', 'Activo',
        ], $headerStyle));

        foreach ($services as $service) {
            $writer->addRow(Row::fromValues([
                $service->name,
                $service->description,
                $service->selling_price,
                $service->tax_percentage,
                $service->category?->name ?? '',
                $service->is_active ? 'Sí' : 'No',
            ]));
        }

        $writer->close();

        return $filePath;
    }
}
