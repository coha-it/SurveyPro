<template>
  <div>
    <h1>Umfrage bearbeiten & erstellen</h1>
    <p>
      Bearbeite deine Umfragen oder erstelle eine Neue:
    </p>
    <p>
      <q-btn
        label="Umfrage erstellen"
        color="primary"
        depressed
        unelevated
        tile
        xoutline
        class="mt-2 mr-2"
        :to="{name: 'backend.survey', params: {id: 'create'}}"
      />
    </p>
    <q-card>
      <q-card-section>
        <q-input
          v-model="sSearch"
          append-icon="search"
          label="Search"
          single-line
          hide-details
        />
      </q-card-section>
      <q-table
        v-if="surveysAllowed && surveysAllowed.length >= 1"
        :columns="aHeaders"
        :data="surveysAllowed"
        :search="sSearch"
        separator="cell"
        class="my-data-table"
        :footer-props="{
          showFirstLastPage: true,
        }"
        :sort-by="['id']"
        :sort-desc="true"
        :pagination.sync="pagination"
        dense
      >
        <template v-slot:body-cell="props">
          <q-td :props="props">
            <template v-if="props.col.field === 'action'">
              <q-btn
                :label="$t('edit')"
                size="sm"
                :to="{ name: 'backend.survey', params: {id: props.row.id } }"
                unelevated
                outline
              />
            </template>
            <template v-else>
              <span class="max_lines max_lines_4">
                {{ props.value }}
              </span>
            </template>
          </q-td>
        </template>
      </q-table>
    </q-card>
  </div>
</template>

<script>
// import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  middleware: 'canCreateSurveys',

  data () {
    return {
      sSearch: '',
      pagination: {
        sortBy: 'id',
        descending: true,
        // page: 1,
        rowsPerPage: 50
      },
      aHeaders: [
        {
          label: 'Title',
          field: 'title',
          name: 'title',
          sortable: true
        },
        {
          label: 'ID',
          field: 'id',
          align: 'left',
          name: 'id',
          sortable: true
        },
        {
          label: 'Description Short',
          field: 'desc_short',
          name: 'desc_short',
          sortable: true
        },
        {
          label: 'Description Long',
          field: 'desc_long',
          name: 'desc_long',
          sortable: true
        },
        {
          label: 'Author',
          field: 'author',
          name: 'author',
          sortable: true
        },
        {
          label: 'Created By',
          field: 'created_by',
          name: 'created_by',
          sortable: true
        },
        {
          label: 'Active',
          field: 'active',
          name: 'active',
          sortable: true
        },
        {
          label: 'Start',
          field: 'start_datetime',
          name: 'start_datetime',
          sortable: true
        },
        {
          label: 'End',
          field: 'end_datetime',
          name: 'end_datetime',
          sortable: true
        },
        {
          label: 'Finished',
          field: 'is_finished',
          name: 'is_finished',
          sortable: true
        },
        {
          label: 'Canceled',
          field: 'is_canceled',
          name: 'is_canceled',
          sortable: true
        },
        {
          label: 'Only Editable by Creator',
          field: 'only_editable_by_creator',
          name: 'only_editable_by_creator',
          sortable: true
        },
        {
          label: 'Public',
          field: 'is_public',
          name: 'is_public',
          sortable: true
        },
        {
          label: 'Bearbeiten',
          field: 'action',
          name: 'action'
        }
      ]
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveysAllowed: 'surveys/backendSurveysAllowed'
    })
  },

  created: function () {
    this.$store.dispatch('surveys/fetchBackendSurveysAllowed')
  }
}

</script>
