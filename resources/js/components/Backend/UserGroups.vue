<template lang="pug">
  UserDataModalContent(
    s-edit-text="Gruppen Bearbeiten",
    s-create-text="Neue Gruppe erstellen",
    s-input-label="Gruppenname",
    s-input-label2="Gruppenbeschreibung Öffentlich",
    s-input-label3="Gruppenbeschreibung für Moderatoren",
    s-parent-model="group",
    :a-parent-models="user.groups_moderating",
    :a-parent-headers="getParentHeaders()"
  )
</template>
<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import UserDataModalContent from '~/components/Backend/UserDataModalContent'

export default {
  middleware: 'canCreateGroups',

  components: {
    UserDataModalContent
  },

  data: () => ({
    oSelectedGroup: null,
    oGroup: {}
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
      axios.get('/api/get-group', {
        params: {
          id: id
        }
      })
        .then(response => {
          if (response.data) {
            this.oGroup = response.data
          }
        })
    },

    getParentHeaders () {
      return [
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
      ]
    }
  }
}
</script>
