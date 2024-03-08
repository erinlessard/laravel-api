<?php declare(strict_types=1);

namespace App\Actions\Task;

use App\Models\Task;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static run(Task $task): void
 */
class ProcessTask
{
    use AsAction;

    public function handle(Task $task): void
    {
        match ($task->type)
        {
            Type::call_reason => ProcessCallReason::dispatch($task->id),
            Type::call_actions => ProcessCallActions::dispatch($task->id),
            Type::call_segments => ProcessCallSegments::dispatch($task->id),
            Type::summary => ProcessSummary::dispatch($task->id),
            Type::satisfaction => ProcessSatisfaction::dispatch($task->id),
        };
    }
}
