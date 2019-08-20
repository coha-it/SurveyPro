<template>
    <div>
        <h1>FAQ</h1>
        <p>Frequently asked questions</p>

        <input type="text" v-model="search" placeholder="Search title.."/>

        <template>
            <v-card class="mx-auto">
                <v-list two-line>


                      <template v-for="(item, index) in filteredList">
                        <div :key="item.title">
                            <v-list-item >
                                <template>
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.title"></v-list-item-title>
                                        <v-list-item-subtitle class="text--primary" v-text=" 'Frage'+ ': ' +item.headline"></v-list-item-subtitle>
                                        <v-list-item-subtitle v-text="'Antwort' + ': '+ item.subtitle"></v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-icon @click="onClickToggle(item)" text color="grey lighten-1">
                                            <template v-if="item.active">keyboard_arrow_up</template>
                                            <template v-else>keyboard_arrow_down</template>
                                        </v-icon>
                                    </v-list-item-action>
                                </template>
                            </v-list-item>

                            <template>
                              <v-fade-transition mode="out-in">
                                <div style="padding: 5px 15px 20px;" v-show="item.active" class="mx-auto">{{ item.text }}</div>
                              </v-fade-transition>
                            </template>

                            <v-divider v-if="index + 1 < items.length" :key="index"></v-divider>

                          </div>
                      </template>

                      <template v-for="(item, index) in unfilteredList">
                        <div :key="item.title">
                            <v-list-item disabled>
                                <template>
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.title"></v-list-item-title>
                                        <v-list-item-subtitle class="text--primary" v-text=" 'Frage'+ ': ' +item.headline"></v-list-item-subtitle>
                                        <v-list-item-subtitle v-text="'Antwort' + ': '+ item.subtitle"></v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-icon @click="onClickToggle(item)" text color="grey lighten-1">
                                            <template v-if="item.active">keyboard_arrow_up</template>
                                            <template v-else>keyboard_arrow_down</template>
                                        </v-icon>
                                    </v-list-item-action>
                                </template>
                            </v-list-item>

                            <v-divider v-if="index + 1 < items.length" :key="index"></v-divider>

                          </div>
                      </template>

                </v-list>
            </v-card>
        </template>

    </div>
</template>


<script>
  export default {

    methods: {
        onClickToggle(item) {
            item.active = !item.active;
        },
    },

    data: () => ({
      selected: [],
      expand: true,
      search: '',
      items: [
        {
          title: 'Zahlarten',
          subtitle: "I'll be in your neighborhood doing errands this weekend. Do you want to hang out?",
          text: 'Längerer Text',
          active: false,
        },
        {
          title: 'Probleme',
          subtitle: "Wish I could come, but I'm out of town this weekend.",
          text: 'Längerer Text',
          active: false,
        }
      ],
    }),

    computed: {
      filteredList() {
        return this.items.filter(item => {
          let txt = item.title + item.subtitle + item.text;
          return txt.toLowerCase().includes(this.search.toLowerCase())
        })
      },
      unfilteredList() {
        return this.items.filter(item => {
          let txt = item.title + item.subtitle + item.text;
          return !txt.toLowerCase().includes(this.search.toLowerCase())
        })
      }
    }

  }
</script>

<style lang="scss" scoped>
.v-list-item.v-list-item--disabled {
    opacity: .5;
    background: #f2f2f2;
}
</style>