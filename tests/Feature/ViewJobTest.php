<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses( RefreshDatabase::class);

test('returns a job with all tasks given a job ID', function () {
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

    /** @var \Illuminate\Testing\TestResponse $response */
    $response = $this->getJson(route('job.get', $job->id));

    $response->assertStatus(200);
    $response->assertJson(function (\Illuminate\Testing\Fluent\AssertableJson $json) use ($job, $data) {
        $json->where('id', $job->id)
            ->where('text', $data['text'])
            ->hasAll(['created_at', 'updated_at'])
            ->has('tasks', 5, function (\Illuminate\Testing\Fluent\AssertableJson $json) use ($job) {
                $json->where('id', $job->tasks[0]->id)
                    ->where('type', 'call_reason')
                    ->where('processed', 1)
                    ->where('result.result', 'this was a reasonable call')
                    ->where('job_id', $job->id)
                    ->hasAll(['created_at', 'updated_at']);
            });
    });
});
