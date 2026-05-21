<?php

namespace App\Support;

use App\Models\ConvertedLead;
use App\Models\SubjectArea;
use Illuminate\Support\Collection;

class ConvertedLeadSubjectAreaSupport
{
    /**
     * @param  mixed  $value
     * @return list<int>
     */
    public static function parseIds($value): array
    {
        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }
            if (str_starts_with($trimmed, '[')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    $value = $decoded;
                } else {
                    $value = explode(',', $trimmed);
                }
            } else {
                $value = explode(',', $trimmed);
            }
        }

        if (! is_array($value)) {
            return $value ? [(int) $value] : [];
        }

        return collect($value)
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public static function validationRules(): array
    {
        return [
            'value' => 'nullable|array',
            'value.*' => 'integer|exists:subject_areas,id',
        ];
    }

    public static function displayText(Collection $subjectAreas): string
    {
        if ($subjectAreas->isEmpty()) {
            return 'N/A';
        }

        return $subjectAreas->pluck('title')->filter()->implode(', ');
    }

    public static function syncOnConvertedLead(ConvertedLead $convertedLead, $value): array
    {
        $ids = self::parseIds($value);

        $convertedLead->subjectAreas()->sync($ids);
        $convertedLead->load('subjectAreas');

        return [
            'success' => true,
            'message' => 'Updated successfully',
            'value' => self::displayText($convertedLead->subjectAreas),
            'subject_area_ids' => $convertedLead->subjectAreas->pluck('id')->values()->all(),
        ];
    }
}
