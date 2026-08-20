<?php

namespace App\Support;

class HelpdeskSession
{
    public static function user(): ?object
    {
        return session('user');
    }

    public static function charge(): ?object
    {
        return session('charge');
    }

    public static function userId(): ?string
    {
        return self::user()->userid ?? null;
    }

    public static function userName(): string
    {
        return self::normalizeUserName((string) (self::user()->username ?? 'CAMS User'));
    }

    public static function normalizeUserName(?string $name): string
    {
        $value = trim((string) $name);

        return strtolower($value) === 'nic admin' ? 'NIC Admin' : $value;
    }

    public static function email(): ?string
    {
        return self::user()->email ?? null;
    }

    public static function deptCode(): ?string
    {
        return self::charge()->deptcode ?? null;
    }

    public static function chargeId(): ?string
    {
        $chargeId = self::charge()->chargeid ?? null;

        return $chargeId === null ? null : (string) $chargeId;
    }

    public static function roleActionCode(): ?string
    {
        $roleActionCode = self::charge()->roleactioncode ?? null;

        return $roleActionCode === null ? null : (string) $roleActionCode;
    }

    public static function canAccess(): bool
    {
        return in_array(self::roleActionCode(), ['01', '27','29'], true);
    }

    public static function role(): string
    {
        $charge = self::charge();
        $chargeId = self::chargeId();
        $roleActionCode = $charge->roleactioncode ?? null;
        $roleTypeCode = $charge->roletypecode ?? null;
        $userTypeCode = $charge->usertypecode ?? null;

        if ($roleTypeCode === '09') {
            return 'developer';
        }

        if ($chargeId === '1') {
            return 'Stateadmin';
        }

        if ($chargeId === '907') {
            return 'nic_admin';
        }

        if ($roleTypeCode === '05') {
            return 'Stateadmin';
        }

        if ($roleActionCode === '01') {
            return 'nic_admin';
        }

        if ($userTypeCode === 'H' || $roleTypeCode === '08') {
            return 'department_admin';
        }

        return 'user';
    }

    public static function roleLabel(): string
    {
        return match (self::role()) {
            'Stateadmin' => 'StateAdmin',
            'nic_admin' => 'NIC Admin',
            'department_admin' => 'Department Admin',
            'developer' => 'Tech Team',
            default => 'User',
        };
    }

    public static function isSuperAdmin(): bool
    {
        return self::role() === 'Stateadmin';
    }

    public static function isNicAdmin(): bool
    {
        return self::role() === 'nic_admin';
    }

    public static function isDepartmentAdmin(): bool
    {
        return self::role() === 'department_admin';
    }

    public static function isDeveloper(): bool
    {
        return self::role() === 'developer';
    }

    public static function isStaff(): bool
    {
        return in_array(self::role(), ['Stateadmin', 'nic_admin', 'department_admin', 'developer'], true);
    }
}
