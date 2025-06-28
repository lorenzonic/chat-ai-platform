<template>
  <ModernChatbot
    :store="store"
    :prefilled-question="prefilledQuestion"
    :ref-code="refCode"
  />
</template>

<script>
import ModernChatbot from './ModernChatbot.vue'

export default {
  name: 'ChatbotApp',
  components: {
    ModernChatbot
  },
  props: {
    store: {
      type: Object,
      required: true
    },
    prefilledQuestion: {
      type: String,
      default: null
    },
    refCode: {
      type: String,
      default: null
    }
  }
}
</script>
