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
					<v-tabs fixed-tabs color="secondary" icons-and-text v-model="active_tab">
						<v-tab @click="changeTab(0)">
							Basis
							<v-icon>settings</v-icon>
						</v-tab>

						<v-tab @click="changeTab(1)">
							Fragen
							<v-icon>question_answer</v-icon>
						</v-tab>

						<v-tab @click="changeTab(2)">
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
												:placeholder="oSurveyOld.title ? oSurveyOld.title : 'z.B.: &quot;Umfrage Mitarbeiterzufriedenheit&quot; '"
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
												:placeholder="oSurveyOld.author ? oSurveyOld.author : 'z.B.: &quot;Dr. Johannes Müller&quot; '"
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

            <!-- Questions -->
						<v-tab-item>
							<v-list>
                <v-list-item>
                  <v-list-item-content>
                    <!-- No Select Toolbar -->
                    <v-toolbar class="coha--toolbar" v-if="selected.length <= 0"  :flat="sSearch == ''" floating min-height="85px" height="auto">
                        <!-- <v-switch class="mt-6 mr-6" v-model="bTableDense"  color="primary"></v-switch> -->
                        <div class="flex-grow-1"></div>
                        <v-text-field style="max-width: 400px;" v-model="sSearch" :label="$t('Search')" autocomplete="off"  append-icon="search" hide-details outlined></v-text-field>
                        <v-text-field v-model="iItemsPerPage" number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outlined ></v-text-field>
                    </v-toolbar>

                    <v-toolbar class="coha--toolbar" v-else :flat="sSearch == ''" color="primary"  dark floating min-height="85px" height="auto">

                        <v-menu right offset-y>
                            <template v-slot:activator="{ on:menuedit }">
                                <v-btn text rounded v-on="{ ...menuedit }">
                                    <v-icon left>mdi-pencil</v-icon> {{ selected.length + ' ' + $t('edit') }}
                                </v-btn>
                            </template>
                            <v-list>
                              <v-list-item @click="duplicateSelectedQuestions()">
                                Duplizieren
                              </v-list-item>
                              <v-list-item @click="bDeleteQuestionDialog = true">
                                Löschen
                              </v-list-item>
                            </v-list>
                        </v-menu>

												<v-tooltip top>
													<template v-slot:activator="{ on }">
														<v-btn @click="moveSelectedUp()" icon text rounded v-on="on">
															<v-icon>mdi-chevron-up</v-icon>
														</v-btn>
													</template>
													<span>Position Hoch</span>
												</v-tooltip>


												<v-tooltip top>
													<template v-slot:activator="{ on }">
														<v-btn @click="moveSelectedDown()" icon text rounded v-on="on">
															<v-icon>mdi-chevron-down</v-icon>
														</v-btn>
													</template>
													<span>Position Runter</span>
												</v-tooltip>

												<!-- <div class="flex-grow-1"></div> -->

                    </v-toolbar>


                    <!-- Delete - Dialog -->
                    <v-dialog v-model="bDeleteQuestionDialog" max-width="500" dark content-class="naked dark centered">
                      <h2 class="display-2">Fragen Löschen?</h2>
                      <p>Möchten Sie {{ selected.length }} Fragen löschen?</p>
                      <v-container fluid>
                        <v-row align="center">
                          <v-col class="text-center" cols="12" sm="12">
                            <v-btn depressed @click="bDeleteQuestionDialog = false" outlined>Abbruch</v-btn>
                            <v-btn depressed @click.prevent="deleteQuestions(selected)" color="error">Löschen</v-btn>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-dialog>


                    <v-data-table
                      :headers="headers"

                      v-model="selected"
                      :items="oSurvey.questions"

                      dense
                      multi-sort
                      show-select

                      show-expand
                      :expanded.sync="expanded"

                      :search="sSearch"
                      :sort-by="['order']"
                      :sort-desc="[false]"

                      :items-per-page="parseInt(iItemsPerPage)"
                      :footer-props="{
                          showFirstLastPage: true,
                      }"
                    >

                      <template v-slot:item.title="props">
                        <v-edit-dialog :return-value.sync="props.item.title">
                          {{ props.item.title }}
                          <template v-slot:input>
                            <v-text-field
                              v-model="props.item.title"
                              label="Edit"
                              counter
                            ></v-text-field>
                          </template>
                        </v-edit-dialog>
                      </template>

                      <template v-slot:item.subtitle="props">
                        <v-edit-dialog :return-value.sync="props.item.subtitle">
                          {{ props.item.subtitle }}
                          <template v-slot:input>
                            <v-text-field
                              v-model="props.item.subtitle"
                              label="Edit"
                              counter
                            ></v-text-field>
                          </template>
                        </v-edit-dialog>
                      </template>

                      <template v-slot:item.description="props">
                        <v-edit-dialog :return-value.sync="props.item.description">
                          {{ props.item.description }}
                          <template v-slot:input>
                            <v-text-field
                              v-model="props.item.description"
                              label="Edit"
                              counter
                            ></v-text-field>
                          </template>
                        </v-edit-dialog>
                      </template>

                      <template v-slot:item.order="props">
                        <div style="white-space: nowrap;">
                          <v-icon @click="moveUp(props.item, oSurvey.questions)" x-small>mdi-arrow-up</v-icon>
                            {{ props.item.order }}
                          <v-icon @click="moveDown(props.item, oSurvey.questions)" x-small>mdi-arrow-down</v-icon>
                        </div>
                      </template>

                      <!-- Expand Area -->
                      <template v-slot:expanded-item="{ headers, item }">
                        <td colspan="100%">
                          <v-row>
                            <v-col xl="12" sm="12" xs="12">
                              <v-card>
                                <v-card-title>Frage-Einstellungen #{{ item.id }}</v-card-title>
                                <!-- <v-card-text>Einstellungen zur {{ item.order }}. Frage</v-card-text> -->
                              <v-list subheader two-line flat >

                                <v-subheader>Allgemeine Frage-Einstellungen</v-subheader>

                                <v-list-item>
                                  <v-list-item-action>
                                    <v-checkbox v-model="item.is_skippable" :disabled="surveyIsUneditable()" color="primary" :true-value="1" :false-value="0" ></v-checkbox>
                                  </v-list-item-action>
                                  <v-list-item-content>
                                    <v-list-item-title>Überspringbar</v-list-item-title>
                                    <v-list-item-subtitle>Ist diese Frage überspringbar</v-list-item-subtitle>
                                  </v-list-item-content>
                                </v-list-item>

                                <v-list-item>
                                  <v-list-item-content>
                                    <v-text-field
                                      :disabled="surveyIsUneditable()"
                                      dense
                                      persistent-hint
                                      outlined
                                      hint="Titel der Frage. Wird am größten Angezeigt"
                                      placeholder="z.B.: 'Fazit zur Umfrage' "
                                      v-model="item.title"
                                      label="Titel"
                                      required
                                    ></v-text-field>
                                  </v-list-item-content>
                                </v-list-item>

                                <v-list-item>
                                  <v-list-item-content>
                                    <v-text-field
                                      :disabled="surveyIsUneditable()"
                                      dense
                                      persistent-hint
                                      outlined
                                      hint="Subtitel / Untertitel der Frage. Wird unter Titel angezeigt"
                                      placeholder="z.B.: 'Wie fanden Sie diese Umfrage?' "
                                      v-model="item.subtitle"
                                      label="Untertitel"
                                      required
                                    ></v-text-field>
                                  </v-list-item-content>
                                </v-list-item>

                                <v-list-item>
                                  <v-list-item-content>
                                    <v-textarea
                                      :disabled="surveyIsUneditable()"
                                      dense
                                      persistent-hint
                                      outlined
                                      hint="Beschreibung der Umfrage. Wird unter Titel / Subtitel klein angezeigt."
                                      placeholder="z.B.: 'Bewerten Sie diese Umfrage bitte mit 0 (negativ) bis 10 (positiv) Punkten' "
                                      v-model="item.description"
                                      label="Beschreibung"
                                      required
                                    ></v-textarea>
                                  </v-list-item-content>
                                </v-list-item>

                                <v-subheader>Kommentar-Einstellungen</v-subheader>

                                <v-list-item>
                                  <v-list-item-action>
                                    <v-checkbox v-model="item.is_commentable" :disabled="surveyIsUneditable()" color="primary" :true-value="1" :false-value="0" ></v-checkbox>
                                  </v-list-item-action>
                                  <v-list-item-content>
                                    <v-list-item-title>Kommentierbar</v-list-item-title>
                                    <v-list-item-subtitle>Ist diese Frage kommentierbar</v-list-item-subtitle>
                                  </v-list-item-content>
                                </v-list-item>

                                <template v-if="item.is_commentable || true">
                                  <v-list-item>
                                    <v-list-item-action>
                                      <v-checkbox v-model="item.comment_is_required" :disabled="surveyIsUneditable() && item.is_commentable" color="primary" :true-value="1" :false-value="0" ></v-checkbox>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                      <v-list-item-title>Kommentar ist erforderlich</v-list-item-title>
                                      <v-list-item-subtitle>Ist ein Kommentar erforderlich</v-list-item-subtitle>
                                    </v-list-item-content>
                                  </v-list-item>

                                  <v-list-item>
                                    <v-list-item-action>
                                      <v-checkbox v-model="item.comment_is_number" :disabled="surveyIsUneditable()" color="primary" :true-value="1" :false-value="0" ></v-checkbox>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                      <v-list-item-title>Kommentar ist eine Nummer</v-list-item-title>
                                      <v-list-item-subtitle>Wenn der Kommentar eine Nummer sein soll</v-list-item-subtitle>
                                    </v-list-item-content>
                                  </v-list-item>

                                  <v-list-item>
                                    <v-list-item-content>
                                      <v-text-field
                                        :disabled="surveyIsUneditable()"
                                        dense
                                        persistent-hint
                                        outlined
                                        hint="Kommentar: Maximale Zeichen"
                                        placeholder="1 - 255"
                                        type="number"
                                        v-model="item.comment_max_signs"
                                        label="Maximale Zeichen"
                                        required
                                      ></v-text-field>
                                    </v-list-item-content>
                                  </v-list-item>
                                </template>

                                <v-subheader>Einstellungen: Optionen</v-subheader>

                                  <v-list-item>
                                    <v-list-item-content>
                                      <v-row>
                                        <v-col xl="6" sm="6" xs="12">
                                          <v-text-field
                                            :disabled="surveyIsUneditable()"
                                            dense
                                            persistent-hint
                                            outlined
                                            hint="Minimal wählbare Optionen"
                                            placeholder="1 - 255"
                                            type="number"
                                            v-model="item.min_options"
                                            label="Minimale Optionen"
                                            required
                                          ></v-text-field>
                                        </v-col>
                                        <v-col xl="6" sm="6" xs="12">
                                          <v-text-field
                                            :disabled="surveyIsUneditable()"
                                            dense
                                            persistent-hint
                                            outlined
                                            hint="Maximale wählbare Optionen"
                                            placeholder="1 - 255"
                                            type="number"
                                            :min="item.min_options"
                                            v-model="item.max_options"
                                            label="Maximale Optionen"
                                            required
                                          ></v-text-field>
                                        </v-col>
                                      </v-row>
                                    </v-list-item-content>
                                  </v-list-item>
                                </v-list>


                                <v-data-table
                                  :headers="aOptionHeaders"

                                  v-model="aSelectedOptions"
                                  :items="item.options"

                                  dense
                                  multi-sort
                                  show-select

                                  :sort-by="['order']"
                                  :sort-desc="[false]"
                                  :footer-props="{
                                      showFirstLastPage: true,
                                  }"
                                >

                                  <template v-slot:item.value="props">
                                    <v-edit-dialog :return-value.sync="props.item.value">
                                      {{ props.item.value }}
                                      <template v-slot:input>
                                        <v-text-field
                                          v-model="props.item.value"
                                          label="Edit"
                                          type="number"
                                        ></v-text-field>
                                      </template>
                                    </v-edit-dialog>
                                  </template>

                                  <template v-slot:item.title="props">
                                    <v-edit-dialog :return-value.sync="props.item.title">
                                      {{ props.item.title }}
                                      <template v-slot:input>
                                        <v-text-field
                                          v-model="props.item.title"
                                          label="Edit"
                                          counter
                                        ></v-text-field>
                                      </template>
                                    </v-edit-dialog>
                                  </template>

                                  <template v-slot:item.subtitle="props">
                                    <v-edit-dialog :return-value.sync="props.item.subtitle">
                                      {{ props.item.subtitle }}
                                      <template v-slot:input>
                                        <v-text-field
                                          v-model="props.item.subtitle"
                                          label="Edit"
                                          counter
                                        ></v-text-field>
                                      </template>
                                    </v-edit-dialog>
                                  </template>

                                  <template v-slot:item.description="props">
                                    <v-edit-dialog :return-value.sync="props.item.description">
                                      {{ props.item.description }}
                                      <template v-slot:input>
                                        <v-text-field
                                          v-model="props.item.description"
                                          label="Edit"
                                          counter
                                        ></v-text-field>
                                      </template>
                                    </v-edit-dialog>
                                  </template>

                                  <template v-slot:item.order="props">
                                    <div style="white-space: nowrap;">
                                      <v-icon @click="moveUp(props.item, item.options)" x-small>mdi-arrow-up</v-icon>
                                        {{ props.item.order }}
                                      <v-icon @click="moveDown(props.item, item.options)" x-small>mdi-arrow-down</v-icon>
                                    </div>
                                  </template>
                                  <template v-slot:item.color="props">

                                    <v-dialog v-model="props.item.dialog" max-width="290">
                                      <template v-slot:activator="{ on }">
                                        <div><v-btn
                                          v-on="on"
                                          small
                                          depressed
                                          fab
                                          dark
                                          :color="props.item.color"
                                        >
                                          <v-icon dark>mdi-palette</v-icon>
                                        </v-btn></div>
                                        <div>{{ props.item.color }}</div>
                                      </template>
                                      <v-card>
                                        <v-card-title>Farbe auswählen</v-card-title>
                                        <v-card-text>
                                          <v-btn block small :color="props.item.color">{{ props.item.color }}</v-btn>
                                          <template v-for="oColor in aAllOptionColors">
                                            <div style="margin-top: 5px;" v-bind:key="oColor.title" >
                                              <v-btn block small :color="oColor.hex" @click="props.item.color = oColor.hex">{{ oColor.title }}</v-btn>
                                            </div>
                                          </template>
                                        </v-card-text>
                                        <v-color-picker
                                          mode="rgba"
                                          value="#666666"
                                          v-model="props.item.color"
                                          flat
                                        ></v-color-picker>
                                        <v-card-actions>
                                          <v-btn  @click="props.item.dialog = false">Übernehmen und schließen</v-btn>
                                        </v-card-actions>
                                      </v-card>
                                    </v-dialog>
                                  </template>
                                </v-data-table>
                                <v-card-actions>
                                  <v-btn @click="addNewOption(item)">Neue Option</v-btn>&nbsp;
                                  <v-btn @click="bDeleteOptionDialog = true">Ausgewählte Optionen Löschen</v-btn>

                                  <!-- Delete - Dialog -->
                                  <v-dialog v-model="bDeleteOptionDialog" max-width="500" dark content-class="naked dark centered">
                                    <h2 class="display-2">Fragen Löschen?</h2>
                                    <p>Möchten Sie {{ selected.length }} Fragen löschen?</p>
                                    <v-container fluid>
                                      <v-row align="center">
                                        <v-col class="text-center" cols="12" sm="12">
                                          <v-btn depressed @click="bDeleteOptionDialog = false" outlined>Abbruch</v-btn>
                                          <v-btn depressed @click.prevent="deleteOptions(item, aSelectedOptions)" color="error">Löschen</v-btn>
                                        </v-col>
                                      </v-row>
                                    </v-container>
                                  </v-dialog>

                                </v-card-actions>
                              </v-card>
                            </v-col>
                          </v-row>
                        </td>
                      </template>
                    </v-data-table>
                  </v-list-item-content>
                </v-list-item>

                <v-list-item>
                  <v-btn @click="addNewQuestion()">
                    <v-icon left>plus_one</v-icon>
                    Neue Frage hinzufügen
                  </v-btn>
									&nbsp; &nbsp;
                  <v-btn @click="duplicateLastQuestion()" color="primary" :disabled="oSurvey.questions.length <= 0">
                    <v-icon left>control_point_duplicate</v-icon>
                    Letzte Frage duplizieren
                  </v-btn>
                </v-list-item>

                <v-list-item></v-list-item>


                <v-divider></v-divider>

              </v-list>
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
									<v-text-field v-model="oSurvey.title" required :rules="required" style="display: none;"></v-text-field>

									<v-btn
										color="grey"
										dark
										class="mr-4"
									>Zurück</v-btn>
									<v-btn
										color="success"
										type="submit"
										class="mr-4 white--text"
										v-if="surveyIsEditable()"
										:disabled="surveyFormIsInvalid()"
									>Umfrage Speichern {{ isUnsaved() ? '*' : undefined }}</v-btn>
								</v-list-item>
					</v-list>



				</v-form>
			</template>
		</div>

		<!-- Dialog Loading -->
    <v-dialog
      v-model="bIsLoading"
			max-width="500" dark content-class="naked dark centered"
			persistent
    >
			<v-progress-circular
				:size="70"
				:width="5"
				indeterminate
				color="white"
			></v-progress-circular>
			<div>{{ $t('loading.text') }}</div>
		</v-dialog>


	</div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import moment from 'moment'

