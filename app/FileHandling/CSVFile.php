<?php


namespace App\FileHandling;


use App\Exceptions\FileHandlingException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CSVFile
{
    private string $file;
    /**
     * @var mixed|string
     */
    private mixed $delimiter;
    private array $header;
    private array $data;

    public function __construct($file, $delimiter = ',')
    {
        $this->file = $file;
        $this->delimiter = $delimiter;
    }

    /**
     * @throws FileHandlingException
     */
    public function handle(): CSVFile
    {
        $this->check();
        $this->setHeader();
        $this->toArray();
        return $this;
    }

    /**
     * @throws FileHandlingException
     */
    private function check()
    {
        if (!file_exists($this->file) || !is_readable($this->file)) {
            throw new FileHandlingException();
        }
    }

    private function setHeader()
    {
        $this->header = ['date', 'user_id', 'client_type', 'operation', 'amount', 'currency'];
    }

    private function toArray(): void
    {
        $this->data = [];

        if (($handle = fopen($this->file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                if (!$this->header)
                    $this->header = $row;
                else
                    $this->data[] = array_combine($this->header, $row);
            }
            fclose($handle);
        }
    }

    public function getArray(): array
    {
        return $this->data;
    }

    public function getCollectedByUserId(): array
    {
        $collectedData = [];

        while ($this->data != null) {
            $firstRaw = array_shift($this->data);

            $collectedData[$firstRaw['user_id']][] = $firstRaw;

            foreach ($this->data as $key => $raw) {
                if ($firstRaw['user_id'] == $raw['user_id']) {
                    $collectedData[$firstRaw['user_id']][] = $raw;
                    unset($this->data[$key]);
                }
            }
        }

        return $collectedData;
    }
}
