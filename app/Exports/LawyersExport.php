<?php

namespace App\Exports;

use App\Models\Lawyer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LawyersExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithDrawings
{
    public function collection()
    {
        $lawyers = Lawyer::select('nombre', 'apellido', 'correo', 'telefono', 'especialidad')->get();

        $data = [];
        foreach ($lawyers as $lawyer) {
            $data[] = [
                'Nombre' => $lawyer->nombre,
                'Apellido' => $lawyer->apellido,
                'Correo Electrónico' => $lawyer->correo,
                'Teléfono' => $lawyer->telefono,
                'Especialidad' => $lawyer->especialidad,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['Nombre', 'Apellido', 'Correo Electrónico', 'Teléfono', 'Especialidad'];
    }


    public function title(): string
    {
        return 'Lista de Abogados';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '3BB54A'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin'],
            ],
        ]);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('LogoSENA');
        $drawing->setDescription('Logo del SENA');
        $drawing->setPath(public_path('img/LogoInsti.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('F1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return [$drawing];
    }
}
