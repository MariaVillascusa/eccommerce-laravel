<div class="mb-2 flex ">
    <div class="flex flex-col w-2/12 mx-10">
        <x-jet-button wire:click="clearFilters()" class="my-2 w-40 text-center">Limpiar filtros</x-jet-button>

        <div class="sidebar__block">
            <h3 class="sidebar__title font-bold">PRECIO</h3>
            <div class="block__content">
                <div class="block__price">
                    <div id="slide-price" wire:ignore class="my-4"></div>
                </div>
                <div class="block__input flex justify-between items-center">
                    <input type="text" class="w-5/12 text-center rounded-md h-8" id="input-with-keypress-0"
                        wire:model.debounce.1000ms="min_price">
                    <span>-</span>
                    <input type="text" class="w-5/12 text-center rounded-md h-8" id="input-with-keypress-1"
                        wire:model.debounce.1000ms="max_price">
                </div>
            </div>
        </div>
    </div>

    <div class="w-2/12">
        <div>
            <h3 class="font-bold my-2">CATEGORÍAS</h3>
            <select wire:model="category" name="category" id="category" class="h-10">
                <option value="">Selecciona categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <div>
                <h3 class="font-bold my-2">SUBCATEGORÍAS</h3>
                <select wire:model="subcategory" name="subcategory" id="subcategory" class="h-10">
                    <option value="">Selecciona subcategoría</option>
                    @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <h3 class="font-bold my-2">MARCAS</h3>
                <select wire:model="brand" name="brand" id="brand" class="h-10">
                    <option value="">Selecciona marca</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="w-1/12 ml-5">
        <div class="flex flex-col justify-center">
            <h3 class="font-bold my-2">STOCK</h3>
            <div class="form-check">
                @foreach ($stockList as $key => $value)
                    <div class="form-check form-check-inline">
                        <input wire:model="stock" type="radio" class="form-check-input" name="stock"
                            id="stock_{{ $key ?: 'all' }}" value="{{ $key }}">

                        <label class="form-check-label" for="stock_{{ $key ?: 'all' }}">{{ $value[0] }} -
                            {{ $value[1] }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex flex-col justify-center">
            <h3 class="font-bold my-2">STATUS</h3>
            <div class="form-check">
                @foreach ($statusList as $key => $value)
                    <div class="form-check form-check-inline">
                        <input wire:model="status" type="radio" class="form-check-input" name="status"
                            id="status_{{ $key ?: 'all' }}" value="{{ $value }}">

                        <label class="form-check-label" for="status_{{ $key ?: 'all' }}">{{ $key }}</label>
                    </div>
                @endforeach
            </div>

        </div>

    </div>
    <div class="w-2/12">

        <div>
            <h3 class="font-bold my-2 ">COLOR</h3>
            <select wire:model="color" name="color" id="color" class="h-10">
                <option value="">Selecciona un color</option>
                @foreach ($colors as $color)
                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <h3 class="font-bold my-2">TALLA</h3>
            <select wire:model="size" name="size" id="size" class="h-10">
                <option value="">Selecciona una talla</option>
                @foreach ($sizes as $size)
                    <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="w-3/12">
        <h3 class="font-bold my-2">FECHA CREACIÓN</h3>
        <div>
            <div class="flex items-center space-x-1 mb-2">
                <label class="" for="from">Desde: &nbsp;</label>
                <x-jet-input type="date" wire:model="from" class="border border-gray-400 rounded-lg" id="from"
                    placeholder='DD/MM/YYYY'></x-jet-input>
            </div>
            <div class="flex items-center space-x-2">
                <label class="" for="to">Hasta: &nbsp;</label>
                <x-jet-input type="date" wire:model="to" class="border border-gray-400 rounded-lg" id="to"
                    placeholder='DD/MM/YYYY'></x-jet-input>
            </div>
        </div>
    </div>
</div>
