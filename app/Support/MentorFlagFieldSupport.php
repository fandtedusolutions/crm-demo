<?php

namespace App\Support;

use App\Models\ConvertedLead;
use App\Models\Flag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MentorFlagFieldSupport
{
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
