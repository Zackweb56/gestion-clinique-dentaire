@props(['text', 'position' => 'left'])

<div class="relative group" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false">
    {{ $slot }}
    
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-[9999] px-3 py-2 text-sm font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-lg shadow-lg border border-neutral-700 dark:border-neutral-600 whitespace-nowrap pointer-events-none
        @if($position === 'top') bottom-full left-1/2 transform -translate-x-1/2 mb-2
        @elseif($position === 'bottom') top-full left-1/2 transform -translate-x-1/2 mt-2
        @elseif($position === 'left') right-full top-1/2 transform -translate-y-1/2 mr-2
        @elseif($position === 'right') left-full top-1/2 transform -translate-y-1/2 ml-2
        @endif"
        style="display: none;"
    >
        {{ $text }}
        <div class="absolute w-2 h-2 bg-neutral-900 dark:bg-neutral-800 border border-neutral-700 dark:border-neutral-600 transform rotate-45
        @if($position === 'top') top-full left-1/2 -translate-x-1/2 -mt-1
        @elseif($position === 'bottom') bottom-full left-1/2 -translate-x-1/2 -mb-1
        @elseif($position === 'left') left-full top-1/2 -translate-y-1/2 -ml-1
        @elseif($position === 'right') right-full top-1/2 -translate-y-1/2 -mr-1
        @endif"></div>
    </div>
</div> 