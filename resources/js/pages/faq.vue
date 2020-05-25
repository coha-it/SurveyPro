<template>
  <div class="q-pa-md" style="max-width: 770px">
    <h1>FAQ</h1>
    <p>Frequently asked questions</p>

    <q-toolbar class="rounded-borders">
      <q-space />
      <q-input
        v-model="search"
        input-class="text-right"
        type="text"
        placeholder="Filter"
        :autofocus="true"
        borderless
      >
        <template v-slot:append>
          <q-icon v-if="search === ''" name="search" />
          <q-icon v-else name="clear" class="cursor-pointer" @click="search = ''" />
        </template>
      </q-input>
    </q-toolbar>

    <q-list bordered>
      <template>
        <template v-for="(item, index) in filteredList">
          <div :key="item.title">
            <Item :item="item" />
            <q-separator />
          </div>
        </template>

        <template v-for="(item, index) in unfilteredList">
          <div :key="item.title">
            <Item :item="item" :disable="true" />
            <q-separator />
          </div>
        </template>
      </template>
    </q-list>
  </div>
</template>

<script>
import Item from '~/components/FaqItem'

export default {

  components: {
    Item
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
        icon: 'money',
        active: false
      },
      {
        title: 'Kontakt',
        subtitle: "Wish I could come, but I'm out of town this weekend.",
        text: 'Wenn sie uns Kontaktieren möchten, ',
        icon: 'email',
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
