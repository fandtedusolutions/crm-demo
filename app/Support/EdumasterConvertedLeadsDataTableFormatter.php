<?php

namespace App\Support;

use App\Helpers\RoleHelper;
use App\Models\ConvertedLead;
use DOMDocument;
use Illuminate\Support\Facades\Log;

class EdumasterConvertedLeadsDataTableFormatter
{
    public static function columnKeys(bool $showParentPhone): array
    {
        $keys = [
            'index',
            'academic',
            'support',
            'converted_date',
            'register_number', 'course_flag',
            'name_col',
            'type',
            'dob',
            'phone',
            'whatsapp',
        ];

        if ($showParentPhone) {
            $keys[] = 'parent_phone';
        }

        return array_merge($keys, [
            'email',
            'batch',
            'admission_batch',
            'finance_approval',
            'faculty',
            'selected_courses',
            'board_university',
            'course_type',
            'course_name',
            'sslc_back_year',
            'plustwo_back_year',
            'degree_back_year',
            'actions',
        ]);
    }

    public static function dataTableRow(ConvertedLead $convertedLead, int $displayIndex, bool $hasIdCard): array
    {
        $showParentPhone = RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_admission_counsellor();

        $trHtml = view('admin.converted-leads.partials.edumaster-dt-desktop-row', [
            'convertedLead' => $convertedLead,
            'displayIndex' => $displayIndex,
            'hasIdCard' => $hasIdCard,
        ])->render();

        $cells = self::extractTdCellsFromTr($trHtml);
        $keys = self::columnKeys($showParentPhone);

        if (count($cells) !== count($keys)) {
            Log::warning('EduMaster DataTable column mismatch', [
                'converted_lead_id' => $convertedLead->id,
                'td_count' => count($cells),
                'key_count' => count($keys),
            ]);
        }

        $paired = [];
        $n = min(count($keys), count($cells));
        for ($i = 0; $i < $n; $i++) {
            $paired[$keys[$i]] = $cells[$i];
        }

        $paired['DT_RowId'] = 'edumaster_converted_' . $convertedLead->id;
        $paired['DT_RowClass'] = $convertedLead->is_cancelled ? 'cancelled-row' : '';

        return $paired;
    }

    /**
     * @return list<string>
     */
    protected static function extractTdCellsFromTr(string $trHtml): array
    {
        $trHtml = trim($trHtml);
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $wrapped = '<?xml encoding="UTF-8"?><table><tbody>' . $trHtml . '</tbody></table>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $rows = $dom->getElementsByTagName('tr');
        if ($rows->length === 0) {
            return [];
        }

        $row = $rows->item(0);
        $cells = [];
        foreach ($row->childNodes as $child) {
            if ($child instanceof \DOMElement && strtolower($child->tagName) === 'td') {
                $cells[] = $dom->saveHTML($child);
            }
        }

        return $cells;
    }
}

