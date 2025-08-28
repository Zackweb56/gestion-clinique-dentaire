<x-layouts.app :title="__('Services')">
    <div class="relative mb-6 w-full max-w-7xl mx-auto">
        <div class=" mb-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- left side-->
            <div class="w-full md:w-auto">
                <flux:heading size="xl" level="1" class="text-center md:text-left">
                    {{ __('Gesion des Services') }}
                </flux:heading>
                <flux:subheading size="lg" class="mb-6 md:mb-0">{{ __('Gérer vos Services') }}</flux:subheading>
            </div>
    
            <!-- right side-->
            <div class="w-full md:w-auto flex justify-center md:justify-end">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item href="#">{{ __('Tableau de bord') }}</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item href="#">{{ __('Services') }}</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>{{ __('Liste des Services') }}</flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>
        </div>
    
        <flux:separator variant="subtle" />
    </div>
      
    <!-- display users component -->
    @livewire('services.services')
    
</x-layouts.app>