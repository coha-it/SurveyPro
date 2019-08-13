<template>
    <div>
        <v-btn color="blue-grey" text :to="{ path: 'auth' }" >
            <v-icon left dark>mdi-chevron-left</v-icon>
            {{ $t('back') }}
        </v-btn>
    </div>
</template>

<style lang="scss" scoped>
.v-btn {margin-left: -10px;}
</style>