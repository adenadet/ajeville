<?php

namespace App\Imports\CRM;

use App\Http\Traits\CRM\CustomerTrait;
use App\Models\CRM\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    use CustomerTrait;

    public $inserted = 0;
    public $skipped = 0;
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        // Get header from first row
        $headers = $rows->first()->map(fn ($h) => strtolower(trim($h)))->toArray();

        // Remove header row
        $rows = $rows->slice(1);

        $existingEmails = Customer::pluck('email')->map(fn ($e) => strtolower($e))->toArray();
        $existingPhones = Customer::pluck('phone')->toArray();

        foreach ($rows as $row) {
            $data = array_combine($headers, $row->toArray());
            if (!$data) continue;

            $email = strtolower(trim($data['email'] ?? ''));
            $phone = trim($data['phone'] ?? '');

            // Duplicate detection
            if (in_array($email, $existingEmails) || in_array($phone, $existingPhones)) {
                $this->skipped++;
                continue;
            }

            // Create customer via trait
            $this->crm_customer_create($data);
            $this->inserted++;

            // Update caches
            $existingEmails[] = $email;
            $existingPhones[] = $phone;
        }

    }
}
