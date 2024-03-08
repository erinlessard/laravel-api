<?php declare(strict_types=1);

namespace App\Actions\Task;

use App\Models\Task;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static run(Type $taskType): Task
 */
class CreateTask
{
    use AsAction;

    public function handle(Type $taskType): Task
    {
        return new Task(['type' => $taskType]);
    }
}
