<?php

namespace App\Helpers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TeamTelecallerFilterHelper
{
    public static function canSeeAllTeamTelecallerFilterOptions(): bool
    {
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_general_manager()
            || RoleHelper::is_senior_manager();
    }

    public static function canUseTeamTelecallerFilters(): bool
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return false;
        }

        if (self::canSeeAllTeamTelecallerFilterOptions()) {
            return true;
        }

        $isTelecaller = (int) $currentUser->role_id === 3;
        $isTeamLead = AuthHelper::isTeamLead();

        return ! $isTelecaller || $isTeamLead;
    }

    /**
     * @return Collection<int, Team>
     */
    public static function getFilterTeams(): Collection
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && (int) $currentUser->role_id === 3;

        $query = Team::query()
            ->nonMarketing()
            ->with([
                'teamLead:id,name,email',
                'detail:team_id,legal_name,city,b2b_code,comm_officer_name,comm_officer_mobile',
            ])
            ->orderBy('name');

        if (! self::canSeeAllTeamTelecallerFilterOptions()
            && ($isTeamLead || $isTelecaller)
            && $currentUser?->team_id) {
            $query->where('id', $currentUser->team_id);
        }

        return $query->get();
    }

    /**
     * @return array<int>|null Null means no team filter (all teams).
     */
    public static function resolveTeamIds(Request $request): ?array
    {
        $ids = self::normalizeIdList(
            $request->input('team_ids'),
            $request->input('team_id')
        );

        return $ids === [] ? null : $ids;
    }

    /**
     * @return array<int>|null Null means no telecaller filter (all telecallers).
     */
    public static function resolveTelecallerIds(Request $request): ?array
    {
        $ids = self::normalizeIdList(
            $request->input('telecaller_ids'),
            $request->input('telecaller_id')
        );

        return $ids === [] ? null : $ids;
    }

    /**
     * @param  array<int>|null  $teamIds
     * @return Collection<int, User>
     */
    public static function getFilterTelecallers(?array $teamIds = null): Collection
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && (int) $currentUser->role_id === 3;

        $query = User::query()
            ->where('role_id', 3)
            ->where('is_active', true)
            ->with('team:id,name')
            ->select('id', 'name', 'email', 'team_id', 'is_b2b')
            ->orderBy('name');

        if (! self::canSeeAllTeamTelecallerFilterOptions()) {
            if ($isTeamLead && $currentUser?->team_id) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($currentUser->team_id);
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                $query->whereIn('id', $teamMemberIds);
            } elseif ($isTelecaller && ! $isTeamLead) {
                $query->where('id', AuthHelper::getCurrentUserId());
            } else {
                $query->nonMarketingTelecallers();
            }
        } else {
            $query->nonMarketingTelecallers();
        }

        if (! empty($teamIds)) {
            $query->whereIn('team_id', $teamIds);
        }

        return $query->get();
    }

    public static function applyLeadQueryFilters(Builder $query, Request $request): void
    {
        $teamIds = self::resolveTeamIds($request);
        $telecallerIds = self::resolveTelecallerIds($request);

        if ($teamIds !== null) {
            $query->where(function (Builder $teamQuery) use ($teamIds) {
                $teamQuery->whereIn('team_id', $teamIds)
                    ->orWhereHas('telecaller', function (Builder $telecallerQuery) use ($teamIds) {
                        $telecallerQuery->whereIn('team_id', $teamIds);
                    });
            });
        }

        if ($telecallerIds !== null) {
            $query->whereIn('telecaller_id', $telecallerIds);
        }
    }

    public static function applyConvertedLeadQueryFilters(Builder $query, Request $request): void
    {
        $teamIds = self::resolveTeamIds($request);
        $telecallerIds = self::resolveTelecallerIds($request);

        if ($teamIds === null && $telecallerIds === null) {
            return;
        }

        $query->whereHas('lead', function (Builder $leadQuery) use ($teamIds, $telecallerIds) {
            if ($teamIds !== null) {
                $leadQuery->where(function (Builder $teamQuery) use ($teamIds) {
                    $teamQuery->whereIn('team_id', $teamIds)
                        ->orWhereHas('telecaller', function (Builder $telecallerQuery) use ($teamIds) {
                            $telecallerQuery->whereIn('team_id', $teamIds);
                        });
                });
            }

            if ($telecallerIds !== null) {
                $leadQuery->whereIn('telecaller_id', $telecallerIds);
            }
        });
    }

    /**
     * @param  mixed  $primary
     * @param  mixed  $legacy
     * @return array<int>
     */
    private static function normalizeIdList(mixed $primary, mixed $legacy): array
    {
        $values = [];

        foreach ([$primary, $legacy] as $input) {
            if ($input === null || $input === '' || $input === 'all') {
                continue;
            }

            if (is_array($input)) {
                $values = array_merge($values, $input);
                continue;
            }

            if (is_string($input) && str_contains($input, ',')) {
                $values = array_merge($values, explode(',', $input));
                continue;
            }

            $values[] = $input;
        }

        $ids = [];
        foreach ($values as $value) {
            if ($value === null || $value === '' || $value === 'all') {
                continue;
            }

            $id = (int) $value;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
