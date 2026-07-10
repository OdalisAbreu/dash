<x-filament-panels::page>
    <div class="flex items-center justify-between gap-3">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Así se ve el dashboard público que consultan el resto de usuarios.
        </p>
        <a href="{{ $this->getDashboardUrl() }}" target="_blank" rel="noopener"
           class="fi-btn inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            Abrir en pestaña nueva
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700" style="height: calc(100vh - 220px);">
        <iframe src="{{ $this->getDashboardUrl() }}" title="Vista previa del dashboard"
                class="h-full w-full" loading="lazy"></iframe>
    </div>
</x-filament-panels::page>
