<template>
    <default-field :field="field" :errors="errors" :show-help-text="showHelpText">
        <template slot="field">
            <video v-show="!isPhotoTaken" ref="camera" :width="450" :height="337.5" autoplay></video>
            <canvas v-show="isPhotoTaken" ref="canvas" :width="450" :height="337.5"></canvas>
            <canvas v-show="isPhotoTaken" id="photoTaken" ref="canvas2" class="hidden" :width="1280" :height="960"></canvas>

            <button class="text-xs text-white btn btn-sm btn-default bg-primary leading-tight" type="button" @click="takePhoto" v-text="isPhotoTaken ? 'Recommencer' : 'Prendre une photo'"></button>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            isCameraOpen: false,
            isLoading: false,
            isPhotoTaken: false,
        }
    },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        toggleCamera() {
            if (this.isCameraOpen) {
                this.isCameraOpen = false
                this.isPhotoTaken = false
                this.isShotPhoto = false
                this.stopCameraStream()
            } else {
                this.isCameraOpen = true
                this.createCameraElement()
            }
        },

        createCameraElement() {
            this.isLoading = true

            const constraints = (window.constraints = {
                audio: false,
                video: true
            })

            navigator.mediaDevices
                .getUserMedia(constraints)
                .then(stream => {
                    this.isLoading = false
                    this.$refs.camera.srcObject = stream
                })
                .catch(error => {
                    this.isLoading = false
                    alert("May the browser didn't support or there is some errors.")
                });
        },

        stopCameraStream() {
            let tracks = this.$refs.camera.srcObject.getTracks();

            tracks.forEach(track => {
                track.stop();
            });
        },

        takePhoto() {
            if(!this.isPhotoTaken) {
                this.isShotPhoto = true;

                const FLASH_TIMEOUT = 50;

                setTimeout(() => {
                this.isShotPhoto = false;
                }, FLASH_TIMEOUT);
            }

            this.isPhotoTaken = !this.isPhotoTaken;

            const context = this.$refs.canvas.getContext('2d');
            context.drawImage(this.$refs.camera, 0, 0, 450, 337.5);

            const context2 = this.$refs.canvas2.getContext('2d');
            context2.drawImage(this.$refs.camera, 0, 0, 1280, 960);

            this.value = document.getElementById("photoTaken").toDataURL("image/jpeg", 1);
        },
    },

    created() {
        this.toggleCamera()
    }
}
</script>
