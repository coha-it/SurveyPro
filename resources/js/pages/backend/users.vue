<template>
    <div>
        <h1>Users</h1>
        <p>Create Users with PAN & PIN</p>
        <br>



        <template>
            <v-card>
                <v-card-title>
                    Created Users
                    <div class="flex-grow-1"></div>
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    :headers="headers"
                    :items="users"
                    :search="search"
                    show-select
                    >

                    <!-- PAN -->
                    <template v-slot:item.pan.pan="{ item }">
                        <span class="coha--list-item pan">{{ item.pan.pan.substring(0,3) }}</span><span class="coha--list-item pan">{{ item.pan.pan.substring(3,6) }}</span>
                    </template>

                    <!-- PIN -->
                    <template v-slot:item.pan.pin="{ item }">
                        <span class="coha--list-item pin">{{ item.pan.pin }}</span>
                    </template>

                    <!-- Groups -->
                    <template v-slot:item.groups="{ item }">
                        <v-chip v-for="group in item.groups" v-bind:key="group.id" dark>{{ group.name }}</v-chip>
                        &nbsp;
                        <v-chip>
                            <select>
                                <option selected>{{ $t('+ Add') }}</option>
                                <option v-for="group in all_groups" v-if="!item.groups.includes(group)" v-bind:key="group.id">{{ group.name }}</option>
                            </select>
                        </v-chip>
                    </template>

                    <template v-slot:item.action="{ item }">
                        <v-icon
                            small
                            class="mr-2"
                            @click="editItem(item)"
                        >
                            edit
                        </v-icon>
                        <v-icon
                            small
                            @click="deleteItem(item)"
                        >
                            delete
                        </v-icon>
                    </template>

                </v-data-table>
            </v-card>
        </template>

        <!-- <v-btn v-on:click="getUsers">Get Users</v-btn> -->



    </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
    middleware: 'canCreateUsers',

    computed: mapGetters({
        user: 'auth/user'
    }),

    data () {
      return {
        search: '',
        headers: [
          {
            text: 'ID',
            align: 'left',
            value: 'id',
          },
          { text: this.$t('PAN'), value: 'pan.pan' },
          { text: this.$t('PIN'), value: 'pan.pin' },
          { text: this.$t('groups'), value: 'groups'},
          { text: this.$t('updated_at'), value: 'updated_at'},
          { text: this.$t('created_at'), value: 'created_at'},
        //   { text: 'Locked', value: 'pan.locked_until'},
          { text: 'Actions', value: 'action', sortable: false },
        ],

        all_groups: [],

        users: [],
      }
    },

    created: function() {
        this.getUsers();
        this.getGroups();
    },


    methods: {
        getUsers() {
            var _this = this;
            axios.get('/api/get/users/created')
                .then(function (response) {
                    _this.users = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(function () {
                });
        },

        getGroups() {
            var _this = this;
            axios.get('/api/get/groups/moderating')
                .then(function (response) {
                    _this.all_groups = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(function () {
                });
        }
    }

}

</script>

<style lang="scss" scoped>
.coha--list-item {

    &.pin,
    &.pan {
        font-family: Roboto Mono;
    }

    &.pan {
        &:first-child {
            margin-right: .275em;
        }
    }

    &.pin {

    }


}
</style>