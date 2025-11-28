<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnitsTemplateExport implements FromArray, WithHeadings
{
    protected $data;
    protected $headings;

    public function __construct($headings, $data)
    {
        $this->headings = $headings;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }
}
