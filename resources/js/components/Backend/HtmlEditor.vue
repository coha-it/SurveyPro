<template>
  <span>
    <template v-if="useHtml">
      <q-item-label>
        <q-editor
          v-model="model[field]"
          :disable="disable"
          :toolbar="toolbar"
          :fonts="fonts"
          min-height="5rem"
          :hint="hint"
          :label="label"
          :placeholder="placeholder"
          outlined
          persistent-hint
          required
        />
      </q-item-label>
      <q-item-label caption class="q-px-md">
        {{ label }}
      </q-item-label>
    </template>
    <template v-else>
      <q-input
        v-model="model[field]"
        type="textarea"
        :disable="disable"
        dense
        persistent-hint
        outlined
        :placeholder="placeholder"
        :label="label"
        :hint="hint"
        required
      >
        <template v-slot:append>
          <q-icon :name="help_icon">
            <q-tooltip self="center middle">
              Beschreibung der Umfrage. Wird unter Titel / Subtitel klein angezeigt.
            </q-tooltip>
          </q-icon>
        </template>
      </q-input>
    </template>
  </span>
</template>

<script>
export default {
  props: {
    model: {
      type: Object,
      required: true
    },
    field: {
      type: String,
      required: true
    },
    disable: {
      type: Boolean,
      default: false
    },
    useHtml: {
      type: Number,
      default: 0
    },
    hint: {
      type: String,
      default: 'Lange Beschreibung der Umfrage'
    },
    label: {
      type: String,
      default: 'Langbeschreibung'
    },
    placeholder: {
      type: String,
      default: 'Bitte hier Ihre Beschreibung einfügen'
    }
  },

  data () {
    return {

      toolbar: [
        [
          {
            label: this.$q.lang.editor.align,
            icon: this.$q.iconSet.editor.align,
            fixedLabel: true,
            list: 'only-icons',
            options: ['left', 'center', 'right', 'justify']
          }
        ],
        ['bold', 'italic', 'strike', 'underline', 'subscript', 'superscript'],
        ['token', 'hr', 'link', 'custom_btn'],
        ['print', 'fullscreen'],
        [
          {
            label: this.$q.lang.editor.formatting,
            icon: this.$q.iconSet.editor.formatting,
            list: 'no-icons',
            options: [
              'p',
              'h1',
              'h2',
              'h3',
              'h4',
              'h5',
              'h6',
              'code'
            ]
          },
          {
            label: this.$q.lang.editor.fontSize,
            icon: this.$q.iconSet.editor.fontSize,
            fixedLabel: true,
            fixedIcon: true,
            list: 'no-icons',
            options: [
              'size-1',
              'size-2',
              'size-3',
              'size-4',
              'size-5',
              'size-6',
              'size-7'
            ]
          },
          {
            label: this.$q.lang.editor.defaultFont,
            icon: this.$q.iconSet.editor.font,
            fixedIcon: true,
            list: 'no-icons',
            options: [
              'default_font',
              'arial',
              'arial_black',
              'comic_sans',
              'courier_new',
              'impact',
              'lucida_grande',
              'times_new_roman',
              'verdana'
            ]
          },
          'removeFormat'
        ],
        ['quote', 'unordered', 'ordered', 'outdent', 'indent'],

        ['undo', 'redo'],
        ['viewsource']
      ],
      fonts: {
        arial: 'Arial',
        arial_black: 'Arial Black',
        comic_sans: 'Comic Sans MS',
        courier_new: 'Courier New',
        impact: 'Impact',
        lucida_grande: 'Lucida Grande',
        times_new_roman: 'Times New Roman',
        verdana: 'Verdana'
      }
    }
  },

  methods: {
    isAvailable () {
      const model = this.model
      const field = this.field

      return model && field && typeof model[field] !== 'undefined'
    }
  }
}
</script>
