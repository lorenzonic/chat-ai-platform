<template>
  <div class="min-h-screen bg-white"
       :style="{
         fontFamily: `'${store.chat_font_family || 'Inter'}', sans-serif`
       }">
    <!-- Header migliorato -->
    <header class="shadow-lg" :style="{
      background: `linear-gradient(135deg, ${store.chat_theme_color}, ${adjustColor(store.chat_theme_color, -20)})`,
      borderRadius: '0 0 24px 24px'
    }">
      <div class="max-w-2xl mx-auto px-6 py-4">
        <div class="flex items-center space-x-4">
          <div v-if="store.chat_avatar_image" class="w-10 h-10 overflow-hidden ring-2 ring-white/30" style="border-radius: 50%;">
            <img :src="store.chat_avatar_image" :alt="store.assistant_name" class="w-full h-full object-cover">
          </div>
          <div v-else class="w-10 h-10 bg-white/20 flex items-center justify-center ring-2 ring-white/30" style="border-radius: 50%;">
            <div class="w-2.5 h-2.5 bg-white animate-pulse" style="border-radius: 50%;"></div>
          </div>
          <div>
            <h1 class="text-white font-semibold text-xl">{{ store.name }}</h1>
            <p class="text-white/90 text-sm">{{ store.assistant_name }} - Online ora</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Chat Container migliorato -->
    <div class="max-w-2xl mx-auto p-6">
      <div class="bg-white shadow-xl border border-gray-100 overflow-hidden chatbot-container" style="border-radius: 24px;">

        <!-- Messages Container migliorato -->
        <div
          ref="messagesContainer"
          class="h-96 p-6 overflow-y-auto scroll-smooth bg-gray-50/30 messages-container"
          style="border-radius: 16px;"
        >
          <TransitionGroup name="message" tag="div">
            <div
              v-for="message in messages"
              :key="message.id"
              class="mb-3 "
              :class="message.isUser ? 'chatbot-message-user' : 'chatbot-message-ai'"
            >
              <!-- Message AI -->
              <div v-if="!message.isUser" class="flex items-start space-x-3 mb-4">
                <div v-if="store.chat_avatar_image" class="w-8 h-8 overflow-hidden flex-shrink-0 ring-2 ring-gray-100" style="border-radius: 50%;">
                  <img :src="store.chat_avatar_image" :alt="store.assistant_name" class="w-full h-full object-cover">
                </div>
                <div v-else class="w-8 h-8 flex items-center justify-center flex-shrink-0 text-xs font-bold text-white ring-2 ring-gray-100"
                     :style="{ backgroundColor: store.chat_theme_color, borderRadius: '50%' }">
                  {{ store.assistant_name ? store.assistant_name.charAt(0).toUpperCase() : 'AI' }}
                </div>
                <div class="bg-white px-4 py-3 shadow-md max-w-xs border border-gray-200 message-bubble" style="border-radius: 20px 20px 20px 6px;">
                  <div class="text-gray-800 text-sm leading-relaxed message-content" v-html="formatMessage(message.text)"></div>
                  <div class="text-xs text-gray-500 mt-2">{{ formatTime(message.timestamp) }}</div>
                </div>
              </div>

              <!-- Message User -->
              <div v-else class="flex items-start justify-end space-x-3 mb-4">
                <div class="text-white px-4 py-3 shadow-md max-w-xs" style="border-radius: 20px 20px 6px 20px;"
                     :style="{ backgroundColor: store.chat_theme_color }">
                  <div class="text-sm leading-relaxed message-content" v-html="formatMessage(message.text)"></div>
                  <div class="text-xs text-white/80 mt-2">{{ formatTime(message.timestamp) }}</div>
                </div>
                <div class="w-8 h-8 bg-gray-300 flex items-center justify-center flex-shrink-0 ring-2 ring-gray-100" style="border-radius: 50%;">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
            </div>
          </TransitionGroup>

          <!-- Loading indicator migliorato -->
          <div v-if="isLoading" class="flex items-start space-x-3 mb-4 chatbot-message-ai">
            <div v-if="store.chat_avatar_image" class="w-8 h-8 overflow-hidden flex-shrink-0 ring-2 ring-gray-100" style="border-radius: 50%;">
              <img :src="store.chat_avatar_image" :alt="store.assistant_name" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-8 h-8 flex items-center justify-center text-xs font-bold text-white ring-2 ring-gray-100"
                 :style="{ backgroundColor: store.chat_theme_color, borderRadius: '50%' }">
              {{ store.assistant_name ? store.assistant_name.charAt(0).toUpperCase() : 'AI' }}
            </div>
            <div class="bg-white px-4 py-3 shadow-md border border-gray-200" style="border-radius: 20px 20px 20px 6px;">
              <div class="flex items-center space-x-2">
                <span class="text-gray-600 text-sm">{{ store.assistant_name }} sta scrivendo</span>
                <div class="typing-dots">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Suggestions migliorati -->
        <div v-if="showSuggestions" class="px-6 py-4 border-t border-gray-100 bg-white">
          <div class="flex flex-wrap gap-2">
            <button
              v-for="suggestion in suggestions"
              :key="suggestion"
              @click="sendSuggestion(suggestion)"
              class="text-sm px-4 py-2 border-2 transition-all duration-300 hover:shadow-lg transform hover:scale-105 suggestion-button"
              style="border-radius: 16px;"
              :style="{ borderColor: store.chat_theme_color + '40', color: store.chat_theme_color }"
              :onmouseover="`this.style.backgroundColor='${store.chat_theme_color}'; this.style.color='white'`"
              :onmouseout="`this.style.backgroundColor='transparent'; this.style.color='${store.chat_theme_color}'`"
            >
              {{ suggestion }}
            </button>
          </div>
        </div>

        <!-- Input Area migliorato -->
        <div class="p-6 border-t border-gray-100 bg-white">
          <form @submit.prevent="sendMessage" class="flex space-x-3">
            <input
              v-model="newMessage"
              :disabled="isLoading"
              ref="messageInput"
              type="text"
              :placeholder="`Scrivi a ${store.assistant_name}...`"
              class="flex-1 px-4 py-3 text-sm border border-gray-200 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 disabled:opacity-50 bg-gray-50/50"
              style="border-radius: 16px;"
              :style="{ focusRingColor: store.chat_theme_color }"
            >
            <button
              type="submit"
              :disabled="!newMessage.trim() || isLoading || isMessageTooLong"
              class="text-white px-5 py-3 font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-105 text-sm"
              style="border-radius: 16px;"
              :style="{ backgroundColor: store.chat_theme_color }"
            >
              <svg v-if="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
              <div v-else class="w-5 h-5 border-2 border-white border-t-transparent animate-spin" style="border-radius: 50%;"></div>
            </button>
          </form>

          <!-- Info migliorate -->
          <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
            <span v-if="store.opening_hours && getCurrentStatus()" class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-green-400 animate-pulse" style="border-radius: 50%;"></div>
              <span class="font-medium">{{ getCurrentStatus() }}</span>
            </span>
            <span :class="{ 'text-red-500 font-semibold': isMessageTooLong }">
              {{ characterCount }}/{{ maxMessageLength }}
            </span>
          </div>
        </div>
      </div>

      <!-- Lead Collection Modal -->
      <div v-if="showLeadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white max-w-md w-full mx-4" style="border-radius: 20px;">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">💌 Resta in contatto!</h3>
              <button @click="closeLeadModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <p class="text-gray-600 mb-4 text-sm">
              Lascia i tuoi dati per ricevere aggiornamenti, offerte speciali e consigli personalizzati da {{ store.name }}!
            </p>

            <form @submit.prevent="submitLeadForm">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                  <input
                    v-model="leadForm.email"
                    type="email"
                    required
                    class="w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                    style="border-radius: 8px;"
                    :style="{ '--focus-color': store.chat_theme_color }"
                    placeholder="la-tua-email@esempio.com"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                  <input
                    v-model="leadForm.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                    style="border-radius: 8px;"
                    :style="{ '--focus-color': store.chat_theme_color }"
                    placeholder="Il tuo nome"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp (opzionale)</label>
                  <input
                    v-model="leadForm.whatsapp"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                    style="border-radius: 8px;"
                    :style="{ '--focus-color': store.chat_theme_color }"
                    placeholder="+39 123 456 7890"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Interessi (opzionale)</label>
                  <input
                    v-model="leadForm.tag"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                    style="border-radius: 8px;"
                    :style="{ '--focus-color': store.chat_theme_color }"
                    placeholder="Es: piante da interno, giardinaggio..."
                  >
                </div>
              </div>

              <div class="flex space-x-3 mt-6">
                <button
                  type="button"
                  @click="closeLeadModal"
                  class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors text-sm font-medium"
                  style="border-radius: 8px;"
                >
                  Non ora
                </button>
                <button
                  type="submit"
                  :disabled="!leadForm.email || isSubmittingLead"
                  class="flex-1 px-4 py-2 text-white font-medium transition-all disabled:opacity-50 text-sm"
                  style="border-radius: 8px;"
                  :style="{ backgroundColor: store.chat_theme_color }"
                >
                  <span v-if="!isSubmittingLead">Iscriviti 📧</span>
                  <span v-else class="flex items-center justify-center">
                    <div class="w-4 h-4 border-2 border-white border-t-transparent animate-spin mr-2" style="border-radius: 50%;"></div>
                    Invio...
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Store Info migliorata -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-400 bg-white/50 px-4 py-2 inline-block shadow-sm" style="border-radius: 20px;">
          Powered by AI • {{ store.name }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick, computed } from 'vue'

