<?php declare(strict_types=1);

namespace App\Actions\Task;

enum Type: string
{
    case call_actions = 'call_actions';
    case call_reason = 'call_reason';
    case call_segments = 'call_segments';
    case satisfaction = 'satisfaction';
    case summary = 'summary';

    public static function values(): array
    {
        return array_column(self::cases(), 'name');
    }
}
