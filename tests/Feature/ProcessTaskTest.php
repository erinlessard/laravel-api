<?php

use App\Actions\Task\ProcessSummary;
use App\Models\Job;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses( RefreshDatabase::class);

it('processes a Call Action task and provides results', function () {
    $task = Task::factory()
        ->for(Job::factory())
        ->create(['type' => 'call_actions']);

    \App\Actions\Task\ProcessCallActions::dispatchNow($task->id);

    $task->refresh();
    $this->assertEquals(1, $task->processed);
    $this->assertEquals(
        json_encode(['result' => 'successfully processed call action']),
        json_encode($task->result)
    );
});

it('processes a Call Reason task and provides results', function () {
    $task = Task::factory()
        ->for(Job::factory())
        ->create(['type' => 'call_reason']);

    \App\Actions\Task\ProcessCallReason::dispatchNow($task->id);

    $task->refresh();
    $this->assertEquals(1, $task->processed);
    $this->assertEquals(
        json_encode(['result' => 'this was a reasonable call']),
        json_encode($task->result)
    );
});
//
it('processes a Call Segments task and provides results', function () {
    $task = Task::factory()
        ->for(Job::factory())
        ->create(['type' => 'call_segments']);

    \App\Actions\Task\ProcessCallSegments::dispatchNow($task->id);

    $task->refresh();
    $this->assertEquals(1, $task->processed);
    $this->assertNull($task->result);
});
//
it('processes a Satisfaction task and provides results', function () {
    $task = Task::factory()
        ->for(Job::factory())
        ->create(['type' => 'satisfaction']);

    \App\Actions\Task\ProcessSatisfaction::dispatchNow($task->id);

    $task->refresh();
    $this->assertEquals(1, $task->processed);
    $this->assertEquals(
        json_encode(['result' => '5/10', ]),
        json_encode($task->result)
    );
});
//
it('processes a Summary task and provides results', function () {
    $task = Task::factory()
        ->for(Job::factory())
        ->create(['type' => 'summary']);

    ProcessSummary::dispatchNow($task->id);

    $task->refresh();
    $this->assertEquals(1, $task->processed);
    $this->assertEquals(
        json_encode(['result' => 'a long summary result']),
        json_encode($task->result)
    );
});
