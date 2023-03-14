<template>
    <div class="min-h-full w-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md px-6 py-4 bg-brand-900 overflow-hidden text-white text-2xl text-center">
            <slot name="title"></slot>
        </div>
        <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden">
            <slot/>
        </div>
    </div>
</template>
