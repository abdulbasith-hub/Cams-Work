<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HelpdeskV2Session
{
    public const ROLE_USER = 'user';
    public const ROLE_STATE_ADMIN = 'state_admin';
    public const ROLE_NIC_ADMIN = 'nic_admin';
    public const ROLE_LAYER_LEAD = 'layer_lead';
    public const ROLE_DEVELOPER = 'developer';
    public const ROLE_TESTER = 'tester';
    public const ROLE_WATCHLIST = 'watchlist';

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
        $userId = self::user()->userid ?? null;

        return $userId === null ? null : (string) $userId;
    }

    public static function userName(): string
    {
        return trim((string) (self::user()->username ?? 'CAMS User'));
    }

    public static function email(): ?string
    {
        return self::user()->email ?? null;
    }

    public static function developerUser(): ?object
    {
        $userId = self::userId();
        $email = self::email();

        if (!$userId && !$email) {
            return null;
        }

        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->where(function ($query) use ($userId, $email) {
                if ($userId) {
                    $query->where('devuserid', $userId);
                }

                if ($email) {
                    $query->orWhereRaw('LOWER(email) = ?', [strtolower((string) $email)]);
                }
            })
            ->first(['devuserid', 'devename', 'email', 'senior_flag']);
    }

    public static function developerUserId(): ?string
    {
        $developer = self::developerUser();

        return $developer ? (string) $developer->devuserid : self::userId();
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

    public static function roleTypeCode(): ?string
    {
        $roleTypeCode = self::charge()->roletypecode ?? null;

        return $roleTypeCode === null ? null : (string) $roleTypeCode;
    }

    public static function role(): string
    {
        if (self::isStateAdmin()) {
            return self::ROLE_STATE_ADMIN;
        }

        if (self::isNicAdmin()) {
            return self::ROLE_NIC_ADMIN;
        }

        if (self::isLayerLead()) {
            return self::ROLE_LAYER_LEAD;
        }

        if (self::isDeveloperPerson()) {
            return self::ROLE_DEVELOPER;
        }

        return self::ROLE_USER;
    }

    public static function roleLabel(?string $role = null): string
    {
        return match ($role ?? self::role()) {
            'stateadmin', 'superadmin' => 'State Admin',
            'nicadmin' => 'NIC Admin',
            self::ROLE_STATE_ADMIN => 'State Admin',
            self::ROLE_NIC_ADMIN => 'NIC Admin',
            self::ROLE_LAYER_LEAD => 'Senior Developer',
            self::ROLE_DEVELOPER => 'Developer',
            self::ROLE_TESTER => 'Tester',
            self::ROLE_WATCHLIST => 'Watchlist',
            default => 'User',
        };
    }

    public static function tableRole(string $role): string
    {
        return match ($role) {
            self::ROLE_STATE_ADMIN => 'stateadmin',
            self::ROLE_NIC_ADMIN => 'nicadmin',
            self::ROLE_LAYER_LEAD => 'layer_lead',
            self::ROLE_DEVELOPER => 'developer',
            self::ROLE_TESTER => 'tester',
            self::ROLE_WATCHLIST => 'watchlist',
            default => $role,
        };
    }

    public static function tableRoleAliases(string $role): array
    {
        return match ($role) {
            self::ROLE_STATE_ADMIN => ['state_admin', 'stateadmin', 'superadmin'],
            self::ROLE_NIC_ADMIN => ['nic_admin', 'nicadmin'],
            self::ROLE_LAYER_LEAD => ['layer_lead', 'additional_layer'],
            self::ROLE_DEVELOPER => ['developer'],
            self::ROLE_TESTER => ['tester'],
            self::ROLE_WATCHLIST => ['watchlist'],
            default => [$role],
        };
    }

    public static function canAccess(): bool
    {
        return self::userId() !== null
            && (
                self::isStateAdmin()
                || self::isNicAdmin()
                || self::isDeveloperPerson()
                || self::hasHelpdeskCharge()
            );
    }

    public static function hasHelpdeskCharge(?string $userId = null): bool
    {
        $userId = $userId ?: self::userId();

        if (!$userId) {
            return false;
        }

        $helpdeskRoleActionCode = (string) (view()->shared('Helpdesk_roleactioncode') ?? '27');

        return DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->where('uc.userid', $userId)
            ->where('uc.statusflag', 'Y')
            ->where('rm.roleactioncode', $helpdeskRoleActionCode)
            ->exists();
    }

    public static function isStateAdmin(): bool
    {
        $stateChargeId = (string) (view()->shared('Stateadminchargeid') ?? '1');

        return self::chargeId() === $stateChargeId || self::roleTypeCode() === '05';
    }

    public static function isNicAdmin(): bool
    {
        $nicChargeId = (string) (view()->shared('NICAdminchargeid') ?? '907');
        $adminRoleAction = (string) (view()->shared('Admin_roleactioncode') ?? '01');

        return self::chargeId() === $nicChargeId
            || self::roleActionCode() === $adminRoleAction
            || self::roleTypeCode() === (string) (view()->shared('NIC_roletypecode') ?? '07');
    }

    public static function isLayerLead(?string $userId = null): bool
    {
        if (!$userId) {
            $developer = self::developerUser();

            return ($developer->senior_flag ?? null) === 'Y';
        }

        if (!$userId) {
            return false;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', $userId)
            ->where('statusflag', 'Y')
            ->where('senior_flag', 'Y')
            ->exists();
    }

    public static function isDeveloperPerson(?string $userId = null): bool
    {
        $developer = $userId ? self::developerById($userId) : self::developerUser();
        $userId = $userId ?: self::userId();

        if (!$userId) {
            return false;
        }

        $roleActionCodes = collect(view()->shared('helpdesk_developer_roleactioncodes') ?? ['21', '22'])
            ->map(fn ($code) => (string) $code)
            ->all();

        return self::roleTypeCode() === '09'
            || in_array(self::roleActionCode(), $roleActionCodes, true)
            || $developer !== null;
    }

    public static function activeLayerLeads(): Collection
    {
        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->where('senior_flag', 'Y')
            ->orderBy('devename')
            ->get(['devuserid', 'devename', 'email']);
    }

    public static function activeDevelopers(): Collection
    {
        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->where(function ($query) {
                $query->whereNull('senior_flag')
                    ->orWhere('senior_flag', '!=', 'Y');
            })
            ->orderBy('devename')
            ->get(['devuserid', 'devename', 'email']);
    }

    public static function activeTesters(): Collection
    {
        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->orderBy('devename')
            ->get(['devuserid', 'devename', 'email']);
    }

    public static function developerById(?string $userId): ?object
    {
        if (!$userId) {
            return null;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', $userId)
            ->where('statusflag', 'Y')
            ->first(['devuserid', 'devename', 'email', 'senior_flag']);
    }
}
