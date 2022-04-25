<?php


namespace App\CommissionFee;


use App\Currencies\Currencies;
use App\Exceptions\CurrencyNotFoundException;
use Illuminate\Support\Carbon;

class UserOperation
{
    private mixed $rules;
    private int $amountInDuration;
    private array $previousLine;
    private array $currentLine;
    private float $calculatedFee;
    public int $counter;
    private int $countInDuration;

    public function __construct()
    {
        $this->amountInDuration = 0;
        $this->countInDuration = 0;
        $this->counter = 0;
        $this->previousLine = [];
    }

    public function setPreviousLine()
    {
        $this->previousLine = $this->currentLine;
    }

    private function setRules($line)
    {
        $this->rules = config('commission-rules.' . $line['operation'] . '.' . $line['client_type']);
    }

    /**
     * @throws CurrencyNotFoundException
     */
    public function handle($line): float|int
    {
        $this->calculatedFee = 0;
        $this->currentLine = $line;
        $this->setRules($line);
        $this->counter += 1;

        $currencies = new Currencies();

        if (!$currencies->isBase($this->currentLine['currency'])) {
            $this->currentLine['amount'] = $currencies->calculateAmountFor(
                $this->currentLine['currency'],
                $this->currentLine['amount']
            );

            $this->currentLine['currency'] = $currencies->getBase();
        }

        if (!$this->isDurationSet()) {
            return $this->currentLine['amount'] * $this->rules['fee'];
        };

        return (new Rules($this))->chain();
    }

    public function getCalculatedFee(): float
    {
        return round($this->calculatedFee,1);
    }

    private function isDurationSet(): bool
    {
        return isset($this->rules['duration']) && !is_null($this->rules['duration']);
    }

    public function isMaxNumberSet(): bool
    {
        return isset($this->rules['max_number']) && !is_null($this->rules['max_number']);
    }

    public function isMaxAmountSet(): bool
    {
        return isset($this->rules['max_amount']) && !is_null($this->rules['max_amount']);
    }

    public function isAmountExceeded(): bool
    {
        return $this->amountInDuration > $this->rules['max_amount'];
    }

    public function areDatesInTheTimespan($firstDate, $secondDate): bool
    {
        if ($this->rules['duration'] == 'week') {
            return Carbon::parse($secondDate)->isSameWeek(Carbon::parse($firstDate));
        }

        if ($this->rules['duration'] == 'month') {
            return Carbon::parse($secondDate)->isSameMonth(Carbon::parse($firstDate));
        }
    }

    public function isMaxNumberExceeded(): bool
    {
        return $this->countInDuration > $this->rules['max_number'];
    }

    public function addAmountInDuration()
    {
        if (empty($this->previousLine)) {
            $this->amountInDuration = $this->currentLine['amount'];
            return;
        }

        if ($this->areDatesInTheTimespan($this->previousLine['date'], $this->currentLine['date'])) {
            $this->amountInDuration += $this->currentLine['amount'];
            return;
        }

        $this->amountInDuration = $this->currentLine['amount'];
    }

    public function addNumberInDuration()
    {
        if (empty($this->previousLine)) {
            $this->countInDuration = 1;
            return;
        }

        if ($this->areDatesInTheTimespan($this->previousLine['date'], $this->currentLine['date'])) {
            $this->countInDuration += 1;
            return;
        }

        $this->countInDuration = 1;
    }

    public function calculateFeeBasedOnMaxAmount(): float|int
    {
        if (($this->amountInDuration - $this->currentLine['amount']) > $this->rules['max_amount']) {
            $this->calculatedFee = $this->currentLine['amount'] * $this->rules['fee'];
        } else {
            $this->calculatedFee = ($this->amountInDuration - $this->rules['max_amount']) * $this->rules['fee'];
        }

        return $this->calculatedFee;
    }

    public function calculateFeeBasedOnMaxNumber(): float|int
    {
        return $this->calculatedFee = $this->currentLine['amount'] * $this->rules['fee'];
    }
}
