<?php


namespace App\CommissionFee\Operations;


use App\CommissionFee\ClientTypes\AbstractClientType;
use App\CommissionFee\Singleton;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

abstract class AbstractOperation extends Singleton
{
    protected AbstractClientType $clientType;
    protected mixed $amount;
    private int $userId;
    private array $rules;
    private int $number;
    private array $lines;

    protected function __construct(AbstractClientType $clientType)
    {
        parent::__construct();
        $this->clientType = $clientType;
        $this->amount = 0;
        $this->number = 0;
        $this->userId = 0;
    }

    public function handle(array $line): float|int
    {
        $this->setRules();

        $this->lines[] = $line;

        return $this->calculateFee($line);
    }

    public function getClassName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function setRules()
    {
        $className = strtolower($this->clientType->getClassName());

        $ruleName = substr($className, 0, strpos($className, 'type'));

        $this->rules = config('commission-rules.' . strtolower($this->getClassName()) . '.' . $ruleName);
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    public function addAmount(float $amount)
    {
        $this->amount = $this->amount + $amount;
    }

    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    private function calculateFee(array $line): float|int
    {
        if(!$this->isMaxNumberSet()){
            return $line['amount'] * $this->rules['fee'];
        }

        if ($this->isMaxNumberSet() && !$this->isMaxNumberExceeded()) {
            if ($this->isMaxAmountSet() && !$this->isAmountExceeded()) {
                return 0;
            }
        };

        if ($this->isMaxNumberSet() && $this->isMaxNumberExceeded()) {
            return $line['amount'] * $this->rules['fee'];
        };

        if ($this->isMaxAmountSet() && $this->isAmountExceeded()) {
            return ($line['amount'] - $this->rules['max_amount']) * $this->rules['fee'];
        }

        return $line['amount'] * $this->rules['fee'];
    }

    private function isMaxNumberExceeded(): bool
    {
        return $this->getNumberInDuration() > $this->rules['max_number'];
    }

    private function isAmountExceeded(): bool
    {
        return $this->getAmountInDuration() > $this->rules['max_amount'];
    }

    public function getNumberInDuration(): int // for now weekly
    {
        $number = 0;

        foreach ($this->lines as $key => $line) {
            if (isset($this->lines[$key + 1]) && $this->areInAWeek($line['date'], $this->lines[$key + 1]['date'])) {
                $number++;
            }
        }

        return $number;
    }

    public function getAmountInDuration()
    {
        $amount = 0;

        $reverseLines = array_reverse($this->lines);

        foreach ($reverseLines as $key => $line) {
            $amount = $line['amount'];

            if (isset($reverseLines[$key + 1]) && $this->areInAWeek($line['date'], $reverseLines[$key + 1]['date'])) {
                $amount += $reverseLines[$key + 1]['amount'];
            }
        }

        dump($amount);
        return $amount;
    }

    private function isMaxNumberSet(): bool
    {
        return isset($this->rules['max_number']) && !is_null($this->rules['max_number']);
    }

    private function isMaxAmountSet(): bool
    {
        return isset($this->rules['max_amount']) && !is_null($this->rules['max_amount']);
    }

    public function areInAWeek($firstDate, $secondDate): bool
    {
        return  Carbon::parse($secondDate)->isSameWeek(Carbon::parse($firstDate));
    }
}
