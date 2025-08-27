@props(['items' => []])

<nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
    <a href="{{ route('produk') }}" class="hover:text-teal-600 transition-colors">
        <i class="fas fa-home"></i>
    </a>
    
    @foreach($items as $index => $item)
        <span class="text-gray-400">
            <i class="fas fa-chevron-right text-xs"></i>
        </span>
        
        @if($index === count($items) - 1)
            <span class="text-gray-800 font-medium">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}" class="hover:text-teal-600 transition-colors">
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>
