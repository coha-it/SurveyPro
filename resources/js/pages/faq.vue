<template>
  <div>
    <h1>FAQ</h1>
    <p>Frequently asked questions</p>

    <input v-model="search" type="text" placeholder="Search title..">

    <template>
      <q-card class="mx-auto">
        <q-list two-line>
          <template v-for="(item, index) in filteredList">
            <div :key="item.title">
              <q-item>
                <template>
                  <q-item-content>
                    <q-item-title v-text="item.title" />
                    <q-item-subtitle class="text--primary" v-text=" 'Frage'+ ': ' +item.headline" />
                    <q-item-subtitle v-text="'Antwort' + ': '+ item.subtitle" />
                  </q-item-content>
                  <q-item-action>
                    <q-icon text color="grey lighten-1" @click="onClickToggle(item)">
                      <template v-if="item.active">
                        keyboard_arrow_up
                      </template>
                      <template v-else>
                        keyboard_arrow_down
                      </template>
                    </q-icon>
                  </q-item-action>
                </template>
              </q-item>

              <template>
                <q-fade-transition mode="out-in">
                  <div v-show="item.active" style="padding: 5px 15px 20px;" class="mx-auto">
                    {{ item.text }}
                  </div>
                </q-fade-transition>
              </template>

              <q-separator v-if="index + 1 < items.length" :key="index" />
            </div>
          </template>

          <template v-for="(item, index) in unfilteredList">
            <div :key="item.title">
              <q-item disabled>
                <template>
                  <q-item-content>
                    <q-item-title v-text="item.title" />
                    <q-item-subtitle class="text--primary" v-text=" 'Frage'+ ': ' +item.headline" />
                    <q-item-subtitle v-text="'Antwort' + ': '+ item.subtitle" />
                  </q-item-content>
                  <q-item-action>
                    <q-icon text color="grey lighten-1" @click="onClickToggle(item)">
                      <template v-if="item.active">
                        keyboard_arrow_up
                      </template>
                      <template v-else>
                        keyboard_arrow_down
                      </template>
                    </q-icon>
                  </q-item-action>
                </template>
              </q-item>

              <q-separator v-if="index + 1 < items.length" :key="index" />
            </div>
          </template>
        </q-list>
      </q-card>
    </template>
  </div>
</template>

<script>
export default {

  data: () => ({
    selected: [],
    expand: true,
    search: '',
    items: [
      {
        title: 'Zahlarten',
        subtitle: "I'll be in your neighborhood doing errands this weekend. Do you want to hang out?",
        text: 'Längerer Text',
        active: false
      },
      {
        title: 'Probleme',
        subtitle: "Wish I could come, but I'm out of town this weekend.",
        text: 'Längerer Text',
        active: false
      }
    ]
  }),

  computed: {
    filteredList () {
      return this.items.filter(item => {
        let txt = item.title + item.subtitle + item.text
        return txt.toLowerCase().includes(this.search.toLowerCase())
      })
    },
    unfilteredList () {
      return this.items.filter(item => {
        let txt = item.title + item.subtitle + item.text
        return !txt.toLowerCase().includes(this.search.toLowerCase())
      })
    }
  },

  methods: {
    onClickToggle (item) {
      item.active = !item.active
    }
  }

}
</script>

<style lang="scss" scoped>
.q-item.q-item--disabled {
    opacity: .5;
    background: #f2f2f2;
}
</style>
