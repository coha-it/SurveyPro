<template>
    <span>

        <v-snackbar v-model="snack" :timeout="snackTimeout" :color="snackColor" top>
            <span v-html="snackText"></span>
            <v-btn text @click="snack = false">{{ $t('closer_button') }}</v-btn>
        </v-snackbar>

        <v-dialog v-model="editDialog" max-width="900px" max-height="1000px" min-height="500px" height="90%" persistent scrollable transition="dialog-bottom-transition">
            <template v-slot:activator="{ on }">
                <v-btn color="primary" depressed v-on="on">{{ sEditText }}</v-btn>
            </template>
            <v-card>
                <v-toolbar dark color="primary">
                    <v-btn icon dark @click="editDialog = false">
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ sEditText }}</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <br>
                    <v-card>
                        <v-card-title>
                            <!-- New Company -->
                            <v-dialog v-model="createDialog" persistent max-width="290">
                                <template v-slot:activator="{ on }">
                                    <v-btn color="primary" depressed dark v-on="on">{{ sCreateText }}</v-btn>
                                </template>
                                <v-card>
                                    <v-card-title class="headline">{{ sCreateText }}</v-card-title>
                                    <v-card-text>
                                        <v-text-field :label="sInputLabel" required v-model="field_name"></v-text-field>
                                    </v-card-text>
                                    <v-card-actions>
                                        <div class="flex-grow-1"></div>
                                        <v-btn color="green darken-1" text @click="createDialog = false">{{ $t('cancel') }}</v-btn>
                                        <v-btn color="green darken-1" text @click="createModel(field_name)">{{ $t('save') }}</v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>     
                            <div class="flex-grow-1"></div>
                            <!-- <v-text-field v-model="sSearch" append-icon="search" label="Search" single-line hide-details></v-text-field> -->
                        </v-card-title>
                        <v-data-table
                            :headers="headers"
                            :items="oModels"
                            :items-per-page="20"
                            class="elevation-1"
                            :search="sSearch"
                            multi-sort>
                            <template v-slot:item.name="props">
                                <v-edit-dialog
                                    :return-value.sync="props.item.name"
                                    @save="save(props.item)"
                                    @cancel="cancel"
                                    @open="open"
                                    @close="close" 
                                    lazy 
                                >
                                <span v-if="props.item.name.length > 0">{{ props.item.name }}</span>
                                <span v-else class="red--text c-code-text">{{ $t('empty') }}</span>
                                    <template v-slot:input>
                                        <v-text-field
                                            v-model="props.item.name"
                                            label="Edit"
                                            single-line
                                            :rules="[minChars]"
                                            counter 
                                            required 
                                        ></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </template>
                        </v-data-table>
                    </v-card>       
                </v-card-text>
                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="primary" text @click="editDialog = false">
                        close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>

import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },

    props: [
        'sCreateText',
        'sEditText',
        'sInputLabel',
        'p_sModel',
        'p_oModels',
    ],

    data() {
        return {
            editDialog: false,
            createDialog: false,
            field_name: '',
            sSearch: '',

            // Company Headers
            headers: [
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('name'), value: 'name' },
                { text: this.$t('public'), value: 'public' },
                { text: this.$t('created_by'), value: 'created_by' },
                { text: this.$t('updated_at'), value: 'updated_at'},
                { text: this.$t('created_at'), value: 'created_at'},
            ],

            // Snackbar
            snack: false,
            snackColor: '',
            snackText: '',
            snackTimeout: 3000,

            // From Parent
            sModel:  this.p_sModel,
            oModels: this.p_oModels,

            // Rules
            minChars: v => v.length >= 1 || this.$t('validation.length.short'),
        }
    },

    methods: {
        save(item) {
            if(!item.name) return;

            this.snackTimeout = 5000;
            this.snack = true
            this.snackColor = 'success'
            this.snackText = this.$t('data_saved')
            axios.patch('/api/'+ this.sModel +'/update', {
                id: item.id,
                name: item.name
            });
        },

        createModel(name) {
            var _this = this;
            _this.createDialog = false;

            axios.post('/api/'+ this.sModel +'/create', {
                name: name
            }).then(function (response) {
                _this.oModels.push(response.data);
            });
        },

        cancel () {
            // this.snackTimeout = 3000;
            // this.snack = true
            // this.snackColor = 'primary'
            // this.snackText = this.$t('canceled')
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