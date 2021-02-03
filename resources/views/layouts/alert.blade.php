@if(session('alert.success') || session('alert.error'))
    <div class="@if(session('alert.success')) bg-green-500 @elseif(session('alert.error')) bg-red-500 @endif">
        <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                    <p class="font-medium text-white truncate">
                        <span class="md:inline">
                            @if (session('alert.success'))
                                {{ session('alert.success') }}
                            @elseif(session('alert.error'))
                                {{ session('alert.error') }}
                            @endif
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
