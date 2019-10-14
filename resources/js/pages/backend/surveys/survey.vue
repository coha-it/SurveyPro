<template>
    <div>

        <v-snackbar v-model="oSnackbar.bActive" :timeout="oSnackbar.iDuration" :color="oSnackbar.sColor" top>
            <span v-html="oSnackbar.sText"></span>
            <v-btn text @click="oSnackbar.bActive = false">{{ $t('closer_button') }}</v-btn>
        </v-snackbar>

        <template>
            <router-link :to="oBackRoute" class="small ml-auto my-auto">
                {{ $t('Back to Surveys') }}
            </router-link>
        </template>

        <h1> 
            <template v-if="bCreate">{{ "Survey - Create new" }}</template>
            <template v-if="bEdit && oSurvey">{{ 'Survey Edit' }} #{{ oSurvey.id }}</template>
        </h1>
        
        <p>
            <template v-if="bCreate">{{ "Create a new Survey" }}</template>
            <template v-if="bEdit && oSurvey">{{ 'Edit Survey ' }} "{{ oSurvey.title }}"</template>
        </p>

        <!-- Data Sheet -->
        <template>
            <v-form ref="form" lazy-validation>
                <v-text-field v-model="oSurvey.title" label="Title" required></v-text-field>
                <v-checkbox v-model="oSurvey.active" label="Active"></v-checkbox>

                <v-text-field label="Prepend inner" prepend-inner-icon="place"></v-text-field>

                <v-text-field v-model="oSurvey.start_datetime"></v-text-field>
                <v-text-field v-model="oSurvey.end_datetime"></v-text-field>

                <v-btn color="success" class="mr-4" @click="updateSurvey(oSurvey)">Speichern</v-btn>
                <v-btn color="error" class="mr-4">Reset Form</v-btn>
                <v-btn color="warning">Reset Validation</v-btn>
            </v-form>
        </template>


    </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {

    data() {
        return {
            oBackRoute: { name: 'backend.surveys' },
            bCreate: false,
            bEdit: false,

            oSurvey : {},
            oSurveyOld: {},

            // Snackbar
            oSnackbar: {
                bActive: false,
                sColor: '',
                sText: '',
                iDuration: 3000,
            },

        };
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
            surveyCreated: 'surveys/surveyCreated'
        })
    },


    watch: {
        surveyCreated(promise) 
        {
            // save Promise result in local state
            this.oSurvey = this.duplicateObject(promise);
            this.oSurveyOld = this.duplicateObject(promise);
        }
    },

    created: function() {
        var route = this.$route;
        var params = route.params;
        var id = parseInt(params.id);

        if (params.id == 'create') 
        {
            this.startCreateMode();
            this.oSurvey.start_datetime = '2019-08-08 00:00:00';
            this.oSurvey.end_datetime = '2019-08-08 00:00:00';

        } 
        else if (typeof id == "number") 
        {
            this.$store.dispatch('surveys/fetchSurveyCreated', id)
            this.startEditMode();
        } 
        else 
        {   
            this.$router.push(this.oBackRoute);
        }

    },

    methods: {

        showSnackbar: function() {
            this.oSnackbar.bActive = true;
        },

        showSnackbarError: function(text) {
            this.showSnackbar();

            this.oSnackbar.iDuration = 6000;
            this.oSnackbar.sColor = 'error';
            this.oSnackbar.sText = text;
        },

        showSnackbarSuccess: function(text) {
            this.showSnackbar();

            this.oSnackbar.iDuration = 3000;
            this.oSnackbar.sColor = 'success';
            this.oSnackbar.sText = text;
        },

        startEditMode: function() {
            this.bCreate = false;
            this.bEdit = true;
        },

        startCreateMode: function() {
            this.bCreate = true;
            this.bEdit = false;
        },

        duplicateObject: function(obj) {
            return JSON.parse(JSON.stringify(obj));
        },

        updateSurvey: function() {
            var _t = this;

            // Update Users
            this.$store.dispatch('surveys/updateSurvey', {
                survey: _t.oSurvey
            }).then(function(e) {
                // Success
                if(!e || !e.response || !e.response.data || !e.response.data.error) {
                    _t.showSnackbarSuccess(_t.$t('data_saved'));
                    _t.oSurvey = _t.duplicateObject(e.data);
                    _t.oSurveyOld = _t.duplicateObject(e.data);
                    _t.startEditMode();

                    _t.$router.push({
                        name: 'backend.survey', 
                        params: {
                            id: _t.oSurvey.id
                        }
                    });
                }
                _t.bLoading = false;
            }).catch(function(e) {
                console.log(e);
                // Error
                if(e.reponse && e.reponse.data && e.response.data.error)
                {
                    var errText = '';
                    for (var e in e.response.data.error) {
                        errText += ': ' + err[e];
                    }
                }

                _t.showSnackbarError(_t.$t('data_unsaved') + "<br />" + errText);

                _t.bLoading = false;
            });
        }
    }

}
</script>