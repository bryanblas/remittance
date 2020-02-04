<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Services\Outlet\OutletFacade as Outlet;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Excel implements FromArray, WithHeadings
{
    use Exportable;
    private $data;
    private $headers;

    public function setData($data, $headers)
    {
        $this->data = $data;
        $this->headers = $headers;
        return $this;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings() : array
    {
        return $this->headers;
    }
}
