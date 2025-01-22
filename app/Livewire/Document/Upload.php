<?php

namespace App\Livewire\Document;

use App\Models\Activity;
use App\Models\Entities;
use App\Models\Item;
use App\Models\Program;
use App\Models\SubActivity;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser;

class Upload extends Component
{
    use WithFileUploads;

    #[Validate([
        'documents' => 'required|array',
        'documents.*' => 'required|mimes:pdf|max:10240'
    ])]
    public $documents = [];

    public $processing = false;
    public $success = false;
    public $error = null;
    public $processedCount = 0;
    public $totalFiles = 0;

    public function save()
    {
        try {
            $this->processing = true;
            $this->error = null;
            $this->processedCount = 0;
            $this->totalFiles = count($this->documents);

            foreach ($this->documents as $document) {
                $path = $document->storeAs('temp', $document->getClientOriginalName());

                if (!$path) {
                    throw new \Exception('Failed to store file: ' . $document->getClientOriginalName());
                }

                $parser = new Parser();
                $pdf = $parser->parseFile(storage_path('app/private/' . $path));
                $text = $pdf->getText();

                if ($this->parseAndStore($text)) {
                    $this->processedCount++;
                }
            }

            $this->success = true;

        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->processing = false;
            $this->documents = [];
        }
    }

    protected function parseAndStore($text)
    {
        // Extract entity information
        preg_match('/Urusan Pemerintahan\s*:\s*(\d+)\s*(.+?)\n/s', $text, $affairMatches);
        preg_match('/Bidang Urusan\s*:\s*[\d.]+\s*(.+?)\n/s', $text, $sectorMatches);
        preg_match('/Unit Organisasi\s*:\s*[\d.]+\s*(.+?)\n/s', $text, $orgMatches);
        preg_match('/Sub Unit Organisasi\s*:\s*(.+?)\n/s', $text, $subOrgMatches);

        // Create or find entity
        $entity = Entities::firstOrCreate([
            'affair' => $affairMatches[2] ?? '',
            'sector' => $sectorMatches[1] ?? '',
            'organization' => $orgMatches[1] ?? '',
            'sub_organization' => $subOrgMatches[1] ?? '-',
        ]);

        // Extract program information
        preg_match('/Program\s*:\s*([\d.]+)\s*(.+?)\n/s', $text, $programMatches);

        // Create or find program
        $program = Program::firstOrCreate([
            'entity_id' => $entity->id,
            'name' => $programMatches[2] ?? '',
        ]);

        // Extract activity information
        preg_match('/Kegiatan\s*:\s*([\d.]+)\s*(.+?)\n/s', $text, $activityMatches);

        // Create or find activity
        $activity = Activity::firstOrCreate([
            'program_id' => $program->id,
            'name' => $activityMatches[2] ?? '',
        ]);

        // Extract sub-activity information
        preg_match('/Sub Kegiatan\s*:\s*([\d.]+)\s*(.+?)\n/s', $text, $subActivityMatches);
        preg_match('/Sumber Pendanaan\s*:\s*(.+?)\n/s', $text, $fundingMatches);
        preg_match('/Lokasi\s*:\s*(.+?)\n/s', $text, $locationMatches);
        preg_match('/Waktu Pelaksanaan\s*:\s*(.+?)\n/s', $text, $timeMatches);
        preg_match('/Kelompok Sasaran\s*:\s*(.+?)\n/s', $text, $targetMatches);
        preg_match('/Alokasi 2024\s*:\s*Rp\.\s*([\d,.]+)/s', $text, $prevYearMatches);
        preg_match('/Alokasi 2025\s*:\s*Rp\.\s*([\d,.]+)/s', $text, $currentYearMatches);
        preg_match('/Alokasi 2026\s*:\s*Rp\.\s*([\d,.]+)/s', $text, $nextYearMatches);

        // Create sub-activity
        $subActivity = SubActivity::create([
            'activity_id' => $activity->id,
            'name' => $subActivityMatches[2] ?? '',
            'funding_source' => $fundingMatches[1] ?? '',
            'location' => $locationMatches[1] ?? '',
            'execution_time' => $timeMatches[1] ?? '',
            'target_group' => $targetMatches[1] ?? '',
            'current_year' => '2025',
            'current_year_allocation' => $this->cleanAmount($currentYearMatches[1] ?? '0'),
            'previous_year' => '2024',
            'previous_year_allocation' => $this->cleanAmount($prevYearMatches[1] ?? '0'),
            'next_year' => '2026',
            'next_year_allocation' => $this->cleanAmount($nextYearMatches[1] ?? '0'),
        ]);

        // Parse items
        $this->parseItems($text, $subActivity->id);

        return true;
    }

    protected function parseItems($text, $subActivityId)
    {
        // Keep detailed account code but simplify section matching
        preg_match_all('/(\d+\.\d+\.\d+\.\d+\.\d+\.\d+)[^\n]*\n(?:(?!\d+\.\d+\.\d+\.\d+\.\d+)[\s\S])*?(?=\d+\.\d+\.\d+\.\d+\.\d+|\z)/m', $text, $sections, PREG_SET_ORDER);
        
        foreach ($sections as $section) {
            $fullText = $section[0];
            $accountCode = $section[1];
            
            // Regular items pattern (with tax)
            preg_match_all('/([^:\n]+?)\s+Spesifikasi : Spesifikasi:\s*([^\n]+)\s+(\d+)\s+(\w+(?:\s*\/\s*[^0-9\n]+)?)\s+([\d,.]+,\d+)\s+(\d+)\s*%\s*Rp\.\s*([\d,.]+,\d+)/s', $fullText, $matches1, PREG_SET_ORDER);
            
            // Perjalanan dinas pattern
            preg_match_all('/([^:\n]+?)\s+Spesifikasi : Spesifikasi:\s*([^0-9]+?)(\d+)\s+(\w+\s*\/\s*\w+)\s+([\d,.]+,\d+)\s+0\s*%\s*Rp\.\s*([\d,.]+,\d+)/s', $fullText, $matches2, PREG_SET_ORDER);
            
            // Process regular items
            foreach ($matches1 as $match) {
                $this->createItem($match, $accountCode, $subActivityId, true);
            }
            
            // Process perjalanan dinas items
            foreach ($matches2 as $match) {
                $name = trim($match[1]);
                if (strpos($name, '[ # ]') !== false || strpos($name, '[ - ]') !== false) {
                    continue;
                }
                
                $price = $this->cleanAmount($match[5]);
                $quantity = (int) $match[3];
                $total = $price * $quantity;
                
                Item::create([
                    'sub_activity_id' => $subActivityId,
                    'account_code' => $accountCode,
                    'name' => $name,
                    'specification' => trim($match[2]), // Now contains clean specification without prefix
                    'quantity' => $quantity,
                    'unit' => trim($match[4]),
                    'price' => $price,
                    'tax' => 0,
                    'total' => $total,
                ]);
            }
        }
    }

    protected function createItem($match, $accountCode, $subActivityId, $hasTax)
    {
        $name = trim($match[1]);
        if (strpos($name, '[ # ]') !== false || strpos($name, '[ - ]') !== false) {
            return;
        }
        
        $price = $this->cleanAmount($hasTax ? $match[5] : $match[5]);
        $quantity = (int) $match[3];
        $baseTotal = $price * $quantity;
        
        Item::create([
            'sub_activity_id' => $subActivityId,
            'account_code' => $accountCode,
            'name' => $name,
            'specification' => trim($match[2]), // Now contains clean specification without prefix
            'quantity' => $quantity,
            'unit' => $match[4],
            'price' => $price,
            'tax' => $hasTax ? ($baseTotal * 11/100) : 0,
            'total' => $hasTax ? ($baseTotal + ($baseTotal * 11/100)) : $baseTotal,
        ]);
    }

    protected function cleanAmount($amount)
    {
        // Remove the decimal part and any commas/dots
        $amount = preg_replace('/,\d+$/', '', $amount);
        return (float) str_replace([',', '.'], ['', ''], $amount);
    }

    public function getEntities()
    {
        return Entities::with(['programs.activities.subActivities.items'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.document.upload', [
            'entities' => $this->getEntities(),
        ])->layout('layouts.base');
    }
}
