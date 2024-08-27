<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-upload-image__container">
    <!-- Preview image -->
    <vs-row v-if="previewImage || hasValue">
      <vs-col vs-lg="4" vs-sm="12">
        <div class="skijasi-upload-image__preview">
          <img :src="previewImage || value" class="skijasi-upload-image__preview-image" />
          <vs-button
            class="skijasi-upload-image__remove-button"
            color="danger"
            icon="close"
            @click="removeImage"
          />
        </div>
      </vs-col>
    </vs-row>

    <!-- File input -->
    <vs-input
      :label="label"
      :placeholder="placeholder"
      @click.prevent="$refs.image.click()"
      readonly
      v-model="value"
      icon="attach_file"
      icon-after="true"
    />
    <input
      type="file"
      class="skijasi-upload-image__input--hidden"
      ref="image"
      :accept="availableMimetypes.image.validMime.join(',')"
      @change="onFilePicked"
    />

    <!-- Cropper modal -->
    <vs-dialog v-model="showCropper" title="Crop Image" @close="onCropperClose">
      <div class="cropper-container" v-if="showCropper">
        <div class="button-group" v-if="showCropper">
    <button @click="cancelCrop" class="cancel-button">Odustani</button>
    <button @click="cropImage" class="confirm-button">Potvrdi</button>
  </div>
        <cropper
          v-if="showCropper"
          ref="cropper"
          :src="cropperImage"
          :aspectRatio="2/3"
    :guides="true"
    :viewMode="2"
    :autoCropArea="0.97"
    :dragMode="'move'"
    :movable="true"
    :zoomable="true"
    :rotatable="true"
    :scalable="true"
    :cropBoxMovable="true"
    :cropBoxResizable="true"
          @ready="onCropperReady"
        ></cropper>
      </div>
    
    </vs-dialog>

<!-- ... other code ... -->

   

    <vs-popup
      :title="$t('action.delete.title')"
      :active.sync="showDeleteImage"
      class="skijasi-upload-image__popup-dialog--delete"
    >
      <p>{{ $t("action.delete.text") }}</p>
      <div class="skijasi-upload-image__popup-dialog-content--delete">
        <vs-button color="primary" type="relief" @click="closeDeleteDialog">
          {{ $t("action.delete.cancel") }}
        </vs-button>
        <vs-button color="danger" type="relief" @click="deleteImage()">
          {{ $t("action.delete.accept") }}
        </vs-button>
      </div>
    </vs-popup>
  </vs-col>
</template>

<script>
import { mapState } from "vuex";
import * as _ from "lodash";
import { disableBodyScroll, enableBodyScroll } from "body-scroll-lock";

import Cropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';

