@section('title', 'Account Manager | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Buttons -->
        <div class="d-flex justify-content-between mb-4">
            {{-- Filter Button --}}
            <x-secondary-button
                type="button" {{-- Ensure it's type="button" --}}
                data-bs-toggle="modal"
                data-bs-target="#accountFilterModal" {{-- Matches the modal ID --}}
            >
                <span class="material-icons-outlined">filter_alt</span>
                Filter
            </x-secondary-button>

            {{-- Filter Modal --}}

            {{-- Add Account Button --}}
            <x-primary-button href="{{ route('account.add') }}" class="py-2">
                <span class="material-icons-outlined">add</span>
                Add Account
            </x-primary-button>
        </div>

        <!-- Employee Table -->
        <div class="card account-manager-card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover custom-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Role</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach ($employees as $employee)
                                <tr>
                                    <td class="align-middle">{{ $employee->firstName }} {{ $employee->lastName }}</td>
                                    <td class="align-middle">{{ $employee->email }}</td>
                                    <td class="align-middle">{{ $employee->status }}</td>
                                    <td class="align-middle">{{ $employee->role }}</td>
                                    <td class="align-middle">
                                        <x-primary-button href="{{ route('account.edit', $employee->employeeID) }}">
                                            <span class="material-icons-outlined">edit</span>
                                        </x-primary-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Modal Definition in accounts.account-manager.blade.php --}}
    <x-modal name="accountFilterModal" maxWidth="md"> {{-- Use the ID set in the button's data-bs-target --}}
        {{-- Use a GET form to submit filters as query parameters --}}
        {{-- Make sure the route name 'accounts.account-manager.index' is correct --}}
        <form method="GET" action="{{ route('account.manager') }}" id="accountFilterForm">

            <div class="modal-header">
                <h5 class="modal-title" id="accountFilterModalLabel">Filter Accounts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Role Filter - Using Checkboxes as an example --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2">Roles</label>
                    {{-- Replace with your actual roles --}}
                    @php
                        $availableRoles = ['Admin', 'Manager', 'Employee']; // Example roles
                        $selectedRoles = request('roles', []); // Get current roles from request
                    @endphp
                    @foreach($availableRoles as $role)
                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="roles[]" {{-- Use roles[] for multiple selections --}}
                            value="{{ $role }}"
                            id="role_{{ $role }}"
                            {{ in_array($role, $selectedRoles) ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_{{ $role }}">
                            {{ $role }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <hr>

                {{-- Status Filter - Using Radio buttons as an example --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2">Status</label>
                    @php
                        $availableStatuses = ['Active', 'Inactive']; // Example statuses
                        $selectedStatus = request('status'); // Get current status from request
                    @endphp
                    @foreach($availableStatuses as $status)
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="status"
                            id="status_{{ $status }}"
                            value="{{ $status }}"
                                {{ $selectedStatus == $status ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_{{ $status }}">
                            {{ $status }}
                        </label>
                    </div>
                    @endforeach
                    {{-- Option to clear status filter --}}
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="status"
                            id="status_any"
                            value="" {{-- Empty value clears the filter --}}
                                {{ is_null($selectedStatus) || $selectedStatus == '' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_any">
                            Any
                        </label>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                {{-- Button to clear filters within the modal --}}
                <x-secondary-button type="button" id="clearAccountFilters">
                    Clear Filters
                </x-secondary-button>
                {{-- Apply button submits the form --}}
                <x-primary-button type="submit">
                    Apply Filters
                </x-primary-button>
            </div>

        </form>
    </x-modal>

    {{-- Optional JavaScript to handle 'Clear Filters' button inside the modal --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const clearButton = document.getElementById('clearAccountFilters');
        const filterForm = document.getElementById('accountFilterForm');

        if (clearButton && filterForm) {
            clearButton.addEventListener('click', function() {
                // Clear checkboxes
                filterForm.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                // Check the 'Any' status radio
                const anyStatusRadio = filterForm.querySelector('input[name="status"][value=""]');
                if (anyStatusRadio) {
                    anyStatusRadio.checked = true;
                }
                // Optionally submit the cleared form immediately
                // filterForm.submit();
                // Or just clear visually and let the user click "Apply Filters"
            });
        }
    });
    </script>
</x-app-layout>