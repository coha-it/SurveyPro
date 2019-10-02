<template>
    <div>
        <h1>Groups</h1>


        <UserDataModal 
            sEditText="Gruppen Bearbeiten"
            sCreateText="Neue Gruppe erstellen" 
            sInputLabel="Gruppenname"
            sInputLabel2="Gruppenbeschreibung Öffentlich"
            sInputLabel3="Gruppenbeschreibung für Moderatoren"
            p_sModel="group"
            :p_oModels="user.groups_moderating"
            :p_aHeaders="[
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('name'), value: 'name' },
                { text: this.$t('description_public'), value: 'description_public' },
                { text: this.$t('description_mods'), value: 'description_mods' },
                { text: this.$t('updated_at'), value: 'updated_at'},
                { text: this.$t('created_at'), value: 'created_at'}
            ]" />


        <br><br>
        <!-- Go Through all Groups -->
        <h2>Gruppe anzeigen</h2>

        <v-combobox
          v-model="oSelectedGroup"
          :items="user.groups_moderating"
          label="Wählen Sie eine moderierende Gruppe"
          item-text="name"
          clearable
        >
            <template v-slot:item="{ item }">
                {{ item.name }}&nbsp; <small> #{{ item.id }}</small>
            </template>
        </v-combobox>



        <v-data-table
            v-if="oGroup && oGroup.users"
            :headers="headers"
            :items="oGroup.users"
            v-model="selected"
            show-select 
            multi-sort 
            :footer-props="{
                showFirstLastPage: true,
            }"
            dense
            >
        </v-data-table>



    </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import UserDataModal from '~/components/BackendUserDataModal'

export default {
    middleware: 'canCreateGroups',

    components: {
        UserDataModal,
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },


    data: () => ({
        oSelectedGroup: null,
        oGroup: {},
        selected: [],



        headers: [
                {
                    text: 'ID',
                    align: 'left',
                    value: 'id',
                },
                { 
                    text: 'PAN', 
                    value: 'pan.pan', 
                },
                { text: 'groups', value: 'groups'},
                { text: 'company', value: 'company'},
                { text: 'department', value: 'department'},
                { text: 'location', value: 'location'},
                { text: 'updated_at', value: 'updated_at'},
                { text: 'created_at', value: 'created_at'},
                { text: 'Actions', value: 'action', sortable: false },
        ], 
    }),

    methods: {
        getGroup(id) {
            var _this = this;
            axios.get('/api/get-group', {
                params: {
                    id: id
                }
            })
            .then(function(response) {
                if(response.data) {
                    _this.oGroup = response.data;
                }
            });
        }
    },

    watch: {
        oSelectedGroup(group) {
            if(typeof group === 'object' && group.id) {
                this.getGroup(group.id);
            }
        }
    },

}
</script>