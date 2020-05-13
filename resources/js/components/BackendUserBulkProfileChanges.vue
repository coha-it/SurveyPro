<template>
  <q-item clickable>
    <q-item-section>{{ menuText }}</q-item-section>
    <q-item-section side>
      <q-icon name="keyboard_arrow_right" />
    </q-item-section>
    <q-menu anchor="top right" self="top left">
      <q-list>
        <q-item clickable @click="dialog = true">
          <q-item-section>
            <q-item-label>Ändern</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-menu>

    <q-dialog v-model="dialog" :maximized="maximizedToggle" transition="dialog-bottom-transition" max-width="700" persistent>
      <q-card>
        <q-toolbar class="bg-primary text-white" dark color="primary">
          <div>{{ title }}</div>
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
              Firma wählen
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section style="max-width:400px;">
              <q-select
                outlined
                clearable
                :label="label"
                required
                :options="aItems"
                v-model="oNewItem"
                option-label="name"
              />
            </q-item-section>
          </q-item>
          <q-item>
            <q-btn unelevated color="primary" dark @click="changeUser(selected, oNewItem)">Änderungen übernehmen</q-btn>
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
          oNewItem: '',
          maximizedToggle: false
      };
    },

    methods: {
        changeUser(aSelectedUsers, item) {
            for (var i in aSelectedUsers) {
              console.log(item);
                var oUser = aSelectedUsers[i];
                oUser[this.sId] = item.id;
                oUser[this.sModel] = item;
            }
            this.dialog = false;
        },
    }
}
</script>
