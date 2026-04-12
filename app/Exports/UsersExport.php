<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected Request $request
    ) {}

    public function collection(): Collection
    {
        return User::query()
            ->filter($this->request->get('filters'))
            ->search($this->request->get('search'))
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return User::excelHeadings();
    }

    public function map($user): array
    {
        return User::excelMapFromModel($user);
    }
}
