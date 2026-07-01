<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Get lead status color based on status ID
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusColor($statusId)
    {
        switch ($statusId) {
            case 1: // Un Touched Leads
                return 'primary'; // Blue - neutral, needs attention
            case 2: // Follow-up
                return 'warning'; // Orange - urgent, needs action
            case 3: // Not-interested IN FULL COURSE
                return 'danger'; // Red - negative outcome
            case 4: // Disqualified
                return 'dark'; // Dark - rejected
            case 5: // DNP
                return 'secondary'; // Gray - neutral
            case 6: // Demo
                return 'info'; // Cyan - informational
            case 7: // Interested to Buy
                return 'success'; // Green - positive outcome
            case 8: // Positive
                return 'success'; // Green - very positive
            case 9: // May Buy Later
                return 'warning'; // Orange - potential future
            default:
                return 'secondary';
        }
    }

    /**
     * Get lead status color class for Bootstrap
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusColorClass($statusId = null)
    {
        switch ((int) $statusId) {
            case 1: // Un Touched Leads
                return 'bg-primary text-white';
            case 2: // Follow-up
                return 'bg-warning text-dark';
            case 3: // Not-interested IN FULL COURSE
                return 'bg-danger text-white';
            case 4: // Disqualified
                return 'bg-dark text-white';
            case 5: // DNP
                return 'bg-secondary text-white';
            case 6: // Demo
                return 'bg-info text-white';
            case 7: // Interested to Buy
                return 'bg-success text-white';
            case 8: // Positive
                return 'bg-success text-white';
            case 9: // May Buy Later
                return 'bg-warning text-dark';
            default:
                return 'bg-secondary text-white';
        }
    }

    /**
     * Get lead status badge class for Bootstrap
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusBadgeClass($statusId)
    {
        switch ($statusId) {
            case 1: // Un Touched Leads
                return 'badge bg-primary text-white';
            case 2: // Follow-up
                return 'badge bg-warning text-dark';
            case 3: // Not-interested IN FULL COURSE
                return 'badge bg-danger text-white';
            case 4: // Disqualified
                return 'badge bg-dark text-white';
            case 5: // DNP
                return 'badge bg-secondary text-white';
            case 6: // Demo
                return 'badge bg-info text-white';
            case 7: // Interested to Buy
                return 'badge bg-success text-white';
            case 8: // Positive
                return 'badge bg-success text-white';
            case 9: // May Buy Later
                return 'badge bg-warning text-dark';
            default:
                return 'badge bg-secondary text-white';
        }
    }

    /**
     * Get lead status color for custom styling
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusCustomColor($statusId)
    {
        switch ($statusId) {
            case 1: // Un Touched Leads
                return '#0d6efd'; // Bootstrap primary blue
            case 2: // Follow-up
                return '#fd7e14'; // Bootstrap warning orange
            case 3: // Not-interested IN FULL COURSE
                return '#dc3545'; // Bootstrap danger red
            case 4: // Disqualified
                return '#212529'; // Bootstrap dark
            case 5: // DNP
                return '#6c757d'; // Bootstrap secondary gray
            case 6: // Demo
                return '#0dcaf0'; // Bootstrap info cyan
            case 7: // Interested to Buy
                return '#198754'; // Bootstrap success green
            case 8: // Positive
                return '#198754'; // Bootstrap success green
            case 9: // May Buy Later
                return '#fd7e14'; // Bootstrap warning orange
            default:
                return '#6c757d'; // Bootstrap secondary gray
        }
    }

    /**
     * Get lead status background color for custom styling
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusBackgroundColor($statusId)
    {
        switch ($statusId) {
            case 1: // Un Touched Leads
                return '#e7f1ff'; // Light blue
            case 2: // Follow-up
                return '#fff3cd'; // Light orange
            case 3: // Not-interested IN FULL COURSE
                return '#f8d7da'; // Light red
            case 4: // Disqualified
                return '#f8f9fa'; // Light gray
            case 5: // DNP
                return '#e9ecef'; // Light gray
            case 6: // Demo
                return '#d1ecf1'; // Light cyan
            case 7: // Interested to Buy
                return '#d1e7dd'; // Light green
            case 8: // Positive
                return '#d1e7dd'; // Light green
            case 9: // May Buy Later
                return '#fff3cd'; // Light orange
            default:
                return '#e9ecef'; // Light gray
        }
    }
}
