<?php declare(strict_types=1);

namespace Budgetcontrol\Goals\Entities;

use Budgetcontrol\Library\Model\Entry;
use Illuminate\Database\Eloquent\Collection;

class Goal {

    private string $uuid;
    private string $workspaceId;
    private string $name;
    private float $targetAmount;
    private \DateTimeInterface $dueDate;
    private float $monthlyDeposit;
    private string $description = '';
    private string $icon = '';
    private float $balance = 0.0;
    private int $percentage = 0;
    private array $entries = [];

    public function __construct(
        string $uuid,
        string $workspaceId,
        string $name,
        string $description,
        string $icon,
        float $targetAmount,
        float $balance,
        \DateTimeInterface $dueDate
    ) {
        $this->uuid = $uuid;
        $this->workspaceId = $workspaceId;
        $this->name = $name;
        $this->targetAmount = round($targetAmount, 2);
        $this->dueDate = $dueDate;
        $this->monthlyDeposit = round($this->calculatemonthlyDeposit(), 2);
        $this->description = $description;
        $this->icon = $icon;
        $this->balance = round($balance, 2);
        $this->percentage = $this->calculateTotalPercentage();

    }

    public static function create(
        array $data
    ): self {
        $goal = new self(
            $data['uuid'] ?? \Ramsey\Uuid\Uuid::uuid4()->toString(),
            $data['workspaceId'],
            $data['name'],
            $data['description'] ?? '',
            $data['icon'] ?? '',
            $data['targetAmount'],
            $data['dueDate'],
            $data['balance'] ?? 0.0
        );

        if (isset($data['entries'])) {
            foreach ($data['entries'] as $entryData) {
                $goal->addEntry($entryData);
            }
        }

        return $goal;
    }

    /**
     * Calculates the total percentage completion of the goal.
     *
     * This method determines the overall progress percentage of the goal
     * based on its current state and associated transactions.
     *
     * @return int The calculated percentage completion (0-100)
     */
    private function calculateTotalPercentage(): int {
        if ($this->targetAmount <= 0) {
            return 0;
        }
        $percentage = ($this->balance / $this->targetAmount) * 100;
        return (int) round($percentage);
    }

    /**
     * Calculates the monthly deposit amount for the goal.
     *
     * This method determines the monthly amount that needs to be deposited
     * to reach the goal's target amount within the specified timeframe.
     *
     * @return float The calculated monthly deposit amount
     */
    private function calculatemonthlyDeposit(): float {
        $interval = $this->dueDate->diff(new \DateTime());
        $months = ($interval->y * 12) + $interval->m;
        return $months > 0 ? $this->targetAmount / $months : 0.0;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getWorkspaceId(): string {
        return $this->workspaceId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getTargetAmount(): float {
        return $this->targetAmount;
    }

    public function getDueDate(): \DateTimeInterface {
        return $this->dueDate;
    }

    public function getmonthlyDeposit(): float {
        return $this->monthlyDeposit;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function addEntry(Entry|Collection $entry): void {
        if ($entry instanceof Collection) {
            $this->entries = array_merge($this->entries, $entry->toArray());
        } else {
            $this->entries[] = $entry;
        }
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function getPercentage(): int {
        return $this->percentage;
    }

    public function toArray(): array {
        return [
            'uuid' => $this->uuid,
            'workspaceId' => $this->workspaceId,
            'name' => $this->name,
            'target_amount' => $this->targetAmount,
            'due_date' => $this->dueDate->format('Y-m-d'),
            'monthly_deposit' => $this->monthlyDeposit,
            'description' => $this->description,
            'icon' => $this->icon,
            'balance' => $this->balance,
            'entries' => array_map(fn($entry) => $entry->toArray(), $this->entries),
            'percentage' => $this->percentage,
        ];
    }

}