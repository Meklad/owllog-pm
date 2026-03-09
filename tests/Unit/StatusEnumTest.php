<?php

use App\Enums\Status;
use PHPUnit\Framework\TestCase;

test('status enum has correct values', function () {
    expect(Status::ToDo->value)->toBe('to_do');
    expect(Status::InProgress->value)->toBe('in_progress');
    expect(Status::InReview->value)->toBe('in_review');
    expect(Status::InTest->value)->toBe('in_test');
    expect(Status::Blocked->value)->toBe('blocked');
    expect(Status::Done->value)->toBe('done');
});

test('status enum has all expected cases', function () {
    $cases = Status::cases();
    expect($cases)->toHaveCount(6);

    $caseNames = array_map(fn ($case) => $case->name, $cases);
    expect($caseNames)->toContain('ToDo', 'InProgress', 'InReview', 'InTest', 'Blocked', 'Done');
});

test('status enum is backed by string', function () {
    expect(Status::tryFrom('to_do'))->toBe(Status::ToDo);
    expect(Status::tryFrom('invalid'))->toBeNull();
});
