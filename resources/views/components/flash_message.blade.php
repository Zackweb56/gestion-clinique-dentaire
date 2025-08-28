@session('success')
<div id="toast"
     x-data="{ show: false }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     x-init="
        $nextTick(() => show = true);
        setTimeout(() => show = false, 6000);
     "
     @click.outside="show = false"
     role="alert"
     aria-live="assertive"
     class="fixed bottom-5 right-5 w-full max-w-xs z-50"
>
    <div class="relative bg-white dark:bg-neutral-800 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <!-- Progress bar -->
        <div class="absolute top-0 left-0 h-1 bg-green-500 dark:bg-green-400 w-full"
             x-data="{ progress: 100 }"
             x-init="
                setInterval(() => {
                    if (progress > 0) progress -= 100/5000 * 50;
                }, 50);
             "
             :style="`width: ${progress}%`"
        ></div>

        <!-- Content -->
        <div class="p-4 flex items-start">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="bg-green-100 dark:bg-green-900/50 p-1.5 rounded-full">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="ml-3 flex-1 pt-2">
                <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                    {{ session('success') }}
                </p>
            </div>

            <!-- Close button -->
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false"
                        class="inline-flex text-neutral-400 dark:text-neutral-500 hover:text-neutral-500 dark:hover:text-neutral-400 focus:outline-none"
                        aria-label="Close">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

@endsession

@session('danger')
<div id="toast-danger"
     x-data="{ show: false }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     x-init="
        $nextTick(() => show = true);
        setTimeout(() => show = false, 6000);
     "
     @click.outside="show = false"
     role="alert"
     aria-live="assertive"
     class="fixed bottom-5 right-5 w-full max-w-xs z-50"
>
    <div class="relative bg-white dark:bg-neutral-800 rounded-lg shadow-lg border border-red-200 dark:border-red-900/50 overflow-hidden">
        <!-- Progress bar (red instead of green) -->
        <div class="absolute top-0 left-0 h-1 bg-red-500 dark:bg-red-400 w-full"
             x-data="{ progress: 100 }"
             x-init="
                setInterval(() => {
                    if (progress > 0) progress -= 100/5000 * 50;
                }, 50);
             "
             :style="`width: ${progress}%`"
        ></div>

        <!-- Content -->
        <div class="p-4 flex items-start">
            <!-- Icon (exclamation triangle for errors) -->
            <div class="flex-shrink-0">
                <div class="bg-red-100 dark:bg-red-900/50 p-1.5 rounded-full">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="ml-3 flex-1 pt-2">
                <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                    {{ session('danger') }}  <!-- Changed to 'error' instead of 'success' -->
                </p>
            </div>

            <!-- Close button -->
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false"
                        class="inline-flex text-neutral-400 dark:text-neutral-500 hover:text-neutral-500 dark:hover:text-neutral-400 focus:outline-none"
                        aria-label="Close">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsession

@session('warning')
<div id="toast-warning"
     x-data="{ show: false }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     x-init="
        $nextTick(() => show = true);
        setTimeout(() => show = false, 6000);
     "
     @click.outside="show = false"
     role="alert"
     aria-live="assertive"
     class="fixed bottom-5 right-5 w-full max-w-xs z-50"
>
    <div class="relative bg-white dark:bg-neutral-800 rounded-lg shadow-lg border border-yellow-200 dark:border-yellow-900/50 overflow-hidden">
        <!-- Progress bar (yellow instead of red) -->
        <div class="absolute top-0 left-0 h-1 bg-yellow-500 dark:bg-yellow-400 w-full"
             x-data="{ progress: 100 }"
             x-init="
                setInterval(() => {
                    if (progress > 0) progress -= 100/5000 * 50;
                }, 50);
             "
             :style="`width: ${progress}%`"
        ></div>

        <!-- Content -->
        <div class="p-4 flex items-start">
            <!-- Icon (exclamation triangle for warnings) -->
            <div class="flex-shrink-0">
                <div class="bg-yellow-100 dark:bg-yellow-900/50 p-1.5 rounded-full">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="ml-3 flex-1 pt-2">
                <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                    {{ session('warning') }}
                </p>
            </div>

            <!-- Close button -->
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false"
                        class="inline-flex text-neutral-400 dark:text-neutral-500 hover:text-neutral-500 dark:hover:text-neutral-400 focus:outline-none"
                        aria-label="Close">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsession