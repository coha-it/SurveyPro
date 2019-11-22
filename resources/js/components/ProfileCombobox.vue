<template>
    <div>
        <v-combobox
            v-model="model"
            :filter="filter"
            :hide-no-data="!search"
            :items="list"
            :item-text="'name'"
            :item-value="'id'"
            :search-input.sync="search"
            :label="$t(sInputLabel)"
            multiple
            dense
            autocomplete="new-password"
        >
            <template v-slot:no-data>
            <v-list-item>
                <span class="subheading">{{ $t(create_text) }}</span>&nbsp; "{{ search }}"
            </v-list-item>
            </template>
            <template v-slot:selection="{ attrs, item, parent, selected }">
            <template v-if="item === Object(item)" v-bind="attrs" :input-value="selected">
                <span style="padding-right: 3px;">
                {{ item.name }}
                </span>
                <v-icon
                small
                @click="parent.selectItem(item)"
                >close</v-icon>
            </template>
            </template>
            <template v-slot:item="{ index, item }">
            <v-text-field
                v-if="editing === item"
                v-model="editing.name"
                autofocus
                flat
                background-color="transparent"
                hide-details
                solo
                @keyup.enter="edit(index, item)"
            ></v-text-field>
            <template v-else small>
                {{ item.name }}
            </template>
            <div class="flex-grow-1"></div>
            <v-list-item-action @click.stop>
                <v-btn
                icon
                @click.stop.prevent="edit(index, item)"
                >
                <v-icon>{{ editing !== item ? 'edit' : 'check' }}</v-icon>
                </v-btn>
            </v-list-item-action>
            </template>
        </v-combobox>

        <!-- <v-btn @click="getListArray">Aktualisieren</v-btn> -->
        <br>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {

    props: [
        'create_text',
        'p_oModel',
        'p_sModel',
        'p_sModels',
        'p_oUser',
        'p_sInputLabel'
    ],

    data () {
        return {
            list: [],
            activator: null,
            attach: null,
            editing: null,
            index: -1,
            nonce: 1,
            menu: false,
            x: 0,
            search: null,
            y: 0,

            model: this.p_oModel,
            sModel: this.p_sModel,
            sModels: this.p_sModels,
            oUser: this.p_oUser,
            sInputLabel: this.p_sInputLabel
        };
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },

    watch: {
        model (val, prev) {
            this.watchProfileInput(val, prev);
        },
    },

    created() {
        if(!this.list || !this.list.length) {
            this.getListArray();
        }

        this.model[0] = this.oUser[this.sModel];
    },

    methods: {
        getListArray() {
            var _this = this;
            // Make a request for a user with a given ID
            axios.get('/api/'+ this.sModels +'/all')
            .then(function (response) {
                // handle success
                _this.list = response.data;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
            .finally(function () {
                // always executed
            });
        },

        edit (index, item) {
            if (!this.editing) {
                this.editing = item
                this.index = index
            } else {
                this.editing = null
                this.index = -1
                // Update Model for Example the Company
                this.updateModel(item);
            }
        },

        setModelForUser(item) {
            axios.patch('/api/user/' + this.sModel + '/set', {
                id: item.id
            });
            this.oUser[this.sModel].id = id;
            this.oUser[this.sModel].name = name;
        },

        updateModel(item) {
            axios.patch('/api/' + this.sModel + '/update', {
                item: item
            });
        },

        filter (item, queryText, itemText) {
            if (item.header) return false
            const hasValue = val => val != null ? val : ''
            const text = hasValue(itemText)
            const query = hasValue(queryText)
            return text.toString()
                .toLowerCase()
                .indexOf(query.toString().toLowerCase()) > -1
        },

        watchProfileInput(val, prev) {
            var _this = this;

            if (val.length === prev.length) return

            if(this.model && this.model.length && this.model.length >= 1) {
                // Update Company ID
                // console.log('Change Company ID');

                var comp = this.model[this.model.length-1];

                if(typeof comp === 'string') {
                    // Make a request for a new one
                    var item = {}
                    item.name = comp;
                    axios.post('/api/' + this.sModel + '/create', {
                        item: item
                    }).then(function (response) {
                        var res = response.data;
                        item.id = res.id;
                        _this.setModelForUser(item);
                        _this.list[_this.list.length-1].id = res.id;
                    });
                } else if (typeof comp === 'object') {
                    // Use Existing Company!
                    _this.setModelForUser(comp);
                }
            } else {
                // Empty Company ID from User
                axios.patch('/api/user/' + this.sModel + '/set', {
                    id: null
                });
            }


            if (val.length > 1) {
                this.$nextTick(() => this.model.shift())
            }
            this.model = val.map(v => {
                if (typeof v === 'string') {
                v = {
                    name: v,

                }
                this.list.push(v)
                this.nonce++
                }
                return v
            })
        }

    },
}
</script>
