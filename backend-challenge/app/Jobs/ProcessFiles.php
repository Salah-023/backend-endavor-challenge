<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\CreditCard;


class ProcessFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The file path of the JSON file to be processed.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $jsonContents = file_get_contents($this->filePath);
        // Parse the JSON data into an associative array
        $jsonData = json_decode($jsonContents, true);

        foreach ($jsonData as $record) {
            // Extract the relevant data from the $record array and assignt it to a user and to credit card and then saving them
            if ($this->isValidRecord($record)) {
                $creditCard = new CreditCard([
                    'type' => $record['credit_card']['type'],
                    'number' => $record['credit_card']['number'],
                    'name' => $record['credit_card']['name'],
                    'expirationDate' => $record['credit_card']['expirationDate'],
                ]);
                $creditCard->save();

                $user = new User([
                    'name' => $record['name'],
                    'address' => $record['address'],
                    'checked' => $record['checked'],
                    'description' => $record['description'],
                    'interest' => $record['interest'],
                    'dateOfBirth' => $record['date_of_birth'],
                    'email' => $record['email'],
                    'account' => $record['account'],
                    'creditCardId' => $creditCard->getKey(),
                ]);
                $user->save();
            }


        }
    }


    protected function isValidRecord($record)
    {
        // If the date_of_birth is missing or empty, treat it as unknown and process the record
        if (!isset($record['date_of_birth'])) {
            return true;
        }

        try {
            $dateOfBirth = \Carbon\Carbon::parse($record['date_of_birth']);
        } catch (\Exception $e) {
            // Failed to parse date_of_birth, handle error or return false, depending on your requirements
            return false;
        }

        // Calculate the age based on the date_of_birth
        $age = $dateOfBirth->age;

        // Check if the age falls within the required criteria (18 to 65 or unknown)
        return $age >= 18 && $age <= 65;
    }
}