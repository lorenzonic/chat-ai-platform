import './bootstrap';

import Alpine from 'alpinejs';
import { createApp } from 'vue';
import ChatbotApp from './components/ChatbotApp.vue';
import AnalyticsDashboard from './components/AnalyticsDashboard.vue';

window.Alpine = Alpine;

Alpine.start();

// Vue setup per componenti specifici
document.addEventListener('DOMContentLoaded', function() {
    // Chatbot component
    const chatbotElement = document.getElementById('modern-chatbot');
    if (chatbotElement) {
        console.log('Mounting Vue chatbot app...');
        try {
            // Ottieni i dati dalle data attributes o dal DOM
            const storeData = chatbotElement.dataset.store ? JSON.parse(chatbotElement.dataset.store) : null;
            const prefilledQuestion = chatbotElement.dataset.prefilledQuestion || null;
            const refCode = chatbotElement.dataset.refCode || null;

            const app = createApp(ChatbotApp, {
                store: storeData,
                prefilledQuestion: prefilledQuestion,
                refCode: refCode
            });

            app.mount('#modern-chatbot');
            console.log('Vue chatbot app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue chatbot app:', error);
        }
    }

    // Analytics dashboard component
    const analyticsElement = document.getElementById('analytics-app');
    if (analyticsElement) {
        console.log('Mounting Vue analytics app...');
        try {
            const app = createApp({
                components: {
                    AnalyticsDashboard
                }
            });

            app.mount('#analytics-app');
            console.log('Vue analytics app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue analytics app:', error);
        }
    }
});
