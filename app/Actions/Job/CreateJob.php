<?php declare(strict_types=1);

namespace App\Actions\Job;

use App\Actions\Task\CreateTask;
use App\Actions\Task\ProcessTask;
use App\Actions\Task\Type;
use App\Models\Job;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static run(string $text, Type ...$tasks): Job
 */
class CreateJob
{
    use AsAction;

    public function handle(string $text, Type ...$tasks): Job
    {
        // create and save job to get a job ID
        $job = new Job(['text' => $text]);
        $job->save();

        // create tasks to process the job's tasks and persist them before dispatching into queue
        // so that if something goes wrong somewhere no data is lost
        $taskModels = collect($tasks)->map(function ($task) {
           return CreateTask::run($task);
        });

        $job->tasks()->saveMany($taskModels);

        // process tasks because they were successfully saved
        $taskModels->each(function ($task) {
            ProcessTask::run($task);
        });

        return $job;
    }

    public function convertTaskStringsToTypes(array $tasks): array
    {
        return collect($tasks)->transform(function ($task) {
           return Type::from($task);
        })->toArray();
    }

    public function asController(ActionRequest $actionRequest): string
    {
        $data = $actionRequest->validated();
        $tasks = $this->convertTaskStringsToTypes($data['tasks']);

        return $this->handle($data['text'], ...$tasks)->id;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'min:1', 'max:2000'],
            'tasks' => [
                'required',
                'array',
            ],
            'tasks.*' => Rule::in(Type::values()),
        ];
    }
}
