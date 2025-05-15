<section>
    <header>
        <h2 class="fw-semibold">
            {{ __('Update Password') }}
        </h2>

        <p>
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 mt-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
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
                        {{ __('Your password has been updated.') }}
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
