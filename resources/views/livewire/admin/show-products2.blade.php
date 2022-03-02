<div>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-600 leading-tight">
                Lista de productos 2
            </h2>

            <x-button-link class="ml-auto" href="{{ route('admin.products.create') }}">
                Agregar producto
            </x-button-link>
        </div>
    </x-slot>

    <x-table-responsive>
        <div x-data="{showFilters: false, showView: false}">
            <div class="px-6 py-4 flex">

                <x-jet-input class="w-2/3" wire:model="search" type="text"
                    placeholder="Introduzca el nombre del producto a buscar" />
                <div class="mx-2 cursor-pointer bg:transparent rounded-md dark:text-gray-800 sm:flex hover:bg-white hover:shadow-md focus:ring focus:ring-offset-2 focus:ring-gray-800 py-4 px-6 flex text-base leading-4 font-normal justify-center items-center"
                    x-on:click="showFilters = !showFilters">
                    <svg class="mr-2" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M6 12C7.10457 12 8 11.1046 8 10C8 8.89543 7.10457 8 6 8C4.89543 8 4 8.89543 4 10C4 11.1046 4.89543 12 6 12Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6 4V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M6 12V20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M12 18C13.1046 18 14 17.1046 14 16C14 14.8954 13.1046 14 12 14C10.8954 14 10 14.8954 10 16C10 17.1046 10.8954 18 12 18Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 4V14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 18V20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M18 9C19.1046 9 20 8.10457 20 7C20 5.89543 19.1046 5 18 5C16.8954 5 16 5.89543 16 7C16 8.10457 16.8954 9 18 9Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M18 4V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M18 9V20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>Filtros
                </div>
                <div class="mx-2 cursor-pointer bg:transparent rounded-md dark:text-gray-800 sm:flex hover:bg-white hover:shadow-md focus:ring focus:ring-offset-2 focus:ring-gray-800 py-4 px-6 flex text-base leading-4 font-normal justify-center items-center"
                    x-on:click="showView = !showView">
                    <i class="fa-solid fa-eye"></i> &nbsp;Vista
                </div>
            </div>
            <div class="bg-white rounded border shadow-md p-2 px-4 font-normal z-99" x-show="showFilters">
                @include('livewire.admin.partials._filters')
            </div>

            @if ($products->count())
                <div x-data="{showColumnFilter: false}">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <div class="flex items-center py-3" x-show=showView>
                                <div class="mx-10">
                                    <span>Número de productos mostrados</span>
                                    <select name="page-select" wire:model="productsPerPage" class="form-control w-20">
                                        <option value="5" selected>5</option>
                                        <option value="10" selected>10</option>
                                        <option value="15" selected>15</option>
                                    </select>
                                </div>
                                <div class="relative px-10 cursor-pointer"
                                    x-on:click="showColumnFilter = !showColumnFilter">
                                    <i class="fas fa-columns"></i> Columnas mostradas
                                    <div class="absolute bg-white w-2/3 p-3 rounded border shadow-md z-99"
                                        x-show="showColumnFilter" x-on:click.away="showColumnFilter = false">
                                        <div>
                                            @foreach ($columns as $column)
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" wire:model="selectedColumns"
                                                        value="{{ $column }}">
                                                    <span class="ml-1">{{ $column }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <tr>
                                @if ($this->showColumn('Imagen'))
                                    <th scope="col">
                                        <span class="sr-only">Imagen</span>
                                    </th>
                                @endif
                                @if ($this->showColumn('Nombre'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('name')" role="button" href="#">
                                            Nombre</a>
                                @endif
                                </th>
                                @if ($this->showColumn('Precio'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('price')" role="button" href="#">
                                            Precio</a>
                                @endif
                                </th>
                                @if ($this->showColumn('Categoría'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('categories.name')" role="button" href="#">
                                            Categoría</a>
                                @endif
                                </th>
                                @if ($this->showColumn('Marca'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('brands.name')" role="button" href="#">
                                            Marca</a>
                                @endif
                                </th>
                                @if ($this->showColumn('Stock'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('products.quantity')" role="button" href="#">
                                            Stock</a>
                                @endif
                                </th>
                                @if ($this->showColumn('Colores'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Colores
                                @endif
                                </th>
                                @if ($this->showColumn('Tallas'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tallas
                                @endif
                                </th>
                                @if ($this->showColumn('Fecha'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('products.created_at')" role="button" href="#">
                                            Fecha</a>
                                @endif

                                </th>
                                @if ($this->showColumn('Estado'))
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a wire:click.prevent="sortBy('status')" role="button" href="#">
                                            Estado</a>
                                @endif
                                </th>

                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Editar</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($products as $product)
                                {{-- @if ($product->stock >= config('stock.stock')[$this->stock][0] && $product->stock <= config('stock.stock')[$this->stock][1]) --}}
                                <tr>
                                    @if ($this->showColumn('Imagen'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex-shrink-0 h-10 w-10 object-cover">
                                                <img class="h-10 w-10 rounded-full"
                                                    src="{{ $product->images->count() ? Storage::url($product->images->first()->url) : 'img/default.jpg' }}"
                                                    alt="">
                                            </div>
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Nombre'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $product->name }}
                                            </div>
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Precio'))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->price }} &euro;
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Categoría'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $product->subcategory->category->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $product->subcategory->name }}
                                            </div>
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Marca'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $product->brand->name }}</div>
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Stock'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ !$product->subcategory->color
                                                    ? $product->quantity
                                                    : (!$product->subcategory->size
                                                        ? array_sum(
                                                            $product->colors->pluck('pivot')->pluck('quantity')->all(),
                                                        )
                                                        : '-') }}
                                            </div>
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Colores'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{-- {{ !$product->subcategory->color
                                                        ? '-'
                                                        : (!$product->subcategory->size
                                                            ? implode(', ', $product->colors->pluck('name')->all())
                                                            : implode(
                                                                ', ',
                                                                array_unique(
                                                                    $product->sizes->pluck('colors')->collapse()->pluck('name')->all(),
                                                                ),
                                                            )) }} --}}
                                                @if (!$product->subcategory->color)
                                                    -
                                                @else
                                                    @if (!$product->subcategory->size)
                                                        <ul>
                                                            @foreach ($product->colors as $color)
                                                                <li>
                                                                    {{ $color->name }}:
                                                                    {{ $color->pivot->quantity }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        {{-- {{ implode(
                                                            ', ',
                                                            array_unique(
                                                                $product->sizes->pluck('colors')->collapse()->pluck('name')->all(),
                                                            ),
                                                        ) }} --}}
                                                        <ul>
                                                            @foreach ($product->sizes as $size)
                                                                <li>
                                                                    <span>{{ $size->name }} -</span>
                                                                    @foreach ($size->colors as $color)
                                                                        <span>{{ $color->name }}:</span>
                                                                        <span>{{ $color->pivot->quantity }}</span>
                                                                    @endforeach

                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                @endif
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Tallas'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if (!$product->subcategory->size)
                                                    -
                                                @else
                                                    <ul>
                                                        @foreach ($product->sizes as $size)
                                                            <li>
                                                                {{ $size->name }}:
                                                                {{ array_sum($size->colors->pluck('pivot')->pluck('quantity')->all()) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </div>
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Fecha'))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->created_at }}
                                        </td>
                                    @endif

                                    @if ($this->showColumn('Estado'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $product->status == 1 ? 'red' : 'green' }}-100 text-{{ $product->status == 1 ? 'red' : 'green' }}-800">
                                                {{ $product->status == 1 ? 'Borrador' : 'Publicado' }}
                                            </span>
                                        </td>
                                    @endif

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    </td>
                                </tr>
                                {{-- @endif --}}
                            @endforeach

                            <!-- More people... -->
                        </tbody>
                    </table>
                </div>
        </div>
    @else
        <div class="px-6 py-4">
            No existen productos coincidentes
        </div>
        @endif

        @if ($products->hasPages())
            <div class="px-6 py-4">
                {{ $products->links() }}
            </div>
        @endif

    </x-table-responsive>

    @push('scripts')
        <script>
            window.onload = function() {
                let slider = document.getElementById('slide-price');

                let start_min_price = Math.floor(@this.min_price);
                let start_max_price = Math.ceil(@this.max_price);

                if (slider) {
                    let input0 = document.getElementById('input-with-keypress-0');
                    let input1 = document.getElementById('input-with-keypress-1');
                    let inputs = [input0, input1];

                    noUiSlider.create(slider, {
                        start: [start_min_price, start_max_price],
                        connect: true,
                        range: {
                            'min': start_min_price,
                            'max': start_max_price
                        }
                    });

                    slider.noUiSlider.on('update', function(values, handle) {
                        @this.set('min_price', values[0]);
                        @this.set('max_price', values[1]);

                        inputs[handle].value = values[handle];
                    })
                }
            }
        </script>
    @endpush

    @push('style')
        <style>
            #slide-price {
                height: 10px;
            }

            #slide-price .noUi-connect {
                background: #6466F1;
            }

            #slide-price .noUi-handle {
                height: 18px;
                width: 18px;
                top: -5px;
                right: -9px;
                border-radius: 9px;
            }


            #slide-price .noUi-touch-area::before,
            .noUi-handle-lower::after {
                display: none
            }

        </style>
    @endpush
