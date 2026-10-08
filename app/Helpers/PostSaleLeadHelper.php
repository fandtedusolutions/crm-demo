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

    /**
     * Post-sale telecaller flagged so the team lead does not see them.
     * Their leads stay with the Post-sale GM.
     */
    public static function isGmOnlyTelecaller($user): bool
    {
        return $user
            && (int) ($user->is_postsale ?? 0) === 1
            && (int) ($user->hide_from_team_lead ?? 0) === 1;
    }

    /**
     * Team leads stay off these telecallers. Post-sale GM, admin, and managers still see them.
     */
    public static function teamLeadShouldHideGmOnlyTelecallers(): bool
    {
        if (RoleHelper::is_postsale_gm()
            || RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_general_manager()
            || RoleHelper::is_senior_manager()) {
            return false;
        }

        return RoleHelper::is_team_lead();
    }

    /**
     * @return array<int>
     */
    public static function gmOnlyTelecallerIds(): array
    {
        return User::query()
            ->where('role_id', 3)
            ->where('is_postsale', 1)
            ->where('hide_from_team_lead', 1)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function excludeHiddenTelecallersFromUserQuery($query): void
    {
        if (! self::teamLeadShouldHideGmOnlyTelecallers()) {
            return;
        }

        $selfId = AuthHelper::getCurrentUserId();
        $query->where(function ($outer) use ($selfId) {
            $outer->where(function ($inner) {
                $inner->where('hide_from_team_lead', 0)
                    ->orWhereNull('hide_from_team_lead')
                    ->orWhere('is_postsale', 0);
            });

            if ($selfId) {
                $outer->orWhere('id', $selfId);
            }
        });
    }

    public static function apply($query, string $column = 'is_postsale', ?Request $request = null): void
    {
        self::hideGmOnlyLeadsFromTeamLead($query, $column);

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

        if (self::teamLeadShouldHideGmOnlyTelecallers() && $lead->telecaller_id) {
            $telecaller = $lead->relationLoaded('telecaller')
                ? $lead->telecaller
                : User::select('id', 'is_postsale', 'hide_from_team_lead')->find($lead->telecaller_id);

            if (self::isGmOnlyTelecaller($telecaller)
                && (int) $telecaller->id !== (int) AuthHelper::getCurrentUserId()) {
                abort(403, 'You cannot work these post-sale leads.');
            }
        }
    }

    private static function hideGmOnlyLeadsFromTeamLead($query, string $column): void
    {
        if (str_contains($column, 'converted_leads.')) {
            return;
        }

        if (! self::teamLeadShouldHideGmOnlyTelecallers()) {
            return;
        }

        $selfId = (int) AuthHelper::getCurrentUserId();
        $ids = array_values(array_filter(
            self::gmOnlyTelecallerIds(),
            fn ($id) => (int) $id !== $selfId
        ));

        if ($ids === []) {
            return;
        }

        $telecallerColumn = preg_replace('/is_postsale$/', 'telecaller_id', $column) ?: 'telecaller_id';
        $query->where(function ($inner) use ($telecallerColumn, $ids) {
            $inner->whereNotIn($telecallerColumn, $ids)
                ->orWhereNull($telecallerColumn);
        });
    }
}
