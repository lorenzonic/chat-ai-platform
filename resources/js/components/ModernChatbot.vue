<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50">
    <!-- Header moderno -->
    <header class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg">
      <div class="max-w-4xl mx-auto px-6 py-6">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold">{{ store.name }}</h1>
            <p class="text-emerald-100 text-sm">🤖 AI Assistant - Chiedi tutto quello che vuoi!</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Chat Container moderno -->
    <div class="max-w-4xl mx-auto p-6">
      <div class="glass rounded-2xl shadow-2xl border border-white/20">
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-4 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <div class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></div>
              </div>
              <span class="font-medium">Chat AI</span>
            </div>
            <div class="text-xs opacity-75">Online</div>
          </div>
        </div>

        <!-- Messages Container -->
        <div
          ref="messagesContainer"
          class="h-96 p-6 overflow-y-auto scroll-smooth chatbot-gradient"
        >
          <TransitionGroup name="message" tag="div">
            <div
              v-for="message in messages"
              :key="message.id"
              class="mb-4"
              :class="message.isUser ? 'chatbot-message-user' : 'chatbot-message-ai'"
            >
              <!-- Message AI -->
              <div v-if="!message.isUser" class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-md max-w-xs lg:max-w-md border border-gray-100">
                  <p class="text-gray-800 text-sm leading-relaxed" v-html="formatMessage(message.text)"></p>
                  <div class="text-xs text-gray-400 mt-2">{{ formatTime(message.timestamp) }}</div>
                </div>
              </div>

              <!-- Message User -->
              <div v-else class="flex items-start justify-end space-x-3">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-md max-w-xs lg:max-w-md">
                  <p class="text-sm leading-relaxed" v-html="formatMessage(message.text)"></p>
                  <div class="text-xs text-emerald-100 mt-2">{{ formatTime(message.timestamp) }}</div>
                </div>
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
            </div>
          </TransitionGroup>

          <!-- Loading indicator -->
          <div v-if="isLoading" class="flex items-start space-x-3 mb-4 chatbot-message-ai">
            <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center">
              <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            </div>
            <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-md border border-gray-100">
              <div class="flex items-center space-x-2">
                <span class="text-gray-500 text-sm">AI sta scrivendo</span>
                <div class="typing-dots">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Suggestions -->
        <div v-if="showSuggestions" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
          <p class="text-sm text-gray-600 mb-3 font-medium">💡 Suggerimenti veloci:</p>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="suggestion in suggestions"
              :key="suggestion"
              @click="sendSuggestion(suggestion)"
              class="suggestion-button text-white text-sm px-4 py-2 rounded-full shadow-lg transition-all duration-200"
            >
              {{ suggestion }}
            </button>
          </div>
        </div>

        <!-- Input Area -->
        <div class="p-6 border-t border-gray-100 bg-white rounded-b-2xl">
          <form @submit.prevent="sendMessage" class="flex space-x-3">
            <div class="flex-1 relative">
              <input
                v-model="newMessage"
                :disabled="isLoading"
                ref="messageInput"
                type="text"
                placeholder="Scrivi la tua domanda..."
                class="chat-input w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 disabled:opacity-50"
              >
              <button
                type="button"
                @click="clearChat"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
            <button
              type="submit"
              :disabled="!newMessage.trim() || isLoading || isMessageTooLong"
              class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
            >
              <svg v-if="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
              <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            </button>
          </form>

          <!-- Character counter -->
          <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
            <span>💡 Suggerimento: Sii specifico per risposte più accurate</span>
            <span :class="{ 'text-red-500': isMessageTooLong }">
              {{ characterCount }}/{{ maxMessageLength }}
            </span>
          </div>

          <!-- Character count indicator -->
          <div class="mt-2 text-right">
            <span class="text-xs" :class="{'text-red-500': isMessageTooLong}">
              {{ characterCount }} / {{ maxMessageLength }} caratteri
            </span>
          </div>
        </div>
      </div>

      <!-- Store Info Card moderna -->
      <div class="mt-6 glass rounded-2xl shadow-xl border border-white/20 p-6">
        <div class="flex items-start space-x-4">
          <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ store.name }}</h2>
            <p v-if="store.description" class="text-gray-600 leading-relaxed">{{ store.description }}</p>
            <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
              <span class="flex items-center space-x-1">
                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                <span>Online</span>
              </span>
              <span>Risposta istantanea</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer informativo -->
      <div class="mt-6 text-center space-y-2">
        <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
          <span class="flex items-center space-x-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Powered by AI</span>
          </span>
          <span class="flex items-center space-x-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
            </svg>
            <span>Sempre disponibile</span>
          </span>
        </div>
        <p class="text-xs text-gray-400">
          💡 Questo chatbot è alimentato da intelligenza artificiale e può fornire informazioni generali.
          Per richieste specifiche, contatta direttamente il negozio.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick, computed } from 'vue'

