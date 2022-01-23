<div x-data>
    <p class="text-xl text-gray-700">Color:</p>
    <select dusk="color-select" wire:model="color_id" class="w-full form-control">
        <option value="" selected disabled>Seleccionar un color</option>
        @foreach ($colors as $color)
            <option value="{{ $color->id }}">{{ __(ucfirst($color->name)) }}</option>
        @endforeach
    </select>
    <div class="flex mt-4">
        <div class="mr-4">
            <x-jet-secondary-button disabled x-bind:disabled="$wire.qty <= 1" wire:loading.attr="disabled"
                wire:target="decrement" wire:click="decrement" dusk="decrement-button">
                -
            </x-jet-secondary-button>
            <span class="mx-2 text-gray-700">{{ $qty }}</span>
            <x-jet-secondary-button x-bind:disabled="$wire.qty >= $wire.quantity" wire:loading.attr="disabled"
                wire:target="increment" wire:click="increment" dusk="increment-button">
                +
            </x-jet-secondary-button>
        </div>
        <div class="flex-1">
            <x-button
            x-bind:disabled="$wire.qty > $wire.quantity"
            x-bind:disabled="!$wire.quantity"
            wire:click="addItem"
            wire:loading.attr="disabled"
            wire.target="addItem"
            class="w-full" color="orange" dusk="addItem-button">
                Agregar al carrito de compras
            </x-button>
        </div>
    </div>
</div>
