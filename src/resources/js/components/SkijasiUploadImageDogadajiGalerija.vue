<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-upload-image__container">
    <!-- Display preview for each image in the array -->
    <vs-row v-if="value && value.length > 0">
      <vs-col vs-lg="4" vs-sm="12" v-for="(image, index) in value" :key="index">
        <div class="skijasi-upload-image__preview">
          <img :src="image" class="skijasi-upload-image__preview-image" />
          <vs-button
            class="skijasi-upload-image__remove-button"
            color="danger"
            icon="close"
            @click="removeImage(index)"
          />
        </div>
      </vs-col>
    </vs-row>

    <!-- Use a hidden input for file selection -->
    <input
      type="file"
      multiple
      class="skijasi-upload-image__input--hidden"
      ref="image"
      :accept="getAcceptedMimeTypes"
      @change="onFilePicked"
    />

    <!-- Button to trigger file selection -->
    <vs-button @click="$refs.image.click()">
      {{ placeholder }}
    </vs-button>
  </vs-col>
</template>

<script>
import { mapState } from "vuex";
import * as _ from "lodash";
import { disableBodyScroll, enableBodyScroll } from "body-scroll-lock";
export default {
  name: "SkijasiUploadImageDogadajiGalerija",
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
      default: "Upload Images",
    },
    value: {
      type: Array,
      default: () => [],
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
  if (files.length > 0) {
    Array.from(files).forEach(file => {
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
      this.uploadImage(file);
    });
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

      const formData = new FormData();
      formData.append("file", file);

      this.$api.skijasiFile.customuploadfiledogadaji(formData)
        .then(response => {
          const newImageUrl = response.data.path;
          const updatedGallery = [...this.value, newImageUrl];
          this.$emit("input", updatedGallery);
        })
        .catch(error => {
          console.error(error);
        });
    },

    removeImage(index) {
      const updatedGallery = [...this.value];
      updatedGallery.splice(index, 1);
      this.$emit("input", updatedGallery);
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
