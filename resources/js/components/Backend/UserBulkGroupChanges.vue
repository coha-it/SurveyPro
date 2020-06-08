<template>
    <q-item clickable>
        <q-item-section>Gruppe</q-item-section>
        <q-item-section side>
          <q-icon name="keyboard_arrow_right" />
        </q-item-section>
        <q-menu anchor="top right" self="top left">
            <q-list>
                <q-item clickable @click="bAddDialog = true">
                  <q-item-section>
                    <q-item-label>Zu Gruppe hinzufügen</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item clickable @click="bDeleteDialog = true">
                  <q-item-section>
                    <q-item-label>Gruppen-Verbindungen lösen</q-item-label>
                  </q-item-section>
                </q-item>
            </q-list>
        </q-menu>

        <q-dialog v-model="bAddDialog" :maximized="maximizedToggle" transition="dialog-bottom-transition" max-width="700" persistent>
          <q-card>
            <q-toolbar class="bg-primary text-white" dark color="primary">
                <div>Gruppe(n) hinzufügen</div>
                <q-space />
                <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
                  <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">Minimieren</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
                  <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">Maximieren</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="close" v-close-popup>
                  <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
                </q-btn>
            </q-toolbar>
            <q-list three-line subheader>
                <q-item>
                  <q-item-section>
                    Gruppen zum hinzufügen wählen
                  </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section style="max-width:400px;">
                        <q-select
                          filled
                          clearable
                          label="Gruppe(n) Hinzufügen"
                          :options="aItems"
                          v-model="aAddItems"
                          option-label="name"
                          option-value="id"
                          multiple
                          />
                    </q-item-section>
                </q-item>

                <q-item>
                  <q-btn dark unelevated color="primary" @click="addGroupToUser(selected, aAddItems)" label="Änderungen übernehmen" />
                </q-item>
            </q-list>
          </q-card>
        </q-dialog>




        <q-dialog v-model="bDeleteDialog" :maximized="maximizedToggle" transition="dialog-bottom-transition" max-width="700" persistent>
          <q-card>
            <q-toolbar class="bg-primary text-white" dark color="primary">
                <div>Gruppe(n) hinzufügen</div>
                <q-space />
                <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
                  <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">Minimieren</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
                  <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">Maximieren</q-tooltip>
                </q-btn>
                <q-btn dense flat icon="close" v-close-popup>
                  <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
                </q-btn>
            </q-toolbar>
            <q-list three-line subheader>
                <q-item>
                  <q-item-section>
                    Gruppen von Nutzern entfernen
                  </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section style="max-width:400px;">
                        <q-select
                          filled
                          clearable
                          label="Gruppe wählen"
                          :options="aItems"
                          v-model="aRemoveItems"
                          option-label="name"
                          option-value="id"
                          multiple
                          />
                    </q-item-section>
                </q-item>
                <q-item>
                  <q-btn dark unelevated color="primary" @click="removeGroupFromUser(selected, aRemoveItems)" label="Änderungen übernehmen" />
                </q-item>
            </q-list>
          </q-card>
        </q-dialog>
    </q-item>
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
          maximizedToggle: false,
          bAddDialog: false,
          bDeleteDialog: false,
          aAddItems: null,
          aRemoveItems: null,
      };
    },

    methods: {
        copyObject(obj) {
            return JSON.parse(JSON.stringify(obj));
        },

        addGroupToUser(aSelectedUsers, aItems) {
            for (var i in aSelectedUsers) {
                var oUser = aSelectedUsers[i];

                for (var j in aItems)
                {
                    var oItem = this.copyObject(aItems[j]);

                    if( !oItem.pivot ) {
                        oItem.pivot = {};
                    }

                    oItem.pivot.is_mod = 0;
                    oItem.pivot.is_member = 1;

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
