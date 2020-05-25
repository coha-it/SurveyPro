<template>
  <span>
    <q-btn
      label="Datei-Import"
      color="primary"
      unelevated
      icon-right="publish"
      @click="bDialog = true"
    />
    <q-dialog v-model="bDialog" maximized max-width="1200" persistent>
      <q-card>
        <q-toolbar class="bg-primary text-white" dark color="primary">
          <q-toolbar-title>Importieren</q-toolbar-title>
          <q-space />
          <q-btn v-close-popup dense flat icon="close">
            <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
          </q-btn>
        </q-toolbar>
        <q-list three-line subheader>
          <q-item>
            <q-item-section>
              <q-item-label>Vorlage / Testdatei</q-item-label>
              <q-item-label caption>
                <p>Bauen Sie die Datei wenn möglich anhand der Vorlage-Datei auf:</p>

                <q-btn-dropdown
                  split
                  depressed
                  outline
                  icon="get_app"
                  label="Testdatei herunterladen"
                  @click="download('import-example-file.csv')"
                >
                  <q-list dense>
                    <q-item v-close-popup clickable @click="download('import-example-file.csv')">
                      <q-item-section avatar>
                        <q-avatar icon="get_app" text-color="primary" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Kleine Testdatei</q-item-label>
                        <q-item-label caption>2 Einträge, 0,1 kb</q-item-label>
                      </q-item-section>
                    </q-item>

                    <q-item v-close-popup clickable @click="download('import-example-file-big.csv')">
                      <q-item-section avatar>
                        <q-avatar icon="get_app" text-color="primary" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>Große Testdatei</q-item-label>
                        <q-item-label caption>8 Einträge, 0,4 kb</q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-btn-dropdown>

              </q-item-label>
            </q-item-section>
          </q-item>

          <q-separator />

          <q-item>
            <q-item-section>
              <q-item-label>Datei hochladen</q-item-label>
              <q-item-label caption>
                Ihre Datei benötigt folgende Voraussetzungen:
                <ul>
                  <li>Ihre Datei muss eine valide <span class="code_font">CSV-Datei</span> sein</li>
                  <li>Der Kopf bzw. die erste Zeile der Datei muss "mail" enthalten. die weiteren Spalten der Vorlagedatei sind optional</li>
                  <li>Der Spaltentrenner Ihrer Datei muss das Semikolon-Zeichen <strong class="code_font">;</strong> sein</li>
                </ul>
                Wählen Sie ihre Datei aus und beginnen Sie den Upload<br><br>
                <q-uploader
                  url="/api/import-csv"
                  label="Datei auswählen und Hochladen"
                  color="primary"
                  square
                  flat
                  bordered
                  auto-upload
                  style="max-width: 300px;"
                  no-thumbnails
                  field-name="file"
                  @removed="removeUploadedUsers"
                  @uploaded="fileUploadSuccess"
                  @failed="fileUploadFailed"
                />
              </q-item-label>
            </q-item-section>
          </q-item>

          <q-separator />

          <template v-if="jUploadedUsers">
            <q-item>
              <q-item-section>
                <q-item-label>Dateiinhalte überprüfen</q-item-label>
                <q-item-label caption>
                  Überprüfen Sie die unten stehenden Inhalte<br><br>

                  <q-scroll-area style="min-height: 150px; height: 35vh; max-height: 350px; white-space: break-spaces;">{{
                    jUploadedUsers
                  }}</q-scroll-area>
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <q-item>
              <q-item-section>
                <q-item-label>Import Starten</q-item-label>
                <q-item-label caption>
                  Starten Sie den Import<br><br>
                  <q-btn :label="'Import von '+ jUploadedUsers.length +' neuen Benutzern ausführen'" color="primary" depressed class="full-width" @click="createImportedUsers" />
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <br><br><br><br>
          </template>
        </q-list>
      </q-card>
    </q-dialog>
  </span>
</template>

<script>
export default {

  data () {
    return {
      bDialog: false,
      jUploadedUsers: null
    }
  },

  methods: {

    download (name) {
      // let path = '/storage/files/examples/'
      let path = 'https://dreamteam-survey.s3.eu-central-1.amazonaws.com/files/example/import/'
      let file = name
      let filepath = path + file

      window.open(filepath, '_blank')
    },

    closeDialog () {
      this.bDialog = false
    },

    createImportedUsers () {
      // Create Users
      this.$parent.createUsers(this.jUploadedUsers.length, this.jUploadedUsers)
      this.removeUploadedUsers()
    },

    removeUploadedUsers () {
      this.jUploadedUsers = null
    },

    fileUploadSuccess (e) {
      // Notify
      this.$q.notify({
        message: 'Upload erfolgreich',
        color: 'green',
        position: 'top',
        timeout: 3000
      })

      this.jUploadedUsers = this.buildCsvJson(e.xhr.response)
    },

    fileUploadFailed (e) {
      this.$q.notify({
        message: e.xhr.response,
        color: 'red',
        position: 'top',
        timeout: 6000
      })
    },

    buildCsvJson (csv) {
      let jCsv = JSON.parse(csv)
      let thead = jCsv.slice(0, 1)
      let tbody = jCsv.slice(1)
      let response = []

      // Go Through Body
      for (let i in tbody) {
        let tr = tbody[i]

        // Go Through Head
        for (let j in thead) {
          let th = thead[j]
          let merged = th.reduce((obj, key, index) => ({ ...obj, [key]: tr[index] }), {})

          response.push(merged)
        }
      }

      return response
    }
  }
}
</script>
