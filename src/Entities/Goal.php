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
    private float $monthlyAmount;
    private string $description = '';
    private string $icon = '';
    private array $entries = [];

    public function __construct(
        string $uuid,
        string $workspaceId,
        string $name,
        string $description,
        string $icon,
        float $targetAmount,
        \DateTimeInterface $dueDate
    ) {
        $this->uuid = $uuid;
        $this->workspaceId = $workspaceId;
        $this->name = $name;
        $this->targetAmount = round($targetAmount, 2);
        $this->dueDate = $dueDate;
        $this->monthlyAmount = round($this->calculateMonthlyAmount(), 2);

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
            $data['dueDate']
        );

        if (isset($data['entries'])) {
            foreach ($data['entries'] as $entryData) {
                $goal->addEntry($entryData);
            }
        }

        return $goal;
    }

    private function calculateMonthlyAmount(): float {
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

    public function getMonthlyAmount(): float {
        return $this->monthlyAmount;
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

    public function toArray(): array {
        return [
            'uuid' => $this->uuid,
            'workspaceId' => $this->workspaceId,
            'name' => $this->name,
            'targetAmount' => $this->targetAmount,
            'dueDate' => $this->dueDate->format('Y-m-d'),
            'monthlyAmount' => $this->monthlyAmount,
            'description' => $this->description,
            'icon' => $this->icon,
            'entries' => array_map(fn($entry) => $entry->toArray(), $this->entries),
        ];
    }

}