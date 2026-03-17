@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section id="hero"
        class="relative h-screen min-h-[600px] md:min-h-[700px] flex items-center justify-center overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <!-- Placeholder for Enchanted River - using a similar tropical blue river image -->
            <img src="{{ asset('HinaTouristImages/enchanted.jpg') }}"
                alt="Hinatuan Enchanted River" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-slate-900/40 via-cyan-900/20 to-transparent bg-gradient-to-b"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 text-center max-w-5xl px-4 animate-fade-up">
            <h1
                class="font-heading font-black text-4xl sm:text-5xl md:text-7xl text-white mb-6 drop-shadow-xl tracking-tight leading-tight">
                Discover the Beauty <br>
                <span
                    class="text-cyan-400 bg-clip-text text-transparent bg-gradient-to-r from-cyan-300 to-teal-300"> of Hinatuan</span>
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-slate-100 mb-10 max-w-2xl mx-auto font-light drop-shadow-md">
                Swim in the sapphire waters, explore hidden islands, and
                witness nature's true wonder.
            </p>

            <!-- Search Bar -->
            <div
                class="bg-white/95 backdrop-blur-md p-2 rounded-3xl md:rounded-full shadow-2xl max-w-2xl mx-auto flex flex-col md:flex-row gap-2 md:items-center animate-slide-in">
                <div class="flex-grow flex items-center px-4 md:border-r border-slate-200 h-12">
                    <i class="fa-solid fa-location-dot text-slate-400 mr-3"></i>
                    <input type="text" placeholder="Where do you want to go?"
                        class="w-full bg-transparent border-none focus:ring-0 text-slate-700 placeholder-slate-400 font-medium text-sm md:text-base">
                </div>
                <!-- <div class="flex-shrink-0 flex items-center px-4 md:border-r border-slate-200 h-12 border-t md:border-t-0">
                    <i class="fa-regular fa-calendar text-slate-400 mr-3"></i>
                    <input type="text" placeholder="Add dates"
                        class="w-full bg-transparent border-none focus:ring-0 text-slate-700 placeholder-slate-400 font-medium cursor-pointer text-sm md:text-base">
                </div> -->
                <button
                    class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold h-12 px-8 rounded-xl md:rounded-full shadow-lg transform hover:scale-105 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-search"></i>
                    <span>Search</span>
                </button>
            </div>
        </div>
            
            <!-- Scroll Down Indicator
            <div class="absolute bottom-50 left-1/2 transform -translate-x-1/2 animate-bounce text-white/70">
                <i class="fa-solid fa-chevron-down text-2xl"></i>
            </div> -->
    </section>

    <!-- Categories Section -->
    <!-- <section id="categories" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12 md:mb-16">
                <span class="text-cyan-600 font-bold tracking-widest uppercase text-xs mb-3 block">Browse by
                    Category</span>
                <h2 class="font-heading font-black text-3xl md:text-4xl text-slate-900">Find Your Perfect Getaway</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6"> -->
                <!-- Category 1 -->
                <!-- <div
                    class="group relative rounded-3xl overflow-hidden aspect-[4/5] cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                        alt="Hotels"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex flex-col justify-end p-6">
                        <div
                            class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mb-3 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300 text-white">
                            <i class="fa-solid fa-hotel text-lg"></i>
                        </div>
                        <h3 class="text-white font-bold text-xl">Top Hotels</h3>
                        <p
                            class="text-slate-300 text-sm mt-1 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-100">
                            Luxury stays worldwide
                        </p>
                    </div>
                </div> -->

                <!-- Category 2 -->
                <!-- <div
                    class="group relative rounded-3xl overflow-hidden aspect-[4/5] cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                        alt="Beaches"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex flex-col justify-end p-6">
                        <div
                            class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mb-3 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 text-white">
                            <i class="fa-solid fa-umbrella-beach text-lg"></i>
                        </div>
                        <h3 class="text-white font-bold text-xl">Beaches</h3>
                        <p
                            class="text-slate-300 text-sm mt-1 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-100">
                            Sun, sand, and sea
                        </p>
                    </div>
                </div> -->

                <!-- Category 3 -->
                <!-- <div
                    class="group relative rounded-3xl overflow-hidden aspect-[4/5] cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                        alt="Mountains"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex flex-col justify-end p-6">
                        <div
                            class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mb-3 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300 text-white">
                            <i class="fa-solid fa-mountain-sun text-lg"></i>
                        </div>
                        <h3 class="text-white font-bold text-xl">Nature</h3>
                        <p
                            class="text-slate-300 text-sm mt-1 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-100">
                            Hiking & Adventure
                        </p>
                    </div>
                </div> -->

                <!-- Category 4 -->
                <!-- <div
                    class="group relative rounded-3xl overflow-hidden aspect-[4/5] cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1416339306562-f3d12fefd36f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                        alt="Urban"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent flex flex-col justify-end p-6">
                        <div
                            class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mb-3 group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300 text-white">
                            <i class="fa-solid fa-city text-lg"></i>
                        </div>
                        <h3 class="text-white font-bold text-xl">Urban</h3>
                        <p
                            class="text-slate-300 text-sm mt-1 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-100">
                            City lights & Nightlife
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Popular Destinations Carousel Style -->
    <section id="destinations" class="py-16 md:py-20 bg-slate-50 relative overflow-hidden">
        <!-- Decorative blobs -->
        <div
            class="absolute top-0 right-0 w-96 h-96 bg-indigo-100/50 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-72 h-72 bg-purple-100/50 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/2">
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div
                class="flex flex-col md:flex-row justify-between items-center md:items-end mb-12 text-center md:text-left gapt-4 md:gap-0">
                <div>
                    <span class="text-indigo-600 font-bold tracking-widest uppercase text-xs mb-3 block">Top Rated</span>
                    <h2 class="font-heading font-black text-3xl md:text-4xl text-slate-900">Popular Destinations</h2>
                </div>
                <a href="{{ route('destinations') }}"
                    class="flex items-center gap-2 text-indigo-600 font-bold hover:gap-3 transition-all group mt-4 md:mt-0">
                    See All <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Destinations Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/enchanted.jpg') }}"
                            alt="Enchanted River"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 5.0
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-heading font-bold text-xl text-slate-900">Enchanted River</h3>
                            <span class="text-cyan-600 font-bold"></span>
                        </div>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Talisay, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm mb-6 line-clamp-2">The famous deep blue river. Witness the daily
                            fish feeding at 12 noon and swim in the crystal clear waters.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/lodestone.jpg') }}"
                            alt="Lodgestone Shores Resort"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.8
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-heading font-bold text-xl text-slate-900">Lodestone Shores Resort</h3>
                            <span class="text-cyan-600 font-bold"></span>
                        </div>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Bagasin Island in Barangay Portlamon, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm mb-6 line-clamp-2">Luxury beachfront resort with pristine white sand shores and crystal clear waters perfect for swimming
                            and diving.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/harip.jpg') }}"
                            alt="Harip Oceanside Beach"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.7
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-heading font-bold text-xl text-slate-900">Harip Oceanside Beach</h3>
                            <span class="text-cyan-600 font-bold"></span>
                        </div>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Harip, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm mb-6 line-clamp-2">A stunning oceanside beach offering beautiful sunset views, soft sand, and excellent snorkeling spots.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section (App Promo) -->
    <section class="py-16 md:py-24 bg-slate-900 relative overflow-hidden">
        <div
            class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] animate-pulse">
        </div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 flex flex-col md:flex-row items-center gap-12 md:gap-16">
            <div class="flex-1 text-center md:text-left">
                <span
                    class="px-4 py-1.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider mb-6 inline-block">
                    Your Ultimate Guide
                </span>
                <h2 class="font-heading font-black text-4xl md:text-5xl text-white mb-6">Explore Hinatuan <br> Like a Local
                </h2>
                <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                    Dive into the mesmerizing turquoise depths of the Enchanted River, embark on unforgettable island-hopping adventures through the hidden coves of Portlamon, and discover the thunderous beauty of tucked-away waterfalls and pristine sandbars.
                </p>
                <!-- <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <button
                        class="px-8 py-4 rounded-xl bg-white text-slate-900 font-bold flex items-center justify-center gap-3 hover:bg-slate-200 transition-colors">
                        <i class="fa-brands fa-apple text-xl"></i> App Store
                    </button>
                    <button
                        class="px-8 py-4 rounded-xl bg-cyan-600 text-white font-bold flex items-center justify-center gap-3 hover:bg-cyan-700 transition-colors">
                        <i class="fa-brands fa-google-play text-xl"></i> Google Play
                    </button>
                </div> -->
            </div>
            <div class="flex-1 relative">
                <!-- Map Image with Hover Effect -->
                <div class="relative w-full max-w-md mx-auto">
                    <img src="HinaTouristImages/hinatuanmap.svg"
                        alt="Hinatuan Map" 
                        class="w-full h-auto transform transition-all duration-500 hover:scale-105 hover:-translate-y-3">
                </div>
            </div>
        </div>
    </section>
@endsection