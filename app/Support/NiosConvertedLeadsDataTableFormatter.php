<?php

namespace App\Support;

use App\Helpers\RoleHelper;
use App\Models\ConvertedLead;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Facades\Log;

class NiosConvertedLeadsDataTableFormatter
{
    public static function columnKeys(bool $showBatchCb, bool $showParentPhone): array
    {
        $keys = [];
        if ($showBatchCb) {
            $keys[] = 'batch_cb';
        }
        $keys = array_merge($keys, [
            'index', 'academic', 'support', 'register_number', 'converted_date', 'dob', 'type', 'name_col',
            'subject', 'subject_area', 'mobile', 'whatsapp',
        ]);
        if ($showParentPhone) {
            $keys[] = 'parent_phone';
        }
        $keys = array_merge($keys, [
            'batch', 'course', 'admission_batch', 'registered_person', 'username', 'password',
            'admission_status', 'student_reg_fee', 'exam_fee', 'ref_no', 'enroll_no', 'mail',
            'id_card', 'tma', 'remarks', 'actions',
        ]);

        return $keys;
    }

    public static function dataTableRow(ConvertedLead $convertedLead, int $displayIndex, bool $hasIdCard): array
    {
        $showBatchCb = RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_academic_assistant();
        $showParentPhone = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor();

        $trHtml = view('admin.converted-leads.partials.nios-dt-desktop-row', [
            'convertedLead' => $convertedLead,
            'displayIndex' => $displayIndex,
            'hasIdCard' => $hasIdCard,
        ])->render();

        $cells = self::extractTdCellsFromTr($trHtml);
        $keys = self::columnKeys($showBatchCb, $showParentPhone);

        if (count($cells) !== count($keys)) {
            Log::warning('NIOS DataTable column mismatch', [
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

        $paired['DT_RowId'] = 'nios_converted_'.$convertedLead->id;
        $paired['DT_RowClass'] = $convertedLead->is_cancelled ? 'cancelled-row' : '';
        $paired['mobile_card'] = view('admin.converted-leads.partials.nios-mobile-card', [
            'convertedLead' => $convertedLead,
            'hasIdCard' => $hasIdCard,
        ])->render();

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
        $wrapped = '<?xml encoding="UTF-8"?><table><tbody>'.$trHtml.'</tbody></table>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $rows = $dom->getElementsByTagName('tr');
        if ($rows->length === 0) {
            return [];
        }

        /** @var \DOMElement $row */
        $row = $rows->item(0);
        $cells = [];
        foreach ($row->childNodes as $child) {
            if ($child instanceof DOMElement && strtolower($child->tagName) === 'td') {
                $cells[] = $dom->saveHTML($child);
            }
        }

        return $cells;
    }
}
