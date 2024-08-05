<?php

namespace NhatHoa\Framework\Facades;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Excel
{
    public static function generate(array $data = [], string $file_name = null, array $headers = null, array $autoSizeColumns = [],string $styleHeader = null)
    {
        if(!$file_name){
           $file_name = "data"; 
        }  
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if($headers){
            $sheet->fromArray($headers, null, 'A1');
        }
        $sheet->fromArray($data, null, 'A2', true);
        foreach($autoSizeColumns as $item){
            $sheet->getColumnDimension($item)->setAutoSize(true);
        }
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => Color::COLOR_BLACK],
            ],
        ];
        $sheet->getStyle($styleHeader)->applyFromArray($headerStyleArray);
        $sheet->freezePane('A2');
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('C2:C' . $highestRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name .'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}