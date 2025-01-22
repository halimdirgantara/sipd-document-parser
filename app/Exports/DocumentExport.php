<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DocumentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $entities;

    public function __construct($entities)
    {
        $this->entities = $entities;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->entities);
    }

    public function headings(): array
    {
        return [
            'Affair',
            'Sector',
            'Organization',
            'Sub Organization',
            'Program',
            'Activity',
            'Sub Activity',
            'Account Code',
            'Item Name',
            'Specification',
            'Quantity',
            'Unit',
            'Price',
            'Tax',
            'Total'
        ];
    }

    public function map($entity): array
    {
        $rows = [];
        
        foreach ($entity->programs as $program) {
            foreach ($program->activities as $activity) {
                foreach ($activity->subActivities as $subActivity) {
                    foreach ($subActivity->items as $item) {
                        $rows[] = [
                            $entity->affair,
                            $entity->sector,
                            $entity->organization,
                            $entity->sub_organization,
                            $program->name,
                            $activity->name,
                            $subActivity->name,
                            $item->account_code,
                            $item->name,
                            $item->specification,
                            $item->quantity,
                            $item->unit,
                            $item->price,
                            $item->tax,
                            $item->total
                        ];
                    }
                }
            }
        }

        return $rows;
    }
}
