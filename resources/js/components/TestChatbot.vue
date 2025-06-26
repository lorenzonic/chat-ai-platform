<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50 p-6">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-emerald-600 mb-4">Test Vue Component</h1>
      <div class="bg-white rounded-lg shadow-lg p-6">
        <p class="text-gray-800">Se vedi questo, Vue sta funzionando!</p>
        <p class="mt-2 text-sm text-gray-600">Store: {{ store.name }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TestChatbot',
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
