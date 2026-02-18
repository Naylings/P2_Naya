<?php

namespace App\Imports;

use App\Models\Warga;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class WargaImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnFailure
{
    use Importable;

    public function model(array $row)
    {
        return new Warga([
            'nik'      => $row['nik'],
            'name'     => $row['name'],
            'no_kk'    => $row['no_kk'],
            'gender'   => $row['gender'],
            'address'  => $row['address'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nik' => 'required|digits:16|unique:wargas,nik',
            '*.name' => 'required',
            '*.no_kk' => 'required',
        ];
    }
}
