<x-app-layout>
    <div class="container py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                    class="swiper mySwiper2 mb-2">
                    <div class="swiper-wrapper">
                        @foreach ($product->images as $image)
                            <div class="swiper-slide" data-thumb="{{ Storage::url($image->url) }}">
                                <img dusk="image-product-{{ $loop->index }}"
                                    src="{{ Storage::url($image->url) }}" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($product->images as $image)
                            <div class="swiper-slide">
                                <img dusk="image-product-{{ $loop->index }}"
                                    src="{{ Storage::url($image->url) }}" />
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 text-gray-700">
                    <h2 class="font-bold text-lg">Descripción</h2>
                    {!! $product->description !!}
                </div>
            </div>
            <div>
                <h1 class="text-xl font-bold text-neutral-700">{{ $product->name }}</h1>
                <div class="flex">
                    <p class="text-neutral-700">Marca:<a href=""
                            class="underline capitalize hover:text-orange-500">{{ $product->brand->name }}</a></p>
                    <p class="text-neutral-700 mx-6">5 <i class="fas fa-star text-sm text-yellow-400"></i></p> <a
                        href=""></a>
                    <a class="text-orange-500 hover:text-orange-600 underline" href="">39 reseñas</a>
                </div>
                <p class="text-2xl font-semibold text-neutral-700 my-4">{{ $product->price }} &euro;</p>

                <div class="bg-white rounded-lg shadow-lg mb-6">
                    <div class="flex items-center p-4">
                        <span class="flex items-center justify-center h-10 w-10 rounded-full bg-lime-600">
                            <i class="fas fa-truck text-sm text-white"></i>
                        </span>
                        <div class="ml-4">
                            <p class="text-lg font-semibold text-lime-600">Se hacen envíos solo a la península</p>
                            <p>Recíbelo el {{ Date::now()->addDay(7)->locale('es')->format('l j F') }}</p>
                        </div>
                    </div>
                </div>
                @if ($product->subcategory->size)
                    @livewire('add-cart-item-size', ['product' => $product])
                @elseif($product->subcategory->color)
                    @livewire('add-cart-item-color', ['product' => $product])
                @else
                    @livewire('add-cart-item', ['product' => $product])
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.onload = () => {
                let thumbs = new Swiper('.mySwiper', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });

                let swiper = new Swiper(".mySwiper2", {
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    autoplay: true,
                    thumbs: {
                        swiper: thumbs,
                    },
                });
            }
        </script>
    @endpush

    @push('style')
        <style>
            .mySwiper2 {
                cursor:grab;
            }
            .mySwiper2:active {
                cursor:grabbing;
            }

            .mySwiper .swiper-slide {
                opacity: 0.4;
                cursor: pointer;
            }

            .mySwiper .swiper-slide-thumb-active {
                opacity: 1;
            }

        </style>
    @endpush
</x-app-layout>
