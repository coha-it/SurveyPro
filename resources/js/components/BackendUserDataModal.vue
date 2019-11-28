<template>
    <span>
        <q-btn @click="editDialog = true" color="primary" depressed tile outline class="mt-2 mr-2">{{ sEditText }}</q-btn>
        <q-dialog
          v-model="editDialog"
          max-width="900px"
          max-height="1000px"
          min-height="500px"
          height="90%"
          persistent
          scrollable
          transition="dialog-bottom-transition"
          :maximized="maximizedToggle"
          >
            <q-card>
                <q-toolbar class="bg-primary text-white" dark color="primary">
                    <q-toolbar-title>{{ sEditText }}</q-toolbar-title>

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
                <q-card-section>
                    <br>
                    <q-card>
                        <q-card-section>
                            <!-- New Company -->
                            <q-btn @click="createDialog = true" color="primary" depressed dark >{{ sCreateText }}</q-btn>
                            <q-dialog v-model="createDialog" persistent>

                              <q-card style="min-width: 350px">
                                <q-card-section>
                                  <div class="text-h6">{{ sCreateText }}</div>
                                </q-card-section>

                                <q-card-section>
                                  <q-input dense v-model="item.name" :label="sInputLabel" required autofocus @keyup.enter="prompt = false" />
                                  <template v-if="sModel == 'group'">
                                       <q-input :label="sInputLabel2" required v-model="item.description_public" />
                                       <q-input :label="sInputLabel3" required v-model="item.description_mods" />
                                  </template>
                                </q-card-section>

                                <q-card-actions align="right" class="text-primary">
                                  <q-btn flat :label="$t('cancel')" v-close-popup />
                                  <q-btn flat :label="$t('save')" v-close-popup @click="createModel(item)" />
                                </q-card-actions>
                              </q-card>

                            </q-dialog>
                            <div class="flex-grow-1"></div>

                        </q-card-section>
                        <q-table
                            :columns="aHeaders"
                            :data="oModels"
                            :items-per-page="20"
                            class="elevation-1 my-sticky-header-table"
                            v-if="aHeaders">


                            <template v-slot:body-cell-name="props">
                              <q-td :props="props">
                                <MyPopupEdit :props="props" val="name" :save="save" />
                              </q-td>
                            </template>

                            <template v-slot:body-cell-description_public="props">
                              <q-td :props="props">
                                <MyPopupEdit :props="props" val="description_public" :save="save" />
                              </q-td>
                            </template>

                            <template v-slot:body-cell-description_mods="props">
                              <q-td :props="props">
                                <MyPopupEdit :props="props" val="description_mods" :save="save" />
                              </q-td>
                            </template>

                        </q-table>
                    </q-card>
                </q-card-section>
            </q-card>
        </q-dialog>
    </span>
</template>

<script>

import { mapGetters } from 'vuex'
import axios from 'axios'
import MyPopupEdit from '~/components/MyPopupEdit'

export default {

    components: {
      MyPopupEdit,
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },

    props: [
        'sCreateText',
        'sEditText',
        'sInputLabel',
        'sInputLabel2',
        'sInputLabel3',
        'p_sModel',
        'p_oModels',
        'p_aHeaders',
    ],

    data() {
        return {
            maximizedToggle: false,
            editDialog: false,
            createDialog: false,
            item: {
                name: '',
                description_public: ''
            },
            sSearch: '',

            // From Parent
            sModel:  this.p_sModel,
            oModels: this.p_oModels,

            // Company Headers
            aHeaders: this.p_aHeaders || [
                {
                  name: 'id',
                  label: this.$t('id'),
                  field: 'id',
                  sortable: true,
                },
                {
                  name: 'name',
                  label: this.$t('name'),
                  field: 'name',
                  sortable: true,
                },
                {
                  name: 'public',
                  label: this.$t('public'),
                  field: 'public',
                  sortable: true,
                },
                {
                  name: 'created_by',
                  label: this.$t('created_by'),
                  field: 'created_by',
                  sortable: true,
                },
                {
                  name: 'updated_at',
                  label: this.$t('updated_at'),
                  field: 'updated_at',
                  sortable: true,
                },
                {
                  name: 'created_at',
                  label: this.$t('created_at'),
                  field: 'created_at',
                  sortable: true,
                },
            ],

            // Rules
            minChars: v => v.length >= 1 || this.$t('validation.length.short'),
        }
    },

    methods: {
        save(item) {
            if(!item.name) return;

            this.$q.notify({
              message: this.$t('data_saved'),
              color: 'green',
              timeout: 5000,
            })

            axios.patch('/api/'+ this.sModel +'/update', {
                item: item
            });
        },

        createModel(item) {
            var _this = this;
            _this.createDialog = false;

            axios.post('/api/'+ this.sModel +'/create', {
                item: item
            }).then(function (response) {
                _this.oModels.push(response.data);
                if(item.name) item.name = '';
                if(item.description_public) item.description_public  = '';
                if(item.description_mods) item.description_mods = '';
            });
        },

        cancel () {

        },

        open () {
        },

        close () {
          // When Edit Dialog Closed
        },
    }
}
</script>

<style>
</style>
