<template>
    <div>
        <h1>Surveys Management</h1>
        <p>Manage your Surveys or <router-link :to="{name: 'backend.survey', params: {id: 'create'}}">create a new one</router-link></p>

        <v-card>
            <v-card-title>
                <v-text-field
                    v-model="sSearch"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table
                v-if="surveysCreated && surveysCreated.length >= 1"
                :headers="aHeaders"
                :items="surveysCreated"
                :search="sSearch" 
                multi-sort 
                :footer-props="{
                    showFirstLastPage: true,
                }"
                dense
                >
                <template v-slot:item.action="{ item }">
                    <router-link :to="{ name: 'backend.survey', params: {id: item.id } }" tag="v-icon" small class="mr-2">edit</router-link>
                </template>
                </v-data-table>
        </v-card>
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
                    text: 'ID',
                    align: 'left',
                    value: 'id',
                },
                { text: "Title", value: 'title'},
                { text: "Description Short", value: 'desc_short'},
                { text: "Description Long", value: 'desc_long'},
                { text: "Author", value: 'author'},
                { text: "Created By", value: 'created_by'},
                { text: "Active", value: 'active'},
                { text: "Start", value: 'start_datetime'},
                { text: "End", value: 'end_datetime'},
                { text: "Finished", value: 'is_finished'},
                { text: "Canceled", value: 'is_canceled'},
                { text: "Only Editable by Creator", value: 'only_editable_by_creator'},
                { text: "Public", value: 'is_public'},

                { text: "Bearbeiten", value: 'action'},
            ]
        }
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
            surveysCreated: 'surveys/surveysCreated',
        })
    },

    created: function() {        
        this.$store.dispatch('surveys/fetchSurveysCreated')
    },
}

</script>