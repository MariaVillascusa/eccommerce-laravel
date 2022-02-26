<div class="mb-2 flex ">
    <div class="flex flex-col w-2/12">
        <x-jet-button wire:click="clearFilters()" class="my-2 w-3/4">Limpiar filtros</x-jet-button>

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

    <div class="w-3/12 mx-10">
        <div>
            <h3 class="font-bold my-2">CATEGORÍAS</h3>
            <select wire:model="category" name="category" id="category">
                <option value="">Selecciona una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <div>
                <h3 class="font-bold my-2">SUBCATEGORÍAS</h3>
                <select wire:model="subcategory" name="subcategory" id="subcategory">
                    <option value="">Selecciona una subcategoría</option>
                    @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="w-3/12 mx-10">
        <div>
            <h3 class="font-bold my-2">MARCAS</h3>
            <select wire:model="brand" name="brand" id="brand">
                <option value="">Selecciona una categoría</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>

            <div class="flex flex-col justify-center">
                <h3 class="font-bold my-2">STOCK</h3>
                <div class="form-check">
                    @foreach ($stockList as $key => $value)
                        <div class="form-check form-check-inline">
                            <input wire:model="stock" type="radio" class="form-check-input" name="stock" id="stock_{{ $key ?: 'all' }}"
                                value="{{ $key }}">

                            <label class="form-check-label"
                                for="stock_{{ $key ?: 'all' }}">De {{ $value[0] }} a {{ $value[1] }}</label>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>



</div>
