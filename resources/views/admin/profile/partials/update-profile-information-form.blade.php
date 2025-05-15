<section>
    <header>
        <h2 class="fw-semibold">
            {{ __('Profile Information') }}
        </h2>

        <p>
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="firstName" :value="__('First Name')" />
            <x-text-input id="firstName" name="firstName" type="text" class="mt-1 block w-full" :value="old('firstName', $user->firstName)" required autofocus autocomplete="firstName" />
            <x-input-error class="mt-2" :messages="$errors->get('firstName')" />
        </div>

        <div class="mt-3">
            <x-input-label for="lastName" :value="__('Last Name')" />
            <x-text-input id="lastName" name="lastName" type="text" class="mt-1 block w-full" :value="old('lastName', $user->lastName)" required autofocus autocomplete="lastName" />
            <x-input-error class="mt-2" :messages="$errors->get('lastName')" />
        </div>

        <!-- Add Phone Number Field -->
        <div class="mt-3">
            <x-input-label for="phoneNumber" :value="__('Phone Number')" />
            <x-text-input 
                id="phoneNumber" 
                name="phoneNumber" 
                type="tel" 
                class="mt-1 block w-full" 
                :value="old('phoneNumber', $user->phoneNumber)" 
                autocomplete="tel" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('phoneNumber')" />
        </div>

        <div class="mt-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 mt-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <div 
                    class="toast align-items-center text-bg-success border-0 position-fixed top-0 start-50 translate-middle-x m-5" 
                    role="alert" 
                    aria-live="assertive" 
                    aria-atomic="true" 
                    data-bs-autohide="true" 
                    data-bs-delay="2000"
                    style="z-index: 1000;"
                >
                    <div class="toast-header">
                        <strong class="me-auto text-success">{{ __('Success') }}</strong>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button> --}}
                    </div>
                    <div class="toast-body">
                        {{ __('Your profile has been updated.') }}
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var toastEl = document.querySelector('.toast');
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    });
                </script>
            @endif
        </div>
    </form>
</section>
