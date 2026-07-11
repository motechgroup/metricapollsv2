<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="w-full max-w-md bg-white border border-gray-200 p-8 rounded-lg shadow-sm space-y-8 animate-fade-in" x-data="{ showPicker: false }">
        <div class="text-center">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">Create Metrica Polls Account</h2>
            <p class="mt-2 text-xs text-gray-500">Join our enterprise-grade research panel and earn payouts.</p>
        </div>

        <div class="space-y-4">
            <!-- Google Sign Up Button -->
            <button type="button" @click="showPicker = !showPicker" class="w-full inline-flex items-center justify-center gap-3 rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <!-- Google Icon SVG -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                    <g transform="matrix(1, 0, 0, 1, 0, 0)">
                        <path d="M21.35,11.1H12v2.7h5.38C16.88,15.22,14.73,16.5,12,16.5c-3.03,0-5.61-2.05-6.52-4.82c-0.23-0.69-0.36-1.42-0.36-2.18s0.13-1.49,0.36-2.18C6.39,4.55,8.97,2.5,12,2.5c1.65,0,3.13,0.59,4.3,1.57l2.02-2.02C16.78,0.77,14.53,0,12,0C7.24,0,3.2,2.72,1.3,6.72C0.89,7.6,0.58,8.54,0.37,9.52c-0.12,0.57-0.18,1.15-0.18,1.75s0.06,1.18,0.18,1.75c0.21,0.98,0.52,1.92,0.93,2.8c1.9,4,5.94,6.72,10.7,6.72c3.24,0,5.97-1.07,7.96-2.91c2.14-1.97,3.39-4.87,3.39-8.1C23.37,12.16,23.3,11.62,21.35,11.1z" fill="#4285F4"/>
                    </g>
                </svg>
                <span>Continue with Google</span>
            </button>

            <!-- Security Alert -->
            <div class="rounded-md bg-blue-50 p-4 border border-blue-150">
                <h4 class="text-xxs font-bold text-blue-800 uppercase tracking-wider mb-1">Google-Only Registration Policy</h4>
                <p class="text-xxs text-blue-700 leading-relaxed">
                    To comply with parastatal and enterprise data security regulations, we require verified Google credentials for panelists. This ensures verified demographics and prevents automated robot submissions.
                </p>
            </div>

            <!-- Google Account Picker (Mock OAuth popup) -->
            <div x-show="showPicker" x-transition class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3" style="display: none;">
                <h3 class="text-xxs font-bold uppercase tracking-wider text-gray-400">Choose a Google Account to Register</h3>
                
                <div class="space-y-2">
                    <button wire:click="loginWithGoogle('ben.otieno@gmail.com', 'Ben Otieno')" class="w-full text-left bg-white border border-gray-150 p-2.5 rounded-md hover:bg-gray-100 transition flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-gray-900 block">Ben Otieno</span>
                            <span class="text-gray-500">ben.otieno@gmail.com</span>
                        </div>
                        <span class="text-xxs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase">New Panelist</span>
                    </button>

                    <button wire:click="loginWithGoogle('karen.cherono@gmail.com', 'Karen Cherono')" class="w-full text-left bg-white border border-gray-150 p-2.5 rounded-md hover:bg-gray-100 transition flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-gray-900 block">Karen Cherono</span>
                            <span class="text-gray-500">karen.cherono@gmail.com</span>
                        </div>
                        <span class="text-xxs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase">New Panelist</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center border-t border-gray-150 pt-6">
            <p class="text-xs text-gray-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-bold text-gray-900 hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>
