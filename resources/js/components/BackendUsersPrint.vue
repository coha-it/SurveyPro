<template>
    <v-dialog v-model="dialog" lazy transition="disabled" overflowed content-class="naked dark" no-click-animation>
        <template v-slot:activator="{ on }">
            <v-btn text rounded error warning @click="printUsers()">
                <v-icon left>print</v-icon> {{ users.length + ' ' + $t('print') }}
            </v-btn>

            <component is="style" type="text/css">
                @media print {
                    nav,
                    .v-application--wrap,
                    .v-toolbar,
                    .v-toolbar__content {
                        display: none !important;
                    }

                    .v-dialog__content,
                    .v-dialog {
                        position: inherit;
                    }

                }

                    .v-dialog__content,
                    .v-dialog {
                        margin: 0 !important;
                        max-height: 100% !important;
                    }

            </component>

        </template>

        <v-toolbar dark color="primary" fixed absolute top>
          <v-btn icon dark @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <div class="coha--print">
            <div v-for="user in users" v-bind:key="user.id" class="element page-break">
                <div class="inner">

                    <img class="logo" src="/storage/corporate-happiness-gmbh-logo-full-white.svg">

                    <h3>Mitarbeiter-Befragung</h3>
                    <p>Dies ist ihr individueller Zugang für die Mitarbeiter-Befragung</p>

                    <div class="label pan-label">PAN</div>
                    <div class="c-code-text pan">{{ user.pan.pan }}</div>
                    <div class="label pin-label">PIN</div>
                    <div class="c-code-text pin">{{ user.pan.pin }}</div>

                    <p class="subtext">Jetzt anmelden auf: <br>www.surveypro.tk/pan </p>

                    <img class="bg" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDk3MDdGMDNDNzZBMTFFOTgyN0RGNzg4NTBENkE5QjYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDk3MDdGMDRDNzZBMTFFOTgyN0RGNzg4NTBENkE5QjYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpEOTcwN0YwMUM3NkExMUU5ODI3REY3ODg1MEQ2QTlCNiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpEOTcwN0YwMkM3NkExMUU5ODI3REY3ODg1MEQ2QTlCNiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PipDYJYAAAAGUExURe3QAAAAAIZIzsoAAAAMSURBVHjaYmAACDAAAAIAAU9tWeEAAAAASUVORK5CYII=" />
                </div>
            </div>
        </div>
        

    </v-dialog>
</template>

<script>

import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
    props: [
        'p_users'
    ],

    data() {
        return {
            dialog: false,

            // From Parent
            users: this.p_users
        }
    },


    methods: {
        printUsers() {
            var _this = this;

            _this.dialog = true;
            console.log('jo');
            setTimeout(function() {
                // window.print();
                // _this.dialog = false;
            }, 100);
        },
    }
}
</script>

<style lang="scss" scoped>
.coha--print {
    display: block;
    font-size: .5cm;
    .element {
        margin: auto;
        min-width: 200px;
        width: 100vw;
        max-width: 2000px;
        min-height: 500px;
        height: 100vh;
        max-height: 3000px;
        padding: 100px;
        color: #000;
        
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: center;
        align-self: center;

        .inner {
            position: relative;
            display: block;
            width: 8cm;
            height: 13cm;
            padding: .5cm .5cm;
            text-align: center;
            margin: auto;
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

        p {
            margin-bottom: 20px;

            &.subtext {
                font-size: .8em;
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
            margin-bottom: 25px;
            &.pan {
                font-size: 0.7cm;
            }
            &.pin {
                font-size: 0.5cm;
            }
        }

        .label {
            font-size: 1cm;
            font-weight: 900;
            line-height: 1;
            margin-top: 20px;
        }

    }


}
</style>