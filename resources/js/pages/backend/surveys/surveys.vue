<template>
    <div>
        <h1>Surveys Management</h1>
        <p>Manage your Surveys or <router-link :to="{name: 'backend.survey', params: {id: 'create'}}">create a new one</router-link></p>

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
                dense
                >
                <template v-slot:body-cell-action="props">
                  <q-td :props="props">
                    <q-btn
                      :label="$t('edit')"
                      size="sm"
                      :to="{ name: 'backend.survey', params: {id: props.row.id } }"
                      unelevated
                      outline
                      />
                  </q-td>
                </template>
                </q-table>
        </q-card>
    </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'


export default {
    middleware: 'canCreateSurveys',

    data() {
        return {
            sSearch: "",
            aHeaders : [
                {
                  label: "Title",
                  field: 'title',
                  name: 'title',
                  sortable: true,
                },
                {
                    label: 'ID',
                    field: 'id',
                    align: 'left',
                    name: 'id',
                },
                {
                  label: "Description Short",
                  field: 'desc_short',
                  name: 'desc_short',
                  sortable: true,
                },
                {
                  label: "Description Long",
                  field: 'desc_long',
                  name: 'desc_long',
                  sortable: true,
                },
                {
                  label: "Author",
                  field: 'author',
                  name: 'author',
                  sortable: true,
                },
                {
                  label: "Created By",
                  field: 'created_by',
                  name: 'created_by',
                  sortable: true,
                },
                {
                  label: "Active",
                  field: 'active',
                  name: 'active',
                  sortable: true,
                },
                {
                  label: "Start",
                  field: 'start_datetime',
                  name: 'start_datetime',
                  sortable: true,
                },
                {
                  label: "End",
                  field: 'end_datetime',
                  name: 'end_datetime',
                  sortable: true,
                },
                {
                  label: "Finished",
                  field: 'is_finished',
                  name: 'is_finished',
                  sortable: true,
                },
                {
                  label: "Canceled",
                  field: 'is_canceled',
                  name: 'is_canceled',
                  sortable: true,
                },
                {
                  label: "Only Editable by Creator",
                  field: 'only_editable_by_creator',
                  name: 'only_editable_by_creator',
                  sortable: true,
                },
                {
                  label: "Public",
                  field: 'is_public',
                  name: 'is_public',
                  sortable: true,
                },
                {
                  label: "Bearbeiten",
                  field: 'action',
                  name: 'action',
                },
            ]
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
            surveysAllowed: 'surveys/surveysAllowed',
        })
    },

    created: function() {
        this.$store.dispatch('surveys/fetchSurveysAllowed')
    },
}

</script>
