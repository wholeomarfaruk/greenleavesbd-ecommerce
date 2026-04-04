<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Green Leaves BD</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('frontend/img/logo-transparent.png') }}">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS  -->
    <link href="{{ asset('frontend/library/bootstrap/bootstrap.min.css') }}" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Swiperjs css -->
    <link rel="stylesheet" href="{{ asset('frontend/library/swiper/swiper-bundle.min.css') }}">
    <!-- Fancy Box css -->
    <link rel="stylesheet" href="{{ asset('frontend/library/fancybox/fancybox.css') }}">
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom Css  -->
    <style>
        @font-face {
            font-family: 'SolaimanLipi';
            src: url("{{ asset('fonts/SolaimanLipi.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css?v=1.0.4') }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/media.css?v=1.0.2') }}">



    @stack('styles')

    @if (app()->environment('production'))
        <!-- Google Tag Manager -->
    @endif

    <!-- End Google Tag Manager -->
    <meta name="facebook-domain-verification" content="q3e3x73iwktzrop9d227rx2rj9bm8v" />
</head>

<body class="bg-white bg-opacity-50">
    @if (app()->environment('production'))
        <!-- Google Tag Manager (noscript) -->
    @endif
    <!--header start-->
    <header>
        <!--Conatiner-1 Start-->
        <div class="container-fluid px-0">
            <div class="header_top">
                <p>আমাদের যে কোন পণ্য অর্ডার করতে কল বা WhatsApp করুন: +8801893-620392</p>
            </div>
        </div>
        <!--Conatiner-2 Start-->
        <div class="container-fluid bg-white px-0">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <!--logo-->
                    <a class="navbar-brand logo" href="/">
                        <img src="{{ asset('frontend/img/logo/logo.png') }}" class="w-100" alt="Green Leave">
                    </a>
                    <!--Search bar-->
                    <div class="justify-content-center collapse navbar-collapse nav_search" id="navbarSupportedContent">
                        <form class="d-flex" role="search" action="{{ route('search') }}">
                            <input class="form-control me-2" type="search" placeholder="Search" name="search"
                                aria-label="Search" value="{{ request('search') }}" />
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                    <!--Icon-->
                    <div class="icon_box">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav_c"
                            aria-controls="nav_c" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
        <!--Conatiner-3 Start-->
        <div class="container-fluid px-0">
            <nav class="navbar navbar-expand-lg nav">
                <div class="container">
                    {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav_c"
                        aria-controls="nav_c" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button> --}}
                    <div class="collapse navbar-collapse menu" id="nav_c">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="/">Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop') }}">Shop</a>
                            </li>
                            @php
                                $category_menus = \App\Models\Category::where('is_show_in_menu', true)
                                    ->where('is_active', true)
                                    ->orderBy('display_order')
                                    ->get();
                            @endphp
                            @foreach ($category_menus as $menu)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('category.show', $menu->slug) }}">{{ $menu->name }}</a>
                                </li>
                            @endforeach

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('contact') }}">Contact Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('about') }}">About Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!--header end-->
    <aside id="sidebar"></aside>
    <main id="Content-body" class="">
        @yield('content')
    </main>
    <!--footer top start-->
    <footer class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="footer_item">
                        <img class="w-100" src="{{ asset('frontend/img/logo/logo.png') }}" alt="Green Leaves">
                        <h2>Green Leaves Bangladesh</h2>
                        <p>Green Leaves Bangladesh একটি জনপ্রিয় ব্র্যান্ড,
                            যেখানে বিভিন্ন ধরনের খাদ্য পণ্য পাওয়া যায়।
                            এখানে সাশ্রয়ী মূল্যে ভালো মানের পণ্য সরবরাহ করা হয়।</p>
                        <a href="https://www.facebook.com/greenleavesbd0" class="social_icon" style="text-decoration:none;" target="_blank">

                            <i class="fa-brands fa-square-facebook" style="color: rgb(24, 119, 242);"></i>
                        </a>
                        <a href="https://www.youtube.com/@greenleaves172" class="social_icon" style="text-decoration:none;" target="_blank">
                            <i class="fa-brands fa-youtube" style="color: rgb(255, 0, 0);"></i>
                        </a>
                        <a href="#" class="social_icon" style="text-decoration:none;" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/8801893620392" class="social_icon" style="text-decoration:none;" target="_blank">
                            <i class="fa-brands fa-whatsapp" style="color: rgb(37, 211, 102);"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4">
                    <div class="footer_item footer_item1">
                        <h2>গুরুত্বপূর্ণ লিংক</h2>
                        <ul>
                            <li><a href="/">হোম </a></li>
                            <li><a href="{{ route('about') }}">আমাদের সম্পর্কে</a></li>
                            <li><a href="{{ route('contact') }}">যোগাযোগ</a></li>
                            <li><a href="{{ route('shop') }}">শপ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="footer_item footer_item1">
                        <h2>গুরুত্বপূর্ণ লিংক</h2>
                        <ul>
                            <li><a href="/">হোম</a></li>
                            <li><a href="{{ route('about') }}">আমাদের সম্পর্কে</a></li>
                            <li><a href="{{ route('contact') }}">যোগাযোগ</a></li>
                            <li><a href="{{ route('shop') }}">শপ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="footer_item footer_item1">
                        <h2>Contact Us</h2>
                        <p><span><i class="fa-solid fa-phone"></i></span> +8801893-620392</p>
                        <p><span><i class="fa-solid fa-envelope"></i></span>info@sitename.com</p>
                        <p><span><i class="fa-solid fa-location-dot"></i></span>Dhaka, Bangladesh</p>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer_img">
                        <img src="{{ asset('frontend/img/footer/foot.jpg') }}" class="w-100" alt="Green Leaves">
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--footer top end-->
    <!--footer start-->
    <footer class="footer">
        <div class="container">
            <div class="footer_bottom">
                <p>© 2025. All right Reserved Developed By <a target="_blank"
                        href="https://www.facebook.com/alimuzahid.dev/">Ali Muzahid</a></p>
                <p class="footer_p2"></p>
            </div>
        </div>
    </footer>
    <!--footer end-->

    <div>
        <style>
            .new-arrival {
                position: fixed;
                right: 20px;
                top: 150px;
                width: 100px;
                height: 100px;
                background-color: #d30101;
                border-radius: 50%;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                animation: bubbleandshake 2s infinite ease-in-out;
                cursor: grab;
                z-index: 99;
                user-select: none;
            }

            .new-arrival .text {
                font-weight: 700;
                color: #fff;
                text-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                font-size: 12px;

            }

            @keyframes bubbleandshake {
                0% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-10px);
                }

            }
        </style>
        {{-- <div class="new-arrival " id="dragBtn">
            <a href="https://youngstar.life/category/joggers-pant" class="text-center text-decoration-none">
                <dotlottie-wc src="https://lottie.host/8a7eb623-e79f-481e-b63f-555486c2e6a7/0Hmst73iRT.lottie"
                    style="width: 50px;height: 50px" autoplay loop></dotlottie-wc>
                <span class="fw-semibold text-primary-color fs-6 text ms-1 text-center">New Joggers</span>
            </a>
        </div> --}}
    </div>
    <!-- Jquery -->
    <script src="{{ asset('frontend/library/jquery/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap Js  -->
    <script src="{{ asset('frontend/library/bootstrap/bootstrap.bundle.min.js') }}"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <!-- Swiperjs Js  -->
    <script src="{{ asset('frontend/library/swiper/swiper-bundle.min.js') }}"></script>
    <!-- Fancybox js -->
    <script src="{{ asset('frontend/library/fancybox/fancybox.umd.js') }}"></script>
    <!-- Custom Js  -->
    <script src="{{ asset('frontend/js/script.js') }}"></script>
    <!-- Initialize Swiper -->
    <!-- Initialize Swiper -->

    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    </script>

    <script>
        Fancybox.bind("[data-fancybox]", {
            // Optional settings
            Thumbs: {
                autoStart: true,
            },
        });
    </script>


    @stack('scripts')

</body>

</html>