export default {
  name: "SkijasiUploadImage",
  components: {
    Cropper
  },
  props: {
    size: {
      type: String || Number,
      default: "12",
    },
    label: {
      type: String,
      default: "",
    },
    nameusr: {
      type: String,
      default: "",
    },
    prezimeusr: {
      type: String,
      default: "",
    },
    idmember: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "Upload Image",
    },
    value: {
      default: null,
    },
    additionalInfo: {
      type: String,
      default: "",
    },
    alert: {
      type: String || Array,
      default: "",
    },
    sharesOnly: {
      type: Boolean,
    },
    privateOnly: {
      type: Boolean,
    },
  },
  data() {
    return {
      previewImage: null,
      showCropper: false,
      cropperImage: '',
      cropperReady: false,



      previewImage: null, 


      showFileManager: false,
      activeTab: "private",
      showDeleteImage: false,
      page: 1,
      images: [],
      paginator: {
        total: 1,
        perPage: 30,
      },
      isValidImageUrl: undefined,
      model: null,
      sortTypeValue: '',
      sortTypeList: [
        {
          label : "Time",
          value : 'time',
        },
        {
          label : "Alphabet",
          value : 'alphabet',
        }

      ],
    };
  },
  watch: {
    nameusr(newName) {
      this.updateProperties();
    },
    prezimeusr(newPrezime) {
      this.updateProperties();
    },
    idmember(newIdmember) {
      this.updateProperties();
    },

    page: {
      handler() {
        this.getImages();
      },
      immediate: false,
    },
  },
  created() {
    if (this.sharesOnly) {
      this.activeTab = "shares";
    }
  },
  computed: {
    getActiveTab() {
      return this.activeTab;
    },
    getActiveFolder() {
      switch (this.getActiveTab) {
        case "url":
          return;
        case "shares":
          return "/shares";
        default:
          return `/${this.userId}`;
      }
    },
    hasValue() {
      return (
        this.value !== null &&
        this.value !== "null" &&
        this.value !== "" &&
        this.value !== "[]" &&
        this.value !== "{}"
      );
    },
    ...mapState({
      userId(state) {
        return state.skijasi.user.id;
      },
      availableMimetypes(state) {
        return state.skijasi.availableMimetypes;
      },
    }),
  },
  methods: {
    updateUploadedImage(newValue) {
      this.$emit('update:uploadedImage', newValue);
    },

    // Update properties and emit an event
    updateProperties() {
      const updatedValue = {
        nameusr: this.nameusr,
        prezimeusr: this.prezimeusr,
        idmember: this.idmember,
      };
      this.updateUploadedImage(updatedValue);
    },

    
    removeImage() {
      // Set both value and previewImage to null
      this.$emit('input', null);
      this.previewImage = null;
    },

    
    resetState() {
      this.showFileManager = false;
      if (this.sharesOnly) {
        this.activeTab = "shares";
      } else {
        this.activeTab = "private";
      }
      this.showDeleteImage = false;
      this.page = 1;
      this.images = [];
      this.paginator = {
        total: 1,
        perPage: 30,
      };
      if (!this.hasValue) {
        this.model = null;
      }
      this.isValidImageUrl = undefined;
    },
    setActiveTab(tab) {
      if (this.getActiveTab !== tab) {
        this.activeTab = tab;
        this.model = null;
        this.page = 1;
        this.getImages();
      }
    },
    openFileManager() {
      this.showFileManager = true;
      this.disableScrollOnBody();
      this.getImages();
    },
    closeFileManager() {
      this.showFileManager = false;
      this.enableScrollOnBody();
      this.resetState();
    },
    openDeleteDialog() {
      this.showDeleteImage = true;
    },
    closeDeleteDialog() {
      this.showDeleteImage = false;
    },
    emitInput() {
      this.$emit("input", this.model);
      this.closeFileManager();
    },
    disableScrollOnBody() {
      disableBodyScroll(document.querySelector("body"));
    },
    enableScrollOnBody() {
      enableBodyScroll(document.querySelector("body"));
    },
   // ... other methods ...

   onFilePicked(e) {
      const files = e.target.files;
      if (files[0] !== undefined) {
        const file = files[0];
        if (file.size > this.availableMimetypes.image.maxSize * 1024) {
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: this.$t("alert.sizeTooLarge", { size: "5MB" }),
            color: "danger",
          });
          return;
        }
        if (!this.availableMimetypes.image.validMime.includes(file.type)) {
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: this.$t("alert.fileNotAllowed"),
            color: "danger",
          });
          return;
        }
        
        // Reset cropper state
        this.cropperReady = false;
        
        // Open the cropper with the selected image
        this.cropperImage = URL.createObjectURL(file);
        this.showCropper = true;
      }
    },
    
    onCropperReady() {
      this.cropperReady = true;
    },
    
    onCropperClose() {
      this.cropperReady = false;
      this.cropperImage = '';
    },
    
    cropImage() {
      if (!this.cropperReady || !this.$refs.cropper) {
        console.error('Cropper is not ready');
        return;
      }
      
      this.$refs.cropper.getCroppedCanvas().toBlob((blob) => {
        if (!blob) {
          console.error('Failed to create blob');
          return;
        }
        
        // Create a new File object from the Blob
        const croppedFile = new File([blob], 'cropped_image.png', { type: 'image/png' });
        
        // Set preview image
        this.previewImage = URL.createObjectURL(blob);
        
        // Upload the cropped image
        this.uploadImage(croppedFile);
        
        // Close the cropper
        this.showCropper = false;
      }, 'image/png');
    },
    
    cancelCrop() {
      this.showCropper = false;
    },

    uploadImage(file) {
      const formData = new FormData();
      formData.append("file", file);
      formData.append("nameusr", this.nameusr); 
      formData.append("prezimeusr", this.prezimeusr); 
      formData.append("idmember", this.idmember); 

      this.$api.skijasiFile.customuploadfile(formData)
        .then(response => {
          this.value = response.data.path;
          this.$emit("input", response.data.path); 
          URL.revokeObjectURL(this.previewImage);
        })
        .catch(error => {
          console.error(error);
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: this.$t("alert.uploadFailed"),
            color: "danger",
          });
        });
    },

    removeImage() {
      this.$emit('input', null);
      this.previewImage = null;
    },

// ... other methods ...

    sortImages(event) {
      this.getImages(event)
    },
    getImages(sortType) {
      if (this.getActiveFolder) {
        this.$openLoader();
        this.$api.skijasiFile
          .browseUsingLfm({
            workingDir: this.getActiveFolder,
            type: "image",
            sort_type: sortType ? sortType : 'time',
            page: this.page,
          })
          .then((res) => {
            const error = _.get(res, "data.original.error", null);
            if (error) {
              this.$vs.notify({
                title: this.$t("alert.danger"),
                text: error.message,
                color: "danger",
              });
            }

            this.images = res.data.items;
            this.paginator = res.data.paginator;
          })
          .catch((error) => {
            console.log(error);
          })
          .finally(() => {
            this.$closeLoader();
          });
      }
    },



    deleteImage() {
      this.$openLoader();
      this.$api.skijasiFile
        .deleteUsingLfm({
          workingDir: this.getActiveFolder,
          type: "image",
          "items[]": _.find(this.images, { url: this.model }).name,
        })
        .then((res) => {
          const error = _.get(res, "data.original.error", null);
          if (error) {
            this.$vs.notify({
              title: this.$t("alert.danger"),
              text: error.message,
              color: "danger",
            });
          }

          this.getImages();
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.$closeLoader();
          this.closeDeleteDialog();
        });
    },
  },
};
</script>

<style scoped>
.image-preview-container {
  width: 100%;
  margin-top: 10px;
  text-align: center;
}

.image-preview {
  max-width: 100%;
  max-height: 200px; /* Adjust as needed */
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.cropper-container {
  position: relative;
  z-index: 9999;
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  opacity: 1;
}




.button-group {
  display: flex;
  justify-content: center;
  margin-top: 10px;
}


.confirm-button,
.cancel-button {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
}

.confirm-button {
  background-color: #03A9F4;
  color: white;
  
}

.cancel-button {
  background-color: #676767;
  color: white;
  margin-right: 6px;
}

</style>
