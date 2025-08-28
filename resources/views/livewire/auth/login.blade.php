<div class="flex flex-col gap-6">
    {{-- <x-auth-header :title="'Connectez-vous à votre compte'" :description="'Veuillez saisir votre adresse e-mail et votre mot de passe pour vous connecter.'" /> --}}

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="'Adresse e-mail'"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@exemple.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="'Mot de passe'"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="'Mot de passe'"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ 'Mot de passe oublié ?' }}
                </flux:link>
            @endif
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ 'Se connecter' }}</flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ "Vous n'avez pas de compte ?" }}
            <flux:link :href="route('register')" wire:navigate>{{ 'Créer un compte' }}</flux:link>
        </div>
    @endif
</div>
