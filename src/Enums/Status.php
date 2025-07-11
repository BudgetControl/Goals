<?php declare(strict_types=1);

namespace Budgetcontrol\Goals\Enums;

enum Status: string
{
    case ACTIVE = 'active';
    case COMPLETE = 'complete';
    case DELETED = 'deleted';
    case PENDING = 'pending';
    case PAUSED = 'paused';

    /**
     * Get the label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETE => 'Complete',
            self::DELETED => 'Deleted',
            self::PENDING => 'Pending',
            self::PAUSED => 'Paused',
        };
    }

    /**
     * Get all status values.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_map(fn($status) => $status->value, self::cases());
    }
}