<template>
	<div>


			<template>
				<v-btn small outlined depressed color="grey" rounded tag="router-link" :to="oBackRoute" class="small ml-auto my-auto">
					<v-icon left dark>keyboard_arrow_left</v-icon>
					{{ $t('Zurück zu den Umfragen') }}
				</v-btn>
				<br>
				<br>
			</template>

		<div v-if="oSurvey">
			<v-snackbar v-model="oSnackbar.bActive" :timeout="oSnackbar.iDuration" :color="oSnackbar.sColor" top>
				<span v-html="oSnackbar.sText"></span>
				<v-btn text @click="oSnackbar.bActive = false">{{ $t('closer_button') }}</v-btn>
			</v-snackbar>

			<!-- Data Sheet -->
			<template>
				<v-form @submit.prevent="updateSurvey" @keydown="form.onKeydown($event)" v-model="valid" ref="form" style="max-width: 1280px;">
					
					<v-toolbar color="primary" dark >
						<v-toolbar-title>
								<template v-if="bCreate">{{ "Neue Umfrage erstellen" }}</template>
								<template v-if="bEdit && oSurvey">{{ 'Umfrage' }} #{{ oSurvey.id }}</template>
						</v-toolbar-title>
					</v-toolbar>


					<!-- Basic Settings -->
					<v-tabs fixed-tabs color="secondary" icons-and-text>
						<v-tab>
							Basis
							<v-icon>settings</v-icon>
						</v-tab>

						<v-tab>
							Fragen
							<v-icon>question_answer</v-icon>
						</v-tab>

						<v-tab>
							Gruppen
							<v-icon>group</v-icon>
						</v-tab>

						<!-- Einstellungen -->
						<v-tab-item>
							<v-list subheader two-line flat >
									<v-subheader>Textliche Einstellungen</v-subheader>

									<!-- Title -->
									<v-list-item>
										<v-list-item-content>
											<v-text-field 
												:disabled="surveyIsUneditable()"
												dense 
												persistent-hint 
												outlined 
												hint="Titel der Umfrage. Wird angezeigt" 
												v-model="oSurvey.title" 
												label="Title"
												required 
												:rules="required" 
												:placeholder="oSurveyOld.title"
											></v-text-field>
										</v-list-item-content>
									</v-list-item>

									<!-- Author -->
									<v-list-item>
										<v-list-item-content>
											<v-text-field 
												:disabled="surveyIsUneditable()"
												dense 
												persistent-hint 
												outlined 
												hint="Author in Textform. Wird angezeigt" 
												v-model="oSurvey.author" 
												label="Autor" 
												required
											></v-text-field>
										</v-list-item-content>
									</v-list-item>


									<!-- Description Short -->
									<v-list-item>
										<v-list-item-content>
											<v-text-field 
												:disabled="surveyIsUneditable()"
												dense 
												persistent-hint 
												outlined 
												hint="Kurze Beschreibung der Umfrage" 
												v-model="oSurvey.desc_short" 
												label="Kurzbeschreibung" 
												required
											></v-text-field>
										</v-list-item-content>
									</v-list-item>

									<!-- Description Long -->
									<v-list-item>
										<v-list-item-content>
											<v-textarea 
												:disabled="surveyIsUneditable()"
												outlined 
												dense
												persistent-hint 
												v-model="oSurvey.desc_long" 
												hint="Lange Beschreibung der Umfrage" 
												label="Langbeschreibung" 
												required
											></v-textarea>
										</v-list-item-content>
									</v-list-item>


									<v-subheader>Konfigurations-Einstellungen</v-subheader>

									<v-list-item>
										<v-list-item-action>
											<v-checkbox 
												:disabled="surveyIsUneditable()"
											 	v-model="oSurvey.active" 
												color="primary" 
												:true-value="1" 
												:false-value="0"
											></v-checkbox>
										</v-list-item-action>
										<v-list-item-content>
											<v-list-item-title>Aktiviert</v-list-item-title>
											<v-list-item-subtitle>Ist diese Umfrage ausfüllbar (aktiviert) oder nicht ausfüllbar. Standard ist aktiviert.</v-list-item-subtitle>
										</v-list-item-content>
									</v-list-item>

									<v-list-item>
										<v-list-item-action>
											<v-checkbox
											:disabled="surveyIsUneditable()"
											v-model="oSurvey.only_editable_by_creator"
											color="primary"
											:true-value="1" :false-value="0"
											></v-checkbox>
										</v-list-item-action>
										<v-list-item-content>
											<v-list-item-title>Nur für Ersteller Editierbar</v-list-item-title>
											<v-list-item-subtitle>Nur Sie selbst als Ersteller können diese Umfrage editieren. Im Standard sind Umfragen nur für Sie selbst editierbar</v-list-item-subtitle>
										</v-list-item-content>
									</v-list-item>

									<v-list-item>
										<v-list-item-action>
											<v-checkbox 
											:disabled="surveyIsUneditable()"
											v-model="oSurvey.is_public" 
											color="red" 
											:true-value="1" 
											:false-value="0"
											></v-checkbox>
										</v-list-item-action>
										<v-list-item-content>
											<v-list-item-title>Öffentlich</v-list-item-title>
											<v-list-item-subtitle>Diese Umfrage ist öffentlich verfügbar. Im Standard sind Umfragen nicht öffentlich</v-list-item-subtitle>
										</v-list-item-content>
									</v-list-item>


									<template v-if="bEdit">
										<v-list-item>
											<v-list-item-action>
												<v-checkbox 
												v-model="bExtendedSettings" 
												:disabled="oSurvey.is_finished == 1 || oSurvey.is_canceled == 1" 
												color="primary" ></v-checkbox>
											</v-list-item-action>
											<v-list-item-content>
												<v-list-item-title>Erweiterte Einstellungen</v-list-item-title>
												<v-list-item-subtitle>Zeige erweiterte Einstellungen</v-list-item-subtitle>
											</v-list-item-content>
										</v-list-item>

										<template v-if="bExtendedSettings">
											<v-list-item>
												<v-list-item-action>
													<v-checkbox
													 color="error"
													 v-model="oSurvey.is_finished"
													 :true-value="1" :false-value="0" 
													 :disabled="surveyIsUneditable()"
													 ></v-checkbox>
												</v-list-item-action>
												<v-list-item-content>
													<v-list-item-title>Beendet</v-list-item-title>
													<v-list-item-subtitle>Die Umfrage wurde Beendet. Nicht umkehrbar</v-list-item-subtitle>
												</v-list-item-content>
											</v-list-item>

											<v-list-item>
												<v-list-item-action>
													<v-checkbox 
													:disabled="surveyIsUneditable()"
													color="error" 
													v-model="oSurvey.is_canceled" 
													:true-value="1" 
													:false-value="0" ></v-checkbox>
												</v-list-item-action>
												<v-list-item-content>
													<v-list-item-title>Abgebrochen</v-list-item-title>
													<v-list-item-subtitle>Die Umfrage wurde abgebrochen. Nicht umkehrbar</v-list-item-subtitle>
												</v-list-item-content>
											</v-list-item>
										</template>
									</template>


									<v-subheader>Datums-Einstellungen</v-subheader>
									<v-list-item>
							<!-- DateRange-->
							<div>
								<span>
									<v-date-picker 
										:disabled="surveyIsUneditable()"
										required
										:min="getMinDate()" 
										v-model="aDates" 
										range 
										:selected-items-text="getDatesDiffDays() + ' Tage Zeit'"
										color="secondary"
										header-color="primary"
										locale="de-DE"
										></v-date-picker>
								</span>
								<span style="max-width: 770px; display: inline-block;">
									<v-card>
										<v-card-title class="headline">
											Beginn und Ende anpassen
										</v-card-title>

										<v-card-text>
											<p>Sobald ihre Umfrage beginnt - können Sie diese nicht mehr anpassen</p>

											<v-row>
												<v-col cols="12" sm="6" md="6">
													<v-text-field 
													:value="formatDate(oSurvey.start_datetime)"  
													label="Beginnt am"  
													placeholder="Bitte am Kalender wählen" 
													prepend-icon="event_available" 
													readonly 
													:rules="required" 
													disabled></v-text-field>
												</v-col>
												<v-col cols="12" sm="6" md="6">
													<v-dialog ref="dialog2" v-model="bDialogStartTime" :return-value.sync="oTimes.sStartTime" persistent width="290px" >
														<template v-slot:activator="{ on }">
															<v-text-field 
																:disabled="surveyIsUneditable()"
																v-model="oTimes.sStartTime" 
																label="Start-Uhrzeit" 
																prepend-icon="timer" 
																readonly 
																required 
																:rules="required" 
																v-on="on"
															></v-text-field>
														</template>
														<v-time-picker 
														:disabled="surveyIsUneditable()"
														required 
														v-if="bDialogStartTime" 
														:max="getStartTimeMax()" 
														v-model="oTimes.sStartTime" 
														full-width 
														format="24hr" 
														color="secondary">
															<v-spacer></v-spacer>
															<v-btn text color="primary" @click="bDialogStartTime = false">Cancel</v-btn>
															<v-btn text color="primary" @click="$refs.dialog2.save(oTimes.sStartTime)">OK</v-btn>
														</v-time-picker>
													</v-dialog>
												</v-col>
											</v-row>


											<v-row>
												<v-col cols="12" sm="6" md="6">
													<v-text-field 
														:value="formatDate(oSurvey.end_datetime)" 
														label="Endet am" 
														placeholder="Bitte am Kalender wählen" 
														prepend-icon="event_busy" 
														readonly 
														:rules="required" 
														disabled
													></v-text-field>
												</v-col>

												<v-col cols="12" sm="6" md="6">
													<v-dialog ref="dialog4" v-model="bDialogEndTime" :return-value.sync="oTimes.sEndTime" persistent width="290px" >
														<template v-slot:activator="{ on }">
															<v-text-field 
															:disabled="surveyIsUneditable()"
															v-model="oTimes.sEndTime"
															label="End-Uhrzeit" 
															prepend-icon="timer_off"
															required
															readonly
															v-on="on"
															:rules="required" 
															></v-text-field>
														</template>
														<v-time-picker :disabled="surveyIsUneditable()"
														 required 
														 v-if="bDialogEndTime" 
														 :min="getEndTimeMin()" 
														 v-model="oTimes.sEndTime" 
														 full-width format="24hr" color="primary">
															<v-spacer></v-spacer>
															<v-btn text color="primary" @click="bDialogEndTime = false">Cancel</v-btn>
															<v-btn text color="primary" @click="$refs.dialog4.save(oTimes.sEndTime)">OK</v-btn>
														</v-time-picker>
													</v-dialog>
												</v-col>
											</v-row>

											<v-row>
												<v-col>
												<v-text-field
													label="Zeit für die Umfrage"
													hide-details
													:value="getDiffDatetimeLabel()"
													style="max-width: 320px;"
													dense 
													disabled
													prepend-icon="event_note" 
													readonly
												></v-text-field>
												</v-col>
											</v-row>

										</v-card-text>
									</v-card>
								</span>
							</div>
									</v-list-item>
							</v-list>
						</v-tab-item>
						<v-tab-item>
							Fragen
						</v-tab-item>

						<!-- Gruppeneinstellungen -->
						<v-tab-item>
							<v-list subheader two-line flat >
									<v-subheader>Gruppen</v-subheader>

									<!-- Title -->
									<v-list-item>
										<v-list-item-content>
											<v-select
												v-model="oSurvey.groups"
												:items="user.groups_moderating"
												label="Gruppen Auswählen"
												chips
												multiple
												return-object
												item-text="name"
												:disabled="surveyIsUneditable()"
											>
											</v-select>
										</v-list-item-content>
									</v-list-item>
							</v-list>


						</v-tab-item>
					</v-tabs>

					<v-list>
								<v-list-item>
									<v-btn 
										color="grey" 
										dark 
										class="mr-4"
									>Zurück</v-btn>
									<v-btn 
										dark 
										:color="isUnsaved() ? 'success' : 'grey' "  
										type="submit" 
										class="mr-4" 
										:disabled="!valid || surveyIsUneditable()"
									>Speichern</v-btn>
								</v-list-item>
					</v-list>



				</v-form>
			</template>
		</div>
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
			bExtendedSettings: false,

			// Form
			valid: false,
      required: [
        v => !!v || this.$t('required'),
      ],

			// Dates and Times
			aDates: [],
			oTimes : {
				sStartTime: '',
				sEndTime: '',
			},

			// Today
			sToday: new Date().toISOString().substr(0, 10),

			// Tmps Start
			bDialogStartDate: false,
			bDialogStartTime: false,
			sStartDate: new Date().toISOString().substr(0, 10),

			// Tmps End
			bDialogEndDate: false,
			bDialogEndTime: false,
			sEndDate: new Date(new Date() + 5).toISOString().substr(0, 10),

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
			if(this.oSurvey.start_datetime)  this.aDates[0] = this.oSurvey.start_datetime.substr(0, 10);
			if(this.oSurvey.end_datetime)    this.aDates[1] = this.oSurvey.end_datetime.substr(0, 10);
			this.oTimes.sStartTime = this.oSurvey.start_datetime.substr(11, 5);
			this.oTimes.sEndTime   = this.oSurvey.end_datetime.substr(11, 5);

			// If Something inside bExtendedSettings is active
			if(this.oSurvey.is_finished || this.oSurvey.is_canceled) {
				this.bExtendedSettings = true;
			}
		},

		'oSurvey': {
			handler() {
				// console.log('oSurvey Changed');
				// this.$refs.form.validate();
			},
			deep: true
		},

		'aDates': {
			handler() {
				this.updateDatetimes();
			}
		},

		'oTimes': {
			handler() {
				this.updateDatetimes();
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

		surveyIsEditable() {
			return (this.oSurvey.is_editable && this.bEdit) || this.bCreate;
		},

		surveyIsUneditable() {
			return !this.surveyIsEditable();
		},

		getMinDate() {
			return (this.oSurvey.active) ? this.sToday : undefined;
		},

		getDiffDatetimeLabel() {
			if(!this.oSurvey || !this.oSurvey.start_datetime || !this.oSurvey.end_datetime) {
				return '';
			}

			var t1 = new Date(this.oSurvey.start_datetime);
			var t2 = new Date(this.oSurvey.end_datetime);

			// get total seconds between the times
			var delta = Math.abs(t1 - t2) / 1000;

			// calculate (and subtract) whole days
			var iDays = Math.floor(delta / 86400);
			var sDays = '';
			delta -= iDays * 86400;
			if(iDays) sDays = iDays + ' '+ this.$t('days') + ' ';

			// calculate (and subtract) whole hours
			var iHours = Math.floor(delta / 3600) % 24;
			var sHours = '';
			delta -= iHours * 3600;
			if(iHours) sHours = iHours + ' '+ this.$t('hours') + ' ';

			// calculate (and subtract) whole minutes
			var iMinutes = Math.floor(delta / 60) % 60;
			var sMinutes = '';
			delta -= iMinutes * 60;
			if(iMinutes) sMinutes = iMinutes + ' '+ this.$t('minutes') + ' ';

			return sDays + sHours + sMinutes;
		},

		isSameDay(dates) {
			if(dates && dates[0] == dates[1]) {
				return true;
			}
			return false;
		},

		getStartTimeMax () {
			var dates = this.aDates;
			if(this.isSameDay(dates)) {
				return this.oTimes.sEndTime+':00';
			}
		},

		getEndTimeMin () {
			var dates = this.aDates;
			if(this.isSameDay(dates)) {
				return this.oTimes.sStartTime+':00';
			}
		},

		updateDatetimes() {
			if(this.oSurvey) {

				var d1 = (this.aDates[0]) ? this.aDates[0].substr(0, 10) : '';
				var d2 = (this.aDates[1]) ? this.aDates[1].substr(0, 10) : '';

				var t1 = (this.oTimes.sStartTime) ? ' ' + this.oTimes.sStartTime + ':00'   : '';
				var t2 = (this.oTimes.sEndTime)   ? ' ' + this.oTimes.sEndTime   + ':00'   : '';

				var tmp = '';

				if(d1 > d2) {
					tmp = d2;
					d2 = d1;
					d1 = tmp;
				}

				this.oSurvey.start_datetime = d1 + t1 ;
				this.oSurvey.end_datetime   = d2 + t2 ;
			}
		},

		getDatesDiffDays() {
			return this.getDiffDays(this.aDates[0], this.aDates[1]);
		},

		getDiffDays(d1, d2) {
			var date1 = new Date(d1);
			var date2 = new Date(d2);
			var diff_in_time = date2.getTime() - date1.getTime(); 
			var diff_in_days = diff_in_time / (1000 * 3600 * 24); 
  
			return (Math.abs(diff_in_days) + 1) .toString();
		},

		getEvents() {
			return [this.sStartDate, this.sEndDate];
		},

		formatDate(sDate) {
			var locale = '';
			var dDate = new Date(sDate);

			switch (this._i18n.locale) {
				case 'de':
					locale = 'de-DE';
					break;
			
				default:
					locale = this._i18n.fallbackLocale;
					break;
			}

			if(sDate && dDate) {
				return dDate.toLocaleDateString(locale);
			}
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

			var tmp = {
				active: 1,
				only_editable_by_creator: 1,
			};

			if(!this.oSurvey) this.oSurvey = this.copyObject(tmp);
			if(!this.oSurveyOld) this.oSurveyOld = this.copyObject(tmp);
		},

		copyObject(obj) {
			if(typeof obj != 'undefined') {
				var copy = JSON.parse(JSON.stringify(obj));
				if(copy) return copy
			}
		},

		isSaved() {
		    var itemL = this.copyObject(this.oSurvey);
		    var itemR = this.copyObject(this.oSurveyOld);

		    if(itemL && itemR) {
		        // Differences
		        if(JSON.stringify(itemR) != JSON.stringify(itemL)) {
		            console.log(JSON.stringify(itemR), JSON.stringify(itemL));
		            return false;
		        } else {
		            return true;
		        }
		    }
		    return true;
		},

		isUnsaved() {
		    return !this.isSaved();
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

					if(_t.bCreate) {
						_t.$router.push({
							name: 'backend.survey', 
							params: {
								id: _t.oSurvey.id
							}
						});
					}

					_t.startEditMode();

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