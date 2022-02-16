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

    @push('style')
        <style>
            .glide__arrows {
                display: flex;
                justify-content: space-between;
                margin-top: 0.5em;
            }

            .glide__arrow {
                display: block;
                z-index: 2;
                color: white;
                text-transform: uppercase;
                padding: 9px 12px;
                background-color: #404040;
                border: 2px solid rgba(255, 255, 255, 0.5);
                border-radius: 6px;
                box-shadow: 0 0.25em 0.5em 0 rgba(0, 0, 0, 0.1);
                text-shadow: 0 0.25em 0.5em rgba(0, 0, 0, 0.1);
                opacity: 1;
                cursor: pointer;
                transition: opacity 150ms ease, border 300ms ease-in-out;
                // transform: translateY(-50%);
                line-height: 1;
            }

            .glide__arrow:focus {
                outline: none;
            }

            .glide__arrow:hover {
                border-color: white;
                background-color: #202124
            }

            .glide__arrow--left {
                left: 2em;
            }

            .glide__arrow--right {
                right: 2em;
            }

            .glide__arrow--disabled {
                opacity: 0.33;
            }

            .glide__bullets {
                position: relative;
                z-index: 2;
                bottom: 2em;
                left: 50%;
                display: inline-flex;
                list-style: none;
                transform: translateX(-50%);
            }

            .glide__bullet {
                background-color: rgba(255, 255, 255, 0.5);
                width: 9px;
                height: 9px;
                padding: 0;
                border-radius: 50%;
                border: 2px solid transparent;
                transition: all 300ms ease-out;
                cursor: pointer;
                line-height: 0;
                box-shadow: 0 0.25em 0.5em 0 rgba(0, 0, 0, 0.1);
                margin: 0 0.25em;
            }

            .glide__bullet:focus {
                outline: none;
            }

            .glide__bullet:hover,
            .glide__bullet:focus {
                background-color: darkgrey;
            }

            .glide__bullet--active {
                background-color: darkgrey;
            }

            .glide--swipeable {
                cursor: grab;
                cursor: -moz-grab;
                cursor: -webkit-grab;
            }

            .glide--dragging {
                cursor: grabbing;
                cursor: -moz-grabbing;
                cursor: -webkit-grabbing;
            }

        </style>
    @endpush
</x-app-layout>
