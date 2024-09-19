<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-editor__container">
    <label v-if="label != ''" for="" class="skijasi-editor__label">{{ label }}</label>
    <jodit-editor
      :id="editorId"
      v-model="internalValue"
      @input="handleInput"
      :config="config"
    ></jodit-editor>
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="Array.isArray(alert)">
        <p
          class="skijasi-editor__input--error"
          v-for="(info, index) in alert"
          :key="index"
          v-html="info + '<br />'"
        ></p>
      </div>
      <div v-else>
        <span class="skijasi-editor__input--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
import 'jodit/build/jodit.min.css'
import { JoditEditor } from 'jodit-vue'

export default {
  name: 'SkijasiEditor',
  components: {
    JoditEditor,
  },
  data() {
    return {
      internalValue: this.value,
      config: {
        readonly: false,
        height: 500,
        toolbarAdaptive: false,
        toolbarSticky: false,
        showCharsCounter: false,
        showWordsCounter: false,
        showXPathInStatusbar: false,
        uploader: {
          insertImageAsBase64URI: true,
        },
        createAttributes: {
        hr: {
          style: 'height: 2px; background-color: black; border: none;'
        }
      },
        toolbarButtonSize: 'large',
        buttons: [
          'source', '|',
          'bold', 'italic', 'underline', 'strikethrough', '|',
          'ul', 'ol', '|',
          'outdent', 'indent', '|',
          'fontsize', 'brush', 'paragraph', '|',
          'image', 'video', 'table', 'link', '|',
          'align', 'undo', 'redo', '|',
          'hr', 'eraser', 'copyformat', '|',
          'symbol', 'print',
        ],
        style: {
          fontFamily: 'Inter, sans-serif'
        },
      },
    };
  },
  props: {
    editorId: {
      type: String,
      required: true,
    },
    size: {
      type: String,
      default: '12',
    },
    label: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: 'Editor',
    },
    value: {
      type: String,
      required: true,
      default: '',
    },
    additionalInfo: {
      type: String,
      default: '',
    },
    alert: {
      type: [String, Array],
      default: '',
    },
  },
  watch: {
    value(newValue) {
      this.internalValue = newValue;
    },
  },
  methods: {
    handleInput(val) {
      this.$emit('input', val);
    },
  },
};
</script>

<style scoped>
.skijasi-editor__container .jodit-wysiwyg {
  font-family: 'Inter', sans-serif !important;
}
</style>
