<div>
    @include('components.flash_message')
    
    <!-- Users Table - Full Width -->
    <div class="rounded-xl p-6 bg-neutral-900 shadow mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-10 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-neutral-700 placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm"
                    placeholder="{{ __('Rechercher un utilisateur...') }}"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <div wire:loading wire:target="search" class="animate-spin h-5 w-5 text-teal-500">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div>
                @can('créer utilisateur')
                    <flux:button variant="primary" wire:click="createUser" class="inline-flex items-center gap-2">
                        <i class="fas fa-user-plus mr-2"></i>
                        <span class="hidden md:inline">Créer un nouvel utilisateur</span>
                    </flux:button>
                @endcan
            </div>
        </div>
        <flux:separator variant="subtle" />

        <div class="mt-6 overflow-x-auto">
            <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Nom Complet</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Rôle</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200 dark:divide-neutral-700 dark:bg-neutral-900">
                    @forelse ($users as $index => $user)
                        <tr>
                            <td class="px-4 py-2 text-sm text-white">
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800"
                                            >
                                                {{ $user->initials() }}
                                            </span>
                                        </span>
        
                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ $user->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 text-xs rounded-full bg-neutral-700 text-white mr-1">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 text-sm text-neutral-300">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-sm">
                                <div class="flex flex-row gap-2">
                                    @php $isAdmin = auth()->user()->hasRole('Administrateur'); @endphp
                                    @if(!$user->hasRole('Administrateur') || $isAdmin)
                                        @can('modifier utilisateur')
                                        <x-custom-tooltip text="Modifier l'utilisateur">
                                        <button
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="editUser({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="editUser({{ $user->id }})"
                                        >
                                            <i class="fas fa-pen"></i>
                                           
                                            <span wire:loading wire:target="editUser({{ $user->id }})" class="inline-flex items-center">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                        </x-custom-tooltip>
                                        @endcan
                                        @can('supprimer utilisateur')
                                        <x-custom-tooltip text="Supprimer l'utilisateur">
                                        <button
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2 ml-2"
                                            wire:click="confirmDeleteUser({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDeleteUser({{ $user->id }})"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                            
                                            <span wire:loading wire:target="confirmDeleteUser({{ $user->id }})" class="inline-flex items-center">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                        </x-custom-tooltip>
                                        @endcan
                                    @else
                                        <span class="inline-flex items-center gap-1 text-neutral-400 cursor-not-allowed" title="Seul un administrateur peut modifier ou supprimer cet utilisateur.">
                                            <i class="fas fa-lock"></i>
                                            <span class="hidden md:inline">{{ __('Actions protégées') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <svg class="w-12 h-12 text-neutral-400 dark:text-neutral-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Aucun utilisateur trouvé</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Commencez par créer votre premier utilisateur</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Bottom Section with Roles Cards and Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Roles Cards Section - 8 columns -->
        <div class="lg:col-span-8">
            <div class="rounded-xl bg-neutral-900 p-6 shadow">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <h2 class="text-xl font-bold text-white">Rôles</h2>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="roleSearch"
                                class="block w-full pl-10 pr-10 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-neutral-700 placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm"
                                placeholder="{{ __('Rechercher un rôle...') }}"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <div wire:loading wire:target="roleSearch" class="animate-spin h-5 w-5 text-teal-500">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        @can('créer rôle')
                            <flux:button variant="primary" wire:click="createRole" class="inline-flex items-center gap-2">
                                <i class="fas fa-user-shield mr-2"></i>
                                <span class="hidden md:inline">Créer un nouveau rôle</span>
                            </flux:button>
                        @endcan
                    </div>
                </div>
                <flux:separator variant="subtle" />
                
                <!-- Roles Cards Grid -->
                <div class="mt-6 space-y-6" style="max-height: 600px; overflow-y: auto;" x-data="{ get isScrollable() { return $el.scrollHeight > $el.clientHeight } }">
                    @forelse ($roles as $role)
                        <div class="w-full rounded-lg border bg-neutral-800 border-neutral-800 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all duration-200 hover:border-teal-500">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-white">{{ $role->name }}</h3>
                                    <span class="ml-1 px-2 py-1 rounded-full bg-teal-700 text-white text-xs font-semibold">
                                        {{ $role->permissions->count() }} permission{{ $role->permissions->count() > 1 ? 's' : '' }}
                                    </span>
                                </div>
                                {{-- <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="px-3 py-1 rounded-full bg-neutral-700 text-neutral-200 text-xs font-medium">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="px-3 py-1 rounded-full bg-teal-600 text-white text-xs font-medium">
                                            +{{ $role->permissions->count() - 3 }} autres
                                        </span>
                                    @endif
                                </div> --}}
                            </div>
                            <div class="flex flex-row gap-2 items-end md:items-center">
                                @if($role->name !== 'Administrateur')
                                    @can('voir rôles')
                                    <button
                                        class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 hover:bg-teal-100 dark:hover:bg-teal-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="showRolePermissions({{ $role->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="showRolePermissions({{ $role->id }})"
                                    >
                                        <i class="fas fa-eye"></i>
                                        <span class="hidden md:inline">{{ __('Permissions') }}</span>
                                        <span wire:loading wire:target="showRolePermissions({{ $role->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    @endcan
                                    @can('modifier rôle')
                                    <button
                                        class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="editRole({{ $role->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="editRole({{ $role->id }})"
                                    >
                                        <i class="fas fa-pen"></i>
                                        <span class="hidden md:inline">{{ __('Modifier') }}</span>
                                        <span wire:loading wire:target="editRole({{ $role->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    @endcan
                                    @can('supprimer rôle')
                                    <button
                                        class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="confirmDeleteRole({{ $role->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmDeleteRole({{ $role->id }})"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="hidden md:inline">{{ __('Supprimer') }}</span>
                                        <span wire:loading wire:target="confirmDeleteRole({{ $role->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    @endcan
                                @else
                                    <span class="px-3 py-1 text-xs font-medium rounded-md bg-neutral-50 dark:bg-neutral-900/30 text-neutral-500 dark:text-neutral-400 hidden md:inline">
                                        <i class="fas fa-lock"></i>
                                        {{ __('Protégé') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-neutral-400">Aucun rôle trouvé</h3>
                                <p class="mt-1 text-sm text-neutral-500">Commencez par créer votre premier rôle.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Activity Section - 4 columns -->
        <div class="lg:col-span-4">
            <div class="rounded-xl p-6 bg-neutral-900 shadow">
                <h2 class="text-xl font-bold mb-4 text-white">Activité Récente</h2>
                <flux:separator variant="subtle" />

                <div class="mt-6 space-y-4 max-h-[400px] overflow-y-auto">
                    @forelse ($recentLogins as $user)
                        <div class="flex items-center gap-3 p-3 bg-neutral-800 rounded-lg border border-neutral-700 hover:border-teal-500 transition-colors duration-200">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-teal-600 text-white font-bold text-sm shadow-lg">
                                    {{ $user->initials() }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-white text-sm truncate">{{ $user->name }}</div>
                                <div class="text-xs text-neutral-400 truncate">{{ $user->email }}</div>
                            </div>
                            <div class="flex-shrink-0 flex flex-col gap-1 items-end">
                                <div class="flex items-center gap-1 text-xs text-teal-400 font-medium">
                                    <i class="fas fa-sign-in-alt"></i>
                                    @if($user->last_login_at)
                                        <span>
                                            {{ $user->last_login_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-neutral-400">Jamais connecté</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 text-xs font-medium mt-1">
                                    <i class="fas fa-sign-out-alt"></i>
                                    @if($user->log_out_at)
                                        <span class="text-neutral-400">{{ $user->log_out_at->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="text-red-500 font-semibold">Jamais déconnecté</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-neutral-400">Aucune activité récente</h3>
                            <p class="mt-1 text-sm text-neutral-500">Les connexions récentes apparaîtront ici.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Users Modals --}}
    {{-- créer utilisateur Modal --}}
    <flux:modal name="create-user" class="md:w-[600px]">
        <form wire:submit.prevent="storeUser">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Créer un nouvel utilisateur') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Remplissez le formulaire pour créer un nouvel utilisateur') }}
                    </flux:text>
                </div>
    
                <div>
                    <div class="flex items-center mb-1">
                        <label for="name" class="mr-2">{{ __('Nom Complet') }}</label>
                        <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                    </div>
                    <flux:input 
                        wire:model.defer="name" 
                        placeholder="Entrez le nom complet de l'utilisateur"
                    />
                    @error('name')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
    
                <div>
                    <div class="flex items-center mb-1">
                        <label for="email" class="mr-2">{{ __('Email') }}</label>
                        <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                    </div>
                    <flux:input 
                        wire:model.defer="email" 
                        placeholder="Entrez l'adresse email"
                    />
                    @error('email')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="relative" x-data="{ showPassword: false }">
                    <div class="flex items-center mb-1">
                        <label for="password" class="mr-2">{{ __('Mot de passe') }}</label>
                        <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                    </div>
                
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        wire:model.defer="password"
                        placeholder="Entrez un mot de passe ou générez-en un"
                        class="block w-full pl-4 pr-20 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-[#3c3c3c] placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm transition"
                        autocomplete="new-password"
                        id="password"
                    />
                
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
                        <!-- Password generator button -->
                        <x-custom-tooltip text="Générer un mot de passe">
                            <button 
                                type="button"
                                wire:click="generatePassword"
                                class="p-1 mt-8 cursor-pointer text-neutral-400 hover:text-teal-500 transition-colors focus:outline-none"
                                tabindex="-1"
                            >
                                <span wire:loading.remove wire:target="generatePassword">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </span>
                                <span wire:loading wire:target="generatePassword" class="inline-flex mt-1 items-center">
                                    <svg class="animate-spin h-5 w-5 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </x-custom-tooltip>
                
                        <!-- Password visibility toggle -->
                        <x-custom-tooltip text="Masquer/Afficher le mot de passe">
                            <button
                                type="button"
                                class="p-1 mt-8 cursor-pointer text-neutral-400 hover:text-teal-500 transition-colors focus:outline-none"
                                @click="showPassword = !showPassword"
                                tabindex="-1"
                            >
                                <template x-if="!showPassword">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.967 9.967 0 012.302-4.021" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 3l18 18" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.94 17.94A9.977 9.977 0 0021 12c-1.274-4.057-5.064-7-9.542-7a9.953 9.953 0 00-4.846 1.261" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.88 9.88a3 3 0 104.24 4.24" />
                                    </svg>
                                </template>                                
                            </button>
                        </x-custom-tooltip>
                    </div>
                
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>                
    
                <div>
                    <div class="flex items-center mb-1">
                        <label for="selectedRole" class="mr-2">{{ __('Rôle') }}</label>
                        <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                    </div>
                    <flux:select wire:model.defer="selectedRole" id="selectedRole">
                        <option value="">{{ __('-- Sélectionner un rôle --') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('selectedRole')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
    
                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ __('Créer') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- modifier utilisateur Modal --}}
    <flux:modal name="edit-user" class="md:w-[600px]">
        <form wire:submit.prevent="updateUser">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Modifier l\'utilisateur') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Mettre à jour les détails de l\'utilisateur') }}
                    </flux:text>
                </div>

                <flux:input 
                    label="Nom Complet" 
                    wire:model.defer="name" 
                    error="{{ $errors->first('name') }}"
                    placeholder="Entrez le nom Complet de l'utilisateur"
                />

                <flux:input 
                    label="Email" 
                    wire:model.defer="email" 
                    error="{{ $errors->first('email') }}"
                    placeholder="Entrez l'adresse email"
                />

                <div>
                    <div class="flex items-center mb-1">
                        <label for="edit-password" class="mr-2">{{ __('Mot de passe (optionnel)') }}</label>
                    </div>
                    <div class="relative" x-data="{ showPassword: false }">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            wire:model.defer="password"
                            id="edit-password"
                            class="block w-full pl-4 pr-20 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-[#3c3c3c] placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm transition"
                            placeholder="Laissez vide pour conserver le mot de passe actuel"
                            autocomplete="new-password"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <x-custom-tooltip text="Masquer/Afficher le mot de passe">
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="p-1 cursor-pointer text-neutral-400 hover:text-teal-500 transition-colors focus:outline-none"
                                    tabindex="-1"
                                >
                                    <template x-if="!showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.967 9.967 0 012.302-4.021" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.94 17.94A9.977 9.977 0 0021 12c-1.274-4.057-5.064-7-9.542-7a9.953 9.953 0 00-4.846 1.261" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 9.88a3 3 0 104.24 4.24" />
                                        </svg>
                                    </template>
                                </button>
                            </x-custom-tooltip>
                        </div>
                        @error('password')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block font-medium mb-2">{{ __('Rôle') }}</label>
                    <flux:select wire:model.defer="selectedRole">
                        <option value="">{{ __('-- Sélectionner --') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ __('Mettre à jour') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-user" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Supprimer l\'utilisateur') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action ne peut pas être annulée.') }}
                </flux:text>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button 
                    type="button" 
                    variant="danger" 
                    wire:click="deleteUser"
                >
                    {{ __('Supprimer') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    {{-- Users Modals --}}

    {{-- Roles Modals --}}
    {{-- créer rôle Modal --}}
    <flux:modal name="create-role" class="md:w-[600px]">
        <form wire:submit.prevent="storeRole">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Créer un nouveau rôle') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Remplissez le formulaire pour créer un nouveau rôle') }}
                    </flux:text>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <label for="roleName" class="mr-2">{{ __('Nom du rôle') }}</label>
                        <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                    </div>
                    <flux:input 
                        wire:model.defer="roleName" 
                        placeholder="Entrez le nom du rôle"
                    />
                    @error('roleName')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2">{{ __('Permissions') }}</label>
                    {{-- Improved permissions UI: collapsible, grouped, bulk-select --}}
                    @foreach ($permissions->groupBy('group') as $group => $groupPermissions)
                        <div x-data="{ open: true }" class="mb-4 rounded-lg p-4 bg-neutral-50 dark:bg-neutral-700">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold capitalize">{{ $group }}</h3>
                                <div class="flex items-center gap-2">
                                    <!-- Afficher/Masquer -->
                                    <button type="button" @click="open = !open" class="text-xs cursor-pointer px-3 py-1 font-medium rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors inline-flex items-center gap-1">
                                        <i class="fas" :class="open ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        <span class="hidden md:inline" x-text="open ? '{{ __('Masquer') }}' : '{{ __('Afficher') }}'"></span>
                                    </button>
                                    <!-- Tout cocher -->
                                    <button type="button"
                                        wire:click="toggleGroupPermissions('{{ $group }}', true)"
                                        class="text-xs cursor-pointer px-3 py-1 font-medium rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-check-double"></i>
                                        <span class="hidden md:inline">{{ __('Tout cocher') }}</span>
                                    </button>
                                    <!-- Tout décocher -->
                                    <button type="button"
                                        wire:click="toggleGroupPermissions('{{ $group }}', false)"
                                        class="text-xs cursor-pointer px-3 py-1 font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-xmark"></i>
                                        <span class="hidden md:inline">{{ __('Tout décocher') }}</span>
                                    </button>
                                </div>
                            </div>
                            <div x-show="open" class="grid grid-cols-2 gap-2">
                                @foreach ($groupPermissions as $permission)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox"
                                            wire:model.defer="selectedPermissions"
                                            value="{{ $permission->name }}"
                                            class="form-checkbox cursor-pointer accent-teal-600 focus:ring-teal-500"
                                        >
                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ __('Créer le rôle') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- modifier rôle Modal --}}
    <flux:modal name="edit-role" class="md:w-[600px]">
        <form wire:submit.prevent="updateRole">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Modifier le rôle') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Mettre à jour les détails du rôle') }}
                    </flux:text>
                </div>

                <flux:input 
                    label="Nom du rôle" 
                    wire:model.defer="roleName" 
                    error="{{ $errors->first('roleName') }}"
                />

                <div>
                    <label class="block font-medium mb-2">{{ __('Permissions') }}</label>
                    {{-- Improved permissions UI: collapsible, grouped, bulk-select --}}
                    @foreach ($permissions->groupBy('group') as $group => $groupPermissions)
                        <div x-data="{ open: true }" class="mb-4 rounded-lg p-4 bg-neutral-50 dark:bg-neutral-700">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold capitalize">{{ $group }}</h3>
                                <div class="flex items-center gap-2">
                                    <!-- Afficher/Masquer -->
                                    <button type="button" @click="open = !open" class="text-xs cursor-pointer px-3 py-1 font-medium rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors inline-flex items-center gap-1">
                                        <i class="fas" :class="open ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        <span class="hidden md:inline" x-text="open ? '{{ __('Masquer') }}' : '{{ __('Afficher') }}'"></span>
                                    </button>
                                    <!-- Tout cocher -->
                                    <button type="button"
                                        wire:click="toggleGroupPermissions('{{ $group }}', true)"
                                        class="text-xs cursor-pointer px-3 py-1 font-medium rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-check-double"></i>
                                        <span class="hidden md:inline">{{ __('Tout cocher') }}</span>
                                    </button>
                                    <!-- Tout décocher -->
                                    <button type="button"
                                        wire:click="toggleGroupPermissions('{{ $group }}', false)"
                                        class="text-xs cursor-pointer px-3 py-1 font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-xmark"></i>
                                        <span class="hidden md:inline">{{ __('Tout décocher') }}</span>
                                    </button>
                                </div>
                            </div>
                            <div x-show="open" class="grid grid-cols-2 gap-2">
                                @foreach ($groupPermissions as $permission)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox"
                                            wire:model.defer="selectedPermissions"
                                            value="{{ $permission->name }}"
                                            class="form-checkbox cursor-pointer accent-teal-600 focus:ring-teal-500"
                                        >
                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ __('Mettre à jour le rôle') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-role" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Supprimer le rôle') }}
                </flux:heading>
                @if($isRoleInUse)
                    <div class="mt-2">
                        <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                        {{ __('Ce rôle ne peut pas être supprimé') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <p>
                                            {{ __('Ce rôle est actuellement attribué à un ou plusieurs utilisateurs. Veuillez réassigner ces utilisateurs à un autre rôle avant de supprimer celui-ci.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <flux:text class="mt-2">
                        {{ __('Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action ne peut pas être annulée.') }}
                    </flux:text>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
                @if(!$isRoleInUse)
                    <flux:button 
                        type="button" 
                        variant="danger" 
                        wire:click="deleteRole"
                    >
                        {{ __('Supprimer le rôle') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>

    {{-- Role Permissions Modal --}}
    <flux:modal name="role-permissions" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Permissions du rôle') }}: {{ $selectedRoleForPermissions?->name ?? '' }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Liste des permissions attribuées à ce rôle') }}
                </flux:text>
            </div>

            @if($selectedRoleForPermissions)
                <div class="space-y-4">
                    @foreach($selectedRoleForPermissions->permissions->groupBy('group') as $group => $groupPermissions)
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-neutral-700 dark:text-neutral-300 capitalize mb-3">{{ $group }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($groupPermissions as $permission)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-shrink-0">
                                            <svg class="h-4 w-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    @if($selectedRoleForPermissions->permissions->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-neutral-400">Aucune permission</h3>
                            <p class="mt-1 text-sm text-neutral-500">Ce rôle n'a aucune permission attribuée.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </flux:modal>
    {{-- Roles Modals --}}

</div>
