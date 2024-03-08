<?php declare(strict_types=1);

namespace App\Actions\Task;

use App\Models\Task;
use Lorisleiva\Actions\Concerns\AsJob;

class ProcessSatisfaction
{
    use AsJob;

    public function handle(string $taskId): void
    {
        /** @var Task $task */
        $task = Task::find($taskId);

        // do important business logic for this type of task
        $task->setResult(['result' => '5/10']);
        $task->setProcessed();
        $task->save();
    }
}
