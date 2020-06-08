<template>
  <div>
    <h1>Groups</h1>

    <UserDataModal
      s-edit-text="Gruppen Bearbeiten"
      s-create-text="Neue Gruppe erstellen"
      s-input-label="Gruppenname"
      s-input-label2="Gruppenbeschreibung Öffentlich"
      s-input-label3="Gruppenbeschreibung für Moderatoren"
      s-parent-model="group"
      :a-parent-models="user.groups_moderating"
      :a-parent-headers="[
        {
          name: 'id',
          label: this.$t('id'),
          field: 'id',
        },
        {
          name: 'name',
          label: this.$t('name'),
          field: 'name',
        },
        {
          name: 'description_public',
          label: this.$t('description_public'),
          field: 'description_public',
        },
        {
          name: 'description_mods',
          label: this.$t('description_mods'),
          field: 'description_mods',
        },
        {
          name: 'updated_at',
          label: this.$t('updated_at'),
          field: 'updated_at',
        },
        {
          name: 'created_at',
          label: this.$t('created_at'),
          field: 'created_at',
        }
      ]"
    />

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

    <q-table
      v-if="oGroup && oGroup.users"
      v-model="selected"
      :headers="headers"
      :items="oGroup.users"
      show-select
      multi-sort
      :footer-props="{
        showFirstLastPage: true,
      }"
      dense
    />
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import UserDataModal from '~/components/Backend/UserDataModal'

export default {
  middleware: 'canCreateGroups',

  components: {
    UserDataModal
  },

  data: () => ({
    oSelectedGroup: null,
    oGroup: {},
    selected: [],

    headers: [
      {
        text: 'ID',
        align: 'left',
        value: 'id'
      },
      {
        text: 'PAN',
        value: 'pan.pan'
      },
      { text: 'groups', value: 'groups' },
      { text: 'company', value: 'company' },
      { text: 'department', value: 'department' },
      { text: 'location', value: 'location' },
      { text: 'updated_at', value: 'updated_at' },
      { text: 'created_at', value: 'created_at' },
      { text: 'Actions', value: 'action', sortable: false }
    ]
  }),

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },

  watch: {
    oSelectedGroup (group) {
      if (typeof group === 'object' && group.id) {
        this.getGroup(group.id)
      }
    }
  },

  methods: {
    getGroup (id) {
      var _this = this
      axios.get('/api/get-group', {
        params: {
          id: id
        }
      })
        .then(function (response) {
          if (response.data) {
            _this.oGroup = response.data
          }
        })
    }
  }

}
</script>
