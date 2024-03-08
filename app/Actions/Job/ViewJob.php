<?php declare(strict_types=1);

namespace App\Actions\Job;

use App\Models\Job;
use Lorisleiva\Actions\Concerns\AsAction;

class ViewJob
{
    use AsAction;

    public function asController(Job $job): Job
    {
        return $job->load('tasks');
    }
}