export default {

	data() {
		return {

      // Questions
      bDeleteQuestionDialog: false,
      sSearch: '',
      headers: [
        { text: '', value: 'data-table-select' },
        {
            text: 'Reihenfolge',
            align: 'left',
            value: 'order',
        },
        {
            text: 'Title',
            align: 'left',
            value: 'title',
        },
        {
            text: 'Untertitel',
            align: 'left',
            value: 'subtitle',
        },
        {
            text: 'Beschreibung',
            align: 'left',
            value: 'description',
        },
        { value: 'is_skippable', text: 'Überspringbar' },
        { value: 'is_commentable', text: 'Kommentierbar' },
        { value: 'min_options', text: 'Mind. Optionen' },
        { value: 'max_options', text: 'Max Optionen' },

        { text: '', value: 'data-table-expand' },

      ],
      selected: [],
      expanded: [],
      iItemsPerPage: 50,

      // Options
      aOptionHeaders: [
        { text: '', value: 'data-table-select' },
        {
            text: 'Reihenfolge',
            align: 'left',
            value: 'order',
        },
        {
            text: 'Technischer Wert',
            align: 'left',
            value: 'value',
        },
        {
            text: 'Title',
            align: 'left',
            value: 'title',
        },
        {
            text: 'Untertitel',
            align: 'left',
            value: 'subtitle',
        },
        {
            text: 'Beschreibung',
            align: 'left',
            value: 'description',
        },
        { value: 'color', text: 'Color', align:'center' },
      ],
      bDeleteOptionDialog: false,

      // To Delete
      aDeleteQuestionsIds:  [],
      aDeleteOptionsIds:    [],

      // Options
      aSelectedOptions: [],

			// Loading
			bIsLoading: false,

			// Tabs
			active_tab: null,

			// Back Route
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
			sToday: moment().toISOString().substr(0, 10),

			// Tmps Start
			bDialogStartDate: false,
			bDialogStartTime: false,
			sStartDate: this.getStartDate(),

			// Tmps End
			bDialogEndDate: false,
			bDialogEndTime: false,
			sEndDate: this.getEndDate(),

			// Surveys
			oSurvey : null,
			oSurveyOld: null,

      // All Question Option Colors
      aAllOptionColors: [
        {
          title: 'Grey',
          hex: '#C6C6C6',
        },
        {
          title: 'Green',
          hex: '#6CAE75',
        },
        {
          title: 'Red',
          hex: '#D09786',
        }
      ],

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
			surveyAllowed: 'surveys/surveyAllowed'
		}),
	},


	watch: {
		surveyAllowed(promise)
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

      // Go Through it and reorder it!
      this.reorderQuestions();
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
		},

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
			this.$store.dispatch('surveys/fetchSurveyAllowed', id)
			this.startEditMode();
		}
		else
		{
			this.$router.push(this.oBackRoute);
		}

		// Check Tab for Hash
		this.checkTabForHash();

	},

	methods: {

		orderedOptions: function (options) {
			return options.sort((a, b) => (a.order > b.order) ? 1 : -1);
		},

    getStartDate() {
      return moment().toISOString().substr(0, 10);
    },

    getEndDate() {
      return moment(moment() + 5).toISOString().substr(0, 10)
    },

		reorderQuestions() {
			var oQuestions = this.oSurvey.questions;
      for (var i in oQuestions) {
        if (oQuestions.hasOwnProperty(i)) {
          var oQuestion = oQuestions[i];
          oQuestion.order = parseInt(i)+1;
        }
      }
    },

		reorderOptions(question) {
			var oOptions = question.options;
      for (var i in oOptions) {
        if (oOptions.hasOwnProperty(i)) {
          var oOption = oOptions[i];
          oOption.order = parseInt(i)+1;
        }
      }
		},

    // Question Methods

    deleteQuestions(selected) {
			// Delete Them from Arrays
      this.oSurvey.questions    = this.oSurvey.questions.filter(function(x) { return selected.indexOf(x) < 0 });
      this.oSurveyOld.questions = this.oSurveyOld.questions.filter(function(x) { return selected.indexOf(x) < 0 });

      // Push into delete_questions
      this.aDeleteQuestionsIds = this.getIdsFromObject(
        this.copyObject(selected)
      );

			// Empty Selected
      this.selected = [];

			// Reorder Questions
      this.reorderQuestions();

      // Close Dialog
      this.bDeleteQuestionDialog = false;
    },

    deleteOptions(question, selected) {
			// Delete Them from Arrays
      question.options = question.options.filter(function(x) { return selected.indexOf(x) < 0 });

      // Push into delete_questions
      this.aDeleteOptionsIds = this.getIdsFromObject(
        this.copyObject(selected)
      );

			// Empty Selected
      this.selected = [];

			// Reorder Questions
      this.reorderOptions(question);

      // Close Dialog
      this.bDeleteOptionDialog = false;
    },

    move(oMovingElement, aList, iDir) {
      var key1 = this.getPositionByOrder( oMovingElement.order , aList);
			var key2 = key1 + iDir;
			console.log(key1, key2);
      var oElement1 = oMovingElement;
      var oElement2 = aList[key2];

      if(oElement2) {
        var iTmp = parseInt(oElement1.order);

        oElement1.order = parseInt(oElement2.order);
        oElement2.order = parseInt(iTmp);
      }

      aList.sort((a, b) => (a.order > b.order) ? 1 : -1);
		},

		sortSelectedByOrder() {
			this.selected.sort((a, b) => (a.order > b.order) ? 1 : -1);
		},

		moveSelectedUp() {
			this.sortSelectedByOrder();
			for (var i = 0; i < this.selected.length; i++) {
				this.moveUp(
					this.selected[i],
					this.oSurvey.questions
				);
			}
		},

		moveSelectedDown() {
			this.sortSelectedByOrder();
			for (var i = this.selected.length-1; i >= 0; i--) {
				this.moveDown(
					this.selected[i],
					this.oSurvey.questions
				);
			}
		},

    moveUp(oElement1, aList) {
      this.move(oElement1, aList, -1);
    },

    moveDown(oElement1, aList) {
      this.move(oElement1, aList, 1);
    },

    getPositionById(oItem, oObject) {
      return oObject.map(function(x) {return x.id; }).indexOf(oItem.id)
    },

    getPositionByOrder(iOrder, oObject) {
      return oObject.map(function(x) {return x.order; }).indexOf(iOrder)
    },

		getRandomId() {
			return parseInt(Date.now() + Math.random() * 1000);
		},

    addOption(o, question) {
      var aO = question.options;
      o.order = aO.length+1;
			aO.push(o);
			return o;
		},

    addNewOption(question) {
      return this.addOption({
        id: this.getRandomId(),
        color: '#C6C6C6'
			}, question);
		},

    addQuestion(q) {
      var aQ = this.oSurvey.questions;

      // Increment one order-point up
      q.order = aQ.length+1;
      q.is_new = true;

      // Push to other Questions
			aQ.push(q);

			// Return Question
			return q;
    },

    addNewQuestion() {
      return this.addQuestion({
        id: this.getRandomId(),
        options: [],
			});
		},

		duplicateQuestion(question) {
      var aQ = this.oSurvey.questions;

			if(question) {
      	var oNewQ = this.copyObject(question);

				// Delete oNewQ.id;
        oNewQ.id = this.getRandomId();

        // Delete oNewQ.options.id
        oNewQ.options.forEach(option => {
          option.question_id = oNewQ.id;
        });

				return this.addQuestion(oNewQ);
			}
		},

		duplicateSelectedQuestions() {
			var aNewSelected = [];
			this.selected.forEach(question => {
				aNewSelected.push(
					this.duplicateQuestion(question)
				);
			});
			this.selected = aNewSelected;
			console.log(aNewSelected);
		},

    duplicateLastQuestion() {
			var aQ = this.oSurvey.questions;
			var oLastQ = aQ[aQ.length - 1];
			this.duplicateQuestion(oLastQ);
    },

		changeTab(num) {
			window.location.hash = num;
		},

		checkTabForHash() {
			if(window.location.hash) {
				var num = parseInt(window.location.hash.substr(1));
				num ? this.active_tab = num : undefined
			}
		},

		surveyFormIsValid() {
			return this.valid ? true : false;
    },

    getIdsFromObject(obj) {
      return obj.map(function(e) {return e.id;});
    },

		surveyFormIsInvalid() {
			return !this.surveyFormIsValid();
		},

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

			var t1 = moment(this.oSurvey.start_datetime);
			var t2 = moment(this.oSurvey.end_datetime);

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
      var sEndTime = this.oTimes.sEndTime;

      if(this.isSameDay(dates) && sEndTime)
      {
          return sEndTime+':00';
			}
		},

		getEndTimeMin () {
      var dates = this.aDates;

      if(this.isSameDay(dates))
      {
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
        questions: []
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
		            // console.log(JSON.stringify(itemR), JSON.stringify(itemL));
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
			this.bIsLoading = true;

			// Update Users
			this.$store.dispatch('surveys/updateSurvey', {
        survey: _t.oSurvey,
        delete_questions_ids: _t.aDeleteQuestionsIds,
        delete_options_ids: _t.aDeleteOptionsIds,
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

          _t.aDeleteQuestionsIds = [];
          _t.aDeleteOptionsIds = [];

					_t.startEditMode();

				}
				_t.bIsLoading = false;
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
				_t.bIsLoading = false;
			});
		}
	}

}
</script>
