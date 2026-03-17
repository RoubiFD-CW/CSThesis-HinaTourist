<footer class="bg-slate-900 text-slate-400 py-16 text-center text-sm border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12 text-left">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center p-1">
                        <img src="{{ asset('hinatourist-logo.png') }}" class="w-full h-full object-contain" alt="Logo">
                    </div>
                    <span class="font-heading font-bold text-xl text-white">{{ config('app.name') }}</span>
                </a>
                <p class="text-slate-500 leading-relaxed max-w-xs">
                    Discover the beauty of Hinatuan for the modern traveler. Explore with us.
                </p>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4 uppercase tracking-wider text-xs">Explore</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-400 transition-colors">Popular Places</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition-colors">Hidden Gems</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition-colors">Travel Guides</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4 uppercase tracking-wider text-xs">Company</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-400 transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition-colors">Careers</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition-colors">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4 uppercase tracking-wider text-xs">Social</h4>
                <div class="flex gap-4">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all transform hover:-translate-y-1">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all transform hover:-translate-y-1">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all transform hover:-translate-y-1">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p>&copy; {{ date('Y') }} HinaTourist. All rights reserved.</p>
            <div class="flex gap-6 text-xs">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>