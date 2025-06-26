<template>
  <CompactChatbot
    :store="store"
    :prefilled-question="prefilledQuestion"
    :ref-code="refCode"
  />
</template>

<script>
import CompactChatbot from './CompactChatbot.vue'

export default {
  name: 'ChatbotApp',
  components: {
    CompactChatbot
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
