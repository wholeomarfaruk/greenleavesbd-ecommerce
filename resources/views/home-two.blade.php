@extends('layouts.app')
@push('styles')
    <style>
        .sec-style-1 {}

        .sec-style-1 .sec-header {}

        .sec-style-1 .sec-header .sec-title {
            font-size: 20px;
            font-weight: 600;

        }

        .sec-style-1 .sec-body {}

        .sec-style-1 .sec-body .sec-grid-box {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }


        .sec-style-1 .sec-body .sec-grid-box .sec-grid-item {}

        .p-card-1 {
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.2s ease-in-out;

        }

        .p-card-1:hover {
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            transform: scale(1.02);
            transition: all 0.2s ease-in-out;
        }

        .p-card-1 a {
            text-decoration: none;
        }

        .p-card-1 .p-img-box {
            overflow: hidden;
            border-radius: 8px;


        }

        .p-card-1 .p-img-box img {
            width: 100%;
            height: 312px;
            object-fit: cover;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;

        }

        .p-card-1:hover .p-img-box img {

            transform: scale(1.1);
            transition: all 0.2s ease-in-out;
        }

        .p-card-1 .p-info {}

        .p-card-1 .p-info .p-title {
            font-size: 18px;
            font-weight: 600;
            color: rgb(37, 37, 37);
        }

        .p-card-1 .p-info .prices {
            display: flex;
            gap: 10px;
            font-weight: 700;
            align-items: end;
        }

        .p-card-1 .p-info .prices .old-price {
            font-size: 16px;
            color: #8f8f8f;
        }

        .p-card-1 .p-info .prices .price {
            font-size: 18px;
        }

        .p-card-1 .p-info .p-description {
            font-size: 18px;
            color: rgb(37, 37, 37);
            margin-bottom: 0;
            font-weight: 600;
        }

        .p-card-1 .p-info .p-description:hover {
            /* text-decoration: underline; */
            color: var(--text-primary-color);
        }

        .p-card-1 .p-btn-group {}

        .p-card-1 .p-btn-group .btn {}

        @media (max-width:992px) {
            .sec-style-1 .sec-body .sec-grid-box {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .p-card-1 .p-img-box img {

                height: 250px;

            }
        }
    </style>
@endpush
@section('content')

        <!--banner start-->
        @if($slides->count() > 0)
        <section class="hero-slider">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($slides as $slide)


                    <div class="carousel-item active">
                        <div class="d-block w-100 hero-slide"
                            style="background-image: url('{{ asset('storage/images/slides/'.$slide?->image) }}');">
                        </div>
                    </div>
                     @endforeach
                    {{-- <div class="carousel-item">
                        <div class="d-block w-100 hero-slide"
                            style="background-image: url('{{ asset('frontend/img/banner/main_banner2.jpeg') }}');">
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="d-block w-100 hero-slide"
                            style="background-image: url('{{ asset('frontend/img/banner/main_banner3.jpeg') }}');">
                        </div>
                    </div> --}}
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </section>
        @endif
        <!--banner end-->
        <!--our Category start-->
        <section class="our_category">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="category_head">
                            <h1>Our Category</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="#">
                                    <img src="{{ asset('frontend/img/category/baking_powder.jpeg') }}" class="w-100"
                                        alt="Green Leaves">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="#">
                                    <p>Bakery Item</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="#">
                                    <img src="{{ asset('frontend/img/category/strawberry_jelly.jpeg') }}"
                                        class="w-100" alt="Green Leaves">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="#">
                                    <p>Food Item</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="#">
                                    <img src="{{ asset('frontend/img/category/biriyani_masala.jpeg') }}"
                                        class="w-100" alt="Green Leaves">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="#">
                                    <p>Masala</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="#">
                                    <img src="{{ asset('frontend/img/category/soya_sauce.jpeg') }}" class="w-100"
                                        alt="Green Leaves">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="#">
                                    <p>Cooking Item</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="#">
                                    <img src="{{ asset('frontend/img/category/red_food.jpeg') }}" class="w-100"
                                        alt="Green Leaves">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="#">
                                    <p>Food Color</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="#">
                                    <img src="{{ asset('frontend/img/category/vanilla_essence.jpeg') }}"
                                        class="w-100" alt="Green Leaves">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="#">
                                    <p>Food Essence</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--our Category end-->
            <section class="sec-style-1 my-3">
        <div class="container">
            <div class="sec-header">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h2 class="sec-title text-primary-color">Latest Products - নতুন পণ্য</h2>
                    </div>
                    <div class=" text-right">
                        <a href="{{ route('shop') }}" class="sec-title text-primary-color">সব পণ্য দেখুন</a>
                    </div>
                </div>

                <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
            </div>
            <div class="sec-body">
                <div class="sec-grid-box">
                    @foreach ($products->take(8) as $product)
                        <div class="sec-grid-item p-card-1">

                            <div class="p-img-box">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ asset('storage/images/products/' . $product->image) }}" alt="">
                                </a>
                            </div>
                            <div class="p-info">
                                <div class="prices">
                                    @if ($product->discount_price > 0)
                                        <del class="old-price">৳ {{ $product->price }}</del>
                                        <span class="price">৳ {{ $product->discount_price }}</span>
                                    @else
                                        <span class="old-price">Price : </span> <span class="price"> ৳
                                            {{ $product->price }}</span>
                                    @endif

                                </div>
                                <a href="{{ route('product.show', $product->slug) }}">

                                    <h1 class="p-title">{{ $product->name }}</h1>
                                </a>
                                
                            </div>
                            <div class="p-btn-group d-flex gap-2">
                                <a class="btn btn-primary w-100 d-block"
                                    href="{{ route('product.show', $product->slug) }}">Buy Now</a>
                                <button class="btn btn-primary w-100 d-block "  onclick="cartQueue.addToCart({{ $product->id }}, 1)"
                                    href="#"> Add to Cart</button>
                            </div>


                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route('shop') }}" class="btn btn-primary "> See More</a>
                    {{-- {{ $products->links('pagination::bootstrap-5') }} --}}
                </div>
            </div>
        </div>
    </section>
    @foreach ($categories->where('is_homepage_show', true) as $category)
        @if ($category->products->count() > 0)
            <section class="sec-style-1 my-3">
                <div class="container">
                    <div class="sec-header">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow">
                                <h2 class="sec-title text-primary-color">{{ $category->name }}</h2>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('category.show', $category->slug) }}"
                                    class="sec-title text-primary-color">সব পণ্য দেখুন</a>
                            </div>
                        </div>

                        <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
                    </div>
                    <div class="sec-body">
                        <div class="sec-grid-box">
                            @foreach ($category?->products->take(8) as $product)
                                <div class="sec-grid-item p-card-1">

                                    <div class="p-img-box">
                                        <a href="{{ route('product.show', $product->slug) }}">
                                            <img src="{{ asset('storage/images/products/' . $product->image) }}"
                                                alt="">
                                        </a>
                                    </div>
                                    <div class="p-info">
                                        <div class="prices">
                                            @if ($product->discount_price > 0)
                                                <del class="old-price">৳ {{ $product->price }}</del>
                                                <span class="price">৳ {{ $product->discount_price }}</span>
                                            @else
                                                <span class="old-price">Price : </span> <span class="price"> ৳
                                                    {{ $product->price }}</span>
                                            @endif

                                        </div>
                                        <a href="{{ route('product.show', $product->slug) }}">

                                            <h1 class="p-title">{{ $product->name }}</h1>
                                        </a>
                                        <a href="#">
                                            <p class="p-description">
                                                বিস্তারিত দেখুন
                                            </p>
                                        </a>
                                    </div>
                                    <div class="p-btn-group">
                                        <a class="btn btn-primary w-100 d-block"
                                            href="{{ route('product.show', $product->slug) }}">Buy Now</a>
                                    </div>


                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <a href="{{ route('category.show', $category->slug) }}" class="btn btn-primary "> See More -
                                {{ $category->name }}</a>

                            {{-- {{ $products->links('pagination::bootstrap-5') }} --}}
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    <section id="faq" class=" mb-3">
        <div class="container">

            <h1 class="fs-5 fw-bold bg-primary-color text-center py-3 px-3 text-white">সচরাচর জিজ্ঞাস্য প্রশ্নাবলি
            </h1>
            <ul class="list-inline fs-6 fw-medium">
                <li><i class="fa-solid fa-angles-right text-primary-color"></i> সারা বাংলাদেশে ক্যাশ অন ডেলিভারি
                    অ্যাভেইলেবল </li>

                <li><i class="fa-solid fa-angles-right  text-primary-color"></i> আপনি যদি আপনার ক্রয়কৃত পণ্যটি
                    নিয়ে সন্তুষ্ট না হন, তবে শুধু ডেলিভারি চার্জ প্রদান করে ডেলিভারি ম্যানের কাছে সহজেই ফেরত দিতে
                    পারবেন। </li>

                <li><i class="fa-solid fa-angles-right text-primary-color"></i>আমাদের আছে ডেলিভারির পর ৩ দিন
                    পর্যন্ত এক্সচেঞ্জ সুবিধা।
                </li>
            </ul>
        </div>
    </section>
        <!--wholesale program start-->
        <section class="d-block" >
           <img style="width: 100%" src="{{ asset('frontend/img/banner/footer-banner.jpeg') }}" alt="">
        </section>
        <!--wholesale program end-->
        <!--services Section Start-->
        <section class="service">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="service_item">

                            <!--icon-->
                            <div class="service_icon">
                               <i class="fa-solid fa-cart-shopping"></i>
                            </div>

                            <!--text-->
                            <div class="service_text">
                                <h2>Unique Products</h2>
                                <p>Enjoy top quality items for less</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="service_item">
                            <!--icon-->
                            <div class="service_icon">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <!--text-->
                            <div class="service_text">
                                <h2>Online Support</h2>
                                <p>24 hours a day, 7 days a week</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="service_item">
                            <!--icon-->
                            <div class="service_icon">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <!--text-->
                            <div class="service_text">
                                <h2>Free Shipping</h2>
                                <p>Free Shipping for Over 500 taka order</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="service_item">
                            <!--icon-->
                            <div class="service_icon">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <!--text-->
                            <div class="service_text">
                                <h2>secure payment</h2>
                                <p>Enjoy top quality items for less</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--services Section end-->


@endsection
