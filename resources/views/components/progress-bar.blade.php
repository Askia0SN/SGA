@props(['progression' => 0, 'etape' => 1])

<div class="mb-8">
    <div class="flex justify-between text-sm font-medium text-gray-600 mb-2">
        <span class="{{ $etape >= 1 ? 'text-indigo-600' : '' }}">Identité</span>
        <span class="{{ $etape >= 2 ? 'text-indigo-600' : '' }}">Formation</span>
        <span class="{{ $etape >= 3 ? 'text-indigo-600' : '' }}">Documents</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $progression }}%"></div>
    </div>
    <p class="text-right text-xs text-gray-500 mt-1">{{ $progression }}% complété</p>
</div>
