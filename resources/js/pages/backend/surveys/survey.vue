<template>
    <div v-if="oSurvey">

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
            <v-form ref="form" lazy-validation style="max-width: 1280px;">
                <v-text-field v-model="oSurvey.title" label="Title" required></v-text-field>
                <v-checkbox v-model="oSurvey.active" label="Active"></v-checkbox>

                <v-text-field label="Prepend inner" prepend-inner-icon="place"></v-text-field>

                <!-- DateRange-->
                <div>
                    <span>
                        <v-card></v-card>
                    </span>
                    <span>
                        <v-date-picker 
                            :min="sToday" 
                            landscape 
                            v-model="dates" 
                            range 
                            :selected-items-text="getDiffDays(dates[0], dates[1]) + ' Tage Zeit'"
                            locale="de-DE"
                            ></v-date-picker>
                    </span>
                </div>

                <!-- Start Datetime-->
                <template>
                    <v-card>
                        <v-card-title class="headline">Start und Enddatum festlegen</v-card-title>
                        <v-card-text>
                            Sobald ihre Umfrage beginnt - können Sie diese nicht mehr anpassen
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-dialog ref="dialog1" v-model="bDialogStartDate" :return-value.sync="sStartDate" persistent width="290px">
                                            <template v-slot:activator="{ on }">
                                                <v-text-field :value="formatDate(sStartDate, 'de')" label="Startdatum" prepend-icon="event" readonly v-on="on"></v-text-field>
                                            </template>
                                            <v-date-picker v-model="sStartDate" color="success" :min="sToday" :events="getEvents()" >
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary" @click="bDialogStartDate = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="$refs.dialog1.save(sStartDate)">OK</v-btn>
                                            </v-date-picker>
                                        </v-dialog>
                                    </v-col>

                                    <v-col cols="12" sm="6" md="6">
                                        <v-dialog ref="dialog2" v-model="bDialogStartTime" :return-value.sync="sStartTime" persistent width="290px" >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field v-model="sStartTime" label="Start-Uhrzeit" prepend-icon="access_time" readonly v-on="on" ></v-text-field>
                                            </template>
                                            <v-time-picker v-if="bDialogStartTime" v-model="sStartTime" full-width format="24hr" color="success">
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary" @click="bDialogStartTime = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="$refs.dialog2.save(sStartTime)">OK</v-btn>
                                            </v-time-picker>
                                        </v-dialog>
                                    </v-col>
                                </v-row>
                        
                        </v-card-text>
                    </v-card>
                </template>

                <br>

                <!-- End Datetime-->
                <template>
                    <v-card>
                        <v-card-title class="headline">End-Datum festlegen</v-card-title>
                        <v-card-text>
                            Wann soll ihre Umfrage nicht mehr ausfüllbar sein?
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-dialog ref="dialog3" v-model="bDialogEndDate" :return-value.sync="sEndDate" persistent width="290px">
                                            <template v-slot:activator="{ on }">
                                                <v-text-field :value="formatDate(sEndDate, 'de')" label="End-Datum" prepend-icon="event" readonly v-on="on"></v-text-field>
                                            </template>
                                            <v-date-picker v-model="sEndDate" color="red" :min="sStartDate" :events="getEvents()" >
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary" @click="bDialogEndDate = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="$refs.dialog3.save(sEndDate)">OK</v-btn>
                                            </v-date-picker>
                                        </v-dialog>
                                    </v-col>

                                    <v-col cols="12" sm="6" md="6">
                                        <v-dialog ref="dialog4" v-model="bDialogEndTime" :return-value.sync="sStartTime" persistent width="290px" >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field v-model="sStartTime" label="Start-Uhrzeit" prepend-icon="access_time" readonly v-on="on" ></v-text-field>
                                            </template>
                                            <v-time-picker v-if="bDialogEndTime" v-model="sStartTime" full-width format="24hr" color="red">
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary" @click="bDialogEndTime = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="$refs.dialog4.save(sStartTime)">OK</v-btn>
                                            </v-time-picker>
                                        </v-dialog>
                                    </v-col>
                                </v-row>
                        
                        </v-card-text>
                    </v-card>
                </template>

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

            // Dates
            dates: [],

            // Today
            sToday: new Date().toISOString().substr(0, 10),

            // Tmps Start
            bDialogStartDate: false,
            bDialogStartTime: false,
            sStartDate: new Date().toISOString().substr(0, 10),
            sStartTime: null,

            // Tmps End
            bDialogEndDate: false,
            bDialogEndTime: false,
            sEndDate: new Date(new Date() + 5).toISOString().substr(0, 10),
            sEndTime: null,

            // Surveys
            oSurvey : null,
            oSurveyOld: null,

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
            this.oSurvey = this.copyObject(promise);
            this.oSurveyOld = this.copyObject(promise);

            // Go
            this.dates[0] = this.oSurvey.start_datetime.substr(0, 10);
            this.dates[1] = this.oSurvey.end_datetime.substr(0, 10);
            console.log('jo', this.dates);
        },



        'oSurvey': {
            handler() {
                console.log('oSurvey Changed');
            },
            deep: true
            
        }
    },

    created: function() {
        var route = this.$route;
        var params = route.params;
        var id = parseInt(params.id);

        if (params.id == 'create') 
        {
            this.startCreateMode();
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

        getDiffDays(d1, d2) {
            var date1 = new Date(d1);
            var date2 = new Date(d2);
            var diff_in_time = date2.getTime() - date1.getTime(); 
            var diff_in_days = diff_in_time / (1000 * 3600 * 24); 
  
            return (diff_in_days + 1) .toString();
        },

        getEvents() {
            return [this.sStartDate, this.sEndDate];
        },

        formatDate(date) {
            var locale = '';

            switch (this._i18n.locale) {
                case 'de':
                    locale = 'de-DE';
                    break;
            
                default:
                    locale = this._i18n.fallbackLocale;
                    break;
            }

            return new Date(date).toLocaleDateString(locale);
        },

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

        copyObject(obj) {
            if(typeof obj != 'undefined') {
                var copy = JSON.parse(JSON.stringify(obj));
                if(copy) return copy
            }
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
                    _t.oSurvey = _t.copyObject(e.data);
                    _t.oSurveyOld = _t.copyObject(e.data);
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