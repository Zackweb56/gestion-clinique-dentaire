<x-layouts.app :title="__('Dossiers Médicaux')">
    <div class="relative mb-6 w-full max-w-7xl mx-auto">
        <div class="mb-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- left side -->
            <div class="w-full md:w-auto">
                <flux:heading size="xl" level="1" class="text-center md:text-left">
                    {{ __('Gestion des Dossiers Médicaux') }}
                </flux:heading>
                <flux:subheading size="lg" class="mb-6 md:mb-0">
                    {{ __('Gérer les dossiers médicaux des patients') }}
                </flux:subheading>
            </div>
    
            <!-- right side -->
            <div class="w-full md:w-auto flex justify-center md:justify-end">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item href="#">{{ __('Tableau de bord') }}</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item href="#">{{ __('Patients') }}</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>{{ __('Dossiers Médicaux') }}</flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>
        </div>
    
        <flux:separator variant="subtle" />
    </div>
      
    <!-- display component of livewire -->
    @livewire('patients.medical-files')
    
</x-layouts.app>