@section('title', 'Account Settings | ByteVault')

<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-2 text-dark">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="container mx-auto px-4 px-sm-5 px-lg-6 space-y-3">
            <div class="row g-4">
                <!-- Profile Information Card -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm account-settings-card">
                        <div class="card-body p-3 p-sm-4">
                            <div class="card-text">
                                @include('admin.profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Password Update Card -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm account-settings-card">
                        <div class="card-body p-3 p-sm-4">
                            <div class="card-text">
                                @include('admin.profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Delete Account Card -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm account-settings-card">
                        <div class="card-body p-3 p-sm-4">
                            <div class="card-text">
                                @include('admin.profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
