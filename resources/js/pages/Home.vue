<script setup>
import { ref } from 'vue';
import { router as vueRouter } from '../router';
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-blue-600">🇰🇪 Kenyan SMM Panel</h1>
                <div class="space-x-4">
                    <router-link to="/login" class="text-gray-600 hover:text-gray-900">Login</router-link>
                    <router-link to="/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Sign Up</router-link>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                        Grow Your Social Media in Kenya
                    </h2>
                    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                        Fast, reliable, and affordable social media marketing services. 
                        Pay with M-Pesa, cards, or crypto. All prices in KES.
                    </p>
                    <div class="flex justify-center gap-4">
                        <router-link to="/register" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                            Get Started
                        </router-link>
                        <router-link to="/services" class="bg-white text-blue-600 border border-blue-600 px-8 py-3 rounded-lg font-medium hover:bg-blue-50 transition">
                            Browse Services
                        </router-link>
                    </div>
                </div>
            </section>

            <section class="py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h3 class="text-2xl font-bold text-center text-gray-800 mb-12">Why Choose Us?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="text-4xl mb-4">⚡</div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Fast Delivery</h4>
                            <p class="text-gray-600">Most orders start within minutes. Instant API integration.</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl mb-4">🇰🇪</div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">M-Pesa Payments</h4>
                            <p class="text-gray-600">Seamless M-Pesa STK Push integration. Pay in KES instantly.</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl mb-4">🔒</div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Secure & Private</h4>
                            <p class="text-gray-600">No password required for any service. Safe and anonymous.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-gray-50 py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h3 class="text-2xl font-bold text-center text-gray-800 mb-12">Supported Platforms</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <div class="text-3xl mb-2">📸</div>
                            <div class="font-medium">Instagram</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <div class="text-3xl mb-2">🎵</div>
                            <div class="font-medium">TikTok</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <div class="text-3xl mb-2">▶️</div>
                            <div class="font-medium">YouTube</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <div class="text-3xl mb-2">🐦</div>
                            <div class="font-medium">X (Twitter)</div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-gray-800 text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; {{ new Date().getFullYear() }} Kenyan SMM Panel. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
