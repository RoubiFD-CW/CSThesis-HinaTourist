@extends('layouts.app')

@section('content')

    <!-- All Destinations Section -->
    <section class="mt-20 md:mt-20 py-10 md:py-12 bg-slate-50 relative overflow-hidden">

        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center mt-5 mb-5">
            <h1 class="font-heading font-black text-4xl md:text-5xl text-black mb-4"> All Destinations </h1>
            <p class="text-black/90 text-lg">Explore all amazing tourist spots in Hinatuan</p>
        </div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <!-- Destinations Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1: Enchanted River -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/enchanted.jpg') }}" alt="Enchanted River"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Enchanted River</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Talisay, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">The famous deep blue river. Witness the daily fish
                            feeding at 12 noon.</p>
                    </div>
                </div>

                <!-- Card 2: Hinatuan Adventure Park -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/adventurepark.jpg') }}" alt="Hinatuan Adventure Park"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.9
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Hinatuan Adventure Park</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Talisay, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Thrilling outdoor activities including zip-lining,
                            rock climbing, and adventure courses with stunning views.</p>
                    </div>
                </div>

                <!-- Card 3: Lodestone Shores Resort -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/lodestone.jpg') }}" alt="Lodestone Shores Resort"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Lodestone Shores Resort</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Bagasin Island in Barangay
                            Portlamon, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Luxury beachfront resort with pristine white sand
                            shores and crystal clear waters perfect for activities.</p>
                    </div>
                </div>

                <!-- Card 4: Baculin Amazing Sand Bar -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/baculinsandbar.jpg') }}" alt="Baculin Amazing Sand Bar"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Baculin Amazing Sand Bar</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Baculin, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Beautiful sandbar formation ideal for swimming,
                            beachcombing, and enjoying picnics.</p>
                    </div>
                </div>

                <!-- Card 5: Harip Oceanside Beach -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/harip.jpg') }}" alt="Harip Oceanside Beach"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Harip Oceanside Beach</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Harip, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">A stunning oceanside beach offering beautiful sunset
                            views, soft sand, and excellent snorkeling spots.</p>
                    </div>
                </div>

                <!-- Card 6: Rock Island Resort -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/rockisland.jpg') }}" alt="Rock Island Resort"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.9
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Rock Island Resort</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Portlamon, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Exclusive island resort perfect for beach lovers with
                            activities like water sports and island hopping.</p>
                    </div>
                </div>

                <!-- Card 7: Mamaon Beach Resort -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/mamaon.jpg') }}" alt="Mamaon Beach Resort"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Mamaon Beach Resort</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Purok 4, Barangay Portlamon,
                            Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Beachfront resort offering accommodations, water
                            sports, and direct access to pristine sand beach.</p>
                    </div>
                </div>

                <!-- Card 8: Amparitas Integrated Nature Farm -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/amparitas.jpg') }}" alt="Amparitas Integrated Nature Farm"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.6
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Amparitas Nature Farm</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Tagasaka, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Eco-tourism destination featuring organic farming,
                            nature walks, and educational agricultural experiences.</p>
                    </div>
                </div>

                <!-- Card 9: Sibadan Fish Cage and Resort -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/sibadan.jpg') }}" alt="Sibadan Fish Cage and Resort"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Sibadan Fish Cage Resort</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Portlamon, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Unique floating resort with fish farming experiences,
                            fresh seafood dining, and close-up marine encounters.</p>
                    </div>
                </div>

                <!-- Card 10: Landong Bay -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/landongbay.jpg') }}" alt="Landong Bay"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Landong Bay</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Landong Bay, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Popular ferry departure point with scenic views,
                            local restaurants, and excellent gateway.</p>
                    </div>
                </div>

                <!-- Card 11: Davince Hidden Paradise -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/davince.jpg') }}" alt="Davince Hidden Paradise"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.9
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Davince Hidden Paradise</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Harip, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Secluded beach destination offering privacy, pristine
                            waters, and perfect spot for romantic getaways.</p>
                    </div>
                </div>

                <!-- Card 12: Tarusan Cold Spring -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/tarusan.jpg') }}" alt="Tarusan Cold Spring"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.6
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Tarusan Cold Spring</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Tarusan, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Natural freshwater spring destination perfect for
                            cooling off, swimming, and enjoy thermal pools.</p>
                    </div>
                </div>

                <!-- Card 13: Llamas Beach Resort -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/llamas.jpg') }}" alt="Llamas Beach Resort"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-900 shadow-md">
                            <i class="fa-solid fa-star text-yellow-500 mr-1"></i> 4.9
                        </div>
                        <button
                            class="absolute bottom-4 right-4 w-10 h-10 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all shadow-lg transform translate-y-14 group-hover:translate-y-0 duration-300">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Llamas Beach Resort</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Campa, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Upscale beachfront resort with modern amenities, spa
                            services, fine dining, and water sports facilities.</p>
                    </div>
                </div>

                <!-- Card 14: Puro Brigida's Beach -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/purobrigada.jpg') }}" alt="Puro Brigida's Beach"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Puro Brigida's Beach</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Cambatong, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Charming local beach spot with authentic coastal
                            charm, fresh seafood restaurants, and people.</p>
                    </div>
                </div>

                <!-- Card 15: Bunsadan Falls -->
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('HinaTouristImages/bunsadan.png') }}" alt="Bunsadan Falls"
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
                        <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Bunsadan Falls</h3>
                        <div class="flex items-center text-slate-400 text-sm mb-4">
                            <i class="fa-solid fa-location-dot mr-2 text-cyan-400"></i> Barangay Bigaan, Hinatuan
                        </div>
                        <p class="text-slate-500 text-sm line-clamp-2">Majestic waterfall surrounded by lush vegetation.
                            Perfect for hiking, photography, and swimming.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection