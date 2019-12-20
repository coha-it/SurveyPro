<template>
  <div>
      <template>
        <q-btn icon="keyboard_arrow_left" small outline unelevated color="grey" rounded tag="router-link" :to="oBackRoute" class="small ml-auto my-auto" :label="$t('Zurück zu den Umfragen')" />
        <br>
        <br>
      </template>

    <div v-if="oSurvey">

      <!-- Data Sheet -->
      <template>
        <q-form v-on:submit.prevent v-model="valid" ref="form" style="max-width: 1280px;">

          <q-toolbar color="primary" dark >
            <q-toolbar-title>
                <template v-if="bCreate">{{ "Neue Umfrage erstellen" }}</template>
                <template v-if="bEdit && oSurvey">{{ 'Umfrage' }} #{{ oSurvey.id }}</template>
            </q-toolbar-title>
          </q-toolbar>
          <!-- Basic Settings -->
          <q-tabs
            v-model="active_tab"
            fixed-tabs
            icons-and-text
            align="justify"
            inline-label
            indicator-color="white"
            class="bg-primary text-white shadow-2"
          >
            <q-tab @click="changeTab('basis')" name="basis" label="Basis" icon="settings" />
            <q-tab @click="changeTab('fragen')" name="fragen" label="Fragen" icon="question_answer" />
            <q-tab @click="changeTab('gruppen')" name="gruppen" label="Gruppen" icon="group" />
          </q-tabs>

          <q-separator />

          <q-tab-panels v-model="active_tab" animated>
            <!-- Einstellungen -->
            <q-tab-panel name="basis">
              <q-list subheader two-line flat >
                  <q-item-label>Textliche Einstellungen</q-item-label>

                  <!-- Title -->
                  <q-item>
                    <q-item-section>
                      <q-input
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
                      />
                    </q-item-section>
                  </q-item>

                  <!-- Author -->
                  <q-item>
                    <q-item-section>
                      <q-input
                        :disabled="surveyIsUneditable()"
                        dense
                        persistent-hint
                        outlined
                        hint="Author in Textform. Wird angezeigt"
                        :placeholder="oSurveyOld.author ? oSurveyOld.author : 'z.B.: &quot;Dr. Johannes Müller&quot; '"
                        v-model="oSurvey.author"
                        label="Autor"
                        required
                      />
                    </q-item-section>
                  </q-item>

                  <!-- Description Short -->
                  <q-item>
                    <q-item-section>
                      <q-input
                        :disabled="surveyIsUneditable()"
                        dense
                        persistent-hint
                        outlined
                        hint="Kurze Beschreibung der Umfrage"
                        v-model="oSurvey.desc_short"
                        label="Kurzbeschreibung"
                        required
                      />
                    </q-item-section>
                  </q-item>

                  <!-- Description Long -->
                  <q-item>
                    <q-item-section>
                      <q-input type="textarea"
                        :disabled="surveyIsUneditable()"
                        outlined
                        dense
                        persistent-hint
                        v-model="oSurvey.desc_long"
                        hint="Lange Beschreibung der Umfrage"
                        label="Langbeschreibung"
                        required
                      />
                    </q-item-section>
                  </q-item>

                  <q-item-label>Konfigurations-Einstellungen</q-item-label>

                  <q-item>
                    <q-item-section side top>
                      <q-checkbox
                        :disabled="surveyIsUneditable()"
                         v-model="oSurvey.active"
                        color="primary"
                        :true-value="1"
                        :false-value="0"
                      />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Aktiviert</q-item-label>
                      <q-item-label caption>Ist diese Umfrage ausfüllbar (aktiviert) oder nicht ausfüllbar. Standard ist aktiviert.</q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section side top>
                      <q-checkbox
                      :disabled="surveyIsUneditable()"
                      v-model="oSurvey.only_editable_by_creator"
                      color="primary"
                      :true-value="1" :false-value="0"
                      />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Nur für Ersteller Editierbar</q-item-label>
                      <q-item-label caption>Nur Sie selbst als Ersteller können diese Umfrage editieren. Im Standard sind Umfragen nur für Sie selbst editierbar</q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section side top>
                      <q-checkbox
                      :disabled="surveyIsUneditable()"
                      v-model="oSurvey.is_public"
                      color="red"
                      :true-value="1"
                      :false-value="0"
                      />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Öffentlich</q-item-label>
                      <q-item-label caption>Diese Umfrage ist öffentlich verfügbar. Im Standard sind Umfragen nicht öffentlich</q-item-label>
                    </q-item-section>
                  </q-item>

                  <template v-if="bEdit">
                    <q-item>
                      <q-item-section side top>
                        <q-checkbox
                        v-model="bExtendedSettings"
                        :disabled="oSurvey.is_finished == 1 || oSurvey.is_canceled == 1"
                        color="primary" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Erweiterte Einstellungen</q-item-label>
                        <q-item-label caption>Zeige erweiterte Einstellungen</q-item-label>
                      </q-item-section>
                    </q-item>

                    <template v-if="bExtendedSettings">
                      <q-item>
                        <q-item-section side top>
                          <q-checkbox
                           color="error"
                           v-model="oSurvey.is_finished"
                           :true-value="1" :false-value="0"
                           :disabled="surveyIsUneditable()"
                           />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Beendet</q-item-label>
                          <q-item-label caption>Die Umfrage wurde Beendet. Nicht umkehrbar</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item>
                        <q-item-section side top>
                          <q-checkbox
                          :disabled="surveyIsUneditable()"
                          color="error"
                          v-model="oSurvey.is_canceled"
                          :true-value="1"
                          :false-value="0" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Abgebrochen</q-item-label>
                          <q-item-label caption>Die Umfrage wurde abgebrochen. Nicht umkehrbar</q-item-label>
                        </q-item-section>
                      </q-item>
                    </template>
                  </template>

                  <q-item-label header>Datums-Einstellungen</q-item-label>

                <!-- Dates-->
                <q-item class="row q-gutter-md">
                  <div class="col col-12 col-sm-6 col-md-6">
                    <q-card>
                      <q-card-section>
                        <div class="text-h6">Beginn und Ende anpassen</div>
                      </q-card-section>

                      <q-card-section>
                        <p>Sobald ihre Umfrage beginnt - können Sie diese nicht mehr anpassen</p>

                        <div class="row">
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="formatDate(oSurvey.start_datetime)"
                              label="Beginnt am"
                              placeholder="Bitte auswählen"
                              readonly
                              disabled
                              color="teal"
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="event_available" />
                              </template>
                              <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                <q-date
                                  :disabled="surveyIsUneditable()"
                                  required
                                  v-model="oSurvey.start_datetime"
                                  :options="getMinStartDate"
                                  :selected-items-text="getDatesDiffDays() + ' Tage Zeit'"
                                  color="secondary"
                                  header-color="primary"
                                  mask="YYYY-MM-DD HH:mm:ss">
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn :label="$t('closer_button')" color="primary" flat v-close-popup />
                                  </div>
                                </q-date>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              v-if="oSurvey.start_datetime"
                              :value="formatTime(oSurvey.start_datetime)"
                              label="Beginnt um"
                              placeholder="Bitte auswählen"
                              readonly
                              :rules="required"
                              disabled
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="schedule" />
                              </template>
                              <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                <q-time
                                  format24h
                                  v-model="oSurvey.start_datetime"
                                  :options="getMinStartTime"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                  color="purple">
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn :label="$t('closer_button')" color="primary" flat v-close-popup />
                                  </div>
                                </q-time>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                        </div>

                        <div class="row" v-if="oSurvey.start_datetime">
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="formatDate(oSurvey.end_datetime)"
                              label="Endet am"
                              readonly
                              :rules="required"
                              disabled
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="event_busy" />
                              </template>
                              <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                <q-date
                                  :disabled="surveyIsUneditable()"
                                  required
                                  v-model="oSurvey.end_datetime"
                                  :options="getMaxEndDate"
                                  range
                                  color="secondary"
                                  header-color="primary"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                  >
                                    <div class="row items-center justify-end q-gutter-sm">
                                      <q-btn :label="$t('closer_button')" color="primary" flat v-close-popup />
                                    </div>
                                  </q-date>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                          <div class="col col-12 col-sm-6 col-md-6">
                            <q-input
                              :value="formatTime(oSurvey.end_datetime)"
                              label="Endet um"
                              readonly
                              :rules="required"
                              disabled
                              icon="schedule"
                              standout
                            >
                              <template v-slot:prepend>
                                <q-icon name="timelapse" />
                              </template>
                              <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                <q-time
                                  format24h
                                  v-model="oSurvey.end_datetime"
                                  mask="YYYY-MM-DD HH:mm:ss"
                                  color="purple"
                                  :options="getMaxEndTime"
                                >
                                  <div class="row items-center justify-end q-gutter-sm">
                                    <q-btn :label="$t('closer_button')" color="primary" flat v-close-popup />
                                  </div>
                                </q-time>
                              </q-popup-proxy>
                            </q-input>
                          </div>
                        </div>

                        <div class="row justify-center full-width">
                          <div class="col">
                            <q-chat-message
                              :text="['Hallo! Die Umfrage ersteckt sich über folgende Zeit: <br/><strong>'+ getDiffDatetimeLabel() + '</strong>. Startend am ' + formatDate(oSurvey.start_datetime) + ' bis zum ' + formatDate(oSurvey.end_datetime)  ]"
                              sent
                              :bg-color="getDiffDatetimeColor()"
                              text-color="white"
                              size="8"
                            >
                              <q-btn slot="avatar" disable flat round readonly unelevated icon="timer" />
                            </q-chat-message>
                          </div>
                        </div>

                      </q-card-section>
                    </q-card>
                  </div>

                  <div class="col col-12 col-sm-6 col-md-6">
                    <q-date
                      :events="events"
                      :event-color="eventColor"
                      :options="events"
                      :value="sTodayDatetime"
                      :title="getDiffDatetimeLabel()"
                      subtitle="Zeit für die Umfrage"
                    />
                  </div>
                </q-item>

              </q-list>
            </q-tab-panel>

            <!-- Questions -->
            <q-tab-panel name="fragen">
              <q-list>
                <q-item>
                  <q-item-section>
                    <!-- No Select Toolbar -->
                    <q-toolbar class="coha--toolbar" v-if="selected.length <= 0"  :flat="sSearch == ''" floating min-height="85px" height="auto">
                        <!-- <v-switch class="mt-6 mr-6" v-model="bTableDense"  color="primary"></v-switch> -->
                        <div class="flex-grow-1"></div>
                        <q-input style="max-width: 400px;" v-model="sSearch" :label="$t('Search')" autocomplete="off"  append-icon="search" hide-details outlined/>
                        <q-input v-model="iQuestionsPerPage" number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outlined />
                    </q-toolbar>

                    <q-toolbar class="coha--toolbar" v-else :flat="sSearch == ''" color="primary"  dark floating min-height="85px" height="auto">
                      <v-menu right offset-y>
                        <template v-slot:activator="{ on:menuedit }">
                          <q-btn text rounded v-on="{ ...menuedit }">
                            <v-icon left>mdi-pencil</v-icon> {{ selected.length + ' ' + $t('edit') }}
                          </q-btn>
                        </template>
                        <q-list>
                          <q-item @click="duplicateSelectedQuestions()">
                            Duplizieren
                          </q-item>
                          <q-item @click="bDeleteQuestionDialog = true">
                            Löschen
                          </q-item>
                        </q-list>
                      </v-menu>
                      <q-tooltip top>
                          <template v-slot:activator="{ on }">
                            <q-btn @click="moveSelectedUp()" icon text rounded v-on="on">
                              <v-icon>mdi-chevron-up</v-icon>
                            </q-btn>
                          </template>
                          <span>Position Hoch</span>
                      </q-tooltip>
                      <q-tooltip top>
                          <template v-slot:activator="{ on }">
                            <q-btn @click="moveSelectedDown()" icon text rounded v-on="on">
                              <q-icon icon="chevron-down" />
                            </q-btn>
                          </template>
                          <span>Position Runter</span>
                      </q-tooltip>
                      <!-- <div class="flex-grow-1"></div> -->
                    </q-toolbar>

                    <!-- Delete - Dialog -->
                    <q-dialog v-model="bDeleteQuestionDialog" max-width="500" dark content-class="naked dark centered">
                      <h2 class="display-2">Fragen Löschen?</h2>
                      <p>Möchten Sie {{ selected.length }} Fragen löschen?</p>
                      <v-container fluid>
                        <div class="row" align="center">
                          <div class="col text-center" cols="12" sm="12">
                            <q-btn depressed @click="bDeleteQuestionDialog = false" outlined>Abbruch</q-btn>
                            <q-btn depressed @click.prevent="deleteQuestions(selected)" color="error">Löschen</q-btn>
                          </div>
                        </div>
                      </v-container>
                    </q-dialog>


                    <q-table
                      :columns="headers"

                      v-model="selected"
                      :data="oSurvey.questions"

                      multi-sort
                      show-select

                      show-expand
                      :expanded.sync="expanded"

                      :search="sSearch"
                      :sort-by="['order']"
                      :sort-desc="[false]"

                      :items-per-page="parseInt(iQuestionsPerPage)"
                      :footer-props="{
                        showFirstLastPage: true,
                      }"
                    >
                      <template v-slot:body-cell-title="props">
                        <q-td :props="props">
                          <q-dialog>
                            {{ props.row.title }}
                            <template v-slot:input>
                              <q-input
                                v-model="props.row.title"
                                label="Edit"
                                counter
                              />
                            </template>
                          </q-dialog>
                        </q-td>
                      </template>

                      <template v-slot:item.subtitle="props">
                        <v-edit-dialog :return-value.sync="props.item.subtitle">
                          {{ props.item.subtitle }}
                          <template v-slot:input>
                            <q-input
                              v-model="props.item.subtitle"
                              label="Edit"
                              counter
                            />
                          </template>
                        </v-edit-dialog>
                      </template>

                      <template v-slot:item.description="props">
                        <v-edit-dialog :return-value.sync="props.item.description">
                          {{ props.item.description }}
                          <template v-slot:input>
                            <q-input
                              v-model="props.item.description"
                              label="Edit"
                              counter
                            />
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
                        <template v-if="item">
                          <td colspan="100%">
                            <div class="row">
                              <div class="col" xl="12" sm="12" xs="12">
                                <q-card>
                                  <v-card-title>Frage-Einstellungen #{{ item.id }}</v-card-title>
                                  <!-- <v-card-text>Einstellungen zur {{ item.order }}. Frage</v-card-text> -->
                                <q-list subheader two-line flat >

                                  <q-item-label>Allgemeine Frage-Einstellungen</q-item-label>

                                  <q-item>
                                    <q-item-section>
                                      <q-checkbox v-model="item.is_skippable" :disabled="surveyIsUneditable()" color="primary" :true-value="1" :false-value="0" />
                                    </q-item-section>
                                    <q-item-section>
                                      <q-item-label>Überspringbar</q-item-label>
                                      <q-item-label caption>Ist diese Frage überspringbar</q-item-label>
                                    </q-item-section>
                                  </q-item>

                                  <q-item>
                                    <q-item-section>
                                      <q-input
                                        :disabled="surveyIsUneditable()"
                                        dense
                                        persistent-hint
                                        outlined
                                        hint="Titel der Frage. Wird am größten Angezeigt"
                                        placeholder="z.B.: 'Fazit zur Umfrage' "
                                        v-model="item.title"
                                        label="Titel"
                                        required
                                      />
                                    </q-item-section>
                                  </q-item>

                                  <q-item>
                                    <q-item-section>
                                      <q-input
                                        :disabled="surveyIsUneditable()"
                                        dense
                                        persistent-hint
                                        outlined
                                        hint="Subtitel / Untertitel der Frage. Wird unter Titel angezeigt"
                                        placeholder="z.B.: 'Wie fanden Sie diese Umfrage?' "
                                        v-model="item.subtitle"
                                        label="Untertitel"
                                        required
                                      />
                                    </q-item-section>
                                  </q-item>

                                  <q-item>
                                    <q-item-section>
                                      <q-input type="textarea"
                                        :disabled="surveyIsUneditable()"
                                        dense
                                        persistent-hint
                                        outlined
                                        hint="Beschreibung der Umfrage. Wird unter Titel / Subtitel klein angezeigt."
                                        placeholder="z.B.: 'Bewerten Sie diese Umfrage bitte mit 0 (negativ) bis 10 (positiv) Punkten' "
                                        v-model="item.description"
                                        label="Beschreibung"
                                        required
                                      />
                                    </q-item-section>
                                  </q-item>

                                  <q-item-label>Kommentar-Einstellungen</q-item-label>

                                  <q-item>
                                    <q-item-section>
                                      <q-checkbox v-model="item.is_commentable" :disabled="surveyIsUneditable()" color="primary" :true-value="1" :false-value="0" />
                                    </q-item-section>
                                    <q-item-section>
                                      <q-item-label>Kommentierbar</q-item-label>
                                      <q-item-label caption>Ist diese Frage kommentierbar</q-item-label>
                                    </q-item-section>
                                  </q-item>

                                  <template v-if="item.is_commentable || true">
                                    <q-item>
                                      <q-item-section>
                                        <q-checkbox v-model="item.comment_is_required" :disabled="surveyIsUneditable() && item.is_commentable" color="primary" :true-value="1" :false-value="0" />
                                      </q-item-section>
                                      <q-item-section>
                                        <q-item-label>Kommentar ist erforderlich</q-item-label>
                                        <q-item-label caption>Ist ein Kommentar erforderlich</q-item-label>
                                      </q-item-section>
                                    </q-item>

                                    <q-item>
                                      <q-item-section>
                                        <q-checkbox v-model="item.comment_is_number" :disabled="surveyIsUneditable()" color="primary" :true-value="1" :false-value="0" />
                                      </q-item-section>
                                      <q-item-section>
                                        <q-item-label>Kommentar ist eine Nummer</q-item-label>
                                        <q-item-label caption>Wenn der Kommentar eine Nummer sein soll</q-item-label>
                                      </q-item-section>
                                    </q-item>

                                    <q-item>
                                      <q-item-section>
                                        <q-input
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
                                        />
                                      </q-item-section>
                                    </q-item>
                                  </template>

                                  <q-item-label>Einstellungen: Optionen</q-item-label>

                                    <q-item>
                                      <q-item-section>
                                        <div class="row">
                                          <div class="col" xl="3" md="3" sm="6" xs="12">
                                            <q-input
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
                                            />
                                          </div>
                                          <div class="col" xl="3" md="3" sm="6" xs="12">
                                            <q-input
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
                                            />
                                          </div>
                                        </div>
                                      </q-item-section>
                                    </q-item>
                                  </q-list>


                                  <!-- Selected Toolbar -->
                                  <q-toolbar class="coha--toolbar" v-if="aSelectedOptions && aSelectedOptions.length" :flat="sSearch == ''" color="primary"  dark floating min-height="85px" height="auto">
                                    <q-btn
                                      v-if="aSelectedOptions && aSelectedOptions.length"
                                      @click="bDeleteOptionDialog = true"
                                      depressed
                                      color="red"
                                      dark
                                    >
                                      <v-icon left>mdi-delete</v-icon>&nbsp;
                                      Ausgewählte Optionen Löschen
                                    </q-btn>

                                  </q-toolbar>

                                  <!-- No Selected Toolbar -->
                                  <q-toolbar class="coha--toolbar" v-else  :flat="sSearch == ''" floating min-height="85px" height="auto">
                                      <div class="flex-grow-1"></div>
                                      <q-input v-model="iOptionsPerPage" number type="number" hide-details style="max-width: 150px;" label="Zeilen pro Seite" class="ml-5" outlined />
                                  </q-toolbar>

                                  <q-table
                                    :headers="aOptionHeaders"

                                    v-model="aSelectedOptions"
                                    :items="item.options"

                                    dense
                                    multi-sort
                                    show-select

                                    :items-per-page="parseInt(iOptionsPerPage)"
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
                                          <q-input
                                            v-model="props.item.value"
                                            label="Edit"
                                            type="number"
                                          />
                                        </template>
                                      </v-edit-dialog>
                                    </template>

                                    <template v-slot:item.title="props">
                                      <v-edit-dialog :return-value.sync="props.item.title">
                                        {{ props.item.title }}
                                        <template v-slot:input>
                                          <q-input
                                            v-model="props.item.title"
                                            label="Edit"
                                            counter
                                          />
                                        </template>
                                      </v-edit-dialog>
                                    </template>

                                    <template v-slot:item.subtitle="props">
                                      <v-edit-dialog :return-value.sync="props.item.subtitle">
                                        {{ props.item.subtitle }}
                                        <template v-slot:input>
                                          <q-input
                                            v-model="props.item.subtitle"
                                            label="Edit"
                                            counter
                                          />
                                        </template>
                                      </v-edit-dialog>
                                    </template>

                                    <template v-slot:item.description="props">
                                      <v-edit-dialog :return-value.sync="props.item.description">
                                        {{ props.item.description }}
                                        <template v-slot:input>
                                          <q-input
                                            v-model="props.item.description"
                                            label="Edit"
                                            counter
                                          />
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

                                      <q-dialog v-model="props.item.dialog" max-width="290">
                                        <template v-slot:activator="{ on }">
                                          <div><q-btn
                                            v-on="on"
                                            small
                                            depressed
                                            fab
                                            dark
                                            :color="props.item.color"
                                          >
                                            <v-icon dark>mdi-palette</v-icon>
                                          </q-btn></div>
                                          <div>{{ props.item.color }}</div>
                                        </template>
                                        <q-card>
                                          <v-card-title>Farbe auswählen</v-card-title>
                                          <v-card-text>
                                            <q-btn block small :color="props.item.color">{{ props.item.color }}</q-btn>
                                            <template v-for="oColor in aAllOptionColors">
                                              <div style="margin-top: 5px;" v-bind:key="oColor.title" >
                                                <q-btn block small :color="oColor.hex" @click="props.item.color = oColor.hex">{{ oColor.title }}</q-btn>
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
                                            <q-btn  @click="props.item.dialog = false">Übernehmen und schließen</q-btn>
                                          </v-card-actions>
                                        </q-card>
                                      </q-dialog>
                                    </template>
                                  </q-table>
                                  <v-card-actions>
                                    <q-btn @click="addNewOption(item)">
                                      <v-icon left>plus_one</v-icon>
                                      Neue Option hinzufügen
                                    </q-btn>&nbsp;
                                    <q-btn color="primary" @click="duplicateLastOption(item)">
                                      <v-icon left>control_point_duplicate</v-icon>
                                      Letzte Option duplizieren
                                    </q-btn>

                                    <!-- Delete - Dialog -->
                                    <q-dialog v-model="bDeleteOptionDialog" max-width="500" dark content-class="naked dark centered">
                                      <h2 class="display-2">Optionen Löschen?</h2>
                                      <p>Möchten Sie {{ aSelectedOptions.length }} Optionen löschen?</p>
                                      <v-container fluid>
                                        <div class="row" align="center">
                                          <div class="col text-center" cols="12" sm="12">
                                            <q-btn depressed @click="bDeleteOptionDialog = false" outlined>Abbruch</q-btn>
                                            <q-btn depressed @click.prevent="deleteOptions(item, aSelectedOptions)" color="error">Löschen</q-btn>
                                          </div>
                                        </div>
                                      </v-container>
                                    </q-dialog>

                                  </v-card-actions>
                                </q-card>
                              </div>
                            </div>
                          </td>
                        </template>
                      </template>
                    </q-table>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-btn @click="addNewQuestion()">
                    <v-icon left>plus_one</v-icon>
                    Neue Frage hinzufügen
                  </q-btn>
                  &nbsp; &nbsp;
                  <q-btn @click="duplicateLastQuestion()" color="primary" :disabled="oSurvey.questions.length <= 0">
                    <v-icon left>control_point_duplicate</v-icon>
                    Letzte Frage duplizieren
                  </q-btn>
                </q-item>

                <q-item></q-item>


                <v-divider></v-divider>

              </q-list>
            </q-tab-panel>

            <!-- Gruppeneinstellungen -->
            <q-tab-panel name="gruppen">
              <q-list subheader two-line flat >
                  <q-item-label>Gruppen</q-item-label>

                  <!-- Title -->
                  <q-item>
                    <q-item-section>
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
                    </q-item-section>
                  </q-item>
              </q-list>


            </q-tab-panel>
          </q-tab-panels>

          <q-list>
              <q-item>
                  <q-input v-model="oSurvey.title" required :rules="required" style="display: none;"/>

                  <q-btn
                    color="grey"
                    dark
                    class="mr-4"
                  >Zurück</q-btn>
                  <q-btn
                    color="green"
                    type="submit"
                    @click="updateSurvey()"
                    class="mr-4 white--text"
                    v-if="surveyIsEditable()"
                    :disabled="surveyFormIsInvalid()"
                  >Umfrage Speichern {{ isUnsaved() ? '*' : undefined }}</q-btn>

                  <!-- Save as FAB -->
                  <q-page-sticky
                      bottom
                      right
                      :open-on-hover="true"
                      v-model="bFabButtonInner"
                      fixed
                  >

                      <template v-slot:activator v-slot:extension>
                          <q-page-sticky>
                            <q-btn
                              v-show="isUnsaved()"
                              v-model="bFabButtonInner"
                              color="success"
                              dark
                              fab
                              type="submit"
                              @click="updateSurvey()"
                              :icon="bFabButtonInner ? 'mdi-content-save' : false"
                            >
                            </q-btn>
                          </q-page-sticky>
                      </template>
                      <q-btn
                        fab
                        dark
                        small
                        color="warning"
                        icon="mdi-restore"
                      />
                  </q-page-sticky>
                </q-item>
          </q-list>



        </q-form>
      </template>
    </div>


    <!-- Dialog Loading -->
    <q-dialog
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
    </q-dialog>


  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import moment from 'moment'


