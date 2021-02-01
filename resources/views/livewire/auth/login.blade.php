<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">{{ __('other.login') }}</h2>
        </div>

        <form class="mt-8 space-y-6" wire:submit.prevent="submit">
            <div class="rounded-md -space-y-px">
                <div>
                    <label for="email" class="sr-only">{{ __('auth.login.email') }}</label>
                    <input id="email" name="email" type="email" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="{{ __('auth.login.email') }}" wire:model="email" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">{{ __('auth.login.password') }}</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="{{ __('auth.login.password') }}" wire:model="password" required>
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500" wire:model="remember">
                    <label for="remember" class="ml-2 block text-sm">{{ __('auth.login.remember') }}</label>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('other.login') }}
                </button>
            </div>
        </form>
    </div>
</div>
