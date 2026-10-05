<?php

namespace App\Helpers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class PostSaleLeadHelper
{
    /**
     * Post-sales general manager, or a telecaller / team lead flagged as post-sale.
     * These users only see and create is_postsale = 1 records.
     */
    public static function seesOnlyPostSaleRecords(): bool
    {
        return RoleHelper::is_postsale_gm() || RoleHelper::is_postsale_telecaller();
    }

    /**
     * Regular telecallers and team leads stay on the normal lead pool.
     */
    public static function hidesPostSaleRecords(): bool
    {
        if (self::seesOnlyPostSaleRecords()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (! $user || (int) $user->role_id !== 3) {
            return false;
        }

        return ! RoleHelper::is_senior_manager();
    }

    public static function apply($query, string $column = 'is_postsale', ?Request $request = null): void
    {
        if (self::seesOnlyPostSaleRecords()) {
            $query->where($column, 1);

            return;
        }

        if (self::hidesPostSaleRecords()) {
            $query->where(function ($inner) use ($column) {
                $inner->whereNull($column)->orWhere($column, 0);
            });

            return;
        }

        if (! $request || ! $request->filled('is_postsale')) {
            return;
        }

        $value = (string) $request->input('is_postsale');
        if ($value === 'postsale' || $value === '1') {
            $query->where($column, 1);
        } elseif ($value === 'normal' || $value === '0') {
            $query->where(function ($inner) use ($column) {
                $inner->whereNull($column)->orWhere($column, 0);
            });
        }
    }

    public static function resolveForLead(Request $request, $telecallerId = null, bool $isCreate = true): ?int
    {
        if (self::seesOnlyPostSaleRecords()) {
            return 1;
        }

        if (RoleHelper::is_admin_or_super_admin()) {
            return $request->boolean('is_postsale') ? 1 : 0;
        }

        if (! $isCreate) {
            return null;
        }

        if ($telecallerId) {
            $telecaller = User::select('id', 'is_postsale', 'team_id')->find($telecallerId);
            if ($telecaller && (int) $telecaller->is_postsale === 1) {
                return 1;
            }
            if ($telecaller && $telecaller->team && (int) $telecaller->team->is_postsale === 1) {
                return 1;
            }
        }

        return 0;
    }

    public static function denyUnlessVisible(Lead $lead): void
    {
        $isPostSale = (int) ($lead->is_postsale ?? 0) === 1;

        if (self::seesOnlyPostSaleRecords() && ! $isPostSale) {
            abort(403, 'You can only work post-sale leads.');
        }

        if (self::hidesPostSaleRecords() && $isPostSale) {
            abort(403, 'You cannot work post-sale leads.');
        }
    }
}
