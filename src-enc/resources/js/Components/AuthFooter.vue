<template>
    <div class="bg-gray-200 text-black">
        <div class="container mx-auto py-3">
            <div class="flex w-full">
                <div class="flex-auto sm:flex-none">
                    <strong>{{ getAppName() }}</strong>&nbsp;&copy; {{ new Date().getFullYear() }}
                </div>
            </div>
        </div>
    </div>
</template>
