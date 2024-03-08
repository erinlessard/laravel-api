<?php

use App\Actions\Task\Type;
use App\Models\Job;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\{post};

uses( RefreshDatabase::class);

it('creates a new job', function () {
    $data = [
        'text' => 'text string',
        'tasks' => [
            'call_actions',
            'call_reason',
        ]
    ];

    $types = (\App\Actions\Job\CreateJob::make())->convertTaskStringsToTypes($data['tasks']);
    $job = \App\Actions\Job\CreateJob::run($data['text'], ...$types);

    $this->assertInstanceOf(Job::class, $job);
    $this->assertTrue(\Illuminate\Support\Str::isUlid($job->id));
    $this->assertDatabaseHas('jobs', ['id' => $job->id]);
});

it('creates and processes tasks after creating a job', function () {
    $data = [
        'text' => 'a long text string',
        'tasks' => [
            'call_reason',
            'call_actions',
            'call_segments',
            'satisfaction',
            'summary'
        ]
    ];

    $types = (\App\Actions\Job\CreateJob::make())->convertTaskStringsToTypes($data['tasks']);
    $job = \App\Actions\Job\CreateJob::run($data['text'], ...$types);

    $this->assertDatabaseCount(Task::class, 5);

    // ensure the tasks were processed after creation (uses sync driver in test)
    $job = Job::find($job->id);
    foreach($job->tasks AS $task) {
        $this->assertEquals(1, $task->processed);
    }
});

it('validates POST data and creates a new job', function () {
    $data = [
        'text' => 'a long text string',
        'tasks' => [
            'call_reason',
            'call_actions',
            'call_segments',
            'satisfaction',
            'summary'
        ]
    ];

    $response = post(uri: route('job.post'), data: $data);

    $response->assertStatus(200);
    $jobId = $response->getContent();
    $this->assertTrue(\Illuminate\Support\Str::isUlid($jobId));
    $this->assertDatabaseHas('jobs', ['id' => $jobId]);
});

it('uses POST data to create and processes tasks after creating a job', function () {
    $data = [
        'text' => 'a long text string',
        'tasks' => [
            'call_reason',
            'call_actions',
            'call_segments',
            'satisfaction',
            'summary'
        ]
    ];

    $response = post(uri: route('job.post'), data: $data);

    $jobId = $response->getContent();

    $this->assertDatabaseCount(Task::class, 5);

    // ensure the tasks were processed after creation (uses sync driver in test)
    $job = Job::find($jobId);
    foreach($job->tasks AS $task) {
        $this->assertEquals(1, $task->processed);
    }
});

it('pushes process task actions into queue on job creation', function () {
    Queue::fake();
    Queue::assertNothingPushed();

    \App\Actions\Job\CreateJob::run(
        'blah',
        Type::call_reason,
        Type::call_actions,
        Type::call_segments,
        Type::satisfaction,
        Type::summary
    );

    \App\Actions\Task\ProcessCallReason::assertPushed(1);
    \App\Actions\Task\ProcessCallActions::assertPushed(1);
    \App\Actions\Task\ProcessCallSegments::assertPushed(1);
    \App\Actions\Task\ProcessSatisfaction::assertPushed(1);
    \App\Actions\Task\ProcessSummary::assertPushed(1);
});

it('rejects incorrect task type in POST data', function () {
    $response = post(uri: route('job.post'), data: [
        'text' => 'a long text string',
        'tasks' => [
            'call_reason',
            'call_actions',
            'invalid type',
        ]
    ]);

    $response->assertInvalid();
});

it('job text larger than 2000 length fails POST data validation', function () {
    $response = post(uri: route('job.post'), data: [
        'text' => fake()->realTextBetween(2000, 3000),
        'tasks' => [
            'call_reason',
            'call_actions',
        ]
    ]);

    $response->assertInvalid();
});

it('rejects empty POST data', function () {
    // text can't be empty
    $response = post(uri: route('job.post'), data: [
        'text' => '',
        'tasks' => []
    ]);

    $response->assertInvalid();

    // missing text from payload
    $response2 = post(uri: route('job.post'), data: [
        'tasks' => []
    ]);

    $response2->assertInvalid();

    // missing tasks from payload
    $response3 = post(uri: route('job.post'), data: [
        'text' => 'ba',
    ]);

    $response3->assertInvalid();
});
