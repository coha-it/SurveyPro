<template lang="pug">
q-card
  q-card-section
    // New Company
    q-btn(color='primary' depressed='' dark='' @click='createDialog = true') {{ sCreateText }}
    q-dialog(v-model='createDialog' persistent='')
      q-card(style='min-width: 350px')
        q-card-section
          .text-h6 {{ sCreateText }}
        q-card-section
          q-input(v-model='item.name' dense='' :label='sInputLabel' required='' autofocus='' @keyup.enter='prompt = false')
          template(v-if="sModel == 'group'")
            q-input(v-model='item.description_public' :label='sInputLabel2' required='')
            q-input(v-model='item.description_mods' :label='sInputLabel3' required='')
        q-card-actions.text-primary(align='right')
          q-btn(v-close-popup='' flat='' :label="$t('cancel')")
            q-btn(v-close-popup='' flat='' :label="$t('save')" @click='createModel(item)')
    .flex-grow-1
  q-table.elevation-1.my-sticky-header-table(v-if='aHeaders' :columns='aHeaders' :data='oModels' :items-per-page='20')
    template(v-slot:body-cell-name='props')
      q-td(:props='props')
        MyPopupEdit(:props='props' val='name' :save='save')
    template(v-slot:body-cell-description_public='props')
      q-td(:props='props')
        MyPopupEdit(:props='props' val='description_public' :save='save')
    template(v-slot:body-cell-description_mods='props')
      q-td(:props='props')
        MyPopupEdit(:props='props' val='description_mods' :save='save')
</template>

<script>
import MyPopupEdit from '~/components/MyPopupEdit'
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {

  components: {
    MyPopupEdit
  },

  props: {

    sParentModel: {
      type: String,
      default: null
    },
    aParentModels: {
      type: Array,
      default: null
    },
    aParentHeaders: {
      type: Array,
      default: null
    },

    sCreateText: {
      type: String,
      default: null
    },
    sEditText: {
      type: String,
      default: null
    },

    sInputLabel: {
      type: String,
      default: null
    },
    sInputLabel2: {
      type: String,
      default: null
    },
    sInputLabel3: {
      type: String,
      default: null
    }
  },

  data () {
    return {
      createDialog: false,
      item: {
        name: '',
        description_public: ''
      },
      sSearch: '',

      // From Parent
      sModel: this.sParentModel,
      oModels: this.aParentModels,

      // Company Headers
      aHeaders: this.aParentHeaders || [
        {
          name: 'id',
          label: this.$t('id'),
          field: 'id',
          sortable: true
        },
        {
          name: 'name',
          label: this.$t('name'),
          field: 'name',
          sortable: true
        },
        {
          name: 'public',
          label: this.$t('public'),
          field: 'public',
          sortable: true
        },
        {
          name: 'created_by',
          label: this.$t('created_by'),
          field: 'created_by',
          sortable: true
        },
        {
          name: 'updated_at',
          label: this.$t('updated_at'),
          field: 'updated_at',
          sortable: true
        },
        {
          name: 'created_at',
          label: this.$t('created_at'),
          field: 'created_at',
          sortable: true
        }
      ],

      // Rules
      minChars: v => v.length >= 1 || this.$t('validation.length.short')
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },

  methods: {
    save (item) {
      if (!item.name) return

      this.$q.notify({
        message: this.$t('data_saved'),
        color: 'green',
        timeout: 5000
      })

      axios.patch('/api/' + this.sModel + '/update', {
        item: item
      })
    },
    createModel (item) {
      this.createDialog = false

      axios.post('/api/' + this.sModel + '/create', {
        item: item
      }).then(response => {
        this.oModels.push(response.data)
        if (item.name) item.name = ''
        if (item.description_public) item.description_public = ''
        if (item.description_mods) item.description_mods = ''
      })
    }
  }
}
</script>