export default {

  data() {
    return {

      // Timeing
      range: '',
      min: '',
      max: '',

      // FAB
      bFabButtonInner: false,

      // Questions
      bDeleteQuestionDialog: false,
      sSearch: '',
      headers: [
        {
          label: '',
          align: false,
          name: 'data-table-select',
          field: 'data-table-select',
          sortable: true
        },
        {
          label: 'Reihenfolge',
          align: 'left',
          name: 'order',
          field: 'order',
          sortable: true
        },
        {
          label: 'Title',
          align: 'left',
          name: 'title',
          field: 'title',
          sortable: true
        },
        {
          label: 'Untertitel',
          align: 'left',
          name: 'subtitle',
          field: 'subtitle',
          sortable: true
        },
        {
          label: 'Beschreibung',
          align: 'left',
          name: 'description',
          field: 'description',
          sortable: true
        },
        {
          label: 'Überspringbar',
          align: null,
          name: 'is_skippable',
          field: 'is_skippable',
          sortable: true
        },
        {
          label: 'Kommentierbar',
          align: null,
          name: 'is_commentable',
          field: 'is_commentable',
          sortable: true
        },
        {
          label: 'Mind. Optionen',
          align: null,
          name: 'min_options',
          field: 'min_options',
          sortable: true
        },
        {
          label: 'Max Optionen',
          align: null,
          name: 'max_options',
          field: 'max_options',
          sortable: true
        },
        {
          label: '',
          align: null,
          name: 'data-table-expand',
          field: 'data-table-expand',
          sortable: false,
        }
      ],
      selected: [],
      expanded: [],
      iQuestionsPerPage: 50,

      // Options
      aOptionHeaders: [
        { text: '', value: 'data-table-select' },
        {
          text: 'Reihenfolge',
          align: 'left',
          value: 'order'
        },
        {
          text: 'Technischer Wert',
          align: 'left',
          value: 'value'
        },
        {
          text: 'Title',
          align: 'left',
          value: 'title'
        },
        {
          text: 'Untertitel',
          align: 'left',
          value: 'subtitle'
        },
        {
          text: 'Beschreibung',
          align: 'left',
          value: 'description'
        },
        {
          text: 'Color',
          align: 'center',
          value: 'color'
        }
      ],
      bDeleteOptionDialog: false,
      iOptionsPerPage: 50,

      // To Delete
      aDeleteQuestionsIds:  [],
      aDeleteOptionsIds:    [],

      // Options
      aSelectedOptions: [],

      // Loading
      bIsLoading: false,

      // Tabs
      active_tab: 'basis',

      // Back Route
      oBackRoute: { name: 'backend.surveys' },
      bCreate: false,
      bEdit: false,
      bExtendedSettings: false,

      // Form
      valid: true,
      required: [
        v => !!v || this.$t('required'),
      ],

      // Dates and Times
      oTimes : {
        sStartTime: '',
        sEndTime: '',
      },

      // Today
      sTodayDate: moment().format("YYYY-MM-DD"),
      sTodayTime: moment().format("HH:mm:ss"),
      sTodayDatetime: moment().format("YYYY-MM-DD HH:mm:ss"),

      // Tmps Start
      // sStartDate: '', // this.getStartDate(),

      // Tmps End
      // sEndDate: '', // this.getEndDate(),

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
      // if(this.oSurvey.start_datetime)  this.aDates[0] = this.oSurvey.start_datetime.substr(0, 10);
      // if(this.oSurvey.end_datetime)    this.aDates[1] = this.oSurvey.end_datetime.substr(0, 10);
      // this.oTimes.sStartTime = this.oSurvey.start_datetime.substr(11, 5);
      // this.oTimes.sEndTime   = this.oSurvey.end_datetime.substr(11, 5);

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
        // this.updateDatetimes();
      }
    },

    'oTimes': {
      handler() {
        // this.updateDatetimes();
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
      return moment().format().substr(0, 10);
    },

    // getEndDate() {
    //   return moment(moment() + 5).format().substr(0, 10)
    // },

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

    events(d) {
      var entry = moment(d).format("YYYY-MM-DD");
      var start = moment(this.oSurvey.start_datetime).format("YYYY-MM-DD");
      var end   = moment(this.oSurvey.end_datetime).format("YYYY-MM-DD");

      return (entry >= start && entry <= end);
    },

    eventColor(d) {
      var entry = moment(d).format("YYYY-MM-DD");
      var start = moment(this.oSurvey.start_datetime).format("YYYY-MM-DD");
      var end   = moment(this.oSurvey.end_datetime).format("YYYY-MM-DD");

      switch (entry) {
        case end:
          return 'red'
          break;

        case start:
          return 'green'
          break;

        default:
          return 'primary'
          break;
      }

    },


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
      this.aSelectedOptions = [];

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

    duplicateOption(option, question) {
      if(question && option) {
        var oNewOption = this.copyObject(option);

        // Delete oNewOption.id;
        oNewOption.id = this.getRandomId();

        return this.addOption(oNewOption, question);
      }
    },

    duplicateLastOption(question) {
      var oLast = question.options[question.options.length - 1];
      this.duplicateOption(oLast, question);
    },



    // QUESTIONS
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
        var tab = window.location.hash.substr(1);
        tab ? this.active_tab = tab : undefined
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

    getMaxEndDate(date) {
      return this.getMinMaxDate(date, 'end');
    },

    getMinStartDate (date) {
      return this.getMinMaxDate(date, 'start');
    },

    getMinMaxDate(date, type) {
      var d1 = this.getDate(date);
      var active = this.oSurvey.active;

      switch (type) {
        case 'start':
          if(this.oSurvey.end_datetime) {
            var end = this.getDate(this.oSurvey.end_datetime);
            return d1 < end && (active ? this.sTodayDate <= d1  : true)
          }
          break;

        case 'end':
          var start = this.getDate(this.oSurvey.start_datetime);
          return d1 >= start
          break;
      }

      return (active ? this.sTodayDate <= d1 : true);
    },

    getDate(ts) {
      return moment(ts).format('YYYY-MM-DD');
    },

    getMinStartTime(hr, min, sec) {
      return this.getMinMaxTime(hr,min,sec, 'start');
    },

    getMaxEndTime(hr, min, sec) {
      return this.getMinMaxTime(hr,min,sec, 'end');
    },

    getMinMaxTime(hr, min, sec, type) {
      if(this.oSurvey.start_datetime && this.oSurvey.end_datetime)
      {
        var startDate = this.getDate(this.oSurvey.start_datetime);
        var endDate   = this.getDate(this.oSurvey.end_datetime);
        if(startDate == endDate)
        {
          var endDatetime   = moment(this.oSurvey.end_datetime    ).format("YYYY-MM-DD HH:mm:ss");
          var startDatetime = moment(this.oSurvey.start_datetime  ).format("YYYY-MM-DD HH:mm:ss");

          // SelTime
          var selTime       = moment(this.oSurvey.start_datetime).toDate();
          if(hr) selTime.setHours(hr);
          if(min) selTime.setMinutes(min);
          if(sec) selTime.setSeconds(sec);

          // Format again
          selTime = moment(selTime).format("YYYY-MM-DD HH:mm:ss");

          switch (type) {
            case 'start':
              return endDatetime > selTime;
              break;

            case 'end':
              return startDatetime < selTime;
              break;
          }

        }
      }

      return true;
    },

    getDiffDatetime() {
      if(!this.oSurvey || !this.oSurvey.start_datetime || !this.oSurvey.end_datetime) {
        return 0;
      }

      var t1 = moment(this.oSurvey.start_datetime);
      var t2 = moment(this.oSurvey.end_datetime);

      return Math.abs(t1 - t2) / 1000;
    },

    getDiffDatetimeColor() {
      var delta = this.getDiffDatetime();
      var iDays = Math.floor(delta / 86400);

      switch (true) {
        case (iDays > 14):
          return 'positive'
          break;

        case (iDays > 7):
          return 'grey'
          break;

        case (iDays > 3):
          return 'warning'
          break;

        default:
          return 'negative';
          break;
      }

      return 'grey';

    },

    getDiffDatetimeLabel() {
      // get total seconds between the times
      var delta = this.getDiffDatetime();

      if(!delta) return '';

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

    // getStartTimeMax () {
    //   var dates = this.aDates;
    //   var sEndTime = this.oTimes.sEndTime;

    //   if(this.isSameDay(dates) && sEndTime)
    //   {
    //       return sEndTime+':00';
    //   }
    // },

    // getEndTimeMin () {
    //   var dates = this.aDates;

    //   if(this.isSameDay(dates))
    //   {
    //     return this.oTimes.sStartTime+':00';
    //   }
    // },

    // updateDatetimes() {
    //   if(this.oSurvey) {

    //     var d1 = (this.aDates[0]) ? this.aDates[0].substr(0, 10) : '';
    //     var d2 = (this.aDates[1]) ? this.aDates[1].substr(0, 10) : '';

    //     var t1 = (this.oTimes.sStartTime) ? ' ' + this.oTimes.sStartTime + ':00'   : '';
    //     var t2 = (this.oTimes.sEndTime)   ? ' ' + this.oTimes.sEndTime   + ':00'   : '';

    //     var tmp = '';

    //     if(d1 > d2) {
    //       tmp = d2;
    //       d2 = d1;
    //       d1 = tmp;
    //     }

    //     this.oSurvey.start_datetime = d1 + t1 ;
    //     this.oSurvey.end_datetime   = d2 + t2 ;
    //   }
    // },

    getDatesDiffDays() {
      return this.getDiffDays(this.oSurvey.start_datetime, this.oSurvey.end_datetime);
    },

    getDiffDays(d1, d2) {
      var date1 = new Date(d1);
      var date2 = new Date(d2);
      var diff_in_time = date2.getTime() - date1.getTime();
      var diff_in_days = diff_in_time / (1000 * 3600 * 24);

      return (Math.abs(diff_in_days) + 1) .toString();
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

    formatTime(sDate) {
      return sDate ? sDate.substr(11,5) + ' Uhr' : '';
    },

    showSnackbarError: function(text) {
      var _this = this;
      _this.$q.notify({
        message: _this.$t(text),
        color: 'red',
        position: 'top',
        timeout: 6000,
      })
    },

    showSnackbarSuccess: function(text) {
      var _this = this;
      _this.$q.notify({
        message: _this.$t(text),
        color: 'green',
        position: 'top',
        timeout: 3000,
      })
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
        var bReturn = true;
        var itemL = this.copyObject(this.oSurvey);
        var itemR = this.copyObject(this.oSurveyOld);

        if(itemL && itemR) {
            // Differences
            if(JSON.stringify(itemR) != JSON.stringify(itemL)) {
                // console.log(JSON.stringify(itemR), JSON.stringify(itemL));
                bReturn = false;
            } else {
                bReturn = true;
            }
        }
        return bReturn;
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
