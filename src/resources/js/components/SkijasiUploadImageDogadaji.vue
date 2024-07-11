<template>
<!-- ... other code ... -->
<vs-col :vs-lg="size" vs-xs="12" class="skijasi-upload-image__container">
    <!-- Only display this row if there is a preview image or an existing value -->
    <vs-row v-if="previewImage || hasValue">
      <vs-col vs-lg="4" vs-sm="12">
        <div class="skijasi-upload-image__preview">
          <!-- Use previewImage if it's available, otherwise fallback to value -->
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


  <vs-input
    :label="label"
    :placeholder="placeholder"
    @click.prevent="$refs.image.click()"
    readonly
    v-model="value"
    icon="attach_file"
    icon-after="true"
  />
  <!-- This input will handle file selection -->
  <input
  type="file"
  class="skijasi-upload-image__input--hidden"
  ref="image"
  :accept="getAcceptedMimeTypes"
  @change="onFilePicked"
/>
   
  
  <!-- ... other code ... -->

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
export default {
  name: "SkijasiUploadImageDogadaji",
  props: {
    size: {
      type: String || Number,
      default: "12",
    },
    label: {
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
      hardcodedMimeTypes: {
      image: {
        validMime: ['image/jpeg','image/jpg', 'image/png', 'image/webp'],
        maxSize: 7120 // 5MB in KB
      }
    },


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
    getAcceptedMimeTypes() {
    return this.hardcodedMimeTypes.image.validMime.join(',');
  },
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

      // availableMimetypes(state) {
      //   return state.skijasi.availableMimetypes;
      // },
    }),
  },
  methods: {
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


   onFilePicked(e) {
  const files = e.target.files;
  if (files[0] !== undefined) {
    const file = files[0];
    if (file.size > this.hardcodedMimeTypes.image.maxSize * 1024) {
      this.$vs.notify({
        title: this.$t("alert.danger"),
        text: this.$t("alert.sizeTooLarge", { size: "7MB" }),
        color: "danger",
      });
      return;
    }
    if (!this.hardcodedMimeTypes.image.validMime.includes(file.type)) {
      this.$vs.notify({
        title: this.$t("alert.danger"),
        text: this.$t("alert.fileNotAllowed"),
        color: "danger",
      });
      return;
    }
    this.previewImage = URL.createObjectURL(file);
    this.uploadImage(file);
  }
},



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


    uploadImage(file) {
  const formData = new FormData();
  formData.append("file", file);
  // Retrieve the name and idmember values

  // Post the form data to the customUploadFile endpoint
  this.$api.skijasiFile.customuploadfiledogadaji(formData)
  .then(response => {
    // Handle the response from the server
        // Update the value to show the image preview from the server
        this.value = response.data.path; // Assuming 'path' is the key where the image URL is stored
    this.previewImage = this.value; // Update the preview image to the final URL


     // Emit the event to parent component with the new image URL
     this.$emit("input", response.data.path); 

    // Revoke the object URL if you want to release memory
    URL.revokeObjectURL(this.previewImage);
    console.log(response.data);
    // You can now update your component's data or emit an event with the file's path if needed
  })
  .catch(error => {
    // Handle any errors
    console.error(error);
  });
},


/*
    uploadImage(file) {
      const files = new FormData();
      files.append("upload", file);
      files.append("type", "image");
      files.append("working_dir", this.getActiveFolder);
      this.$api.skijasiFile
        .uploadUsingLfm(files)
        .then((res) => {
          const error = _.get(res, "data.original.error", null);
          if (error) {
            this.$vs.notify({
              title: this.$t("alert.danger"),
              text: error.message,
              color: "danger",
            });
          } else {
            this.$vs.notify({
              title: this.$t("alert.success"),
              text: "Upload successful",
              color: "success",
            });
          }
        })
        .catch((error) => {
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        })
        .finally(() => {
          this.getImages();
        });
    },

    */
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
</style>
