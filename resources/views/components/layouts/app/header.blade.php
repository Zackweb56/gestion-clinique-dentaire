<flux:header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar class="flex items-center justify-between w-full">
        <div class="flex items-center gap-4">
            <!-- Mobile sidebar toggle -->
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            
            <!-- Title -->
            <h1 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 sm:text-sm lg:text-2xl">
                Dentexa - Administration
            </h1>
        </div>
        
        <!-- Theme dropdown -->
        <div class="flex items-center gap-2">
            
            <!-- User dropdown -->
            <flux:dropdown position="bottom" align="end">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="cursor-pointer"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->getRoleNames()->first() }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Paramétrage') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Se déconnecter') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </flux:navbar>
    
</flux:header>