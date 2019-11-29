<template>
    <span>
      <q-btn
        icon-left="print"
        text
        rounded
        unelevated
        error
        warning
        @click="printView()"
        :disabled="disabled"
        :label="users.length + ' ' + $t('print')"
        />

      <q-dialog content-class="coha--print-wrapper" v-model="dialog" maximized>
        <div>
          <q-toolbar class="bg-primary text-white" dark color="primary">
            <q-btn icon dark @click="dialog = false">
                <q-icon>mdi-close</q-icon>
            </q-btn>
            <q-btn text rounded dark @click="printUsers()" class="ml-3 mr-3">
                <q-icon left>print</q-icon> Drucken
            </q-btn>
            <q-toggle class="mt-7 ml-1 mr-5" v-model="colorfull" :label="colorfull ? 'Farbenfroh' : 'Schwarz & Weiß' " color="accent" />
            <q-toggle class="mt-7 ml-1 mr-5" v-model="oPrint.bShowCompany" label="Firma" color="secondary" />
            <q-toggle class="mt-7 ml-1 mr-5" v-model="oPrint.bShowDepartment" label="Abteilung" color="secondary" />
            <q-toggle class="mt-7 ml-1 mr-5" v-model="oPrint.bShowLocation" label="Ort" color="secondary" />
            <div class="flex-grow-1"></div>
          </q-toolbar>

          <div class="coha--print">
              <div v-for="user in users" v-bind:key="user.id" class="element page-break" :class="colorfull ? '' : 'blackAndWhite'">
                  <div class="inner">

                      <img class="logo" :src="'/storage/corporate-happiness-gmbh-logo-full' + ( (colorfull) ? '-white' : '') + '.svg' ">

                      <h3>Mitarbeiter-Befragung</h3>
                      <p>
                          Dies ist ihr individueller Zugang für die Mitarbeiter-Befragung.<br>
                          <template v-if="user.company && user.company.name && oPrint.bShowCompany">
                              {{ $t('company') }}: {{ user.company.name }}<br>
                          </template>
                          <template v-if="user.department && user.department.name && oPrint.bShowDepartment">
                              {{ $t('department') }}: {{ user.department.name }}<br>
                          </template>
                          <template v-if="user.location && user.location.name && oPrint.bShowLocation">
                              {{ $t('location') }}: {{ user.location.name }}<br>
                          </template>
                      </p>

                      <div class="label pan-label">PAN</div>
                      <div class="c-code-text pan">{{ user.pan.pan }}</div>
                      <div class="label pin-label">PIN</div>
                      <div class="c-code-text pin">{{ user.pan.pin }}</div>

                      <p>
                          <qrcode-vue :value="url(user)" level="H"></qrcode-vue>
                      </p>

                      <p class="subtext">Jetzt anmelden auf: <br>www.surveypro.tk/pan </p>

                      <img class="bg" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDk3MDdGMDNDNzZBMTFFOTgyN0RGNzg4NTBENkE5QjYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDk3MDdGMDRDNzZBMTFFOTgyN0RGNzg4NTBENkE5QjYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpEOTcwN0YwMUM3NkExMUU5ODI3REY3ODg1MEQ2QTlCNiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpEOTcwN0YwMkM3NkExMUU5ODI3REY3ODg1MEQ2QTlCNiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PipDYJYAAAAGUExURe3QAAAAAIZIzsoAAAAMSURBVHjaYmAACDAAAAIAAU9tWeEAAAAASUVORK5CYII=" />
                  </div>
              </div>
          </div>
        </div>
      </q-dialog>

      <component v-if="bBeforePrint" is="style" type="text/css">

        #app {
          display: none !important;
        }

        html,
        body,
        .q-body--fullscreen-mixin,
        .q-body--prevent-scroll,
        .q-body--dialog,
        .q-dialog,
        .q-dialog__inner,
        .q-dialog__inner--maximized>div,
        .coha--print-wrapper,
        .coha--print-wrapper .q-dialog__inner,
        .coha--print-wrapper .q-dialog__inner--maximized>div,
        .coha--print-wrapper .coha--print
        {
          max-height: unset !important;
          height: auto !important;
          position: static !important;
          overflow: scroll !important;
          will-change: inherit !important;
          -webkit-overflow-scrolling: inherit !important;
        }


        nav,
        .v-application--wrap,
        .q-toolbar{
            display: none !important;
        }

        .fixed, .fixed-bottom, .fixed-bottom-left, .fixed-bottom-right, .fixed-center, .fixed-full, .fixed-left, .fixed-right, .fixed-top, .fixed-top-left, .fixed-top-right, .fullscreen {
          position: static !important;
        }
      </component>

    </span>
</template>

<script>

import { mapGetters } from 'vuex'
import axios from 'axios'
import QrcodeVue from 'qrcode.vue'

export default {
    props: [
        'users',
        'disabled'
    ],

    components: {
        QrcodeVue
    },

    data() {
        return {
            bBeforePrint: false,
            dialog: false,
            colorfull: true,
            oPrint: {
                bShowCompany: true,
                bShowDepartment: true,
                bShowLocation: true
            }
        }
    },


    methods: {
        printView() {
          var _this = this;
          _this.dialog = true;
        },
        printUsers() {
          var _this = this;

          _this.bBeforePrint = true;

          setTimeout(function() {
            window.print();
            setTimeout(function() {
              _this.bBeforePrint = false;
            }, 10);
          }, 10);

        },
        url(user) {
            return "https://surveypro.tk/p/"+user.pan.pan;
        }
    }
}
</script>

<style lang="scss" scoped>

.coha--print-wrapper {
    // position: fixed;
    // left: 0;
    // top: 0;
    // width: auto;
    // height: auto;
    // z-index: 99999;
    // background: #ffff;
    // overflow: visible;
  .coha--print {
      display: block;
      font-size: .5cm;
      .element {
          margin: auto;
          width: 100vw;
          height: 100vh;
          color: #000;
          display: inline-block;

          letter-spacing: 0.05em;
          display: flex;
          align-items: center;
          justify-content: center;
          align-self: center;

          page-break-inside: avoid;

          .inner {
              page-break-inside: avoid;
              position: relative;
              display: block;
              width: 110mm;
              height: 170mm;
              padding: 2mm 10mm;
              text-align: center;
              margin: auto;
              h3 {
                margin: 0;
              }
              .bg {
                  // background: #e8d03e;
                  width: 100%;
                  height: 100%;
                  position: absolute;
                  left: 0;
                  right: 0;
                  z-index: -1;
                  top: 0;
                  pointer-events: none;

              }
          }

          // Black and White
          &.blackAndWhite {
              .inner {
                  background-color: #fff;
                  border: 2px solid grey;
                  .bg {
                      display: none;
                  }
              }
          }

          p {
              margin-bottom: 12px;
              font-size: 4mm;

              &.subtext {
                  font-size: 3.5mm;
                  line-height: 1.15;
              }
          }

          h3 {
              font-weight: 600;
          }

          .logo {
              width: 4cm;
              display: inline-block;
          }

          .c-code-text {
              font-weight: 900;
              margin-bottom: 10px;
              &.pan {
                  font-size: 0.7cm;
              }
              &.pin {
                  font-size: 0.5cm;
              }
          }

          .label {
              font-size: 8mm;
              font-weight: 900;
              line-height: 1;
              margin-top: 5px;
          }
      }
  }
}

.q-dialog__inner>div {
  overflow: inherit;
}

@media print {

}

</style>
