<?php

namespace App\Enums;

enum Status: string
{
    case ToDo = 'to_do';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case InTest = 'in_test';
    case Blocked = 'blocked';
    case Done = 'done';
}
