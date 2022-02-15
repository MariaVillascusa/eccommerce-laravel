<x-app-layout>
    <div class="container py-8">
        @foreach ($categories as $category)
            <section class="mb-6">
                <div class="flex items-center mb-2">
                    <h1 class="text-lg uppercase font-semibold text-gray-700">
                        {{ $category->name }}
                    </h1>
                    <a href="{{ route('categories.show', $category) }}"
                        class="text-orange-500 hover:text-orange-400 hover:underline ml-2 font-semibold">Ver
                        más</a>
                </div>

                @livewire('category-products', ['category' => $category])
            </section>
        @endforeach
    </div>

    @push('scripts')
        <script>
            window.onload = () => {
                Livewire.on('glider', function(id) {

                    new Glide('.glide-' + id, {
                        type: 'carousel',
                        gap: 15,
                        swipeThreshold: 10,
                        perView: 5,
                        perTouch: 5,
                        breakpoints: {
                            760: {
                                perView: 2
                            },
                            880: {
                                perView: 3
                            },
                            1024: {
                                perView: 3.5
                            },
                            1120: {
                                perView: 4
                            },
                            1250: {
                                perView: 4.5
                            }
                        }
                    }).mount()
                })
            }

        </script>
    @endpush

</x-app-layout>