export default {
  name: 'ModernChatbot',
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
  },
  setup(props) {
    const messages = ref([])
    const newMessage = ref('')
    const isLoading = ref(false)
    const messagesContainer = ref(null)
    const messageInput = ref(null)
    const sessionId = ref('')
    const showSuggestions = ref(true)
    const maxMessageLength = ref(500)

    const suggestions = [
      '🌱 Che piante consigli?',
      '🕒 Orari di apertura?',
      '💧 Come curarle?',
      '🌿 Piante per appartamento?',
      '☀️ Piante per giardino?'
    ]

    // Character count for input
    const characterCount = computed(() => newMessage.value.length)
    const isMessageTooLong = computed(() => characterCount.value > maxMessageLength.value)

    // Genera session ID
    const generateSessionId = () => {
      return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now()
    }

    // Formatta timestamp
    const formatTime = (timestamp) => {
      return new Date(timestamp).toLocaleTimeString('it-IT', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    // Scroll to bottom
    const scrollToBottom = () => {
      nextTick(() => {
        if (messagesContainer.value) {
          messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
      })
    }

    // Add message
    const addMessage = (text, isUser = false) => {
      const message = {
        id: Date.now() + Math.random(),
        text,
        isUser,
        timestamp: Date.now()
      }
      messages.value.push(message)
      scrollToBottom()
      saveChatHistory() // Auto-save chat history

      // Play notification sound for AI messages
      if (!isUser) {
        playNotificationSound()
      }

      return message
    }

    // Format markdown-like text
    const formatMessage = (text) => {
      return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
        .replace(/\*(.*?)\*/g, '<em>$1</em>') // Italic
        .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-xs">$1</code>') // Code
        .replace(/\n/g, '<br>') // Line breaks
    }

    // Generate welcome message
    const getWelcomeMessage = () => {
      const messages = [
        `🌱 Ciao! Sono l'assistente AI di ${props.store.name}. Come posso aiutarti oggi?`,
        `👋 Benvenuto da ${props.store.name}! Sono qui per rispondere alle tue domande.`,
        `🤖 Salve! Sono l'AI di ${props.store.name}. In cosa posso esserti utile?`,
        `✨ Ciao! Sono qui per aiutarti con tutto quello che riguarda ${props.store.name}!`
      ]
      return messages[Math.floor(Math.random() * messages.length)]
    }

    // Play notification sound
    const playNotificationSound = () => {
      try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEeFTOH0fPTgjMGEW7A7+OZRQ0PVqzn77BdGAg+ltryxHkpBSl+zPLZizcIGmW57+OaRAoMUKXh8LpuJAU2jdXzzn0vBSF1xe/eizEHElyx5+mjUhELTKDf87phHhU0hdDz04IzBhJwwO/hmEQODlOq5O+zYBoGPJPY88p9KwUme8rx2Ys2CRllu+3kmlAJC1Gn4fG9byMGPI7V8tGALgYeg8vw24s2CRdks+3kn08MDVSr5/C9cCUHN47U8tKCMwcSbcDv4ZlGDgxRpuPwu28kBjiO1fDPfywGJHfG8N2QQAoTXrTp66hVFAlFnt/zu2EdFDCG0fLSgzQHEW/A7eCZRg4OUarm7rJcFQk8kdXy0oEzBxFs5/nkwEHF')
        audio.volume = 0.1
        audio.play().catch(() => {}) // Ignore errors if audio fails
      } catch (error) {
        // Ignore audio errors
      }
    }

    // Save chat history to localStorage
    const saveChatHistory = () => {
      const chatHistory = {
        messages: messages.value,
        timestamp: Date.now()
      }
      localStorage.setItem('chat_history_' + props.store.slug, JSON.stringify(chatHistory))
    }

    // Load chat history from localStorage
    const loadChatHistory = () => {
      try {
        const saved = localStorage.getItem('chat_history_' + props.store.slug)
        if (saved) {
          const chatHistory = JSON.parse(saved)
          // Only load if less than 24 hours old
          if (Date.now() - chatHistory.timestamp < 24 * 60 * 60 * 1000) {
            messages.value = chatHistory.messages || []
            return true
          }
        }
      } catch (error) {
        console.log('Error loading chat history:', error)
      }
      return false
    }

    // Send message
    const sendMessage = async () => {
      if (!newMessage.value.trim() || isLoading.value) return

      const messageText = newMessage.value.trim()
      addMessage(messageText, true)
      newMessage.value = ''
      isLoading.value = true
      showSuggestions.value = false

      try {
        const response = await fetch(`/api/chatbot/${props.store.slug}/message`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({
            message: messageText,
            session_id: sessionId.value,
            ref: props.refCode
          })
        })

        const data = await response.json()

        if (data.success) {
          addMessage(data.response, false)
          playNotificationSound() // Play sound on new response
          if (data.session_id) {
            sessionId.value = data.session_id
            localStorage.setItem('chatbot_session_' + props.store.slug, sessionId.value)
          }
        } else {
          addMessage(data.error || 'Si è verificato un errore. Riprova tra poco.', false)
        }
      } catch (error) {
        console.error('Chat error:', error)
        addMessage('Errore di connessione. Controlla la connessione internet e riprova.', false)
      } finally {
        isLoading.value = false
      }
    }

    // Send suggestion
    const sendSuggestion = (suggestion) => {
      newMessage.value = suggestion
      sendMessage()
    }

    // Clear chat
    const clearChat = () => {
      messages.value = []
      showSuggestions.value = true
      addMessage(getWelcomeMessage(), false)
    }

    // Track QR scan
    const trackQrScan = async (refCode) => {
      try {
        await fetch(`/api/chatbot/${props.store.slug}/track-scan`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ ref: refCode })
        })
      } catch (error) {
        console.error('QR tracking error:', error)
      }
    }

    // Initialize
    onMounted(() => {
      // Get or create session
      sessionId.value = localStorage.getItem('chatbot_session_' + props.store.slug) || generateSessionId()
      localStorage.setItem('chatbot_session_' + props.store.slug, sessionId.value)

      // Try to load chat history, if not found add welcome message
      if (!loadChatHistory()) {
        addMessage(getWelcomeMessage(), false)
      }

      // Check for pre-filled question from props
      if (props.prefilledQuestion) {
        newMessage.value = props.prefilledQuestion
        if (props.refCode) {
          trackQrScan(props.refCode)
        }
      }

      // Focus input
      nextTick(() => {
        if (messageInput.value) {
          messageInput.value.focus()
        }
      })
    })

    return {
      messages,
      newMessage,
      isLoading,
      messagesContainer,
      messageInput,
      showSuggestions,
      suggestions,
      maxMessageLength,
      characterCount,
      isMessageTooLong,
      sendMessage,
      sendSuggestion,
      clearChat,
      formatTime,
      formatMessage,
      saveChatHistory,
      loadChatHistory
    }
  }
}
</script>

<style>
.message-enter-active {
  transition: all 0.3s ease;
}
.message-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #10b981, #14b8a6);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #059669, #0f766e);
}
</style>
