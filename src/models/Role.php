<?php

namespace models;

enum Role: string{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';

    public static function toArray(): array {
        return array_map(fn($role) => $role->value, self::cases());
    }
}