export default {
  name: 'CompactChatbot',
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

    // Lead collection state
    const showLeadModal = ref(false)
    const isSubmittingLead = ref(false)
    const leadForm = ref({
      email: '',
      name: '',
      whatsapp: '',
      tag: '',
      latitude: null,
      longitude: null,
      city: null,
      region: null,
      country: null,
      country_code: null,
      postal_code: null,
      timezone: null,
      location_accuracy: null
    })
    const leadSubmitted = ref(false)
    const messagesSinceStart = ref(0)

    // New lead flow state
    const hasUserName = ref(false)
    const hasUserEmail = ref(false)
    const awaitingNameResponse = ref(true)
    const awaitingEmailAfterFirstAI = ref(false)

    const suggestions = computed(() => {
      return props.store.chat_suggestions || [
        '🕒 Orari',
        '📍 Dove siete',
        '🌱 Consigli',
        '💧 Cura piante',
        '📞 Contatti'
      ]
    })

    // Character count for input
    const characterCount = computed(() => newMessage.value.length)
    const isMessageTooLong = computed(() => characterCount.value > maxMessageLength.value)

    // Adjust color brightness
    const adjustColor = (color, amount) => {
      const num = parseInt(color.replace("#", ""), 16);
      const amt = Math.round(2.55 * amount);
      const R = (num >> 16) + amt;
      const G = (num >> 8 & 0x00FF) + amt;
      const B = (num & 0x0000FF) + amt;
      return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }

    // Get current opening status
    const getCurrentStatus = () => {
      if (!props.store.opening_hours) return null

      const now = new Date()
      const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']
      const today = dayNames[now.getDay()]
      const currentTime = now.toTimeString().slice(0, 5)

      const todayHours = props.store.opening_hours[today]
      if (!todayHours || todayHours.closed) {
        return 'Chiuso oggi'
      }

      if (todayHours.open && todayHours.close) {
        if (currentTime >= todayHours.open && currentTime <= todayHours.close) {
          return `Aperto fino alle ${todayHours.close}`
        } else {
          return `Apre alle ${todayHours.open}`
        }
      }

      return null
    }

    // Generate welcome message with store context
    const getWelcomeMessage = () => {
      // Always start by asking for name
      return `Ciao! 👋 Sono ${props.store.assistant_name}, il tuo assistente virtuale per ${props.store.name}.\n\nPer offrirti un'esperienza più personalizzata, come ti chiami? 😊`
    }

    // Format markdown-like text with enhanced support for plant care responses
    const formatMessage = (text) => {
      return text
        .replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-gray-900">$1</strong>')
        .replace(/\*(.*?)\*/g, '<em class="italic text-gray-700">$1</em>')
        .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-xs font-mono">$1</code>')
        // Converti punti elenco in liste HTML
        .replace(/^• (.+)$/gm, '<li class="ml-4 mb-1">• $1</li>')
        .replace(/^- (.+)$/gm, '<li class="ml-4 mb-1">• $1</li>')
        .replace(/^\* (.+)$/gm, '<li class="ml-4 mb-1">• $1</li>')
        // Converti liste numerate
        .replace(/^(\d+)\. (.+)$/gm, '<li class="ml-4 mb-1">$1. $2</li>')
        // Migliora la spaziatura tra paragrafi
        .replace(/\n\n/g, '<br><br>')
        .replace(/\n/g, '<br>')
        // Evidenzia sezioni importanti (es: "ATTENZIONE:", "IMPORTANTE:")
        .replace(/^(ATTENZIONE|IMPORTANTE|NOTA|CONSIGLIO):/gm, '<strong class="text-amber-600 font-bold">$1:</strong>')
    }

    // Generate session ID
    const generateSessionId = () => {
      return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now()
    }

    // Format timestamp
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

      // Handle AI responses for email request
      if (!isUser && awaitingEmailAfterFirstAI.value && hasUserName.value && !hasUserEmail.value) {
        // Add email request after AI response
        setTimeout(() => {
          const emailRequestMessage = {
            id: Date.now() + Math.random(),
            text: `Perfetto ${leadForm.value.name}! 😊\n\nPer inviarti le nostre migliori offerte e consigli personalizzati, potresti condividere la tua email? 📧✨\n\n*Non preoccuparti, rispettiamo la tua privacy e puoi cancellarti quando vuoi.*`,
            isUser: false,
            timestamp: Date.now()
          }
          messages.value.push(emailRequestMessage)
          scrollToBottom()
          awaitingEmailAfterFirstAI.value = false
        }, 1000)
      }

      return message
    }

    // Send message
    const sendMessage = async () => {
      if (!newMessage.value.trim() || isLoading.value) return

      const messageText = newMessage.value.trim()

      // Check if user is providing email after name
      if (hasUserName.value && !hasUserEmail.value && !awaitingNameResponse.value && !awaitingEmailAfterFirstAI.value) {
        // Check if this looks like an email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (emailRegex.test(messageText)) {
          hasUserEmail.value = true
          leadForm.value.email = messageText

          addMessage(messageText, true)

          // Save the lead automatically
          await saveLeadData()

          // Send welcome message after email collection
          const welcomeWithName = `Grazie ${leadForm.value.name}! 🎉\n\nOra posso aiutarti al meglio. Cosa vorresti sapere su ${props.store.name}? 🌱\n\n*Hai già la mia email nella lista per offerte speciali!* ✅`
          addMessage(welcomeWithName, false)

          showSuggestions.value = true
          newMessage.value = ''
          return
        }
      }

      // If we're waiting for name, handle name collection
      if (awaitingNameResponse.value) {
        // Save the name and update states
        hasUserName.value = true
        leadForm.value.name = messageText.trim()
        awaitingNameResponse.value = false
        awaitingEmailAfterFirstAI.value = true

        addMessage(messageText, true)

        // Send name confirmation
        const nameConfirmation = `Piacere di conoscerti, ${leadForm.value.name}! 😊\n\nAdesso dimmi, cosa ti interessa di più? Posso aiutarti con consigli sulle piante, informazioni sui nostri prodotti, orari di apertura e molto altro! 🌿`
        setTimeout(() => {
          addMessage(nameConfirmation, false)
        }, 800)

        newMessage.value = ''
        return
      }

      addMessage(messageText, true)
      newMessage.value = ''

      // If we're asking for email, don't send to AI yet
      if (!hasUserEmail.value && hasUserName.value) {
        return
      }

      isLoading.value = true
      showSuggestions.value = false

      try {
        // Include store context in the message
        let contextualMessage = messageText
        if (props.store.chat_context) {
          contextualMessage = `Contesto negozio: ${props.store.chat_context}\n\nCliente: ${leadForm.value.name || 'Cliente'}\nDomanda: ${messageText}`
        }

        const response = await fetch(`/api/chatbot/${props.store.slug}/message`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({
            message: contextualMessage,
            session_id: sessionId.value,
            ref: props.refCode,
            user_name: leadForm.value.name,
            latitude: leadForm.value.latitude,
            longitude: leadForm.value.longitude,
            location_data: {
              city: leadForm.value.city,
              region: leadForm.value.region,
              country: leadForm.value.country,
              country_code: leadForm.value.country_code,
              postal_code: leadForm.value.postal_code,
              timezone: leadForm.value.timezone,
              accuracy: leadForm.value.location_accuracy
            }
          })
        })

        const data = await response.json()

        if (data.success) {
          addMessage(data.response, false)
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

    // Lead collection methods
    const closeLeadModal = () => {
      showLeadModal.value = false
    }

    // Get user location
    const getUserLocation = () => {
      return new Promise((resolve) => {
        if (!navigator.geolocation) {
          resolve(null)
          return
        }

        navigator.geolocation.getCurrentPosition(
          (position) => {
            resolve({
              latitude: position.coords.latitude,
              longitude: position.coords.longitude,
              accuracy: position.coords.accuracy
            })
          },
          (error) => {
            console.warn('Geolocation error:', error)
            resolve(null)
          },
          {
            timeout: 10000,
            maximumAge: 300000, // 5 minutes
            enableHighAccuracy: false
          }
        )
      })
    }

    // Get location info from IP
    const getLocationFromIP = async () => {
      try {
        const response = await fetch('https://ipapi.co/json/')
        if (response.ok) {
          const data = await response.json()
          return {
            latitude: data.latitude,
            longitude: data.longitude,
            country: data.country_name,
            country_code: data.country_code,
            region: data.region,
            city: data.city,
            postal_code: data.postal,
            timezone: data.timezone
          }
        }
      } catch (error) {
        console.warn('IP location error:', error)
      }
      return null
    }

    // Save lead data automatically
    const saveLeadData = async () => {
      if (!leadForm.value.email || !leadForm.value.name) return

      try {
        // Get user location (GPS first, then IP fallback)
        const gpsLocation = await getUserLocation()
        const ipLocation = await getLocationFromIP()

        // Combine location data
        const locationData = {
          latitude: gpsLocation?.latitude || ipLocation?.latitude,
          longitude: gpsLocation?.longitude || ipLocation?.longitude,
          country: ipLocation?.country,
          country_code: ipLocation?.country_code,
          region: ipLocation?.region,
          city: ipLocation?.city,
          postal_code: ipLocation?.postal_code,
          timezone: ipLocation?.timezone
        }

        // Update leadForm with location data for future chat messages
        leadForm.value.latitude = locationData.latitude
        leadForm.value.longitude = locationData.longitude
        leadForm.value.city = locationData.city
        leadForm.value.region = locationData.region
        leadForm.value.country = locationData.country
        leadForm.value.country_code = locationData.country_code
        leadForm.value.postal_code = locationData.postal_code
        leadForm.value.timezone = locationData.timezone
        leadForm.value.location_accuracy = gpsLocation?.accuracy

        const response = await fetch(`/api/stores/${props.store.slug}/save-lead`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            ...leadForm.value,
            ...locationData,
            session_id: sessionId.value,
            source: 'chatbot_flow'
          })
        })

        const data = await response.json()

        if (data.success) {
          leadSubmitted.value = true
          // Store in localStorage to prevent showing again
          localStorage.setItem('lead_submitted_' + props.store.slug, 'true')
        }
      } catch (error) {
        console.error('Lead auto-save error:', error)
      }
    }

    const submitLeadForm = async () => {
      if (!leadForm.value.email || isSubmittingLead.value) return

      isSubmittingLead.value = true

      try {
        // Get user location (GPS first, then IP fallback)
        const gpsLocation = await getUserLocation()
        const ipLocation = await getLocationFromIP()

        // Combine location data
        const locationData = {
          latitude: gpsLocation?.latitude || ipLocation?.latitude,
          longitude: gpsLocation?.longitude || ipLocation?.longitude,
          country: ipLocation?.country,
          country_code: ipLocation?.country_code,
          region: ipLocation?.region,
          city: ipLocation?.city,
          postal_code: ipLocation?.postal_code,
          timezone: ipLocation?.timezone
        }

        const response = await fetch(`/api/stores/${props.store.slug}/save-lead`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            ...leadForm.value,
            ...locationData,
            session_id: sessionId.value,
            source: 'chatbot'
          })
        })

        const data = await response.json()

        if (data.success) {
          leadSubmitted.value = true
          showLeadModal.value = false

          // Reset form
          leadForm.value = {
            email: '',
            name: '',
            whatsapp: '',
            tag: ''
          }

          // Show success message in chat
          addMessage('✅ Perfetto! Ti ho aggiunto alla nostra lista. Riceverai presto aggiornamenti e offerte speciali!', false)

          // Store in localStorage to prevent showing again
          localStorage.setItem('lead_submitted_' + props.store.slug, 'true')
        } else {
          addMessage('❌ Si è verificato un errore nel salvataggio. Riprova più tardi.', false)
        }
      } catch (error) {
        console.error('Lead submission error:', error)
        addMessage('❌ Errore di connessione. Controlla la connessione internet e riprova.', false)
      } finally {
        isSubmittingLead.value = false
      }
    }

    // Clear chat
    const clearChat = () => {
      messages.value = []
      messagesSinceStart.value = 0

      // Reset lead flow state
      if (!leadSubmitted.value) {
        hasUserName.value = false
        hasUserEmail.value = false
        awaitingNameResponse.value = true
        awaitingEmailAfterFirstAI.value = false
        showSuggestions.value = false
        leadForm.value = {
          email: '',
          name: '',
          whatsapp: '',
          tag: '',
          latitude: null,
          longitude: null,
          city: null,
          region: null,
          country: null,
          country_code: null,
          postal_code: null,
          timezone: null,
          location_accuracy: null
        }
        addMessage(getWelcomeMessage(), false)
      } else {
        showSuggestions.value = true
        const returningMessage = props.store.chat_opening_message ||
          `Ciao! 👋 Sono ${props.store.assistant_name}, il tuo assistente virtuale per ${props.store.name}. Come posso aiutarti oggi?`
        addMessage(returningMessage, false)
      }
    }

    // Get location on chat start
    const initializeLocation = async () => {
      try {
        const gpsLocation = await getUserLocation()
        const ipLocation = await getLocationFromIP()

        // Update leadForm with location data immediately
        leadForm.value.latitude = gpsLocation?.latitude || ipLocation?.latitude
        leadForm.value.longitude = gpsLocation?.longitude || ipLocation?.longitude
        leadForm.value.city = ipLocation?.city
        leadForm.value.region = ipLocation?.region
        leadForm.value.country = ipLocation?.country
        leadForm.value.country_code = ipLocation?.country_code
        leadForm.value.postal_code = ipLocation?.postal_code
        leadForm.value.timezone = ipLocation?.timezone
        leadForm.value.location_accuracy = gpsLocation?.accuracy
      } catch (error) {
        console.warn('Could not get location:', error)
      }
    }

    // Initialize
    onMounted(async () => {
      sessionId.value = localStorage.getItem('chatbot_session_' + props.store.slug) || generateSessionId()
      localStorage.setItem('chatbot_session_' + props.store.slug, sessionId.value)

      // Initialize location data
      await initializeLocation()

      // Check if lead was already submitted
      leadSubmitted.value = localStorage.getItem('lead_submitted_' + props.store.slug) === 'true'

      // If lead already submitted, skip the name/email flow
      if (leadSubmitted.value) {
        hasUserName.value = true
        hasUserEmail.value = true
        awaitingNameResponse.value = false
        awaitingEmailAfterFirstAI.value = false
        showSuggestions.value = true

        // Use custom or default welcome message for returning users
        const returningMessage = props.store.chat_opening_message ||
          `Ciao! 👋 Sono ${props.store.assistant_name}, il tuo assistente virtuale per ${props.store.name}. Come posso aiutarti oggi?`
        addMessage(returningMessage, false)
      } else {
        // New user - start with name collection
        addMessage(getWelcomeMessage(), false)
        showSuggestions.value = false
      }

      if (props.prefilledQuestion && leadSubmitted.value) {
        newMessage.value = props.prefilledQuestion
        if (props.refCode) {
          // Track QR scan
        }
      }

      nextTick(() => {
        if (messageInput.value) {
          messageInput.value.focus()
        }
      })

      // Initialize location
      initializeLocation()
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
      // Lead collection
      showLeadModal,
      isSubmittingLead,
      leadForm,
      leadSubmitted,
      closeLeadModal,
      submitLeadForm,
      // New lead flow
      hasUserName,
      hasUserEmail,
      awaitingNameResponse,
      awaitingEmailAfterFirstAI,
      saveLeadData,
      // Methods
      sendMessage,
      sendSuggestion,
      clearChat,
      formatTime,
      formatMessage,
      adjustColor,
      getCurrentStatus,
      getWelcomeMessage
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

.chatbot-message-ai {
  animation: slideInLeft 0.3s ease-out;
}

.chatbot-message-user {
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.typing-dots {
  display: inline-flex;
  align-items: center;
}

.typing-dots span {
  height: 3px;
  width: 3px;
  background: #6b7280;
  border-radius: 50%;
  display: inline-block;
  margin: 0 1px;
  animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: 0s; }
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

/* Stili per formattazione migliorata delle risposte */
.message-content {
  line-height: 1.5;
}

.message-content li {
  margin-bottom: 4px;
  padding-left: 4px;
}

.message-content strong {
  color: #1f2937;
}

.message-content em {
  color: #4b5563;
}

.message-content code {
  font-family: 'Courier New', monospace;
  background-color: #f3f4f6;
  padding: 2px 4px;
  border-radius: 6px;
}

/* Stili aggiuntivi per migliorare il layout */
.chatbot-container {
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

.message-bubble {
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
}

/* Animazioni migliorati per i suggerimenti */
.suggestion-button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.suggestion-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Miglioramenti per l'input */
input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Smooth scrolling per i messaggi */
.messages-container {
  scroll-behavior: smooth;
}

.messages-container::-webkit-scrollbar {
  width: 4px;
}

.messages-container::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 20px;
}

.messages-container::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 20px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-6px);
  }
}
</style>
