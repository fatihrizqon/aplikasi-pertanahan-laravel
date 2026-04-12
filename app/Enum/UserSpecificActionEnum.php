<?php

namespace App\Enum;

enum UserSpecificActionEnum: string
{
    case ResetPassword = 'reset_user_password';
    case Activate      = 'activate_user';
    case ExportData    = 'export_user';
    case ImportData    = 'import_user';

    public static function casesArray(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function label(): string {
        return match($this) {
            self::ResetPassword => 'Reset Password',
            self::Activate      => 'Activate / Deactivate User',
            self::ExportData    => 'Export User',
            self::ImportData    => 'Import User',
        };
    }
}
