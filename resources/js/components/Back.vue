<template>
    <router-link :to="{ path: $store.state.route.from.fullPath }" class="small ml-auto my-auto">
        {{ $t('back') }}
    </router-link>
</template>
