<template>
    <div>
        <v-menu offset-x open-on-hover>
            <template v-slot:activator="{ on: menu }">
                <v-list-item v-on="{ ...menu }">
                    <v-list-item-title>{{ menuText }}</v-list-item-title>
                    <v-list-item-action>
                        <v-icon>mdi-chevron-right</v-icon>
                    </v-list-item-action>
                </v-list-item>
            </template>
            <v-list>
                <v-list-item @click="dialog = !dialog">
                    <v-list-item-title>Ändern</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
        <v-dialog v-model="dialog" transition="dialog-bottom-transition" max-width="700" persistent>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click="dialog = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>{{ title }}</v-toolbar-title>
                <div class="flex-grow-1"></div>
                <v-toolbar-items>
                    <v-btn dark text @click="changeUser(selected, oNewItem)">Änderungen übernehmen</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-list three-line subheader>
                <v-subheader>Firma wählen</v-subheader>
                <v-list-item>
                    <v-list-item-content style="max-width:400px;">
                        <v-select 
                            outlined 
                            clearable 
                            :label="label" 
                            required 
                            :items="aItems"
                            v-model="oNewItem"
                            item-text="name"
                            return-object></v-select>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-dialog>
    </div>
</template>

<script>

import { mapGetters } from 'vuex'

export default {

    props: [
        'aItems',
        'selected',
        'sId',
        'sModel',
        'menuText',
        'title',
        'label'
    ],

    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },

    data () {
      return {
          dialog: false,
          oNewItem: {}
      };
    },

    methods: {
        changeUser(aSelectedUsers, item) {
            for (var i in aSelectedUsers) {
                var oUser = aSelectedUsers[i];
                oUser[this.sId] = item.id;
                oUser[this.sModel] = item;
            }
            this.dialog = false;
        },
    }
}
</script>