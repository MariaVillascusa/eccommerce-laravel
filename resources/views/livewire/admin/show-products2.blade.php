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

        <div class="px-6 py-4">
            <x-jet-input class="w-full" wire:model="search" type="text"
                placeholder="Introduzca el nombre del producto a buscar" />
        </div>

        @if ($products->count())
            <div
                x-data="{columns: { image: true, name: true, price: true, category: true, brand: true, stock: true, colors: false, sizes: false, created_at: false, state: false  }, showColumnFilters: false, showFilters: false}">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <div class="flex items-center py-3">
                            <div class="mx-10">
                                <span>Número de productos mostrados</span>
                                <select name="page-select" wire:model="productsPerPage" class="form-control w-20">
                                    <option value="5" selected>5</option>
                                    <option value="10" selected>10</option>
                                    <option value="15" selected>15</option>
                                </select>

                            </div>
                            <div class="relative px-10 cursor-pointer"
                                x-on:click="showColumnFilters = !showColumnFilters">
                                <i class="fas fa-columns"></i> Columnas mostradas
                                <div class="absolute bg-white rounded border shadow-md p-2 px-4 font-normal z-99"
                                    x-show="showColumnFilters" x-on:click.away="showColumnFilters = false">
                                    <div class="mb-2">
                                    </div>
                                    <template x-for="column in Object.keys(columns)" :key="column">
                                        <label class="flex items-center">
                                            <input type="checkbox" x-on:click="columns[column] = !columns[column]"
                                                :checked="columns[column]" class="mr-1"> <span x-text="column"
                                                class="capitalize"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                        <tr>
                            <th scope="col" x-show="columns.image">
                                <span class="sr-only">Imagen</span>
                            </th>
                            <th x-show="columns.name" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre
                            </th>
                            <th x-show="columns.price" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio
                            </th>
                            <th x-show="columns.category" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th x-show="columns.brand" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marca
                            </th>
                            <th x-show="columns.stock" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th x-show="columns.colors" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Colores
                            </th>
                            <th x-show="columns.sizes" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tallas
                            </th>
                            <th x-show="columns.created_at" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Creación
                            </th>
                            <th x-show="columns.state" scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>

                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Editar</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr>
                                <td x-show="columns.image" class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex-shrink-0 h-10 w-10 object-cover">
                                        <img class="h-10 w-10 rounded-full"
                                            src="{{ $product->images->count() ? Storage::url($product->images->first()->url) : 'img/default.jpg' }}"
                                            alt="">
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap" x-show="columns.name">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $product->name }}
                                    </div>
                                </td>

                                <td x-show="columns.price" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->price }} &euro;
                                </td>

                                <td x-show="columns.category" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->subcategory->category->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $product->subcategory->name }}</div>
                                </td>

                                <td x-show="columns.brand" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->brand->name }}</div>
                                </td>

                                <td x-show="columns.stock" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ !$product->subcategory->color
                                            ? $product->quantity
                                            : (!$product->subcategory->size
                                                ? array_sum(
                                                    $product->colors->pluck('pivot')->pluck('quantity')->all(),
                                                )
                                                : array_sum(
                                                    $product->sizes->pluck('colors')->collapse()->pluck('pivot')->pluck('quantity')->all(),
                                                )) }}
                                    </div>
                                </td>

                                <td x-show="columns.colors" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ !$product->subcategory->color
                                            ? '-'
                                            : (!$product->subcategory->size
                                                ? implode(', ', $product->colors->pluck('name')->all())
                                                : implode(
                                                    ', ',
                                                    array_unique(
                                                        $product->sizes->pluck('colors')->collapse()->pluck('name')->all(),
                                                    ),
                                                )) }}
                                    </div>
                                </td>

                                <td x-show="columns.sizes" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ !$product->subcategory->size ? '-' : implode(', ', $product->sizes->pluck('name')->all()) }}
                                    </div>
                                </td>

                                <td x-show="columns.created_at"
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->created_at }}
                                </td>

                                <td x-show="columns.state" class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $product->status == 1 ? 'red' : 'green' }}-100 text-{{ $product->status == 1 ? 'red' : 'green' }}-800">
                                        {{ $product->status == 1 ? 'Borrador' : 'Publicado' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                </td>
                            </tr>
                        @endforeach

                        <!-- More people... -->
                    </tbody>
                </table>
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
