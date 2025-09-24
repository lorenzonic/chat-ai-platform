<template>
  <div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100"
    :style="backgroundGradient"
  >
    <!-- Header moderno personalizzato con blur effect -->
    <header
      class="text-white shadow-xl backdrop-blur-md border-b border-white/20"
      :style="headerStyle"
    >
      <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="flex items-center space-x-4">
          <div
            class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center shadow-lg backdrop-blur-sm"
            :style="avatarStyle"
          >
            <img
              v-if="store.chat_avatar_image"
              :src="store.chat_avatar_image"
              :alt="store.name"
              class="w-12 h-12 rounded-xl object-cover"
            />
            <svg v-else class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold tracking-tight" :style="fontStyle">{{ store.name }}</h1>
            <p class="text-white/90 text-base mt-1" :style="fontStyle">
             {{ store.assistant_name || 'AI Assistant' }} • {{ welcomeSubtitle }}
            </p>
          </div>
        </div>
      </div>
    </header>

    <!-- Chat Container moderno con glass morphism -->
    <div class="max-w-4xl mx-auto p-6 -mt-4 relative z-10">
      <div class="glass-modern rounded-3xl shadow-2xl border border-white/30 overflow-hidden">
        <!-- Chat Header personalizzato completamente arrotondato -->
        <div
          class="text-white p-5 relative overflow-hidden rounded-t-3xl"
          :style="chatHeaderStyle"
        >
          <!-- Background decoration -->
          <div class="absolute inset-0 bg-gradient-to-r from-black/10 to-transparent"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div
                class="w-10 h-10 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm shadow-lg"
                :style="onlineIndicatorStyle"
              >
                <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-lg"></div>
              </div>
              <div>
                <span class="font-semibold text-lg" :style="fontStyle">{{ store.assistant_name || 'Chat AI' }}</span>
                <div class="text-sm text-white/80" :style="fontStyle">Sempre pronto ad aiutarti</div>
              </div>
            </div>
            <div class="text-xs opacity-90 bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm" :style="fontStyle">Online</div>
          </div>
        </div>

        <!-- Messages Container con altezza massimizzata -->
        <div
          ref="messagesContainer"
          class="h-[600px] p-6 overflow-y-auto scroll-smooth bg-gradient-to-b from-white/50 to-blue-50/30"
        >
          <TransitionGroup name="message" tag="div">
            <div
              v-for="message in messages"
              :key="message.id"
              class="mb-4"
              :class="message.isUser ? 'chatbot-message-user' : 'chatbot-message-ai'"
            >
              <!-- Message AI con design migliorato -->
              <div v-if="!message.isUser" class="flex items-start space-x-4">
                <div
                  class="w-10 h-10 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg"
                  :style="aiAvatarStyle"
                >
                  <img
                    v-if="store.chat_avatar_image"
                    :src="store.chat_avatar_image"
                    :alt="store.assistant_name"
                    class="w-8 h-8 rounded-2xl object-cover"
                  />
                  <svg v-else class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div style="border-radius: 20px;"
                  class="bg-white/90 backdrop-blur-sm rounded-3xl px-6 py-4 shadow-xl max-w-xs lg:max-w-md border border-white/50 hover:shadow-2xl transition-all duration-300"
                  :style="aiMessageStyle"
                >
                  <p class="text-gray-800 text-sm leading-relaxed" :style="fontStyle" v-html="formatMessage(message.text)"></p>
                  <div class="text-xs text-gray-500 mt-3 flex items-center" :style="fontStyle">
                    <svg class="w-3 h-3 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ formatTime(message.timestamp) }}
                  </div>
                </div>
              </div>

              <div v-else class="flex items-start justify-end space-x-4">
                <div
                  class="text-white rounded-3xl px-6 py-4 shadow-xl max-w-xs lg:max-w-md backdrop-blur-sm border border-white/20 hover:shadow-2xl transition-all duration-300"
                  :style="userMessageStyle"
                >
                  <p class="text-sm leading-relaxed" :style="fontStyle" v-html="formatMessage(message.text)"></p>
                  <div class="text-xs text-white/90 mt-3 flex items-center justify-end" :style="fontStyle">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ formatTime(message.timestamp) }}
                  </div>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
            </div>
          </TransitionGroup>

          <!-- Loading indicator completamente arrotondato -->
          <div v-if="isLoading" class="flex items-start space-x-4 mb-6 chatbot-message-ai">
            <div
              class="w-10 h-10 rounded-3xl flex items-center justify-center shadow-lg"
              :style="aiAvatarStyle"
            >
              <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            </div>
            <div
              class="bg-white/90 backdrop-blur-sm rounded-3xl px-6 py-4 shadow-xl border border-white/50"
              :style="aiMessageStyle"
            >
              <div class="flex items-center space-x-3">
                <span class="text-gray-600 text-sm font-medium" :style="fontStyle">{{ store.assistant_name || 'AI' }} sta pensando</span>
                <div class="typing-dots-modern">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Suggestions compatti -->
        <div v-if="showSuggestions" class="px-6 py-3 bg-gradient-to-r from-gray-50/80 to-blue-50/80 backdrop-blur-sm border-t border-white/30">
          <!-- Smart Suggestions (NLP-powered) compatti -->
          <div v-if="smartSuggestions.length > 0" class="mb-3">
            <div class="flex items-center mb-2">
              <div class="w-5 h-5 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mr-2 shadow-sm">
                <span class="text-white text-xs">🧠</span>
              </div>
              <p class="text-sm font-medium text-gray-700" :style="{ color: primaryColor, ...fontStyle }">
                Suggerimenti intelligenti
              </p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="suggestion in smartSuggestions"
                :key="suggestion"
                @click="sendSuggestion(suggestion)"
                class="px-3 py-2 rounded-3xl text-xs font-medium transition-all duration-200 shadow-sm hover:shadow-md border border-white/50 backdrop-blur-sm hover:scale-105"
                :style="smartSuggestionStyleCompact"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>

          <!-- Custom Store Suggestions compatti -->
          <div v-if="customStoreSuggestions.length > 0">
            <div class="flex items-center mb-2">
              <div class="w-5 h-5 rounded-2xl bg-gradient-to-r from-green-500 to-teal-500 flex items-center justify-center mr-2 shadow-sm">
                <span class="text-white text-xs">⭐</span>
              </div>
              <p class="text-sm font-medium text-gray-700" :style="fontStyle">{{ store.name }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="suggestion in customStoreSuggestions"
                :key="suggestion"
                @click="sendSuggestion(suggestion)"
                class="px-3 py-2 rounded-3xl text-xs font-medium transition-all duration-200 shadow-sm hover:shadow-md border border-white/50 backdrop-blur-sm hover:scale-105"
                :style="customSuggestionStyleCompact"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>



          <!-- NLP Info (dev only) -->
          <div v-if="lastNlpData && showNlpInfo" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs text-yellow-800 font-medium mb-2">🔍 Analisi NLP ultima domanda:</p>
            <div class="text-xs text-yellow-700 space-y-1">
              <div><strong>Intent:</strong> {{ lastNlpData.intent }} ({{ lastNlpData.intent_confidence || 0 }})</div>
              <div v-if="lastNlpData.sentiment"><strong>Sentiment:</strong> {{ lastNlpData.sentiment.sentiment }} ({{ lastNlpData.sentiment.confidence }})</div>
              <div v-if="lastNlpData.keywords.length"><strong>Keywords:</strong> {{ lastNlpData.keywords.join(', ') }}</div>
              <div v-if="lastNlpData.entities && lastNlpData.entities.length"><strong>Entità:</strong> {{ formatEntities(lastNlpData.entities) }}</div>
              <div><strong>Fonte:</strong> {{ lastNlpData.source }}</div>
            </div>
            <button
              @click="showNlpInfo = false"
              class="mt-2 text-xs text-yellow-600 hover:text-yellow-800"
            >
              Nascondi dettagli
            </button>
          </div>

          <!-- Debug toggle (only in development) -->
          <div v-if="isDevelopment" class="mt-2">
            <button
              @click="showNlpInfo = !showNlpInfo"
              class="text-xs text-gray-500 hover:text-gray-700 underline"
            >
              {{ showNlpInfo ? 'Nascondi' : 'Mostra' }} info NLP
            </button>
          </div>
        </div>

        <!-- Input Area ridisegnato e completamente arrotondato -->
        <div class="p-4 bg-gradient-to-r from-white/90 to-blue-50/90 backdrop-blur-sm border-t border-white/30 rounded-b-3xl">
          <form @submit.prevent="sendMessage" class="flex space-x-4">
            <div class="flex-1 relative">
              <!-- Input principale elegante -->
              <input
                v-model="newMessage"
                :disabled="isLoading"
                ref="messageInput"
                type="text"
                :placeholder="`Scrivi a ${store.assistant_name || 'AI'}...`"
                class="w-full px-6 py-4 pr-20 border-2 border-gray-200/50 rounded-3xl focus:outline-none focus:ring-4 focus:ring-blue-200/50 focus:border-blue-400 transition-all duration-300 disabled:opacity-50 bg-white/90 backdrop-blur-sm shadow-lg placeholder-gray-400"
                :style="{ fontFamily: fontFamily }"
              />
              <!-- Bottone clear -->
              <button
                type="button"
                @click="clearChat"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100 z-10"
                title="Pulisci chat"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
              <!-- Indicatore di focus elegante -->
              <div class="absolute inset-0 rounded-3xl bg-gradient-to-r opacity-0 focus-within:opacity-10 transition-opacity duration-300 pointer-events-none" :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }"></div>
            </div>
            <button
              type="submit"
              :disabled="!newMessage.trim() || isLoading || isMessageTooLong"
              class="text-white px-6 py-4 rounded-3xl font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-xl hover:shadow-2xl hover:scale-110 flex items-center justify-center min-w-[60px] backdrop-blur-sm"
              :style="sendButtonStyle"
            >
              <svg v-if="!isLoading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
              <div v-else class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            </button>
          </form>

          <!-- Character counter compatto -->
          <div class="flex justify-between items-center mt-2 px-1">
            <div class="flex items-center space-x-1 text-xs">
              <div class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></div>
              <span class="text-gray-500" :style="fontStyle">💡 Sii specifico</span>
            </div>
            <span
              :class="{ 'text-red-500 font-semibold': isMessageTooLong, 'text-gray-400': !isMessageTooLong }"
              class="text-xs px-2 py-0.5 rounded-lg bg-white/50 backdrop-blur-sm border border-gray-200/30"
              :style="fontStyle"
            >
              {{ characterCount }}/{{ maxMessageLength }}
            </span>
          </div>
        </div>
      </div>

      <!-- Store Info Card personalizzata -->
      <div class="mt-6 glass rounded-2xl shadow-xl border border-white/20 p-6">
        <div class="flex items-start space-x-4">
          <!-- Avatar personalizzato dello store -->
          <div v-if="store.chat_avatar_image" class="w-16 h-16 rounded-2xl overflow-hidden border-2 flex-shrink-0" :style="{ borderColor: primaryColor }">
            <img :src="store.chat_avatar_image" :alt="store.name" class="w-full h-full object-cover" />
          </div>
          <div v-else class="w-16 h-16 rounded-2xl flex items-center justify-center" :style="storeAvatarStyle">
            <span class="text-2xl font-bold text-white">{{ getBusinessIcon() }}</span>
          </div>
          <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900 mb-2" :style="fontStyle">{{ store.name }}</h2>
            <p v-if="store.description" class="text-gray-600 leading-relaxed mb-3" :style="fontStyle">{{ store.description }}</p>
            <div class="mb-3">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :style="assistantBadgeStyle">
                <span class="mr-1">🤖</span>
                {{ assistantName }}
              </span>
            </div>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
              <span class="flex items-center space-x-1" :style="fontStyle">
                <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: primaryColor }"></div>
                <span>Online</span>
              </span>
              <span :style="fontStyle">Risposta istantanea</span>
              <span v-if="store.chat_ai_tone" :style="fontStyle">Tono {{ store.chat_ai_tone }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer informativo personalizzato -->
      <div class="mt-6 text-center space-y-2">
        <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">

          <span class="flex items-center space-x-1" :style="fontStyle">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
            </svg>
            <span>Sempre disponibile</span>
          </span>
          <span v-if="store.assistant_name" class="flex items-center space-x-1" :style="{ color: primaryColor, ...fontStyle }">
        <span>{{ assistantName }}</span>
          </span>
        </div>
        <p class="text-xs text-gray-400" :style="fontStyle">
          💡 Questo chatbot è alimentato da intelligenza artificiale e può fornire informazioni su {{ store.name }}.
          {{ assistantName }} è qui per aiutarti!
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

    // === NUOVO: VARIABILI NLP ===
    const smartSuggestions = ref([])
    const lastNlpData = ref(null)
    const showNlpInfo = ref(false)
    const isDevelopment = ref(process.env.NODE_ENV === 'development' || true) // Temporaneamente sempre abilitato per test

    // === PERSONALIZZAZIONE STORE ===
    const primaryColor = computed(() => props.store.chat_theme_color || '#10b981')
    const fontFamily = computed(() => props.store.chat_font_family || 'Inter')

    // Colore secondario per gradienti
    const secondaryColor = computed(() => {
      // Generiamo un colore complementare basato sul colore primario
      return adjustColor(primaryColor.value, 30) // Più chiaro del primario
    })

    // Computed styles per personalizzazione
    const fontStyle = computed(() => ({
      fontFamily: fontFamily.value
    }))

    const backgroundGradient = computed(() => {
      const color = primaryColor.value
      const lightColor = adjustColor(color, 40)
      const veryLightColor = adjustColor(color, 70)
      return `background: linear-gradient(135deg, ${veryLightColor}10, ${lightColor}20)`
    })

    const headerStyle = computed(() => {
      const color = primaryColor.value
      const darkColor = adjustColor(color, -20)
      return {
        background: `linear-gradient(135deg, ${color}, ${darkColor})`,
        fontFamily: fontFamily.value
      }
    })

    const chatHeaderStyle = computed(() => {
      const color = primaryColor.value
      const darkColor = adjustColor(color, -10)
      return {
        background: `linear-gradient(135deg, ${color}, ${darkColor})`,
        fontFamily: fontFamily.value
      }
    })

    const aiAvatarStyle = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}, ${adjustColor(primaryColor.value, -15)})`
    }))

    const avatarStyle = computed(() => ({
      backgroundColor: 'rgba(255, 255, 255, 0.2)'
    }))

    const onlineIndicatorStyle = computed(() => ({
      backgroundColor: 'rgba(255, 255, 255, 0.2)'
    }))

    const aiMessageStyle = computed(() => ({
      borderColor: `${primaryColor.value}20`,
      fontFamily: fontFamily.value
    }))

    const userMessageStyle = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}, ${adjustColor(primaryColor.value, -15)})`,
      fontFamily: fontFamily.value
    }))

    const smartSuggestionStyle = computed(() => ({
      backgroundColor: `${primaryColor.value}15`,
      color: adjustColor(primaryColor.value, -30),
      borderColor: `${primaryColor.value}30`,
      fontFamily: fontFamily.value
    }))

    const customSuggestionStyle = computed(() => ({
      backgroundColor: `${primaryColor.value}25`,
      color: adjustColor(primaryColor.value, -40),
      fontFamily: fontFamily.value
    }))

    // Stili moderni per i suggerimenti
    const smartSuggestionStyleModern = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}10, ${secondaryColor.value}10)`,
      color: adjustColor(primaryColor.value, -40),
      borderColor: `${primaryColor.value}20`,
      fontFamily: fontFamily.value,
      transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
    }))

    const customSuggestionStyleModern = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}15, ${secondaryColor.value}15)`,
      color: adjustColor(primaryColor.value, -50),
      borderColor: `${primaryColor.value}25`,
      fontFamily: fontFamily.value,
      transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
    }))

    // Stili compatti per i suggerimenti
    const smartSuggestionStyleCompact = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}12, ${secondaryColor.value}12)`,
      color: adjustColor(primaryColor.value, -35),
      borderColor: `${primaryColor.value}25`,
      fontFamily: fontFamily.value,
      fontSize: '11px',
      lineHeight: '1.3'
    }))

    const customSuggestionStyleCompact = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}18, ${secondaryColor.value}18)`,
      color: adjustColor(primaryColor.value, -45),
      borderColor: `${primaryColor.value}30`,
      fontFamily: fontFamily.value,
      fontSize: '11px',
      lineHeight: '1.3'
    }))

    const sendButtonStyle = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}, ${adjustColor(primaryColor.value, -15)})`,
      fontFamily: fontFamily.value,
      ':hover': {
        background: `linear-gradient(135deg, ${adjustColor(primaryColor.value, -10)}, ${adjustColor(primaryColor.value, -25)})`
      }
    }))

    const storeAvatarStyle = computed(() => ({
      background: `linear-gradient(135deg, ${primaryColor.value}, ${adjustColor(primaryColor.value, -15)})`
    }))

    const assistantBadgeStyle = computed(() => ({
      backgroundColor: `${primaryColor.value}15`,
      color: adjustColor(primaryColor.value, -40),
      fontFamily: fontFamily.value
    }))

    // Funzione helper per modificare i colori
    const adjustColor = (color, amount) => {
      const usePound = color[0] === '#'
      const col = usePound ? color.slice(1) : color
      const num = parseInt(col, 16)

      let r = (num >> 16) + amount
      let g = (num >> 8 & 0x00FF) + amount
      let b = (num & 0x0000FF) + amount

      r = r > 255 ? 255 : r < 0 ? 0 : r
      g = g > 255 ? 255 : g < 0 ? 0 : g
      b = b > 255 ? 255 : b < 0 ? 0 : b

      return (usePound ? '#' : '') + (g | (b << 8) | (r << 16)).toString(16).padStart(6, '0')
    }

    // Suggestions personalizzate per lo store
    const customStoreSuggestions = computed(() => {
      if (props.store.chat_suggestions) {
        try {
          return Array.isArray(props.store.chat_suggestions)
            ? props.store.chat_suggestions
            : JSON.parse(props.store.chat_suggestions)
        } catch (e) {
          return []
        }
      }
      return []
    })

    // Default suggestions personalizzate per tipo di business
    const defaultSuggestions = computed(() => {
      const businessType = detectBusinessType()
      const baseIcon = getBusinessIcon()

      switch (businessType) {
        case 'garden_center':
          return [
            `${baseIcon} Che piante consigli?`,
            '🕒 Orari di apertura?',
            '💧 Come curarle?',
            '🌿 Piante per appartamento?',
            '☀️ Piante per giardino?'
          ]
        case 'flower_shop':
          return [
            `${baseIcon} Bouquet disponibili?`,
            '💐 Fiori per matrimonio?',
            '🎁 Composizioni regalo?',
            '🕒 Orari di apertura?',
            '🚚 Consegne a domicilio?'
          ]
        default:
          return [
            `${baseIcon} Prodotti disponibili?`,
            '🕒 Orari di apertura?',
            'ℹ️ Informazioni negozio?',
            '📞 Come contattarvi?',
            '📍 Dove siete?'
          ]
      }
    })

    // Rileva il tipo di business dalla descrizione/nome
    const detectBusinessType = () => {
      const text = `${props.store.name} ${props.store.description || ''}`.toLowerCase()

      // Parole chiave per garden center
      const gardenKeywords = ['garden', 'piante', 'vivaio', 'giardinaggio', 'verde', 'botanica', 'serra', 'coltivazione']
      // Parole chiave per flower shop
      const flowerKeywords = ['fiori', 'flower', 'fiorista', 'bouquet', 'rose', 'matrimonio', 'composizioni']

      if (gardenKeywords.some(keyword => text.includes(keyword))) {
        return 'garden_center'
      } else if (flowerKeywords.some(keyword => text.includes(keyword))) {
        return 'flower_shop'
      }

      return 'general'
    }

    // Icona basata sul business
    const getBusinessIcon = () => {
      const businessType = detectBusinessType()
      switch (businessType) {
        case 'garden_center':
          return '🌱'
        case 'flower_shop':
          return '💐'
        default:
          return '🏪'
      }
    }

    // Colore accent basato sul business type
    const getBusinessAccentColor = () => {
      const businessType = detectBusinessType()
      const baseColor = primaryColor.value

      switch (businessType) {
        case 'garden_center':
          return '#22c55e' // Verde natura
        case 'flower_shop':
          return '#f472b6' // Rosa fiori
        default:
          return baseColor
      }
    }

    // Sottotitolo di benvenuto personalizzato
    const welcomeSubtitle = computed(() => {
      const businessType = detectBusinessType()
      const assistantName = props.store.assistant_name || 'AI Assistant'

      switch (businessType) {
        case 'garden_center':
          return 'Il tuo esperto di piante e giardinaggio!'
        case 'flower_shop':
          return 'Creiamo bouquet perfetti per te!'
        default:
          return 'Chiedi tutto quello che vuoi!'
      }
    })

    // Iniziale dell'assistente per l'avatar
    const assistantInitial = computed(() => {
      const name = props.store.assistant_name || 'AI'
      return name.charAt(0).toUpperCase()
    })

    // Nome dell'assistente per visualizzazione
    const assistantName = computed(() => {
      return props.store.assistant_name || 'Assistente AI'
    })

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

    // Generate welcome message personalizzato
    const getWelcomeMessage = () => {
      const assistantName = props.store.assistant_name || 'Assistente AI'
      const storeName = props.store.name
      const businessType = detectBusinessType()

      let messages = []

      switch (businessType) {
        case 'garden_center':
          messages = [
            `🌱 Ciao! Sono ${assistantName} di ${storeName}. Posso aiutarti con piante, cura e giardinaggio!`,
            `👋 Benvenuto da ${storeName}! Sono qui per rispondere a tutte le tue domande sulle piante.`,
            `🌿 Salve! Sono ${assistantName}, il tuo esperto di piante. Come posso aiutarti oggi?`,
            `✨ Ciao! Da ${storeName} trovi tutto per il tuo verde. Cosa ti serve?`
          ]
          break
        case 'flower_shop':
          messages = [
            `💐 Ciao! Sono ${assistantName} di ${storeName}. Creiamo bouquet perfetti per ogni occasione!`,
            `🌸 Benvenuto da ${storeName}! Posso aiutarti a scegliere i fiori più belli.`,
            `🌹 Salve! Sono ${assistantName}, il tuo consulente floreale. Come posso aiutarti?`,
            `✨ Ciao! Da ${storeName} rendiamo speciali i tuoi momenti con i fiori giusti.`
          ]
          break
        default:
          messages = [
            `👋 Ciao! Sono ${assistantName} di ${storeName}. Come posso aiutarti oggi?`,
            `🤖 Benvenuto da ${storeName}! Sono qui per rispondere alle tue domande.`,
            `✨ Salve! Sono ${assistantName}. In cosa posso esserti utile?`,
            `💬 Ciao! Da ${storeName} siamo qui per aiutarti. Cosa ti serve?`
          ]
      }

      return messages[Math.floor(Math.random() * messages.length)]
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

          // === NUOVO: GESTIONE DATI NLP AVANZATA ===
          if (data.nlp) {
            lastNlpData.value = data.nlp

            // Aggiorna suggerimenti intelligenti se disponibili
            if (data.nlp.suggestions && data.nlp.suggestions.length > 0) {
              smartSuggestions.value = data.nlp.suggestions
              showSuggestions.value = true
            }

            // Aggiungi suggerimenti basati sul sentiment
            if (data.nlp.sentiment) {
              const sentimentSuggestions = handleSentimentBasedSuggestions(data.nlp.sentiment)
              if (sentimentSuggestions.length > 0) {
                smartSuggestions.value = [...smartSuggestions.value, ...sentimentSuggestions].slice(0, 6)
              }
            }

            // Log delle informazioni NLP (solo in dev)
            if (isDevelopment.value) {
              console.log('🧠 Advanced NLP Analysis:', {
                intent: data.nlp.intent,
                intent_confidence: data.nlp.intent_confidence,
                sentiment: data.nlp.sentiment,
                keywords: data.nlp.keywords,
                entities: data.nlp.entities,
                source: data.nlp.source
              })
            }
          }

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

    // Format entities for display
    const formatEntities = (entities) => {
      if (!entities || entities.length === 0) return 'Nessuna'
      return entities.map(ent => `${ent.text} (${ent.label})`).join(', ')
    }

    // Enhanced suggestion handling based on sentiment
    const handleSentimentBasedSuggestions = (sentiment) => {
      if (!sentiment) return []

      let extraSuggestions = []

      if (sentiment.sentiment === 'negative') {
        extraSuggestions = [
          '🆘 Hai un problema urgente?',
          '📞 Vuoi parlare con un esperto?',
          '🔍 Cerchiamo una soluzione rapida'
        ]
      } else if (sentiment.sentiment === 'positive') {
        extraSuggestions = [
          '🌱 Vuoi espandere la tua collezione?',
          '🎁 Idee regalo per amanti delle piante?',
          '📚 Consigli per diventare un esperto?'
        ]
      }

      return extraSuggestions
    }

    // Auto-suggest based on typing patterns
    const handleTypingSuggestions = (text) => {
      const typingSuggestions = []
      const textLower = text.toLowerCase()

      // Suggest completions for common queries
      if (textLower.includes('come') && textLower.length > 5) {
        typingSuggestions.push('Come si cura?', 'Come si annaffia?', 'Come si pota?')
      } else if (textLower.includes('problemi') && textLower.length > 8) {
        typingSuggestions.push('Problemi con le foglie', 'Problemi di crescita', 'Problemi di parassiti')
      } else if (textLower.includes('quale') && textLower.length > 5) {
        typingSuggestions.push('Quale pianta scegliere?', 'Quale concime usare?', 'Quale posizione?')
      }

      return typingSuggestions.slice(0, 3)
    }

    // Clear chat
    const clearChat = () => {
      messages.value = []
      showSuggestions.value = true
      smartSuggestions.value = []
      lastNlpData.value = null
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

      // Always start with welcome message
      addMessage(getWelcomeMessage(), false)

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
      maxMessageLength,
      characterCount,
      isMessageTooLong,
      sendMessage,
      sendSuggestion,
      clearChat,
      formatTime,
      formatMessage,
      // === VARIABILI E FUNZIONI NLP ===
      smartSuggestions,
      lastNlpData,
      showNlpInfo,
      isDevelopment,
      formatEntities,
      handleSentimentBasedSuggestions,
      handleTypingSuggestions,
      // === PERSONALIZZAZIONE STORE ===
      primaryColor,
      secondaryColor,
      fontFamily,
      fontStyle,
      backgroundGradient,
      headerStyle,
      chatHeaderStyle,
      aiAvatarStyle,
      avatarStyle,
      onlineIndicatorStyle,
      aiMessageStyle,
      userMessageStyle,
      smartSuggestionStyle,
      smartSuggestionStyleModern,
      smartSuggestionStyleCompact,
      customSuggestionStyle,
      customSuggestionStyleModern,
      customSuggestionStyleCompact,
      sendButtonStyle,
      storeAvatarStyle,
      assistantBadgeStyle,
      customStoreSuggestions,
      defaultSuggestions,
      welcomeSubtitle,
      assistantInitial,
      assistantName,
      detectBusinessType,
      getBusinessIcon,
      getBusinessAccentColor,
      adjustColor
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

/* Glass effect modernizzato */
.glass-modern {
  backdrop-filter: blur(20px);
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
}

/* Glass effect normale */
.glass {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95);
}

/* Input moderno con focus effect arrotondato */
.chat-input:focus {
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
  border-color: #3b82f6;
  transform: scale(1.02);
}

/* Typing dots animation moderna */
.typing-dots-modern {
  display: inline-flex;
  align-items: center;
  gap: 3px;
}

.typing-dots-modern span {
  height: 6px;
  width: 6px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 50%;
  animation: typingModern 1.6s infinite ease-in-out;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.typing-dots-modern span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots-modern span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typingModern {
  0%, 80%, 100% {
    transform: scale(0.8);
    opacity: 0.6;
  }
  40% {
    transform: scale(1.2);
    opacity: 1;
  }
}

/* Typing dots animation originale */
.typing-dots {
  display: inline-flex;
  align-items: center;
  gap: 2px;
}

.typing-dots span {
  height: 4px;
  width: 4px;
  background-color: #9ca3af;
  border-radius: 50%;
  animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
  0%, 80%, 100% {
    transform: scale(0);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Custom scrollbar moderno */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: linear-gradient(135deg, #f8fafc, #e2e8f0);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #2563eb, #7c3aed);
  transform: scale(1.1);
}

/* Hover effects for suggestions modernizzati */
button:hover {
  transform: translateY(-2px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced animations for messages */
.chatbot-message-ai,
.chatbot-message-user {
  animation: slideInMessage 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes slideInMessage {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Pulse animation for AI thinking modernizzato */
@keyframes pulseModern {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.05);
  }
}

.animate-pulse {
  animation: pulseModern 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Glow effects */
.glow-on-hover:hover {
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
  transform: translateY(-2px);
}

/* Gradient text effect */
.gradient-text {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Responsive design */
@media (max-width: 768px) {
  .max-w-xs {
    max-width: 280px;
  }

  .lg\:max-w-md {
    max-width: 320px;
  }

  .max-w-4xl {
    max-width: 100%;
    margin: 0 1rem;
  }
}
</style>
