<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts
};

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public int $created = 0;
    public int $updated = 0;
    public array $errors = [];
    protected int $currentRow = 1;

    /**
     * Transaction sebelum import
     */
    public function beforeImport()
    {
        DB::beginTransaction();
        $this->errors = [];
    }

    /**
     * Commit atau rollback setelah import
     */
    public function afterImport()
    {
        if (!empty($this->errors)) {
            DB::rollBack();

            $messages = [];
            foreach ($this->errors as $row => $rowErrors) {
                $messages[] = "Row {$row}: Validation failed — " . implode("; ", $rowErrors) . ". Please review and correct.";

            }

            throw new \Exception(
                "Import process could not be completed due to validation errors:\n"
                . implode("\n", $messages)
            );
        }

        DB::commit();
    }

    /**
     * Proses setiap row
     */
    public function model(array $row)
    {
        $rowNumber = $this->currentRow++;

        $data = User::excelMapFromRow($row);

        $user = User::where('email', $data['email'] ?? null)->first() ?? new User();

        $rules = $user->rules('update');

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->errors[$rowNumber] = $validator->errors()->all();
            return null;
        }

        $email = strtolower(trim($data['email'] ?? ''));
        unset($data['email']);

        if (!$user->exists) {
            $this->created++;

            return new User(array_merge($data, [
                'email'    => $email,
                'password' => bcrypt(Str::random(12)),
                'created_by' => auth()->id(),
            ]));
        }

        $user->fill($data);

        if ($user->isDirty()) {
            $user->save();
            $this->updated++;
        }

        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
