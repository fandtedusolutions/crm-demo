@php
    use App\Helpers\DateRangeHelper;
@endphp
<div class="modal fade" id="telecallerCallAnalyticsModal" tabindex="-1" aria-labelledby="telecallerCallAnalyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header tr-modal-header text-white">
                <div>
                    <h5 class="modal-title mb-1" id="telecallerCallAnalyticsModalLabel">
                        <i class="ti ti-chart-dots me-2"></i>Call Analytics
                    </h5>
                    <small class="opacity-75" id="tcCallAnalyticsSubtitle">—</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom bg-light">
                    <form id="tcCallAnalyticsFilterForm" class="row g-2 align-items-end">
                        <input type="hidden" id="tcCallAnalyticsTelecallerId" value="">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1">Date Range</label>
                            <select id="tcCallAnalyticsDateRange" class="form-select form-select-sm js-call-analytics-date-range">
                                @foreach(DateRangeHelper::options() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 js-call-analytics-custom-dates" style="display:none;">
                            <label class="form-label small fw-semibold mb-1">From</label>
                            <input type="date" id="tcCallAnalyticsStartDate" class="form-control form-control-sm js-call-analytics-start-date">
                        </div>
                        <div class="col-md-3 js-call-analytics-custom-dates" style="display:none;">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" id="tcCallAnalyticsEndDate" class="form-control form-control-sm js-call-analytics-end-date">
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-refresh me-1"></i>Apply
                            </button>
                        </div>
                        <div class="col-12">
                            <span class="badge bg-white text-dark border" id="tcCallAnalyticsPeriodBadge">—</span>
                        </div>
                    </form>
                </div>

                <div id="tcCallAnalyticsLoading" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Loading call analytics…</p>
                </div>

                <div id="tcCallAnalyticsContent" class="p-3">
                    <div class="row g-3 mb-3" id="tcCallAnalyticsStats"></div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="ti ti-list me-2"></i>Recent Calls</h6>
                            <span class="badge bg-light text-dark border" id="tcCallAnalyticsCallsCount">0 calls</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="tcCallAnalyticsCallsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date &amp; Time</th>
                                            <th>Contact</th>
                                            <th>Phone</th>
                                            <th>Type</th>
                                            <th>Duration</th>
                                            <th>Remarks</th>
                                            <th class="text-center">Rec.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tcCallAnalyticsCallsBody">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Select a telecaller to view calls.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a href="#" id="tcCallAnalyticsFullReportLink" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                    <i class="ti ti-external-link me-1"></i>Open Full Call Report
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
