<?php

namespace BretRZaun\StatusPage\Enum;

enum ResultType: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';

    public function getCssClass(): string
    {
        return match ($this) {
           self::SUCCESS  => 'success',
           self::WARNING  => 'warning',
           self::ERROR => 'danger'
        };
    }
}