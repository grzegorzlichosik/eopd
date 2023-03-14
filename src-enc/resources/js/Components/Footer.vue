<template>
    <div class="bg-gray-200 text-black">
        <div class="container mx-auto py-3 pt-4">
            <div class="flex w-full">
                <div class="flex-auto text-left sm:flex-none">
                    <strong>{{ getAppName() }}</strong>&nbsp;&copy; {{ new Date().getFullYear() }}
                </div>
                <div
                    v-if="$page.props.user_last_login"
                    class="flex-1 text-right text-sm"
                >
                    Last logged in:
                    {{ toLocalDate($page.props.user_last_login, $page.props.default_date_format) }} at
                    {{ toLocalTime($page.props.user_last_login), $page.props.default_time_format}}
                </div>
            </div>
        </div>
    </div>
</template>

