<?php

namespace App\Enum;

enum GenericActionEnum: string
{
    case View   = 'view';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';

    public static function casesArray(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function label(): string {
        return match($this) {
            self::View => 'View',
            self::Create => 'Create',
            self::Update => 'Update',
            self::Delete => 'Delete',
        };
    }
}
