<?php

namespace App\Support;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\AdmissionBatch;
use App\Models\ConvertedLead;
use App\Models\Flag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MentorFlagFieldSupport
{
    public static function canUserUpdateFlag(): bool
    {
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_academic_assistant()
            || RoleHelper::is_mentor()
            || RoleHelper::is_faculty()
            || RoleHelper::is_mentor_head()
            || RoleHelper::is_hod();
    }

    public static function mentorCanUpdateLead(ConvertedLead $convertedLead): bool
    {
        if (!RoleHelper::is_mentor() && !RoleHelper::is_faculty()) {
            return true;
        }

        if (empty($convertedLead->admission_batch_id)) {
            return false;
        }

        return AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
            ->where('id', $convertedLead->admission_batch_id)
            ->exists();
    }

    public static function flagUpdateDeniedResponse(): array
    {
        return [
            'success' => false,
            'error' => 'You do not have permission to update the flag.',
        ];
    }

    public static function leadUpdateDeniedResponse(): array
    {
        return [
            'success' => false,
            'error' => 'You do not have permission to update this lead.',
        ];
    }

    public static function mentorLeadScopeDeniedJsonResponse(ConvertedLead $convertedLead): ?JsonResponse
    {
        if ((!RoleHelper::is_mentor() && !RoleHelper::is_faculty()) || self::mentorCanUpdateLead($convertedLead)) {
            return null;
        }

        return response()->json(self::leadUpdateDeniedResponse(), 403);
    }

    public static function mentorFieldDeniedJsonResponse(string $field, array $deniedFields): ?JsonResponse
    {
        if ((!RoleHelper::is_mentor() && !RoleHelper::is_faculty()) || !in_array($field, $deniedFields, true)) {
            return null;
        }

        return response()->json([
            'success' => false,
            'error' => 'You do not have permission to edit this field.',
        ], 403);
    }

    public static function flagUpdateJsonResponse(ConvertedLead $convertedLead, $value): JsonResponse
    {
        $result = self::updateOnConvertedLead($convertedLead, $value);

        return response()->json($result, !empty($result['success']) ? 200 : 403);
    }

    public static function forFilterSelect(): Collection
    {
        return Flag::orderBy('title')->get(['id', 'title']);
    }

    public static function applyListingFilter(Builder $query, Request $request): void
    {
        if ($request->filled('flag_id')) {
            $query->where('flag_id', $request->flag_id);
        }
    }

    public static function displayHtml(?Flag $flag): string
    {
        if (!$flag) {
            return '<span class="text-muted">N/A</span>';
        }

        $title = e($flag->title);
        $color = e($flag->color);

        return '<span class="d-inline-flex align-items-center gap-2 mentor-flag-display">'
            . '<span class="rounded border flex-shrink-0" style="width:18px;height:18px;background-color:' . $color . ';"></span>'
            . '<span class="fw-medium">' . $title . '</span>'
            . '</span>';
    }

    public static function updateOnConvertedLead(ConvertedLead $convertedLead, $value): array
    {
        if (!self::canUserUpdateFlag()) {
            return self::flagUpdateDeniedResponse();
        }

        if (!self::mentorCanUpdateLead($convertedLead)) {
            return self::leadUpdateDeniedResponse();
        }

        $convertedLead->flag_id = $value ?: null;
        $convertedLead->save();

        $flag = $value ? Flag::find($value) : null;

        return [
            'success' => true,
            'message' => 'Updated successfully',
            'value' => $flag ? $flag->title : 'N/A',
            'display_html' => self::displayHtml($flag),
            'flag_color' => $flag?->color,
        ];
    }

    public static function validationRule(): string
    {
        return 'nullable|exists:flags,id';
    }
}
