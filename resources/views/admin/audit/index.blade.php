@section('title', 'Audit Logs | ByteVault')

<x-app-layout>
    <div class="container-fluid mx-auto px-4 py-6 position-relative">
        <!-- Header -->
        <div class="d-flex justify-content-end align-items-center mx-1 mb-4">
            <x-secondary-button type="button" data-bs-toggle="modal" data-bs-target="#filterAuditLogsModal">
                <span class="material-icons-outlined">filter_list</span>
                Filter
            </x-secondary-button>
        </div>

        <!-- Audit Table -->
        <div class="card p-4 mx-1">
            <table class="table table-striped inventory-table">
                <thead class="inventory-table-header">
                    <tr>
                        <th class="text-center">Log ID</th>
                        <th class="text-center">Employee</th>
                        <th class="text-center">Action</th>
                        <th class="text-center">Record</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="inventory-table-body table-group-divider">
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td class="text-center align-middle">{{ $log->logID }}</td>
                            <td class="text-center align-middle">
                                {{ $log->employee ? $log->employee->firstName . ' ' . $log->employee->lastName : 'System' }}
                            </td>
                            <td class="text-center align-middle">{{ ucfirst($log->actionType) }}</td>
                            <td class="text-center align-middle">
                                {{ $tableNames[$log->tableName] ?? ucfirst(str_replace('_', ' ', $log->tableName)) }}
                            </td>
                            <td class="text-center align-middle">{{ $log->timestamp->format('F j, Y') }}</td>
                            <td class="align-middle d-flex justify-content-center">
                                @if ($log->details->isNotEmpty())
                                    <x-primary-button class="btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $log->logID }}">
                                        View Details
                                    </x-primary-button>
                                @else
                                    <span class="">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No audit logs available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                <ul class="pagination">
                    <!-- Previous Page Link -->
                    @if ($auditLogs->onFirstPage())
                        <li class="page-item disabled">
                            <span class="material-icons-outlined page-link">
                                navigate_before
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link d-flex justify-content-center align-items-center" href="{{ $auditLogs->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                <span class="material-icons-outlined">
                                    navigate_before
                                </span>
                            </a>
                        </li>
                    @endif

                    <!-- Page Numbers -->
                    @for ($i = 1; $i <= $auditLogs->lastPage(); $i++)
                        <li class="page-item {{ $auditLogs->currentPage() === $i ? 'active' : '' }}">
                            @if ($auditLogs->currentPage() === $i)
                                <span class="page-link">{{ $i }}</span>
                            @else
                                <a class="page-link" href="{{ $auditLogs->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                            @endif
                        </li>
                    @endfor

                    <!-- Next Page Link -->
                    @if ($auditLogs->hasMorePages())
                        <li class="page-item">
                            <a class="page-link d-flex justify-content-center align-items-center" href="{{ $auditLogs->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
                                <span class="material-icons-outlined">
                                    navigate_next
                                </span>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="material-icons-outlined page-link">
                                navigate_next
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
            
            <!-- Modal for Details -->
            @foreach ($auditLogs as $log)
                <div class="modal fade" id="detailsModal{{ $log->logID }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $log->logID }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel{{ $log->logID }}">Audit Log Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <p><strong>Performed By:</strong> {{ $log->employee ? $log->employee->firstName . ' ' . $log->employee->lastName : 'System' }}</p>
                                    <p><strong>Date:</strong> {{ $log->timestamp->format('F j, Y \a\t H:i') }}</p>
                                    <p><strong>Entity Affected:</strong> 
                                        @php
                                            $friendlyName = $tableNames[$log->tableName] ?? ucwords(str_replace('_', ' ', $log->tableName));
                                            $routeName = $routeMap[$log->tableName] ?? null;
                                            $modelClass = '\App\Models\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $log->tableName)));
                                        @endphp
                                        @if ($routeName && in_array($log->actionType, ['create', 'update']) && class_exists($modelClass) && $modelClass::find($log->recordID))
                                            <a href="{{ route($routeName, $log->recordID) }}">{{ $friendlyName }}</a> (ID: {{ $log->recordID }})
                                        @else
                                            {{ $friendlyName }} (ID: {{ $log->recordID }})
                                        @endif
                                    </p>
                                </div>
                                <hr>
                                <div>
                                    <h6 class="@if ($log->actionType === 'create') text-success @elseif ($log->actionType === 'delete') text-danger @else text-primary @endif">
                                        @if ($log->actionType === 'create')
                                            <span class="material-icons-outlined align-middle">add</span> Created Data
                                        @elseif ($log->actionType === 'delete')
                                            <span class="material-icons-outlined align-middle">delete</span> Deleted Data
                                        @else
                                            <span class="material-icons-outlined align-middle">edit</span> Changes
                                        @endif
                                    </h6>
                                    @if ($log->details->isNotEmpty())
                                        <ul class="list-unstyled">
                                            @foreach ($log->details as $detail)
                                                <li class="@if ($log->actionType === 'create') text-success @elseif ($log->actionType === 'delete') text-danger @else text-primary @endif">
                                                    @if ($log->actionType === 'update')
                                                        <strong>{{ ucwords(str_replace('_', ' ', $detail->columnName)) }}:</strong>
                                                        {{ $detail->oldValue ?? 'N/A' }} → {{ $detail->newValue ?? 'N/A' }}
                                                    @elseif ($log->actionType === 'create' && in_array($detail->columnName, ['created', 'created_record']))
                                                        @php
                                                            $data = json_decode($detail->newValue, true);
                                                            if (is_array($data)) {
                                                                foreach ($data as $key => $value) {
                                                                    if (strpos($key, 'Date') !== false || strpos($key, '_at') !== false) {
                                                                        $date = \Carbon\Carbon::parse($value);
                                                                        echo '<li><strong>' . ucwords(str_replace('_', ' ', $key)) . ':</strong> ' . $date->format('F j, Y \a\t H:i') . '</li>';
                                                                    } else {
                                                                        echo '<li><strong>' . ucwords(str_replace('_', ' ', $key)) . ':</strong> ' . e($value) . '</li>';
                                                                    }
                                                                }
                                                            } else {
                                                                echo '<li>' . ($detail->newValue ?? 'N/A') . '</li>';
                                                            }
                                                        @endphp
                                                    @elseif ($log->actionType === 'delete' && in_array($detail->columnName, ['deleted', 'deleted_record']))
                                                        @php
                                                            $data = json_decode($detail->oldValue, true);
                                                            $excludeFields = ['created_at', 'updated_at'];
                                                            if (is_array($data)) {
                                                                foreach ($data as $key => $value) {
                                                                    if (!in_array($key, $excludeFields)) {
                                                                        echo '<li><strong>' . ucwords(str_replace('_', ' ', $key)) . ':</strong> ' . e($value) . '</li>';
                                                                    }
                                                                }
                                                            } else {
                                                                echo '<li>' . ($detail->oldValue ?? 'N/A') . '</li>';
                                                            }
                                                        @endphp
                                                    @else
                                                        <strong>{{ ucwords(str_replace('_', ' ', $detail->columnName)) }}:</strong>
                                                        {{ $log->actionType === 'create' ? ($detail->newValue ?? 'N/A') : ($detail->oldValue ?? 'N/A') }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>No field changes recorded.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Updated Filter Modal -->
    <x-modal name="filterAuditLogsModal" maxWidth="lg">
        <div class="modal-header">
            <h5 class="modal-title" id="filterAuditLogsModal-label">Filter Audit Logs</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="auditLogFilterForm" action="{{ route('audit.index') }}" method="GET">
                <!-- User Name (Single-Select AJAX Searchable Dropdown) -->
                <div class="mb-3">
                    <label for="employeeID" class="form-label">Employee Name</label>
                    <select name="employeeID" id="employeeID" class="form-select select2-user custom-select2">
                        <option value="">Select an employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->employeeID }}" {{ request()->input('employeeID') == $user->employeeID ? 'selected' : '' }}>
                                {{ $user->firstName }} {{ $user->lastName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Type (Single-Select) -->
                <div class="mb-3">
                    <label for="action_type" class="form-label">Action Type</label>
                    <select name="action_type" id="action_type" class="form-select select2 custom-select2">
                        <option value="">Select an Action</option>
                        <option value="login" {{ request()->input('action_type') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request()->input('action_type') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="create" {{ request()->input('action_type') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request()->input('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                    </select>
                </div>

                <!-- Table Name (Single-Select) -->
                <div class="mb-3">
                    <label for="table_name" class="form-label">Record Name</label>
                    <select name="table_name" id="table_name" class="form-select select2 custom-select2">
                        <option value="">Select a Table</option>
                        @foreach($tableNames as $key => $label)
                            <option value="{{ $key }}" {{ request()->input('table_name') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control"
                           value="{{ request()->input('date') }}">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <x-secondary-button type="button" onclick="resetFilters()">Reset</x-secondary-button>
            <x-primary-button type="button" onclick="document.getElementById('auditLogFilterForm').submit()">Apply Filters</x-primary-button>
        </div>
    </x-modal>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2 for other dropdowns
                $('#employeeID, .select2').select2({
                    placeholder: "Select an option",
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap-5',
                    dropdownParent: $('#filterAuditLogsModal')
                });

                // Ensure Select2 renders correctly in modal
                $('#filterAuditLogsModal').on('shown.bs.modal', function () {
                    // Trigger Select2 to re-render
                    $('#employeeID').trigger('change.select2');
                    $('.select2').trigger('change.select2');
                });
            });

            // Reset Filters
            function resetFilters() {
                document.getElementById('auditLogFilterForm').reset();
                $('#employeeID, .select2').val(null).trigger('change'); // Clear all Select2 selections
                $('#date').val(''); // Clear date
                window.location = '{{ route('audit.index') }}'; // Redirect to clear query params
            }
        </script>
    @endpush
</x-app-layout>