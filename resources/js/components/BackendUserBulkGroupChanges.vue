<template>
    <div>
        <v-menu offset-x open-on-hover>
            <template v-slot:activator="{ on: menugroup }">
                <v-list-item v-on="{ ...menugroup }">
                    <v-list-item-title>Gruppe</v-list-item-title>
                    <v-list-item-action>
                        <v-icon>mdi-chevron-right</v-icon>
                    </v-list-item-action>
                </v-list-item>
            </template>
            <v-list>
                <v-list-item @click="bAddDialog = true">
                    <v-list-item-title>Hinzufügen</v-list-item-title>
                </v-list-item>

                <v-list-item @click="bDeleteDialog = true">
                    <v-list-item-title>Löschen</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>

        <v-dialog v-model="bAddDialog" transition="dialog-bottom-transition" max-width="700" persistent>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click="bAddDialog = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>Gruppe(n) hinzufügen</v-toolbar-title>
                <div class="flex-grow-1"></div>
                <v-toolbar-items>
                    <v-btn dark text @click="addGroupToUser(selected, aAddItems)">Änderungen übernehmen</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-list three-line subheader>
                <v-subheader>Gruppen zum hinzufügen wählen</v-subheader>
                <v-list-item>
                    <v-list-item-content style="max-width:400px;">
                        <v-select 
                            outlined 
                            clearable 
                            label="Hinzufügen" 
                            required 
                            :items="aItems"
                            v-model="aAddItems"
                            item-text="name"
                            return-object
                            multiple
                            chips
                            ></v-select>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-dialog>

        <v-dialog v-model="bDeleteDialog" transition="dialog-bottom-transition" max-width="700" persistent>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click="bDeleteDialog = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>Gruppen von Nutzern entfernen</v-toolbar-title>
                <div class="flex-grow-1"></div>
                <v-toolbar-items>
                    <v-btn dark text @click="removeGroupFromUser(selected, aRemoveItems)">Änderungen übernehmen</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-list three-line subheader>
                <v-subheader>Gruppe zum Entfernen wählen</v-subheader>
                <v-list-item>
                    <v-list-item-content style="max-width:400px;">
                        <v-select 
                            outlined 
                            clearable 
                            label="Löschen" 
                            required 
                            :items="aItems"
                            v-model="aRemoveItems"
                            item-text="name"
                            return-object
                            multiple
                            chips
                            ></v-select>
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
    ],

    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },

    data () {
      return {
          bAddDialog: false,
          bDeleteDialog: false,
          aAddItems: [],
          aRemoveItems: [],
      };
    },

    methods: {
        addGroupToUser(aSelectedUsers, aItems) {
            for (var i in aSelectedUsers) {
                var oUser = aSelectedUsers[i];

                for (var j in aItems)
                {
                    var oItem = aItems[j];

                    // If not found 
                    if(oUser.groups.findIndex(group => group.id === oItem.id) === -1) {
                        oUser.groups.push(oItem);
                    }
                }
            }
            this.closeDialog();
        },

        removeGroupFromUser(aSelectedUsers, aItems) {
            for (var i in aSelectedUsers)
            {
                var oUser = aSelectedUsers[i];

                for (var j in aItems)
                {
                    var oItem = aItems[j];

                    // Try to Find
                    var foundIndex = oUser.groups.findIndex(group => group.id === oItem.id);

                    // If Found something
                    if(foundIndex >= 0) {
                        oUser.groups.splice(foundIndex, 1);
                    }
                    
                }

            }
            this.closeDialog();
        },

        closeDialog() {
            this.bAddDialog = false;
            this.bDeleteDialog = false;
        },
    }
}
</script>